<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeDashboardController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\PromotionController;

Route::get('/', function () {
    return view('welcome');
});


// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']) ->name('logout');

// Protected Routes with Role Middleware
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', fn() => view('admin.dashboard'));
});

Route::middleware(['auth', 'role:hr'])->prefix('hr')->group(function () {
    Route::get('/dashboard', fn() => view('hr.dashboard'));
});

Route::middleware(['auth', 'role:supervisor'])
    ->prefix('supervisor')
    ->name('supervisor.')
    ->group(function () {

        Route::get('/dashboard', [App\Http\Controllers\SupervisorController::class, 'dashboard'])
            ->name('dashboard');
            });
            
Route::middleware(['auth', 'role:employee'])->prefix('employee')->name('employee.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [EmployeeDashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [EmployeeDashboardController::class, 'profile'])->name('profile');
    Route::post('/profile', [EmployeeDashboardController::class, 'updateProfile'])->name('profile.update');

    // Attendance
    Route::get('/attendance', [EmployeeDashboardController::class, 'attendance'])->name('attendance');

    // Leave Requests
    Route::get('/leave-requests', [EmployeeDashboardController::class, 'leaveRequests'])->name('leave.index');
    Route::get('/request-leave', [EmployeeDashboardController::class, 'requestLeaveForm'])->name('leave.create');
    Route::post('/request-leave', [EmployeeDashboardController::class, 'requestLeave'])->name('leave.store');

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
    });

// Password Change Route
Route::middleware('auth')->group(function () {
    Route::get('/change-password', [AuthController::class, 'showChangePassword'])->name('password.change');
    Route::post('/change-password', [AuthController::class, 'updatePassword'])->name('password.update');
});

// HR Routes
Route::middleware(['auth', 'role:hr'])
    ->prefix('hr')
    ->name('hr.')
    ->group(function () {

        Route::get('/dashboard', fn() => view('hr.dashboard'))
            ->name('dashboard');

        // Employees (FULL CRUD)
        Route::resource('employees', EmployeeController::class);

        // Promotions (HR ONLY)
        Route::get('employees/{employee}/promote', [PromotionController::class, 'showPromoteForm'])
            ->name('employees.promote.form');

        Route::post('employees/{employee}/promote', [PromotionController::class, 'promote'])
            ->name('employees.promote'); 

        Route::get('promotions', [PromotionLogController::class, 'index'])
            ->name('promotions.index');

        // Departments
        Route::resource('departments', DepartmentController::class);
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

