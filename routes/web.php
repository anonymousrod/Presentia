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
});

Route::post('logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Changement de mot de passe obligatoire
    Route::get('password/change', [PasswordChangeController::class, 'showChangeForm'])->name('password.change.show');
    Route::post('password/change', [PasswordChangeController::class, 'update'])->name('password.change.update');

    // Frontend Activities
    Route::get('activities', [App\Http\Controllers\ActivityController::class, 'index'])->name('activities.index');
    Route::post('activities/{activity}/register', [App\Http\Controllers\ActivityController::class, 'register'])->name('activities.register');
    Route::post('activities/{activity}/unregister', [App\Http\Controllers\ActivityController::class, 'unregister'])->name('activities.unregister');
    
    // QR Code Scanning
    Route::get('attendance/scan', [App\Http\Controllers\AttendanceScanController::class, 'scan'])->name('attendance.scan');
});

Route::middleware(['auth', 'can:manage-users'])->prefix('admin')->name('admin.')->group(function () {
    // Audit Logs
    Route::get('audit-logs', [App\Http\Controllers\Admin\AuditLogController::class, 'index'])
        ->name('audit-logs.index')
        ->middleware('can:audit.view');

    // Users
    Route::post('users/bulk-status', [App\Http\Controllers\Admin\UserController::class, 'bulkUpdateStatus'])->name('users.bulk-status');
    Route::get('users/export', [App\Http\Controllers\Admin\UserController::class, 'export'])->name('users.export');
    Route::post('users/{user}/restore', [App\Http\Controllers\Admin\UserController::class, 'restore'])->name('users.restore');
    Route::resource('users', App\Http\Controllers\Admin\UserController::class);

    // Activities Admin
    Route::post('activities/{activity}/qr/generate', [App\Http\Controllers\Admin\QrCodeController::class, 'generate'])->name('activities.qr.generate');
    Route::post('activities/{activity}/qr/revoke', [App\Http\Controllers\Admin\QrCodeController::class, 'revoke'])->name('activities.qr.revoke');
    Route::get('activities/{activity}/qr/pdf', [App\Http\Controllers\Admin\QrCodeController::class, 'downloadPdf'])->name('activities.qr.pdf');
    Route::resource('activities', App\Http\Controllers\Admin\ActivityController::class);
});


