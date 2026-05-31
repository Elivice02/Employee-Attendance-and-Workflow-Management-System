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

        Route::get('/leaves', [LeaveController::class, 'supervisorIndex'])
            ->name('leaves.index');

        Route::get('/leaves/{leave}', [LeaveController::class, 'supervisorShow'])
            ->name('leaves.show');

        Route::post('/leaves/{leave}/recommend', [LeaveController::class, 'supervisorRecommend'])
            ->name('leaves.recommend');

    });
            
Route::middleware(['auth', 'role:employee'])->prefix('employee')->name('employee.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [EmployeeDashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [EmployeeDashboardController::class, 'profile'])->name('profile');
    Route::post('/profile', [EmployeeDashboardController::class, 'updateProfile'])->name('profile.update');

    // Attendance
    Route::get('/attendance', [AttendanceController::class, 'employeeRecords'])->name('attendance');

    // Leave Requests
    Route::get('/leave-requests', [LeaveController::class, 'employeeIndex'])->name('leave.index');
    Route::get('/leave-requests/{leave}', [LeaveController::class, 'employeeShow'])->name('leave.show');
    Route::get('/request-leave', [LeaveController::class, 'create'])->name('leave.create');
    Route::post('/request-leave/preview', [LeaveController::class, 'preview'])->name('leave.preview');
    Route::post('/request-leave', [LeaveController::class, 'store'])->name('leave.store');

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
});

// HR Routes
Route::middleware(['auth', 'role:hr'])
    ->prefix('hr')
    ->name('hr.')
    ->group(function () {

        Route::get('/dashboard', [HRDashboardController::class, 'index'])
            ->name('dashboard');

        Route::get('/attendance', [AttendanceController::class, 'hrIndex'])
            ->name('attendance.index');

        Route::post('/attendance/{attendance}/late-review', [AttendanceController::class, 'reviewLate'])
            ->name('attendance.late-review');

        Route::get('/leaves', [LeaveController::class, 'hrIndex'])
            ->name('leaves.index');

        Route::get('/leaves/{leave}', [LeaveController::class, 'hrShow'])
            ->name('leaves.show');

        Route::post('/leaves/{leave}/verify', [LeaveController::class, 'hrVerify'])
            ->name('leaves.verify');

        Route::post('/leaves/{leave}/final-review', [LeaveController::class, 'hrFinalReview'])
            ->name('leaves.final-review');

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

// Password Reset Routes
Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])
    ->name('password.request');

Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])
    ->name('password.email');

Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])
    ->name('password.reset');

Route::post('/reset-password', [ResetPasswordController::class, 'reset'])
    ->name('password.update');
