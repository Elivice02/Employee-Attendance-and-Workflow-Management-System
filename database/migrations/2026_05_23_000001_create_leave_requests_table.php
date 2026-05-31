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
        Schema::create('leave_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('leave_type', ['annual', 'sick', 'maternity', 'paternity', 'emergency', 'unpaid']);
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('total_days')->default(1);
            $table->text('reason');
            $table->string('attachment_path', 500)->nullable();
            $table->enum('status', ['pending', 'supervisor_approved', 'hr_approved', 'rejected'])->default('pending');
            $table->foreignId('supervisor_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('supervisor_comment')->nullable();
            $table->foreignId('hr_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('hr_comment')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('status');
            $table->index('supervisor_id');
            $table->index('hr_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_requests');
    }
};
