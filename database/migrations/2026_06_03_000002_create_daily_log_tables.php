<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('task_id')->nullable()->constrained('tasks')->nullOnDelete();
            $table->date('log_date');
            $table->string('title');
            $table->text('activities');
            $table->text('task_progress')->nullable();
            $table->text('challenges')->nullable();
            $table->enum('status', ['pending', 'reviewed', 'rejected'])->default('pending');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'log_date']);
            $table->index(['status', 'log_date']);
        });

        Schema::create('daily_log_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daily_log_id')->constrained('daily_logs')->cascadeOnDelete();
            $table->foreignId('reviewed_by')->constrained('users')->cascadeOnDelete();
            $table->enum('reviewer_role', ['supervisor', 'hr']);
            $table->enum('status', ['approved', 'rejected']);
            $table->text('comment')->nullable();
            $table->timestamp('reviewed_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_log_reviews');
        Schema::dropIfExists('daily_logs');
    }
};
