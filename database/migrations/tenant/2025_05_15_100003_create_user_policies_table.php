<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_policies', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->uuid('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->uuid('added_by');
            $table->foreign('added_by')->references('id')->on('users');
            $table->uuid('removed_by')->nullable();
            $table->foreign('removed_by')->references('id')->on('users');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_policies');
    }
};
