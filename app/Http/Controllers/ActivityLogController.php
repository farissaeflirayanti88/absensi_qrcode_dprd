<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityLogController extends Controller
{
    /**
     * FR-13: Tampilkan daftar activity log dengan filter
     */
    public function index(Request $request)
    {
        $query = ActivityLog::with('user')->orderBy('created_at', 'desc');

        // Filter berdasarkan tanggal (FR-13 Alternative Scenario)
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Filter berdasarkan user (FR-13 Alternative Scenario)
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter berdasarkan kata kunci aktivitas
        if ($request->filled('search')) {
            $query->where('activity', 'like', '%' . $request->search . '%');
        }

        $logs     = $query->paginate(50)->withQueryString();
        $users    = User::orderBy('name')->get();
        $totalLog = ActivityLog::count();

        // Statistik ringkas
        $todayLog  = ActivityLog::whereDate('created_at', today())->count();
        $weekLog   = ActivityLog::where('created_at', '>=', now()->subDays(7))->count();

        // Log bahwa halaman ini dibuka (FR-13)
        ActivityLog::create([
            'user_id'    => Auth::id(),
            'activity'   => 'Melihat daftar activity log',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return view('activity-logs.index', compact(
            'logs', 'users', 'totalLog', 'todayLog', 'weekLog'
        ));
    }

    /**
     * FR-13: Tampilkan detail satu log
     */
    public function show(ActivityLog $activityLog)
    {
        $activityLog->load('user');

        return view('activity-logs.show', compact('activityLog'));
    }

    /**
     * Hapus satu log (admin only)
     */
    public function destroy(ActivityLog $activityLog)
    {
        $activityLog->delete();

        ActivityLog::create([
            'user_id'    => Auth::id(),
            'activity'   => 'Menghapus log #' . $activityLog->id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()->route('activity-logs.index')
            ->with('success', 'Log berhasil dihapus.');
    }

    /**
     * Hapus log lama (lebih dari N hari)
     */
    public function clearOldLogs(Request $request)
    {
        $request->validate([
            'days' => 'required|integer|min:7|max:365',
        ]);

        $cutoff  = now()->subDays($request->days);
        $deleted = ActivityLog::where('created_at', '<', $cutoff)->delete();

        ActivityLog::create([
            'user_id'    => Auth::id(),
            'activity'   => 'Membersihkan ' . $deleted . ' log lama (> ' . $request->days . ' hari)',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('activity-logs.index')
            ->with('success', $deleted . ' log lama berhasil dihapus.');
    }
}
