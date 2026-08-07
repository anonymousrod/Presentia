<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordChangeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Pages légales
Route::view('/politique-de-confidentialite', 'pages.privacy')->name('privacy');
Route::view('/mentions-legales', 'pages.legal')->name('legal');

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
        ->name('attendance.validate');

    // Profile & Settings
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [App\Http\Controllers\ProfileController::class, 'updateProfile'])->name('profile.update');
    Route::post('/profile/password', [App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('/profile/avatar', [App\Http\Controllers\ProfileController::class, 'updateAvatar'])->name('profile.avatar');
    Route::post('/profile/cover', [App\Http\Controllers\ProfileController::class, 'updateCover'])->name('profile.cover');

    // Notifications
    Route::get('notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::delete('notifications/destroy-all', [App\Http\Controllers\NotificationController::class, 'destroyAll'])->name('notifications.destroy-all');
    Route::delete('notifications/{id}', [App\Http\Controllers\NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::get('notifications/{id}/read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('notifications/read-all', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
});

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    // Global Search
    Route::get('global-search', [App\Http\Controllers\Admin\GlobalSearchController::class, 'search'])->name('global-search');

    // System Settings
    Route::middleware(['can:manage-users'])->group(function () {
        Route::get('settings', [App\Http\Controllers\AppSettingController::class, 'edit'])->name('settings.edit');
        Route::post('settings', [App\Http\Controllers\AppSettingController::class, 'update'])->name('settings.update');

        // Galleries
        Route::get('galleries', [App\Http\Controllers\Admin\GalleryController::class, 'index'])->name('galleries.index');
        Route::post('galleries', [App\Http\Controllers\Admin\GalleryController::class, 'store'])->name('galleries.store');
        Route::post('galleries/{gallery}/toggle', [App\Http\Controllers\Admin\GalleryController::class, 'toggleActive'])->name('galleries.toggle');
        Route::delete('galleries/bulk-destroy', [App\Http\Controllers\Admin\GalleryController::class, 'bulkDestroy'])->name('galleries.bulk-destroy');
        Route::delete('galleries/{gallery}', [App\Http\Controllers\Admin\GalleryController::class, 'destroy'])->name('galleries.destroy');
    });

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

    // Statistics Dashboard
    Route::middleware(['can:stats.view_global'])->prefix('statistics')->name('statistics.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\StatisticsController::class, 'index'])->name('index');
        Route::get('/chart/members-per-group', [App\Http\Controllers\Admin\StatisticsController::class, 'chartMembersPerGroup'])->name('chart.members-per-group');
        Route::get('/chart/presence-evolution', [App\Http\Controllers\Admin\StatisticsController::class, 'chartPresenceEvolution'])->name('chart.presence-evolution');
        Route::get('/chart/presence-by-group', [App\Http\Controllers\Admin\StatisticsController::class, 'chartPresenceByGroup'])->name('chart.presence-by-group');
        Route::get('/chart/individual-participation', [App\Http\Controllers\Admin\StatisticsController::class, 'chartIndividualParticipation'])->name('chart.individual-participation');
        Route::get('/chart/affluence-by-activity', [App\Http\Controllers\Admin\StatisticsController::class, 'chartAffluenceByActivity'])->name('chart.affluence-by-activity');
    });

    // Group Statistics (Accessible by Admins and Group Leaders)
    Route::prefix('statistics/group')->name('statistics.group.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\StatisticsController::class, 'group'])->name('index');
        Route::get('/chart/evolution', [App\Http\Controllers\Admin\StatisticsController::class, 'chartGroupEvolution'])->name('chart.evolution');
        Route::get('/chart/participation', [App\Http\Controllers\Admin\StatisticsController::class, 'chartGroupParticipation'])->name('chart.participation');
    });

    // Notifications — Envoi
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('send-all', [App\Http\Controllers\Admin\NotificationController::class, 'showSendAllForm'])->name('send-all')->middleware('can:notification.send_all');
        Route::post('send-all', [App\Http\Controllers\Admin\NotificationController::class, 'sendAll'])->middleware('can:notification.send_all');
        Route::get('send-group', [App\Http\Controllers\Admin\NotificationController::class, 'showSendGroupForm'])->name('send-group')->middleware('can:notification.send_group');
        Route::post('send-group', [App\Http\Controllers\Admin\NotificationController::class, 'sendGroup'])->middleware('can:notification.send_group');
        Route::get('send-role', [App\Http\Controllers\Admin\NotificationController::class, 'showSendRoleForm'])->name('send-role')->middleware('can:notification.send_role');
        Route::post('send-role', [App\Http\Controllers\Admin\NotificationController::class, 'sendRole'])->middleware('can:notification.send_role');
        Route::get('send-individual', [App\Http\Controllers\Admin\NotificationController::class, 'showSendIndividualForm'])->name('send-individual')->middleware('can:notification.send_individual');
        Route::post('send-individual', [App\Http\Controllers\Admin\NotificationController::class, 'sendIndividual'])->middleware('can:notification.send_individual');
    });

    // Finances (Cotisations et Trésorerie)
    Route::prefix('finance')->name('finance.')->group(function () {
        // Collecte des cotisations
        Route::get('contributions', [App\Http\Controllers\Admin\Finance\ContributionController::class, 'index'])->name('contributions.index')->middleware('can:finance.collect_own_group');
        Route::post('contributions', [App\Http\Controllers\Admin\Finance\ContributionController::class, 'store'])->name('contributions.store')->middleware('can:finance.collect_own_group');

        // Versements à la trésorerie
        Route::post('remittances', [App\Http\Controllers\Admin\Finance\RemittanceController::class, 'store'])->name('remittances.store')->middleware('can:remittance.create');
        Route::get('treasury', [App\Http\Controllers\Admin\Finance\RemittanceController::class, 'index'])->name('treasury.index')->middleware('can:finance.view_all');
        Route::post('remittances/{remittance}/validate', [App\Http\Controllers\Admin\Finance\RemittanceController::class, 'validateRemittance'])->name('remittances.validate')->middleware('can:remittance.validate');
    });
});
