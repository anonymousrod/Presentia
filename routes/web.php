<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordChangeController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Routes d'authentification personnalisées
Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);

    // Réinitialisation de mot de passe (Email / WhatsApp)
    Route::get('forgot-password', [App\Http\Controllers\Auth\PasswordResetController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('forgot-password', [App\Http\Controllers\Auth\PasswordResetController::class, 'sendResetLinkEmail'])->name('password.email');
});

Route::post('logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Changement de mot de passe obligatoire
    Route::get('password/change', [PasswordChangeController::class, 'showChangeForm'])->name('password.change.show');
    Route::post('password/change', [PasswordChangeController::class, 'update'])->name('password.change.update');

    // Frontend Activities
    Route::get('activities', [App\Http\Controllers\ActivityController::class, 'index'])->name('activities.index');
    Route::get('activities/{activity}', [App\Http\Controllers\ActivityController::class, 'show'])->name('activities.show');
    Route::post('activities/{activity}/register', [App\Http\Controllers\RegistrationController::class, 'store'])->name('activities.register');
    Route::put('activities/{activity}/register', [App\Http\Controllers\RegistrationController::class, 'update'])->name('activities.register.update');
    Route::post('activities/{activity}/unregister', [App\Http\Controllers\RegistrationController::class, 'destroy'])->name('activities.unregister');
    Route::get('activities/{activity}/attendance', [App\Http\Controllers\AttendanceManagementController::class, 'index'])->name('activities.attendance.index');
    Route::post('activities/{activity}/attendance', [App\Http\Controllers\AttendanceManagementController::class, 'update'])->name('activities.attendance.update');
    Route::delete('activities/{activity}/attendance', [App\Http\Controllers\AttendanceManagementController::class, 'destroy'])->name('activities.attendance.destroy');
    Route::get('activities/{activity}/attendance/data', [App\Http\Controllers\AttendanceManagementController::class, 'getUpdates'])->name('activities.attendance.data');

    // QR Code Scanning & Attendance
    Route::get('scan', [App\Http\Controllers\AttendanceScanController::class, 'scanner'])->name('scan.alias');
    Route::get('attendance/scan', [App\Http\Controllers\AttendanceScanController::class, 'scanner'])->name('attendance.scan');
    Route::get('attendance/success/{activity}', [App\Http\Controllers\AttendanceScanController::class, 'success'])->name('attendance.success');
    Route::match(['get', 'post'], 'attendance/validate', [App\Http\Controllers\AttendanceController::class, 'validate'])
        ->name('attendance.validate')
        ->middleware('signed');

    // Profile & Settings
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [App\Http\Controllers\ProfileController::class, 'updateProfile'])->name('profile.update');
    Route::post('/profile/password', [App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('/profile/avatar', [App\Http\Controllers\ProfileController::class, 'updateAvatar'])->name('profile.avatar');
    Route::post('/profile/cover', [App\Http\Controllers\ProfileController::class, 'updateCover'])->name('profile.cover');
});

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    // Routes requiring general user management permissions
    Route::middleware(['can:manage-users'])->group(function () {
        // Users
        Route::post('users/bulk-status', [App\Http\Controllers\Admin\UserController::class, 'bulkUpdateStatus'])->name('users.bulk-status');
        Route::get('users/export', [App\Http\Controllers\Admin\UserController::class, 'export'])->name('users.export');
        Route::post('users/{user}/restore', [App\Http\Controllers\Admin\UserController::class, 'restore'])->name('users.restore');
        Route::resource('users', App\Http\Controllers\Admin\UserController::class);
    });

    // Activities Admin
    Route::middleware(['can:access-activities'])->group(function () {
        Route::post('activities/{activity}/qr/generate', [App\Http\Controllers\Admin\QrCodeController::class, 'generate'])->name('activities.qr.generate');
        Route::post('activities/{activity}/qr/revoke', [App\Http\Controllers\Admin\QrCodeController::class, 'revoke'])->name('activities.qr.revoke');
        Route::get('activities/{activity}/qr/pdf', [App\Http\Controllers\Admin\QrCodeController::class, 'downloadPdf'])->name('activities.qr.pdf');
        Route::get('activities/{activity}/download-registrations', [App\Http\Controllers\Admin\ActivityController::class, 'downloadRegistrationsPdf'])->name('activities.download-registrations');
        Route::get('activities/{activity}/download-attendance', [App\Http\Controllers\Admin\ActivityController::class, 'downloadAttendancePdf'])->name('activities.download-attendance');
        Route::resource('activity-types', App\Http\Controllers\Admin\ActivityTypeController::class);
        Route::resource('activities', App\Http\Controllers\Admin\ActivityController::class);
    });

    // Password Requests (WhatsApp)
    Route::middleware(['can:manage-users'])->group(function () {
        Route::get('password-requests', [App\Http\Controllers\Admin\PasswordRequestController::class, 'index'])->name('password-requests.index');
        Route::post('password-requests/{passwordResetRequest}/validate', [App\Http\Controllers\Admin\PasswordRequestController::class, 'validateRequest'])->name('password-requests.validate');
    });

    // Roles & Permissions CRUD
    Route::middleware(['can:role.manage'])->group(function () {
        Route::resource('roles', App\Http\Controllers\Admin\RoleController::class);
        Route::get('users/{user}/permissions', [App\Http\Controllers\Admin\PermissionController::class, 'editUserPermissions'])->name('users.permissions.edit');
        Route::post('users/{user}/permissions', [App\Http\Controllers\Admin\PermissionController::class, 'updateUserPermissions'])->name('users.permissions.update');
    });

    // Audit Logs
    Route::get('audit-logs', [App\Http\Controllers\Admin\AuditLogController::class, 'index'])
        ->name('audit-logs.index')
        ->middleware('can:audit.view');

    // Groups & Member Assignment
    Route::middleware(['can:access-group-management'])->group(function () {
        Route::post('groups/{group}/members', [App\Http\Controllers\Admin\GroupController::class, 'assignMember'])->name('groups.members.assign');
        Route::delete('groups/{group}/members/{user}', [App\Http\Controllers\Admin\GroupController::class, 'removeMember'])->name('groups.members.remove');
        Route::resource('groups', App\Http\Controllers\Admin\GroupController::class);
    });
});
