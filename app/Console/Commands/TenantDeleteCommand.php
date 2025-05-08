<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant;

class TenantDeleteCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:delete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete a tenant and all associated data including database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('===== Tenant Deletion Wizard =====');
        
        // Step 1: Get tenant ID with validation
        $tenantId = $this->ask('Enter tenant name (ID) to delete');
        
        // Validate tenant exists
        $tenant = Tenant::find($tenantId);
        
        if (!$tenant) {
            $this->error("Tenant with ID '{$tenantId}' does not exist.");
            return 1;
        }
        
        // Get domain information for display
        $domain = $tenant->domains()->first();
        $domainName = $domain ? $domain->domain : 'No domain';
        
        // Display tenant information
        $this->info("\nTenant Information:");
        $this->table(
            ['ID', 'Domain', 'Active'],
            [[$tenant->id, $domainName, $tenant->active ? 'Yes' : 'No']]
        );
        
        // Database name that will be deleted
        $databaseName = config('tenancy.database.prefix') . $tenantId . config('tenancy.database.suffix');
        
        $this->warn("\nWARNING: This will permanently delete the tenant and all its data!");
        $this->warn("The following database will be deleted: {$databaseName}");
        
        // Step 2: Confirm deletion with database name for extra safety
        $confirmation = $this->ask("To confirm deletion, please type the tenant name '{$tenantId}' again");
        
        if ($confirmation !== $tenantId) {
            $this->error("Confirmation failed. Tenant deletion aborted.");
            return 1;
        }
        
        // Final confirmation
        if (!$this->confirm("Are you absolutely sure you want to delete this tenant and all its data?", false)) {
            $this->info("Tenant deletion cancelled.");
            return 0;
        }
        
        // Perform deletion
        $this->info("\nDeleting tenant...");
        
        try {
            $tenant->delete();
            
            $this->info("✅ Tenant '{$tenantId}' and its database '{$databaseName}' have been successfully deleted.");
            
        } catch (\Exception $e) {
            $this->error("\n❌ Failed to delete tenant: {$e->getMessage()}");
            return 1;
        }
        
        return 0;
    }
}