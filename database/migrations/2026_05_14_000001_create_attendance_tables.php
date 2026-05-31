<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_settings', function (Blueprint $table) {
            $table->id();
            $table->time('work_start_time')->default('08:00:00');
            $table->unsignedSmallInteger('late_grace_minutes')->default(0);
            $table->text('allowed_networks')->nullable();
            $table->boolean('late_evidence_required')->default(true);
            $table->timestamps();
        });

        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('attendance_date');
            $table->timestamp('check_in_at')->nullable();
            $table->timestamp('check_out_at')->nullable();
            $table->string('check_in_ip', 45)->nullable();
            $table->string('check_out_ip', 45)->nullable();
            $table->string('status')->default('present');
            $table->text('late_explanation')->nullable();
            $table->string('late_evidence_path')->nullable();
            $table->string('late_review_status')->nullable();
            $table->foreignId('late_reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('late_reviewed_at')->nullable();
            $table->text('late_review_note')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'attendance_date']);
            $table->index(['attendance_date', 'status']);
        });

        Schema::create('attendance_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_id')->constrained()->cascadeOnDelete();
            $table->foreignId('recipient_id')->constrained('users')->cascadeOnDelete();
            $table->string('type')->default('late_check_in');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->unique(['attendance_id', 'recipient_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_notifications');
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('attendance_settings');
    }
};
