<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assigned_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('assigned_to')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('due_date')->nullable();
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->enum('status', ['assigned', 'in_progress', 'pending_review', 'completed', 'in_revision', 'archived'])->default('assigned');
            $table->enum('scope', ['operational', 'hr_compliance'])->default('operational');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['assigned_to', 'status']);
            $table->index(['assigned_by', 'status']);
            $table->index('due_date');
        });

        Schema::create('task_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained('tasks')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->text('progress_notes');
            $table->enum('status_after_update', ['assigned', 'in_progress', 'pending_review', 'completed', 'in_revision', 'archived']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_updates');
        Schema::dropIfExists('tasks');
    }
};
