<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Attendance;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $totalEvents = Event::count();
        
        // Query manual untuk attendance karena nama tabel tidak standar
        $todayAttendances = DB::table('attendance')
                            ->whereDate('created_at', today())
                            ->count();
        
        $recentActivities = ActivityLog::with('user')->latest()->take(5)->get();

        return view('dashboard.index', compact('totalEvents', 'todayAttendances', 'recentActivities'));
    }
}