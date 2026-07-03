<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeDashboardController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\HRDashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\PromotionLogController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaskProgressController;
use App\Http\Controllers\DailyLogController;
use App\Http\Controllers\HRReportController;
use App\Http\Controllers\PerformanceReportController;
use App\Http\Controllers\AnnouncementController;

Route::get('/', function () {
    return view('welcome');
});


// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']) ->name('logout');

Route::middleware(['auth', 'role:supervisor'])
    ->prefix('supervisor')
    ->name('supervisor.')
    ->group(function () {

        Route::get('/dashboard', [App\Http\Controllers\SupervisorController::class, 'dashboard'])
            ->name('dashboard');

        Route::get('/attendance', [AttendanceController::class, 'supervisorIndex'])
            ->name('attendance.index');

        Route::get('/reports', [PerformanceReportController::class, 'supervisorTeam'])
            ->name('reports.index');

        Route::get('/team-members', [App\Http\Controllers\SupervisorController::class, 'teamMembers'])
            ->name('team-members.index');

        Route::get('/leaves', [LeaveController::class, 'supervisorIndex'])
            ->name('leaves.index');

        Route::get('/leaves/{leave}', [LeaveController::class, 'supervisorShow'])
            ->name('leaves.show');

        Route::post('/leaves/{leave}/recommend', [LeaveController::class, 'supervisorRecommend'])
            ->name('leaves.recommend');

        Route::get('/tasks', [TaskController::class, 'supervisorIndex'])
            ->name('tasks.index');

        Route::get('/tasks/create', [TaskController::class, 'supervisorCreate'])
            ->name('tasks.create');

        Route::post('/tasks', [TaskController::class, 'supervisorStore'])
            ->name('tasks.store');

        Route::get('/tasks/{task}', [TaskController::class, 'supervisorShow'])
            ->name('tasks.show');

        Route::get('/tasks/{task}/edit', [TaskController::class, 'supervisorEdit'])
            ->name('tasks.edit');

        Route::put('/tasks/{task}', [TaskController::class, 'supervisorUpdate'])
            ->name('tasks.update');

        Route::post('/tasks/{task}/updates', [TaskController::class, 'addUpdate'])
            ->name('tasks.updates.store');

        // Task approval & rejection
        Route::post('/tasks/{task}/approve', [TaskController::class, 'supervisorApprove'])
            ->name('tasks.approve');

        Route::post('/tasks/{task}/reject', [TaskController::class, 'supervisorReject'])
            ->name('tasks.reject');

        // Task progress review
        Route::get('/tasks/{task}/progress', [TaskProgressController::class, 'show'])
            ->name('tasks.progress.show');

        Route::post('/tasks/{task}/progress/{progress}/review', [TaskProgressController::class, 'review'])
            ->name('tasks.progress.review');

        Route::get('/daily-logs', [DailyLogController::class, 'supervisorIndex'])
            ->name('daily-logs.index');

        Route::get('/daily-logs/create', [DailyLogController::class, 'supervisorCreate'])
            ->name('daily-logs.create');

        Route::post('/daily-logs', [DailyLogController::class, 'supervisorStore'])
            ->name('daily-logs.store');

        Route::get('/daily-logs/{dailyLog}', [DailyLogController::class, 'supervisorShow'])
            ->name('daily-logs.show');

        Route::get('/employee-daily-logs', [DailyLogController::class, 'supervisorReviewIndex'])
            ->name('daily-log-reviews.index');

        Route::get('/employee-daily-logs/{dailyLog}', [DailyLogController::class, 'supervisorReviewShow'])
            ->name('daily-log-reviews.show');

        Route::post('/employee-daily-logs/{dailyLog}/review', [DailyLogController::class, 'supervisorReview'])
            ->name('daily-log-reviews.store');

    });
            
Route::middleware(['auth', 'role:employee'])->prefix('employee')->name('employee.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [EmployeeDashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [EmployeeDashboardController::class, 'profile'])->name('profile');
    Route::post('/profile', [EmployeeDashboardController::class, 'updateProfile'])->name('profile.update');

    // Attendance
    Route::get('/attendance', [AttendanceController::class, 'employeeRecords'])->name('attendance');

    Route::get('/performance', [PerformanceReportController::class, 'employeePerformance'])->name('performance.index');

    // Leave Requests
    Route::get('/leave-requests', [LeaveController::class, 'employeeIndex'])->name('leave.index');
    Route::get('/leave-requests/{leave}', [LeaveController::class, 'employeeShow'])->name('leave.show');
    Route::get('/request-leave', [LeaveController::class, 'create'])->name('leave.create');
    Route::post('/request-leave/preview', [LeaveController::class, 'preview'])->name('leave.preview');
    Route::post('/request-leave', [LeaveController::class, 'store'])->name('leave.store');

    // Tasks
    Route::get('/tasks', [TaskController::class, 'employeeIndex'])->name('tasks.index');
    Route::get('/tasks/{task}', [TaskController::class, 'employeeShow'])->name('tasks.show');
    Route::post('/tasks/{task}/start', [TaskController::class, 'employeeStart'])->name('tasks.start');
    Route::post('/tasks/{task}/updates', [TaskController::class, 'addUpdate'])->name('tasks.updates.store');

    // Task progress submission
    Route::get('/tasks/{task}/progress/create', [TaskProgressController::class, 'create'])->name('tasks.progress.create');
    Route::post('/tasks/{task}/progress', [TaskProgressController::class, 'store'])->name('tasks.progress.store');
    Route::put('/tasks/{task}/progress/{progress}', [TaskProgressController::class, 'update'])->name('tasks.progress.update');

    // Daily Logs
    Route::get('/daily-logs', [DailyLogController::class, 'employeeIndex'])->name('daily-logs.index');
    Route::get('/daily-logs/create', [DailyLogController::class, 'employeeCreate'])->name('daily-logs.create');
    Route::post('/daily-logs', [DailyLogController::class, 'employeeStore'])->name('daily-logs.store');
    Route::get('/daily-logs/{dailyLog}', [DailyLogController::class, 'employeeShow'])->name('daily-logs.show');

    // Payroll
    Route::get('/payroll', [EmployeeDashboardController::class, 'payroll'])->name('payroll');

    // Password Change
    Route::get('/change-password', [EmployeeDashboardController::class, 'changePasswordForm'])->name('password.form');
    Route::post('/change-password', [EmployeeDashboardController::class, 'changePassword'])->name('password.update');
});

// Admin Routes
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [AdminController::class, 'dashboard'])
            ->name('dashboard');

        Route::get('/settings', [AdminController::class, 'settings'])
            ->name('settings');

        Route::put('/settings', [AdminController::class, 'updateSettings'])
            ->name('settings.update');

        // HR Management
        Route::get('/hr-managers', [AdminController::class, 'listHR'])
            ->name('hr.index');

        Route::get('/hr-managers/create', [AdminController::class, 'createHR'])
            ->name('hr.create');

        Route::post('/hr-managers', [AdminController::class, 'storeHR'])
            ->name('hr.store');

        Route::get('/hr-managers/{hr}/edit', [AdminController::class, 'editHR'])
            ->name('hr.edit');

        Route::put('/hr-managers/{hr}', [AdminController::class, 'updateHR'])
            ->name('hr.update');

        Route::delete('/hr-managers/{hr}', [AdminController::class, 'destroyHR'])
            ->name('hr.destroy');

        // Departments
        Route::resource('departments', DepartmentController::class);
    });

// Password Change Route
Route::middleware('auth')->group(function () {
    Route::post('/attendance/check-in', [AttendanceController::class, 'checkIn'])->name('attendance.check-in');
    Route::post('/attendance/check-out', [AttendanceController::class, 'checkOut'])->name('attendance.check-out');
    Route::get('/attendance/late-check-in', [AttendanceController::class, 'lateCheckInForm'])->name('attendance.late.create');
    Route::post('/attendance/late-check-in/preview', [AttendanceController::class, 'previewLateCheckIn'])->name('attendance.late.preview');
    Route::post('/attendance/late-check-in/submit', [AttendanceController::class, 'submitLateCheckIn'])->name('attendance.late.store');
    Route::get('/attendance/{attendance}/evidence', [AttendanceController::class, 'editLateEvidence'])->name('attendance.evidence.edit');
    Route::post('/attendance/{attendance}/evidence', [AttendanceController::class, 'updateLateEvidence'])->name('attendance.evidence.update');
    Route::get('/attendance/{attendance}/letter/{type?}', [AttendanceController::class, 'viewLateLetter'])->name('attendance.letter');
    Route::get('/leave-requests/{leave}/pdf', [LeaveController::class, 'pdf'])->name('leave.pdf');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('/change-password', [AuthController::class, 'showChangePassword'])->name('password.change');
    Route::post('/change-password', [AuthController::class, 'updatePassword'])->name('password.change.update');

    // Employee Announcements (View & Read)
    Route::get('/announcements', [AnnouncementController::class, 'userIndex'])
        ->name('announcements.index');

    Route::get('/announcements/{announcement}', [AnnouncementController::class, 'userShow'])
        ->name('announcements.show');

    Route::post('/announcements/{announcement}/read', [AnnouncementController::class, 'markAsRead'])
        ->name('announcements.read');
});

// HR Routes
Route::middleware(['auth', 'role:hr'])
    ->prefix('hr')
    ->name('hr.')
    ->group(function () {

        Route::get('/dashboard', [HRDashboardController::class, 'index'])
            ->name('dashboard');

        // Task management & reports
        Route::get('/tasks', [TaskController::class, 'hrIndex'])
            ->name('tasks.index');

        Route::get('/tasks/create', [TaskController::class, 'hrCreate'])
            ->name('tasks.create');

        Route::post('/tasks', [TaskController::class, 'hrStore'])
            ->name('tasks.store');

        Route::get('/tasks/{task}', [TaskController::class, 'hrShow'])
            ->name('tasks.show');

        // Reports
        Route::get('/reports/weekly', [HRDashboardController::class, 'weeklyReport'])
            ->name('reports.weekly');

        Route::get('/reports/monthly', [HRDashboardController::class, 'monthlyReport'])
            ->name('reports.monthly');

        Route::get('/reports/kpi', [HRDashboardController::class, 'kpiMetrics'])
            ->name('reports.kpi');

        Route::get('/employees/{employee}/performance', [HRDashboardController::class, 'employeePerformance'])
            ->name('employees.performance');

        Route::get('/supervisor-daily-logs', [DailyLogController::class, 'hrReviewIndex'])
            ->name('daily-log-reviews.index');

        Route::get('/supervisor-daily-logs/{dailyLog}', [DailyLogController::class, 'hrReviewShow'])
            ->name('daily-log-reviews.show');

        Route::post('/supervisor-daily-logs/{dailyLog}/review', [DailyLogController::class, 'hrReview'])
            ->name('daily-log-reviews.store');

        // Employees (FULL CRUD)
        Route::resource('employees', EmployeeController::class);

        // Promotions (HR ONLY)
        Route::get('employees/{employee}/promote', [PromotionController::class, 'showPromoteForm'])
            ->name('employees.promote.form');

        Route::post('employees/{employee}/promote', [PromotionController::class, 'promote'])
            ->name('employees.promote'); 

        Route::get('promotions', [PromotionLogController::class, 'index'])
            ->name('promotions.index');

    });

Route::middleware(['auth', 'role:hr,admin'])
    ->prefix('hr')
    ->name('hr.')
    ->group(function () {
        // Announcements (HR/Admin CRUD + Publish)
        Route::get('/announcements', [AnnouncementController::class, 'index'])
            ->name('announcements.index');

        Route::get('/announcements/create', [AnnouncementController::class, 'create'])
            ->name('announcements.create');

        Route::post('/announcements', [AnnouncementController::class, 'store'])
            ->name('announcements.store');

        Route::get('/announcements/{announcement}/edit', [AnnouncementController::class, 'edit'])
            ->name('announcements.edit');

        Route::put('/announcements/{announcement}', [AnnouncementController::class, 'update'])
            ->name('announcements.update');

        Route::get('/announcements/{announcement}/publish', [AnnouncementController::class, 'showPublish'])
            ->name('announcements.publish-form');

        Route::post('/announcements/{announcement}/publish', [AnnouncementController::class, 'publish'])
            ->name('announcements.publish');

        Route::post('/announcements/{announcement}/archive', [AnnouncementController::class, 'archive'])
            ->name('announcements.archive');

        Route::delete('/announcements/{announcement}', [AnnouncementController::class, 'destroy'])
            ->name('announcements.destroy');

        Route::get('/announcements/{announcement}/recipients', [AnnouncementController::class, 'showRecipients'])
            ->name('announcements.recipients');
    });

// Password Reset Routes
Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])
    ->name('password.request');

Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])
    ->name('password.email');

Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])
    ->name('password.reset');

Route::post('/reset-password', [ResetPasswordController::class, 'reset'])
    ->name('password.update');
