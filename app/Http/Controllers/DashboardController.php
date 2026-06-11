<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Attendance;
use App\Models\Participant;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        try {
            // =============================================
            // STATISTIK DASAR (UNTUK 6 CARD)
            // =============================================
            $totalEvents = Event::count();
            $activeEvents = Event::where('is_active', true)->count();
            
            // Acara mendatang (future events that are active)
            $upcomingEvents = Event::where('event_date', '>', Carbon::now())
                ->where('is_active', true)
                ->count();
            
            // Total peserta unik (konstituen yang pernah hadir)
            $totalParticipants = DB::table('attendance')
                ->distinct('participant_id')
                ->count('participant_id');
            
            // Kehadiran hari ini
            $todayAttendances = DB::table('attendance')
                ->whereDate('attendance_time', today())
                ->orWhereDate('created_at', today())
                ->count();
            
            // Acara yang sudah memiliki kehadiran
            $eventsWithAttendances = Event::has('attendances')->count();
            
            // =============================================
            // STATISTIK OPSI 3 (PROFESIONAL UNTUK SIDANG)
            // =============================================
            
            // 1. Total Kehadiran (semua baris attendance)
            $totalKehadiran = Attendance::count();
            
            // 2. Kehadiran 30 Hari Terakhir
            $kehadiran30Hari = Attendance::where('created_at', '>=', now()->subDays(30))->count();
            
            // 3. Rata-rata Kehadiran Harian (30 hari terakhir)
            $rataHarian = $kehadiran30Hari > 0 
                ? round($kehadiran30Hari / 30, 1) 
                : 0;
            
            // 4. Proyeksi Akhir Bulan
            $hariDalamBulan = now()->daysInMonth;
            $hariBerlalu = now()->day;
            $proyeksiAkhirBulan = $hariBerlalu > 0 
                ? round(($kehadiran30Hari / $hariBerlalu) * $hariDalamBulan) 
                : 0;
            
            // 5. Acara Selesai (tanggal sudah lewat)
            $acaraSelesai = Event::where('event_date', '<', now())->count();
            
            // 6. Acara dengan Peserta (yang sudah ada data)
            $acaraDenganPeserta = Event::has('attendances')->count();
            
            // 7. Rata-rata Peserta per Acara (hanya acara dengan peserta)
            $rataPesertaPerAcara = $acaraDenganPeserta > 0 
                ? round($totalKehadiran / $acaraDenganPeserta, 1) 
                : 0;
            
            // 8. Persentase Acara Berjalan (acara dengan peserta / total acara)
            $persentaseAcaraBerjalan = $totalEvents > 0 
                ? round(($acaraDenganPeserta / $totalEvents) * 100) 
                : 0;
            
            // 9. TOP 3 Acara dengan Kehadiran Terbanyak
            $topAcara = Event::withCount('attendances')
                ->having('attendances_count', '>', 0)
                ->orderBy('attendances_count', 'desc')
                ->limit(3)
                ->get();
            
            // 10. Trend Mingguan (7 hari terakhir)
            $trendMingguan = [];
            for ($i = 6; $i >= 0; $i--) {
                $tanggal = now()->subDays($i)->format('Y-m-d');
                $jumlah = Attendance::whereDate('created_at', $tanggal)->count();
                $trendMingguan[] = [
                    'tanggal' => now()->subDays($i)->format('d/m'),
                    'jumlah' => $jumlah,
                    'hari' => now()->subDays($i)->isoFormat('dddd')
                ];
            }
            
            // 11. Statistik Berdasarkan Wilayah (dari alamat peserta)
            $statistikWilayah = DB::table('attendance')
                ->join('participants', 'attendance.participant_id', '=', 'participants.id')
                ->select('participants.address', DB::raw('COUNT(*) as total'))
                ->whereNotNull('participants.address')
                ->where('participants.address', '!=', '')
                ->groupBy('participants.address')
                ->orderBy('total', 'desc')
                ->limit(5)
                ->get();
            
            // 12. Kehadiran per Bulan (6 bulan terakhir)
            $kehadiranPerBulan = [];
            for ($i = 5; $i >= 0; $i--) {
                $bulan = now()->subMonths($i);
                $jumlah = Attendance::whereYear('created_at', $bulan->year)
                    ->whereMonth('created_at', $bulan->month)
                    ->count();
                $kehadiranPerBulan[] = [
                    'bulan' => $bulan->format('M Y'),
                    'jumlah' => $jumlah
                ];
            }
            
            // 13. Recent Events (5 acara terbaru)
            $recentEvents = Event::withCount('attendances')
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();
            
            // 14. Recent Activities (5 aktivitas terbaru)
            $recentActivities = ActivityLog::with('user')
                ->latest()
                ->take(5)
                ->get();
            
            // 15. Today Active Events
            $todayActiveEvents = Event::where('is_active', true)
                ->whereDate('event_date', today())
                ->count();
            
            return view('dashboard.index', compact(
                // Basic stats
                'totalEvents',
                'activeEvents',
                'upcomingEvents',
                'totalParticipants',
                'todayAttendances',
                'eventsWithAttendances',
                'recentEvents',
                'recentActivities',
                'todayActiveEvents',
                
                // Advanced stats (Opsi 3)
                'totalKehadiran',
                'kehadiran30Hari',
                'rataHarian',
                'proyeksiAkhirBulan',
                'acaraSelesai',
                'acaraDenganPeserta',
                'rataPesertaPerAcara',
                'persentaseAcaraBerjalan',
                'topAcara',
                'trendMingguan',
                'statistikWilayah',
                'kehadiranPerBulan'
            ));
            
        } catch (\Exception $e) {
            // Jika ada error, return dengan data default
            return view('dashboard.index', [
                // Basic stats default
                'totalEvents' => 0,
                'activeEvents' => 0,
                'upcomingEvents' => 0,
                'totalParticipants' => 0,
                'todayAttendances' => 0,
                'eventsWithAttendances' => 0,
                'recentEvents' => collect(),
                'recentActivities' => collect(),
                'todayActiveEvents' => 0,
                
                // Advanced stats default
                'totalKehadiran' => 0,
                'kehadiran30Hari' => 0,
                'rataHarian' => 0,
                'proyeksiAkhirBulan' => 0,
                'acaraSelesai' => 0,
                'acaraDenganPeserta' => 0,
                'rataPesertaPerAcara' => 0,
                'persentaseAcaraBerjalan' => 0,
                'topAcara' => collect(),
                'trendMingguan' => [],
                'statistikWilayah' => collect(),
                'kehadiranPerBulan' => []
            ]);
        }
    }

    /**
     * Get dashboard statistics for API/AJAX
     */
    public function getStatistics()
    {
        try {
            $totalEvents = Event::count();
            $eventsWithAttendances = Event::has('attendances')->count();
            
            $stats = [
                // Basic stats
                'totalEvents' => $totalEvents,
                'activeEvents' => Event::where('is_active', true)->count(),
                'upcomingEvents' => Event::where('event_date', '>', Carbon::now())
                    ->where('is_active', true)
                    ->count(),
                'totalParticipants' => DB::table('attendance')
                    ->distinct('participant_id')
                    ->count('participant_id'),
                'todayAttendances' => DB::table('attendance')
                    ->whereDate('attendance_time', today())
                    ->orWhereDate('created_at', today())
                    ->count(),
                'eventsWithAttendances' => $eventsWithAttendances,
                'todayActiveEvents' => Event::where('is_active', true)
                    ->whereDate('event_date', today())
                    ->count(),
                'totalAttendances' => DB::table('attendance')->count(),
                
                // Advanced stats for AJAX
                'attendanceRate' => $totalEvents > 0 
                    ? round(($eventsWithAttendances / $totalEvents) * 100) 
                    : 0,
                'kehadiran30Hari' => Attendance::where('created_at', '>=', now()->subDays(30))->count(),
                'rataHarian' => round(Attendance::where('created_at', '>=', now()->subDays(30))->count() / 30, 1),
            ];
            
            return response()->json([
                'success' => true,
                'data' => $stats,
                'last_updated' => now()->format('Y-m-d H:i:s')
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch statistics: ' . $e->getMessage()
            ], 500);
        }
    }
}