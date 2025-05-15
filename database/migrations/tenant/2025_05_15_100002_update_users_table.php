<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('known_as')->nullable()->after('permission_id');
            $table->uuid('nationality_id')->nullable()->after('known_as')->comment('References countries table');
            $table->enum('gender', ['MALE', 'FEMALE', 'OTHER'])->nullable()->after('nationality_id');
            $table->string('slug')->unique()->nullable()->after('gender');
            $table->string('phone')->nullable()->after('slug');
            $table->string('personal_email')->unique()->nullable()->after('phone');
            $table->uuid('profile_image_id')->nullable()->after('personal_email')->comment('References files table');
            $table->uuid('location_id')->nullable()->after('profile_image_id')->comment('References locations table');
            $table->uuid('position_id')->nullable()->after('location_id')->comment('References positions table');
            $table->uuid('manager_id')->nullable()->after('position_id');
            $table->date('date_of_birth')->nullable()->after('manager_id');
            $table->string('nrc')->nullable()->after('date_of_birth');
            $table->string('passport')->nullable()->after('nrc');
            $table->text('home_address')->nullable()->after('passport');
            $table->string('emergency_contact')->nullable()->after('home_address');
            $table->string('emergency_contact_number')->nullable()->after('emergency_contact');
            $table->uuid('department_id')->nullable()->after('emergency_contact_number');
            $table->date('service_join_date')->nullable()->after('department_id');
            $table->date('terminated_date')->nullable()->after('service_join_date');
            $table->enum('system_status', ['ACTIVE', 'DEACTIVE', 'DELETED'])->default('active')->after('terminated_date');
            $table->date('resignation_date')->nullable()->after('system_status');
            $table->boolean('is_offboarding')->default(false)->after('resignation_date');
            $table->boolean('is_resignation')->default(false)->after('is_offboarding');
            $table->date('contract_start_date')->nullable()->after('is_resignation');
            $table->date('contract_end_date')->nullable()->after('contract_start_date');
            $table->date('probation_end_date')->nullable()->after('contract_end_date');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreign('nationality_id')->references('id')->on('countries')->onDelete('set null');
            $table->foreign('profile_image_id')->references('id')->on('files')->onDelete('set null');
            $table->foreign('location_id')->references('id')->on('locations')->onDelete('set null');
            $table->foreign('position_id')->references('id')->on('positions')->onDelete('set null');
            $table->foreign('manager_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['nationality_id']);
            $table->dropForeign(['profile_image_id']);
            $table->dropForeign(['location_id']);
            $table->dropForeign(['position_id']);
            $table->dropForeign(['manager_id']);
            $table->dropForeign(['department_id']);
            
            $table->dropColumn([
                'known_as',
                'nationality_id',
                'gender',
                'slug',
                'phone',
                'personal_email',
                'profile_image_id',
                'location_id',
                'position_id',
                'manager_id',
                'date_of_birth',
                'nrc',
                'passport',
                'home_address',
                'emergency_contact',
                'emergency_contact_number',
                'department_id',
                'service_join_date',
                'terminated_date',
                'system_status',
                'resignation_date',
                'is_offboarding',
                'is_resignation',
                'contract_start_date',
                'contract_end_date',
                'probation_end_date'
            ]);
        });
    }
};
