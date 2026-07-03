<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('announcement_recipients', function (Blueprint $table) {
            $table->id();

            $table->foreignId('announcement_id')
                  ->constrained('announcements')
                  ->cascadeOnDelete();

            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            // Read tracking
            $table->timestamp('read_at')
                  ->nullable();

            $table->timestamps();

            // Composite unique key (one recipient per announcement)
            $table->unique(['announcement_id', 'user_id']);

            // Indexes for queries
            $table->index('user_id');
            $table->index('read_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcement_recipients');
    }
};
