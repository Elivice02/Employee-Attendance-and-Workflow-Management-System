<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            // Add new columns if they don't exist
            if (!Schema::hasColumn('tasks', 'start_date')) {
                $table->date('start_date')->nullable()->after('description');
            }
            
            if (!Schema::hasColumn('tasks', 'end_date')) {
                $table->date('end_date')->nullable()->after('start_date');
            }
            
            if (!Schema::hasColumn('tasks', 'completion_percentage')) {
                $table->unsignedInteger('completion_percentage')->default(0)->after('status');
            }
            
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE tasks MODIFY priority ENUM('low','medium','high','urgent','critical') NOT NULL DEFAULT 'medium'");
            DB::table('tasks')->where('priority', 'urgent')->update(['priority' => 'critical']);
            DB::statement("ALTER TABLE tasks MODIFY priority ENUM('low','medium','high','critical') NOT NULL DEFAULT 'medium'");

            DB::statement("ALTER TABLE tasks MODIFY status ENUM('pending','assigned','in_progress','pending_review','completed','overdue','in_revision','archived') NOT NULL DEFAULT 'assigned'");
            DB::table('tasks')->where('status', 'pending')->update(['status' => 'assigned']);
            DB::table('tasks')->where('status', 'overdue')->update(['status' => 'in_progress']);
            DB::statement("ALTER TABLE tasks MODIFY status ENUM('assigned','in_progress','pending_review','completed','in_revision','archived') NOT NULL DEFAULT 'assigned'");

            DB::statement("ALTER TABLE task_updates MODIFY status_after_update ENUM('pending','assigned','in_progress','pending_review','completed','overdue','in_revision','archived') NOT NULL");
            DB::table('task_updates')->where('status_after_update', 'pending')->update(['status_after_update' => 'assigned']);
            DB::table('task_updates')->where('status_after_update', 'overdue')->update(['status_after_update' => 'in_progress']);
            DB::statement("ALTER TABLE task_updates MODIFY status_after_update ENUM('assigned','in_progress','pending_review','completed','in_revision','archived') NOT NULL");
        }
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn('start_date');
            $table->dropColumn('end_date');
            $table->dropColumn('completion_percentage');
            
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE task_updates MODIFY status_after_update ENUM('pending','assigned','in_progress','pending_review','completed','overdue','in_revision','archived') NOT NULL");
            DB::table('task_updates')->where('status_after_update', 'assigned')->update(['status_after_update' => 'pending']);
            DB::table('task_updates')->whereIn('status_after_update', ['pending_review', 'in_revision', 'archived'])->update(['status_after_update' => 'in_progress']);
            DB::statement("ALTER TABLE task_updates MODIFY status_after_update ENUM('pending','in_progress','completed','overdue') NOT NULL");

            DB::statement("ALTER TABLE tasks MODIFY status ENUM('pending','assigned','in_progress','pending_review','completed','overdue','in_revision','archived') NOT NULL DEFAULT 'pending'");
            DB::table('tasks')->where('status', 'assigned')->update(['status' => 'pending']);
            DB::table('tasks')->whereIn('status', ['pending_review', 'in_revision', 'archived'])->update(['status' => 'in_progress']);
            DB::statement("ALTER TABLE tasks MODIFY status ENUM('pending','in_progress','completed','overdue') NOT NULL DEFAULT 'pending'");

            DB::statement("ALTER TABLE tasks MODIFY priority ENUM('low','medium','high','urgent','critical') NOT NULL DEFAULT 'medium'");
            DB::table('tasks')->where('priority', 'critical')->update(['priority' => 'urgent']);
            DB::statement("ALTER TABLE tasks MODIFY priority ENUM('low','medium','high','urgent') NOT NULL DEFAULT 'medium'");
        }
    }
};
