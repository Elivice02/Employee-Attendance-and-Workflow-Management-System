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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('type'); // e.g., 'leave_request', 'leave_approved', 'task_assigned', 'late_attendance'
            $table->string('title');
            $table->text('message');
            $table->string('notifiable_type')->nullable(); // Related model type (e.g., 'App\Models\Leave')
            $table->unsignedBigInteger('notifiable_id')->nullable(); // Related model ID
            $table->string('action_url')->nullable(); // URL to navigate to when clicked
            $table->string('icon')->nullable(); // Icon class (e.g., 'bell', 'check', 'task')
            $table->string('color')->nullable(); // Color class (e.g., 'blue', 'green', 'yellow')
            $table->boolean('read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('type');
            $table->index('read');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
