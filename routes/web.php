<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ParticipantController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ActivityLogController;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES (NO AUTH)
|--------------------------------------------------------------------------
*/

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

// Public Attendance Routes (for participants scanning QR)
Route::prefix('attendance')->name('attendance.')->group(function () {
    Route::get('/form/{event}', [AttendanceController::class, 'showForm'])->name('form.public');
    Route::post('/store/{event}', [AttendanceController::class, 'store'])->name('store.public');
    Route::get('/success/{event}/{attendance}', [AttendanceController::class, 'success'])->name('success');
    Route::get('/scan/{qr_hash}', [AttendanceController::class, 'showFormByHash'])->name('scan.qr');
    Route::post('/submit/{qr_hash}', [AttendanceController::class, 'submitByHash'])->name('submit.qr');
});

// QR Code Redirect Route
Route::get('/qrcode/{event}', function ($eventId) {
    return redirect()->route('attendance.form.public', $eventId);
})->name('qrcode.redirect');

// QR Code dengan hash
Route::get('/absensi/{qr_hash}', [AttendanceController::class, 'showFormByHash'])->name('attendance.form.byhash');
Route::post('/absensi/{qr_hash}', [AttendanceController::class, 'submitByHash'])->name('attendance.submit.byhash');

// QR Code Generate
Route::post('events/{event}/qr/generate', [EventController::class, 'generateQRCode'])->name('events.qr.generate');

// Calendar API
Route::get('api/calendar-events', [EventController::class, 'getCalendarEvents'])->name('events.calendar.data');

/*
|--------------------------------------------------------------------------
| PROTECTED ROUTES (AUTH REQUIRED)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::redirect('/', '/dashboard');
    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        Route::get('/statistics', [DashboardController::class, 'getStatistics'])->name('statistics');
        Route::get('/chart-data', [DashboardController::class, 'getChartData'])->name('chart.data');
    });

    /*
    |--------------------------------------------------------------------------
    | EVENTS ROUTES
    | ✅ Semua static route di atas route {event}
    |--------------------------------------------------------------------------
    */
    Route::controller(EventController::class)->prefix('events')->name('events.')->group(function () {

        // STATIC ROUTES — di atas {event}
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/upcoming', 'upcoming')->name('upcoming');
        Route::get('/past', 'past')->name('past');
        Route::get('/calendar', 'calendar')->name('calendar');
        Route::get('/search', 'search')->name('search');
        Route::get('/archived', 'archived')->name('archived');
        Route::post('/bulk-actions', 'bulkActions')->name('bulk-actions');

        // PARAMETER ROUTES — di bawah static
        Route::get('/{event}/detail', 'show')->name('show');
        Route::get('/{event}/edit', 'edit')->name('edit');
        Route::put('/{event}', 'update')->name('update');
        Route::delete('/{event}', 'destroy')->name('destroy');
        Route::get('/{event}/qr', 'qrPage')->name('qr.page');
        Route::post('/{event}/qr/refresh', 'refreshQRCode')->name('qr.refresh');
        Route::post('/{event}/toggle-status', 'toggleStatus')->name('toggle-status');
        Route::get('/{event}/attendances', 'attendances')->name('attendances');
        Route::get('/{event}/export', 'export')->name('export');
        Route::get('/{event}/print', 'print')->name('print');
        Route::get('/{event}/duplicate', 'duplicate')->name('duplicate');
        Route::get('/{event}/statistics', 'analytics')->name('statistics');
        Route::get('/archived/{event}', 'showArchived')->name('show.archived');
        Route::post('/{event}/archive', 'archive')->name('archive');
        Route::post('/{event}/restore', 'restore')->name('restore');
    });

    /*
    |--------------------------------------------------------------------------
    | ATTENDANCES ROUTES
    | ✅ Semua static route di atas route {attendance}
    |--------------------------------------------------------------------------
    */
    Route::controller(AttendanceController::class)->prefix('attendances')->name('attendances.')->group(function () {

        // STATIC ROUTES — di atas {attendance}
        Route::get('/', 'index')->name('index');
        Route::get('/export/pdf', 'exportPdf')->name('export.pdf');
        Route::get('/export/csv', 'exportCsv')->name('export.csv');
        Route::get('/export', 'exportPdf')->name('export');
        Route::post('/bulk-delete', 'bulkDelete')->name('bulk-delete');
        Route::post('/quick-add', 'quickAdd')->name('quick-add');
        Route::post('/import', 'import')->name('import');
        Route::get('/import-template', 'downloadTemplate')->name('import.template');
        Route::post('/add-duplicate-column', 'addDuplicateColumn')->name('add-duplicate-column');
        Route::post('/identify-duplicates', 'identifyDuplicates')->name('identify.duplicates');
        Route::delete('/remove-duplicates', 'removeDuplicates')->name('remove.duplicates');
        Route::get('/duplicates', 'showDuplicates')->name('duplicates');
        Route::get('/api/statistics', 'getStatistics')->name('statistics.api');

        // PARAMETER ROUTES — di bawah static
        Route::get('/{attendance}/edit', 'edit')->name('edit');
        Route::put('/{attendance}', 'update')->name('update');
        Route::delete('/{attendance}', 'destroy')->name('destroy');
        Route::patch('/{attendance}/mark-as-valid', 'markAsValid')->name('mark-as-valid');
    });

    /*
    |--------------------------------------------------------------------------
    | PARTICIPANTS ROUTES
    | ✅ Semua static route di atas route {participant}
    |--------------------------------------------------------------------------
    */
    Route::controller(ParticipantController::class)->prefix('participants')->name('participants.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{participant}', 'show')->name('show');
        Route::get('/{participant}/edit', 'edit')->name('edit');
        Route::put('/{participant}', 'update')->name('update');
        Route::delete('/{participant}', 'destroy')->name('destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | PROFILE ROUTES
    |--------------------------------------------------------------------------
    */
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::put('/', [ProfileController::class, 'update'])->name('update');
        Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password');
        Route::put('/avatar', [ProfileController::class, 'updateAvatar'])->name('avatar.update');
        Route::delete('/avatar', [ProfileController::class, 'deleteAvatar'])->name('avatar.delete');
    });

    /*
    |--------------------------------------------------------------------------
    | ACTIVITY LOG ROUTES
    | ✅ Static route clear-old di atas route {activityLog}
    |--------------------------------------------------------------------------
    */
    Route::controller(ActivityLogController::class)->prefix('activity-logs')->name('activity-logs.')->group(function () {

        // STATIC ROUTES — di atas {activityLog}
        Route::get('/', 'index')->name('index');
        Route::post('/clear-old', 'clearOldLogs')->name('clear-old');

        // PARAMETER ROUTES — di bawah static
        Route::get('/{activityLog}', 'show')->name('show');
        Route::delete('/{activityLog}', 'destroy')->name('destroy');
    });
});

/*
|--------------------------------------------------------------------------
| FALLBACK ROUTE
|--------------------------------------------------------------------------
*/
Route::fallback(function () {
    return auth()->check() ? redirect('/dashboard') : redirect('/login');
});