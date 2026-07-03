<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('attendance_settings')
            ->where(function ($query) {
                $query->whereNull('work_start_time')
                    ->orWhere('work_start_time', '09:00:00');
            })
            ->update([
                'work_start_time' => '08:00:00',
                'late_grace_minutes' => 0,
            ]);
    }

    public function down(): void
    {
        //
    }
};
