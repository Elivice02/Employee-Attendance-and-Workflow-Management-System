<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reference_counters', function (Blueprint $table) {
            $table->id();
            $table->string('type', 20);
            $table->unsignedSmallInteger('year');
            $table->string('department_code', 10);
            $table->unsignedInteger('last_number')->default(0);
            $table->timestamps();

            $table->unique(['type', 'year', 'department_code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reference_counters');
    }
};
