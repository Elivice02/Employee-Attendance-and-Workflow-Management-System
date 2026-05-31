<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->string('ref_no')->nullable()->unique()->after('id');
            $table->unsignedTinyInteger('leave_type_number')->nullable()->after('leave_type');
            $table->string('leave_type_name')->nullable()->after('leave_type_number');
            $table->string('leave_type_standing_order')->nullable()->after('leave_type_name');
            $table->string('current_stage')->default('employee_submitted')->after('status');
            $table->json('section_a')->nullable()->after('attachment_path');
            $table->json('section_b')->nullable()->after('section_a');
            $table->json('section_c')->nullable()->after('section_b');
            $table->json('section_d')->nullable()->after('section_c');
            $table->timestamp('submitted_at')->nullable()->after('section_d');
            $table->timestamp('supervisor_reviewed_at')->nullable()->after('supervisor_comment');
            $table->timestamp('hr_reviewed_at')->nullable()->after('hr_comment');
            $table->string('pdf_path')->nullable()->after('hr_reviewed_at');
        });
    }

    public function down(): void
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->dropUnique(['ref_no']);
            $table->dropColumn([
                'ref_no',
                'leave_type_number',
                'leave_type_name',
                'leave_type_standing_order',
                'current_stage',
                'section_a',
                'section_b',
                'section_c',
                'section_d',
                'submitted_at',
                'supervisor_reviewed_at',
                'hr_reviewed_at',
                'pdf_path',
            ]);
        });
    }
};
