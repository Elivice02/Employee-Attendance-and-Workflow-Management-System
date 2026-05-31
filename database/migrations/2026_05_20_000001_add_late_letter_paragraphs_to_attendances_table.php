<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->text('late_opening_paragraph')->nullable()->after('late_explanation');
            $table->text('late_closing_paragraph')->nullable()->after('late_opening_paragraph');
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn(['late_opening_paragraph', 'late_closing_paragraph']);
        });
    }
};
