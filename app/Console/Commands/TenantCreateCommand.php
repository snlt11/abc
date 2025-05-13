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
        
        $port = config('tenancy.port', 9000);
        $this->info("\nYou can access this tenant at: http://{$domain}:{$port}");
        
        return $tenant;
    }
    
    /**
     * Create a support account for the tenant
     *
     * @return \App\Models\User
     */
    private function createSupportAccount(): User
    {
        $this->info("\nCreating support account...");
        $supportUser = User::create([
            'name' => 'Support',
            'email' => 'support@abc.com',
            'password' => Hash::make('supersecure'),
            'email_verified_at' => now(),
        ]);
        
        return $supportUser;
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