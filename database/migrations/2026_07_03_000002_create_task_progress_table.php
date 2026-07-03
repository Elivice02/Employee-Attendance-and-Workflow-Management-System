<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained('tasks')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('users')->cascadeOnDelete();
            $table->date('progress_date');
            $table->text('work_done');
            $table->unsignedInteger('completion_percentage');
            $table->text('challenges')->nullable();
            $table->string('attachment_path')->nullable();
            $table->timestamp('supervisor_reviewed_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('remarks')->nullable();
            $table->timestamps();

            // Indexes for performance
            $table->index(['task_id', 'progress_date']);
            $table->index(['employee_id', 'progress_date']);
            $table->index('progress_date');
            
            // Unique constraint to prevent duplicate daily entries
            $table->unique(['task_id', 'employee_id', 'progress_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_progress');
    }
};
