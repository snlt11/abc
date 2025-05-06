<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class MakeTenantMigrationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:migration:tenant {name : The name of the migration}
                           {--create= : The table to be created}
                           {--table= : The table to be migrated}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new migration file in the tenant directory';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $create = $this->option('create');
        $table = $this->option('table');
        
        $params = [
            'name' => $name,
            '--path' => 'database/migrations/tenant',
        ];
        
        if ($create) {
            $params['--create'] = $create;
        }
        
        if ($table) {
            $params['--table'] = $table;
        }
        
        $this->info("Creating tenant migration: {$name}");
        Artisan::call('make:migration', $params);
        
        $output = Artisan::output();
        $this->info($output);
        
        $this->info("Migration created successfully in the tenant directory.");
    }
}