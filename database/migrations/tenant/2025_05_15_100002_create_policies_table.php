<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\AccessScope;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up()
    {
        Schema::create('policies', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->uuid('owner_id')->nullable();
            $table->foreign('owner_id')->references('id')->on('users');
            $table->boolean('is_default')->default(false);
            $table->boolean('show_all_employees')->default(false);
            $table->enum('show_employees', array_map('strtoupper', array_column(AccessScope::cases(), 'value')))->default(AccessScope::ALL->value);
            $table->boolean('attendance_nullable')->default(false);
            $table->boolean('nearby_checkin_enable')->default(false);
            $table->integer('easy_check_in_range')->default(100);
            $table->integer('version')->default(1);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->uuid('authorizer_id')->nullable();
            $table->foreign('authorizer_id')->references('id')->on('users');
            $table->boolean('auto_attendance')->default(false);
            $table->integer('country_id')->nullable();
            $table->string('payslip_company_name')->nullable();
            $table->uuid('payslip_logo_id')->nullable();
            $table->foreign('payslip_logo_id')->references('id')->on('files');
            $table->text('payslip_company_address')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        
        $supportEmail = config('abc.support.email');
        $support = DB::table('users')->where('email', $supportEmail)->first();

        if ($support) {
            DB::table('policies')->insert([
                'id' => Str::uuid(),
                'name' => 'Standard Policy',
                'description' => 'Standard policy for company',
                'owner_id' => $support->id,
                'authorizer_id' => $support->id,
                'is_default' => true,
                'show_all_employees' => true,
                'show_employees' => AccessScope::ALL->value,
                'attendance_nullable' => false,
                'nearby_checkin_enable' => true,
                'easy_check_in_range' => 100,
                'version' => 1,
                'start_date' => now(),
                'auto_attendance' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down()
    {
        Schema::dropIfExists('policies');
    }
};
