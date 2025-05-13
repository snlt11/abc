<?php

use App\Enums\Industry;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('registration_number')->unique();
            $table->year('founding_year');
            $table->uuid('industry_id');
            $table->uuid('file_id')->nullable();
            $table->string('time_zone');
            $table->string('address');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('industry_id')->references('id')->on('industries');
            $table->foreign('file_id')->references('id')->on('files');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};