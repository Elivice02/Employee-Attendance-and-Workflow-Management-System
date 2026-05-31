<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->string('late_letter_reference')->nullable()->unique()->after('late_evidence_path');
            $table->timestamp('late_submitted_at')->nullable()->after('late_letter_reference');
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropUnique(['late_letter_reference']);
            $table->dropColumn(['late_letter_reference', 'late_submitted_at']);
        });
    }
};
