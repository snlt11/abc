<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('industries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->unique();
            $table->timestamps();
            $table->softDeletes();
        });

        // Insert predefined industries
        $now = now();
        $industries = [
            ['id' => (string) Str::uuid(), 'name' => 'Technology', 'created_at' => $now, 'updated_at' => $now],
            ['id' => (string) Str::uuid(), 'name' => 'Finance', 'created_at' => $now, 'updated_at' => $now],
            ['id' => (string) Str::uuid(), 'name' => 'Healthcare', 'created_at' => $now, 'updated_at' => $now],
            ['id' => (string) Str::uuid(), 'name' => 'Education', 'created_at' => $now, 'updated_at' => $now],
            ['id' => (string) Str::uuid(), 'name' => 'Manufacturing', 'created_at' => $now, 'updated_at' => $now],
            ['id' => (string) Str::uuid(), 'name' => 'Retail', 'created_at' => $now, 'updated_at' => $now],
            ['id' => (string) Str::uuid(), 'name' => 'Hospitality', 'created_at' => $now, 'updated_at' => $now],
            ['id' => (string) Str::uuid(), 'name' => 'Construction', 'created_at' => $now, 'updated_at' => $now],
            ['id' => (string) Str::uuid(), 'name' => 'Transportation', 'created_at' => $now, 'updated_at' => $now],
            ['id' => (string) Str::uuid(), 'name' => 'Other', 'created_at' => $now, 'updated_at' => $now],
        ];

        DB::table('industries')->insert($industries);
    }

    public function down(): void
    {
        Schema::dropIfExists('industries');
    }
};