<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant;
use App\Models\User;
use App\Models\TenantSetting;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Stancl\Tenancy\Facades\Tenancy;

class TenantCreateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new tenant with interactive prompts';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('===== Tenant Creation Wizard =====');
        
        $tenantId = $this->askWithValidation(
            'Enter tenant name (ID)',
            'id',
            ['required', 'string', 'max:255', 'unique:tenants,id']
        );
        
        $domain = $this->getDomainWithValidation();
        
        $isActive = $this->confirm(' Should this tenant be active?', true);
        
        // Create the tenant
        $this->info('Creating tenant...');
        
        try {
            $this->createTenant($tenantId, $domain, $isActive);
            
            Tenancy::initialize($tenantId);
            
            $supportUser = $this->createSupportAccount();
            $this->createTenantSettings($domain, $supportUser->id);
            
        } catch (\Exception $e) {
            $this->error("\n❌ Failed to create tenant: {$e->getMessage()}");
        }
    }
    
    /**
     * Create a new tenant and its domain
     *
     * @param string $tenantId
     * @param string $domain
     * @param bool $isActive
     * @return \App\Models\Tenant
     */
    private function createTenant(string $tenantId, string $domain, bool $isActive): Tenant
    {
        $tenant = Tenant::create([
            'id' => $tenantId,
            'active' => $isActive, 
        ]);
        
        // Create domain
        $tenant->domains()->create([
            'domain' => $domain
        ]);
        
        $this->info('Tenant created successfully!');
        $this->table(
            ['ID', 'Domain', 'Active'],
            [[$tenant->id, $domain, $isActive ? 'Yes' : 'No']]
        );
        
        $port = config('abc.port', 9000);
        $this->info("\nYou can access this tenant at: http://{$domain}:{$port}");
        
        return $tenant;
    }
    
    /**
     * Create a support account for the tenant
     *
     * @return \App\Models\User
     */
    private function createSupportAccount(): ?User
    {
        $supportEmail = config('abc.support.email');
        $supportPassword = config('abc.support.password');
        
        if (empty($supportEmail) || empty($supportPassword)) {
            throw new \RuntimeException('Support email or password is not configured. Please check your config/abc.php file.');
        }
        
        $this->info("\nChecking support account...");
        
        $supportUser = User::where('email', $supportEmail)->first();
        
        if ($supportUser) {
            $this->info("\nSupport account already exists with email: {$supportEmail}");
            return $supportUser;
        }
        
        $this->info("\nCreating new support account with email: {$supportEmail}");
        
        try {
            $supportUser = User::create([
                'id' => (string) \Illuminate\Support\Str::uuid(),
                'name' => 'Support',
                'email' => $supportEmail,
                'password' => Hash::make($supportPassword),
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            $this->info("\nSupport account created successfully");
            
            return $supportUser;
        } catch (\Exception $e) {
            $this->error("\nFailed to create support account: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Create tenant settings with support account as owner
     *
     * @param string $domain
     * @param string $ownerId
     * @return \App\Models\TenantSetting
     */
    private function createTenantSettings(string $domain, string $ownerId): TenantSetting
    {
        $this->info("\nCreating tenant settings");
        $tenantSettings = TenantSetting::create([
            'subdomain' => explode('.', $domain)[0],
            'owner_id' => $ownerId,
            'support_account_enable' => true,
        ]);
        
        $this->info("\nSupport account and tenant settings created successfully!");
        
        return $tenantSettings;
    }
    
    /**
     * Get domain with validation
     *
     * @return string
     */
    private function getDomainWithValidation(): string
    {
        $domain = $this->ask('Enter tenant domain (without .localhost)');
        
        // Automatically append .localhost if not already included
        if (!empty($domain) && !str_contains($domain, '.')) {
            $domain = $domain . '.localhost';
        }
        
        // Validate the domain
        $validator = Validator::make(['domain' => $domain], [
            'domain' => ['required', 'string', 'max:255', 'unique:domains,domain']
        ]);
        
        if ($validator->fails()) {
            $this->error($validator->errors()->first('domain'));
            return $this->getDomainWithValidation();
        }
        
        return $domain;
    }
    
    /**
     * Ask a question with validation
     *
     * @param string $question
     * @param string $field
     * @param array $rules
     * @return string
     */
    protected function askWithValidation($question, $field, $rules)
    {
        $value = null;
        
        while (!$value) {
            $value = $this->ask($question);
            
            try {
                $validator = Validator::make([$field => $value], [$field => $rules]);
                
                if ($validator->fails()) {
                    $this->error($validator->errors()->first($field));
                    $value = null;
                }
            } catch (\Exception $e) {
                $this->error($e->getMessage());
                $value = null;
            }
        }
        
        return $value;
    }
}