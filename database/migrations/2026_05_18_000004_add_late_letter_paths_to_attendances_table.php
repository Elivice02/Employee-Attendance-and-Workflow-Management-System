<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->string('late_letter_draft_path')->nullable()->after('late_letter_reference');
            $table->string('late_letter_final_path')->nullable()->after('late_letter_draft_path');
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn(['late_letter_draft_path', 'late_letter_final_path']);
        });
    }
};
