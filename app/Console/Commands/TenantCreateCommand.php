<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

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
        
        // Step 1: Get tenant ID with validation
        $tenantId = $this->askWithValidation(
            'Enter tenant name (ID)',
            'id',
            ['required', 'string', 'max:255', 'unique:tenants,id']
        );
        
        // Step 2: Get domain with validation
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
            return 1;
        }
        
        // Step 3: Ask if tenant should be active
        $isActive = $this->confirm(' Should this tenant be active?', true);
        
        // Create the tenant
        $this->info('Creating tenant...');
        
        try {
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
            
            $this->info("\nYou can access this tenant at: http://{$domain}:9000");
            
        } catch (\Exception $e) {
            $this->error("\n❌ Failed to create tenant: {$e->getMessage()}");
        }
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