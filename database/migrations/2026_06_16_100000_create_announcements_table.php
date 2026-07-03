<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->text('message');

            // Creator audit trail
            $table->foreignId('created_by')
                  ->constrained('users')
                  ->cascadeOnDelete();

            // Workflow status
            $table->enum('status', [
                'draft',
                'published',
                'archived'
            ])->default('draft');

            // SMS flag for job dispatch
            $table->boolean('send_sms')
                  ->default(false);

            // Track publish moment
            $table->timestamp('published_at')
                  ->nullable();

            $table->timestamps();

            // Indexes
            $table->index('status');
            $table->index('created_by');
            $table->index('published_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};
