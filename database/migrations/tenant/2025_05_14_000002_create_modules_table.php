<?php

use App\Enums\Module;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('modules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $moduleValues = array_map(fn (Module $module) => $module->value, Module::cases());
            $table->enum('name', $moduleValues);
            $table->boolean('create')->default(false);
            $table->boolean('view')->default(false);
            $table->boolean('update')->default(false);
            $table->boolean('delete')->default(false);
            $table->foreignUuid('permission_id')->constrained('permissions')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('modules');
    }
};
