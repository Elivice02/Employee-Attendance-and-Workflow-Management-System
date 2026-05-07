<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            // Add new columns for HR ownership and control
            $table->text('description')->nullable()->after('name');
            
            // Rename supervisor_id to head_id for clarity
            $table->renameColumn('supervisor_id', 'head_id');
            
            // Add who created this department
            $table->foreignId('created_by')->nullable()->constrained('users')->after('head_id');
            
            // Track department status
            $table->enum('status', ['active', 'inactive'])->default('active')->after('created_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->dropColumn(['description', 'created_by', 'status']);
            $table->renameColumn('head_id', 'supervisor_id');
        });
    }
};
