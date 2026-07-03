<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendance_settings', function (Blueprint $table) {
            $table->string('company_name')->default('Mzumbe Company Ltd')->after('id');
            $table->string('company_address')->nullable()->after('company_name');
            $table->time('work_end_time')->default('17:00:00')->after('work_start_time');
            $table->unsignedSmallInteger('password_expiry_days')->default(90)->after('late_evidence_required');
            $table->unsignedSmallInteger('default_annual_leave_days')->default(20)->after('password_expiry_days');
        });
    }

    public function down(): void
    {
        Schema::table('attendance_settings', function (Blueprint $table) {
            $table->dropColumn([
                'company_name',
                'company_address',
                'work_end_time',
                'password_expiry_days',
                'default_annual_leave_days',
            ]);
        });
    }
};
