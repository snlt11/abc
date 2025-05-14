<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Tenant;
use Illuminate\Support\Facades\Hash;
use Stancl\Tenancy\Facades\Tenancy;
use Illuminate\Support\Facades\Validator;
use App\Helpers\CodeGenerator;

class CreateSupportAccountCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'support:create {--tenant= : The tenant ID to create the support account for}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a support account with default credentials';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('===== Creating Support Account =====');
        
        // Check if tenant option is provided
        $tenantId = $this->option('tenant');
        if (!$tenantId) {
            $this->error('Tenant ID is required. Use --tenant=TENANT_ID');
            return 1;
        }
        
        // Validate that the tenant exists
        $tenant = Tenant::find($tenantId);
        if (!$tenant) {
            $this->error("Tenant with ID '{$tenantId}' does not exist.");
            return 1;
        }
        
        $this->info("Creating support account for tenant: {$tenantId}");
        
        // Default credentials
        $email = config('abc.support.email');
        $name = 'Support';
        $password = config('abc.support.password');
        
        try {
            // Switch to tenant context
            Tenancy::initialize($tenantId);
            
            // Check if user already exists
            $existingUser = User::where('email', $email)->first();
            if ($existingUser) {
                $this->info("Support account already exists for tenant {$tenantId}.");
                $this->table(
                    ['Name', 'Email'],
                    [[$existingUser->name, $existingUser->email]]
                );
                
                // Ask if user wants to reset the password
                if ($this->confirm('Would you like to reset the password for this account?', false)) {
                    // Generate a new password using CodeGenerator
                    $newPassword = CodeGenerator::generate();
                    
                    $existingUser->password = Hash::make($newPassword);
                    $existingUser->save();
                    $this->info("Password has been reset successfully.");
                    $this->info("New password: {$newPassword}");
                }
                
                return 0;
            }
            
            // Create the user
            $userData = [
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'email_verified_at' => now(),
            ];
            
            // Validate user data
            $validator = Validator::make($userData, [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8',
            ]);
            
            if ($validator->fails()) {
                $this->error("Validation failed:");
                foreach ($validator->errors()->all() as $error) {
                    $this->error(" - {$error}");
                }
                return 1;
            }
            
            $user = User::create($userData);
            
            $this->info('Support account created successfully!');
            $this->table(
                ['Name', 'Email'],
                [[$user->name, $user->email]]
            );
            
        } catch (\Exception $e) {
            $this->error("\n❌ Failed to create support account: {$e->getMessage()}");
            return 1;
        }
        
        return 0;
    }
}