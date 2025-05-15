<?php

use App\Enums\AccessScope;
use App\Enums\Module;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        if (!tenancy()->initialized) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->foreignUuid('permission_id')
                  ->nullable()
                  ->after('remember_token')
                  ->constrained('permissions')
                  ->onDelete('set null');
        });
        $supportEmail = config('abc.support.email');
        $supportPassword = config('abc.support.password');
        
        $permissions = [
            [
                'id' => (string) Str::uuid(),
                'name' => 'Admin Permission',
                'description' => 'Full access to all modules and features',
                'access_scope' => AccessScope::ALL->value,
                'authorizer_id' => null,
                'owner_id' => null,
                'is_default' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => (string) Str::uuid(),
                'name' => 'Manager Permission',
                'description' => 'Manager level access with limited administrative capabilities',
                'access_scope' => AccessScope::DEPARTMENT->value,
                'authorizer_id' => null,
                'owner_id' => null,
                'is_default' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => (string) Str::uuid(),
                'name' => 'Employee Permission',
                'description' => 'Basic employee access with limited permissions',
                'access_scope' => AccessScope::TEAM->value,
                'authorizer_id' => null,
                'owner_id' => null,
                'is_default' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];


        $support = DB::table('users')->where('email', $supportEmail)->first();

        if (!$support) {
            $supportId = (string) Str::uuid();
            DB::table('users')->insert([
                'id' => $supportId,
                'name' => 'Support',
                'email' => $supportEmail,
                'password' => Hash::make($supportPassword),
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            $support = (object) [
                'id' => $supportId,
                'email' => $supportEmail
            ];
        }

        foreach ($permissions as &$permission) {
            $permission['authorizer_id'] = $support->id;
            $permission['owner_id'] = $support->id;
        }
        unset($permission);

        foreach ($permissions as $permissionData) {
            DB::table('permissions')->insert($permissionData);
            
            if ($permissionData['name'] === 'Admin Permission') {
                DB::table('users')
                    ->where('id', $support->id)
                    ->update(['permission_id' => $permissionData['id']]);
            }

            foreach (Module::cases() as $module) {
                $modulePermissions = [
                    'id' => (string) Str::uuid(),
                    'name' => $module->value,
                    'permission_id' => $permissionData['id'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                switch ($permissionData['name']) {
                    case 'Admin Permission':
                        $modulePermissions['create'] = true;
                        $modulePermissions['view'] = true;
                        $modulePermissions['update'] = true;
                        $modulePermissions['delete'] = true;
                        break;
                    case 'Manager Permission':
                        $modulePermissions['create'] = true;
                        $modulePermissions['view'] = true;
                        $modulePermissions['update'] = true;
                        $modulePermissions['delete'] = false;
                        break;
                    case 'Employee Permission':
                    default:
                        $modulePermissions['create'] = false;
                        $modulePermissions['view'] = true;
                        $modulePermissions['update'] = false;
                        $modulePermissions['delete'] = false;
                        break;
                }

                DB::table('modules')->insert($modulePermissions);
            }
        }
    }

    public function down(): void
    {
        if (!tenancy()->initialized) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['permission_id']);
            $table->dropColumn('permission_id');
        });

        DB::table('modules')->truncate();
        DB::table('permissions')->truncate();
    }
};
