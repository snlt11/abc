<?php

use App\Enums\AccessScope;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('access_scope', array_map('strtoupper', array_column(AccessScope::cases(), 'value')))
                ->default(AccessScope::ALL->value);
            $table->foreignUuid('authorizer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignUuid('owner_id')->nullable()->constrained('users')->onDelete('set null');
            $table->boolean('is_default')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};
