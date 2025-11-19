<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Event;
use App\Models\Participant;
use App\Models\Attendance;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ParticipantController;
use App\Http\Controllers\ProfileController;


/*
|--------------------------------------------------------------------------
| Debug Routes
|--------------------------------------------------------------------------
*/

Route::get('/test-db', function () {
    try {
        DB::connection()->getPdo();
        echo "✓ Database connected<br>";
        echo "Users: " . User::count() . "<br>";
        echo "Events: " . Event::count() . "<br>";
        echo "Participants: " . Participant::count() . "<br>";
        echo "Attendance: " . Attendance::count() . "<br>";
    } catch (\Exception $e) {
        echo "Database error: " . $e->getMessage();
    }
});


Route::get('/test-qr/{event}', function ($eventId) {
    $event = Event::find($eventId);
    if (!$event) {
        return 'Event tidak ditemukan';
    }

    $url = route('attendance.form.public', ['event' => $eventId]);

    return response()->json([
        'event' => $event->event_name,
        'attendance_url' => $url,
        'qr_urls' => [
            'google' => "https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=" . urlencode($url),
            'qrserver' => "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . urlencode($url),
            'quickchart' => "https://quickchart.io/qr?text=" . urlencode($url),
        ]
    ]);
});


/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);


/*
|--------------------------------------------------------------------------
| PUBLIC ROUTE – FORM ABSENSI (TANPA LOGIN)
|--------------------------------------------------------------------------
*/

Route::prefix('attendance')->name('attendance.')->group(function () {

    // 🟢 Tampilkan form absensi
    Route::get('/{event}/form', [AttendanceController::class, 'showForm'])
         ->name('form.public')
         ->withoutMiddleware('auth')
         ->where('event', '[0-9]+');

    // 🟢 Submit form absensi
    Route::post('/{event}/form', [AttendanceController::class, 'store'])
         ->name('store.public')
         ->withoutMiddleware('auth')
         ->where('event', '[0-9]+');

    // 🟢 Halaman sukses absensi
    Route::get('/{event}/success/{attendance}', [AttendanceController::class, 'success'])
         ->name('success')
         ->withoutMiddleware('auth')
         ->where([
             'event' => '[0-9]+',
             'attendance' => '[0-9]+',
         ]);
});


/*
|--------------------------------------------------------------------------
| QR Code Redirect
|--------------------------------------------------------------------------
*/

Route::get('/qrcode/{event}', function ($eventId) {
    $event = Event::find($eventId);
    if (!$event) {
        abort(404, 'Event tidak ditemukan');
    }
    return redirect()->route('attendance.form.public', $event->id);
})->name('qrcode.redirect');


/*
|--------------------------------------------------------------------------
| PROTECTED ROUTES (HARUS LOGIN)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');


    /*
    |--------------------------------------------------------------------------
    | EVENT MANAGEMENT
    |--------------------------------------------------------------------------
    */

    Route::prefix('events')->name('events.')->group(function () {
        Route::get('/', [EventController::class, 'index'])->name('index');
        Route::get('/create', [EventController::class, 'create'])->name('create');
        Route::post('/', [EventController::class, 'store'])->name('store');
        Route::get('/{event}', [EventController::class, 'show'])->name('show');
        Route::get('/{event}/edit', [EventController::class, 'edit'])->name('edit');
        Route::put('/{event}', [EventController::class, 'update'])->name('update');
        Route::delete('/{event}', [EventController::class, 'destroy'])->name('destroy');

        Route::get('/{event}/qr-code', [EventController::class, 'generateQRCode'])->name('qr-code');
        Route::get('/{event}/attendances', [EventController::class, 'attendances'])->name('attendances');

        Route::post('/{event}/toggle-status', [EventController::class, 'toggleStatus'])->name('toggle-status');
    });


    /*
    |--------------------------------------------------------------------------
    | ATTENDANCE MANAGEMENT (ADMIN)
    |--------------------------------------------------------------------------
    */

    Route::prefix('attendances')->name('attendances.')->group(function () {
        Route::get('/', [AttendanceController::class, 'index'])->name('index');
        Route::get('/export', [AttendanceController::class, 'export'])->name('export');
        Route::get('/export-pdf', [AttendanceController::class, 'exportPdf'])->name('export-pdf');

        Route::delete('/{attendance}', [AttendanceController::class, 'destroy'])->name('destroy');
        Route::get('/{attendance}/edit', [AttendanceController::class, 'edit'])->name('edit');
        Route::put('/{attendance}', [AttendanceController::class, 'update'])->name('update');
    });


    /*
    |--------------------------------------------------------------------------
    | PARTICIPANT MANAGEMENT
    |--------------------------------------------------------------------------
    */

    Route::prefix('participants')->name('participants.')->group(function () {
        Route::get('/', [ParticipantController::class, 'index'])->name('index');
        Route::get('/{participant}', [ParticipantController::class, 'show'])->name('show');
        Route::get('/{participant}/edit', [ParticipantController::class, 'edit'])->name('edit');
        Route::put('/{participant}', [ParticipantController::class, 'update'])->name('update');
        Route::delete('/{participant}', [ParticipantController::class, 'destroy'])->name('destroy');
    });


    // Activity Logs
    Route::get('/activity-logs', function () {
        $logs = \App\Models\ActivityLog::with('user')->latest()->paginate(20);
        return view('activity-logs.index', compact('logs'));
    })->name('activity-logs.index');

    // Redirect root → dashboard
    Route::get('/', function () {
        return redirect('/dashboard');
    });
});


/*
|--------------------------------------------------------------------------
| Fallback
|--------------------------------------------------------------------------
*/

Route::fallback(function () {
    return auth()->check() ? redirect('/dashboard') : redirect('/login');
});
