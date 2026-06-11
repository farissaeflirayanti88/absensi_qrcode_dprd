<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Attendance;
use App\Models\Participant;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EventController extends Controller
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->middleware('auth')->except([
            'attendanceFormPublic', 
            'generateQRCode',
            'getCalendarEvents'
        ]);
    }

    /**
     * ==========================================================
     * HELPER METHODS
     * ==========================================================
     */
    
    /**
     * Cek apakah kolom registration_code ada (dengan cache)
     */
    private function hasRegistrationCodeColumn()
    {
        return Cache::remember('has_registration_code_column', 3600, function() {
            return Schema::hasColumn('events', 'registration_code');
        });
    }

    /**
     * Helper: Generate QR Code URL menggunakan QuickChart dengan timeout handling.
     */
    private function generateQRCodeUrl(Event $event, $size = 300)
    {
        try {
            $attendanceUrl = route('attendance.form.public', $event->id);
            
            if ($event->qr_code_hash) {
                $attendanceUrl .= '?qr=' . $event->qr_code_hash;
            }
            
            // Gunakan HTTP client dengan timeout
            $client = new \GuzzleHttp\Client(['timeout' => 10]);
            
            return "https://quickchart.io/qr?" . http_build_query([
                'text' => $attendanceUrl,
                'size' => $size,
                'format' => 'png',
                'margin' => 1,
                'errorCorrection' => 'H'
            ]);
        } catch (\Exception $e) {
            Log::error('Generate QR URL failed', [
                'event_id' => $event->id,
                'error' => $e->getMessage()
            ]);
            
            // Fallback URL tanpa validasi
            return "https://quickchart.io/qr?text=" . urlencode($attendanceUrl) . "&size={$size}";
        }
    }

    /**
     * Get event statistics dengan cache panjang
     */
    private function getEventStatistics(Event $event): array
    {
        $cacheKey = "event_stats_{$event->id}";
        
        return Cache::remember($cacheKey, now()->addMinutes(15), function() use ($event) {
            return [
                'todayCount' => $event->attendances()
                    ->whereDate('attendance_time', today())
                    ->count(),
                'uniqueParticipants' => $event->attendances()
                    ->select('participant_id')
                    ->distinct()
                    ->count(),
                'last7Days' => $event->attendances()
                    ->where('attendance_time', '>=', now()->subDays(7))
                    ->count(),
                'totalAttendances' => $event->attendances()->count(),
            ];
        });
    }

    /**
     * Clear event cache
     */
    private function clearEventCache(Event $event)
    {
        Cache::forget("event_stats_{$event->id}");
        Cache::forget("event_addresses_{$event->id}");
    }

    /**
     * Get event analytics data.
     */
    private function getEventAnalytics(Event $event): array
    {
        return [
            'attendance_by_hour' => Cache::remember("event_hours_{$event->id}", 3600, function() use ($event) {
                return Attendance::where('event_id', $event->id)
                    ->selectRaw('HOUR(created_at) as hour, COUNT(*) as count')
                    ->groupBy('hour')
                    ->orderBy('hour')
                    ->get();
            }),
            'attendance_by_address' => Cache::remember("event_addresses_{$event->id}", 3600, function() use ($event) {
                return Attendance::where('event_id', $event->id)
                    ->join('participants', 'attendance.participant_id', '=', 'participants.id')
                    ->selectRaw('participants.address, COUNT(*) as count')
                    ->groupBy('participants.address')
                    ->orderBy('count', 'DESC')
                    ->limit(10)
                    ->get();
            }),
        ];
    }

    /**
     * Handle bulk export of events.
     */
    private function handleBulkExport(array $eventIds): StreamedResponse
    {
        $events = Event::whereIn('id', $eventIds)
            ->withCount('attendances')
            ->get();
        
        $fileName = 'events_export_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];
        
        return response()->stream(function() use ($events) {
            $file = fopen('php://output', 'w');
            fwrite($file, "\xEF\xBB\xBF");
            
            fputcsv($file, [
                'ID', 'Nama Event', 'Lokasi', 'Tanggal', 
                'Status', 'Arsip', 'Jumlah Peserta', 'Kategori', 'Dibuat Pada'
            ]);
            
            foreach ($events as $event) {
                fputcsv($file, [
                    $event->id,
                    $event->event_name,
                    $event->location,
                    $event->event_date->format('Y-m-d H:i'),
                    $event->is_active ? 'Aktif' : 'Nonaktif',
                    $event->isArchived() ? 'Ya' : 'Tidak',
                    $event->attendances_count,
                    $event->category ?? '-',
                    $event->created_at->format('Y-m-d H:i:s')
                ]);
            }
            fclose($file);
        }, 200, $headers);
    }

    /**
     * Handle bulk archive
     */
    private function handleBulkArchive(array $selected)
    {
        try {
            DB::beginTransaction();
            
            $eventsToArchive = Event::whereIn('id', $selected)
                ->has('attendances')
                ->notArchived()
                ->get();
            
            if ($eventsToArchive->isEmpty()) {
                return back()
                    ->with('error', 'Tidak ada event yang dapat diarsipkan (tidak ada data absensi atau sudah diarsip).');
            }
            
            foreach ($eventsToArchive as $event) {
                $event->update([
                    'status' => 'archived',
                    'is_active' => false
                ]);
                
                // Clear cache
                $this->clearEventCache($event);
                
                // Activity Log
                if (class_exists(ActivityLog::class)) {
                    ActivityLog::create([
                        'user_id' => auth()->id(),
                        'activity' => 'Mengarsipkan acara (bulk): ' . $event->event_name,
                        'ip_address' => request()->ip(),
                        'user_agent' => request()->userAgent(),
                    ]);
                }
            }
            
            $eventsWithoutAttendances = Event::whereIn('id', $selected)
                ->doesntHave('attendances')
                ->notArchived()
                ->count();
            
            $message = $eventsToArchive->count() . ' event berhasil diarsipkan.';
            
            if ($eventsWithoutAttendances > 0) {
                $message .= ' ' . $eventsWithoutAttendances . ' event tidak diarsipkan karena tidak memiliki data absensi.';
            }
            
            DB::commit();
            
            return back()->with('success', $message);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Bulk archive failed: ' . $e->getMessage());
            return back()->with('error', 'Gagal mengarsipkan event. Silakan coba lagi.');
        }
    }

    /**
     * ==========================================================
     * METHOD: UPCOMING & PAST
     * ==========================================================
     */
    
    /**
     * Display upcoming events (future events)
     */
    public function upcoming(Request $request)
    {
        $query = Event::query()
            ->withCount('attendances')
            ->with('creator:id,name')
            ->notArchived()
            ->where('event_date', '>', now());

        if ($request->has('search') && $request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('event_name', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        if ($request->has('status') && in_array($request->status, ['active', 'inactive'])) {
            $query->where('is_active', $request->status === 'active');
        }

        $sort = $request->get('sort', 'date_asc');
        switch ($sort) {
            case 'date_desc': $query->orderBy('event_date', 'desc'); break;
            case 'name_asc': $query->orderBy('event_name', 'asc'); break;
            case 'name_desc': $query->orderBy('event_name', 'desc'); break;
            default: $query->orderBy('event_date', 'asc');
        }

        $events = $query->paginate(20)->withQueryString();
        
        $totalUpcoming = $query->count();
        $nearestEvent = Event::where('event_date', '>', now())
            ->orderBy('event_date', 'asc')
            ->first();

        return view('events.upcoming', compact('events', 'totalUpcoming', 'nearestEvent'));
    }

    /**
     * Display past events (already finished)
     */
    public function past(Request $request)
    {
        $query = Event::query()
            ->withCount('attendances')
            ->with('creator:id,name')
            ->notArchived()
            ->where('event_date', '<', now());

        if ($request->has('search') && $request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('event_name', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        if ($request->has('status') && in_array($request->status, ['active', 'inactive'])) {
            $query->where('is_active', $request->status === 'active');
        }

        $sort = $request->get('sort', 'date_desc');
        switch ($sort) {
            case 'date_asc': $query->orderBy('event_date', 'asc'); break;
            case 'name_asc': $query->orderBy('event_name', 'asc'); break;
            case 'name_desc': $query->orderBy('event_name', 'desc'); break;
            default: $query->orderBy('event_date', 'desc');
        }

        $events = $query->paginate(20)->withQueryString();
        
        $totalPast = $query->count();
        $totalAttendancesPast = Attendance::whereIn('event_id', $query->pluck('id'))->count();

        return view('events.past', compact('events', 'totalPast', 'totalAttendancesPast'));
    }

    /**
     * ==========================================================
     * CRUD EVENTS
     * ==========================================================
     */

    /**
     * Display a listing of events (excluding archived).
     */
    public function index(Request $request)
    {
        $query = Event::query()
            ->withCount('attendances')
            ->with('creator:id,name')
            ->notArchived();
        
        if ($request->has('search') && $request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('event_name', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        if ($request->has('status') && in_array($request->status, ['active', 'inactive'])) {
            $query->where('is_active', $request->status === 'active');
        }
        
        if ($request->has('date_from') && $request->filled('date_from')) {
            $query->whereDate('event_date', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->filled('date_to')) {
            $query->whereDate('event_date', '<=', $request->date_to);
        }
        
        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'oldest': $query->oldest(); break;
            case 'name_asc': $query->orderBy('event_name', 'asc'); break;
            case 'name_desc': $query->orderBy('event_name', 'desc'); break;
            case 'date_asc': $query->orderBy('event_date', 'asc'); break;
            case 'date_desc': $query->orderBy('event_date', 'desc'); break;
            default: $query->latest();
        }
        
        $events = $query->paginate(20)->withQueryString();
        
        return view('events.index', compact('events'));
    }

    /**
     * Show the form for creating a new event.
     */
    public function create()
    {
        return view('events.create');
    }

    /**
     * Store a newly created event in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'event_name' => 'required|string|max:255|min:3',
                'location'   => 'required|string|max:255|min:3',
                'event_date' => 'required|date_format:Y-m-d|after_or_equal:today',
                'event_time' => 'nullable|date_format:H:i',
                'description' => 'nullable|string|max:1000',
                'is_active' => 'required|in:0,1',
            ]);

            DB::beginTransaction();

            $tanggal = $validated['event_date'];
            $waktu = !empty($validated['event_time']) ? $validated['event_time'] : '09:00';
            $eventDateTime = $tanggal . ' ' . $waktu . ':00';
            
            Log::info('Menyimpan event:', [
                'tanggal_input' => $tanggal,
                'waktu_input' => $waktu,
                'event_date_final' => $eventDateTime
            ]);

            $eventData = [
                'event_name' => trim($validated['event_name']),
                'location' => trim($validated['location']),
                'event_date' => $eventDateTime,
                'description' => !empty($validated['description']) ? trim($validated['description']) : null,
                'is_active' => (bool)$validated['is_active'],
                'created_by' => auth()->id(),
                'qr_code_hash' => Str::uuid()->toString(),
                'qr_code_generated_at' => now(),
                'status' => 'active',
            ];
            
            if ($this->hasRegistrationCodeColumn()) {
                $eventData['registration_code'] = 'EV-' . strtoupper(Str::random(6));
            }

            $event = Event::create($eventData);
            $qrUrl = $this->generateQRCodeUrl($event);
            $event->update(['qr_code_url' => $qrUrl]);

            DB::commit();

            if (class_exists(ActivityLog::class)) {
                ActivityLog::create([
                    'user_id'    => auth()->id(),
                    'activity'   => 'Membuat acara baru: ' . $event->event_name . ' (' . $event->event_date->format('d/m/Y') . ')',
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
            }

            return redirect()->route('events.show', $event->id)
                ->with('success', 'Acara "' . $event->event_name . '" berhasil dibuat pada ' . $event->event_date->format('d/m/Y H:i'));

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput()
                ->with('error', 'Validasi gagal. Periksa kembali data yang diinput.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating event: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal membuat event: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified event.
     * OPTIMASI: Eager loading dengan query optimization
     */
    public function show($id)
    {
        $event = Event::with([
                'attendances' => function($query) {
                    $query->with(['participant:id,name,address,phone'])
                          ->latest('attendance_time')
                          ->limit(10);
                },
                'creator:id,name,email,username,role'
            ])
            ->withCount('attendances')
            ->findOrFail($id);
        
        // Gunakan cache untuk statistik
        $statistics = $this->getEventStatistics($event);
        
        $recentAttendances = $event->attendances()
            ->with('participant:id,name,address')
            ->latest('attendance_time')
            ->limit(5)
            ->get();
        
        return view('events.show', compact('event', 'statistics', 'recentAttendances'));
    }

    /**
     * Show the form for editing the specified event.
     */
    public function edit($id)
    {
        $event = Event::findOrFail($id);
        
        if ($event->isArchived()) {
            return redirect()->route('events.archived')
                ->with('error', 'Event yang sudah diarsipkan tidak dapat diedit.');
        }
        
        $event->form_date = Carbon::parse($event->event_date)->format('Y-m-d');
        $event->form_time = Carbon::parse($event->event_date)->format('H:i');
        
        return view('events.edit', compact('event'));
    }

    /**
     * Update the specified event in storage.
     */
    public function update(Request $request, $id)
    {
        $event = Event::findOrFail($id);
        
        if ($event->isArchived()) {
            return redirect()->route('events.archived')
                ->with('error', 'Event yang sudah diarsipkan tidak dapat diedit.');
        }
        
        try {
            $validated = $request->validate([
                'event_name' => 'required|string|max:255|min:3',
                'location'   => 'required|string|max:255|min:3',
                'event_date' => 'required|date|after_or_equal:today',
                'event_time' => 'nullable|date_format:H:i',
                'description' => 'nullable|string',
                'is_active' => 'boolean',
                'max_participants' => 'nullable|integer|min:1',
                'requires_registration' => 'boolean',
                'category' => 'nullable|string|max:100',
            ]);

            DB::beginTransaction();

            $tanggal = $validated['event_date'];
            if (is_string($tanggal) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal)) {
                // sudah format tanggal
            } else {
                $tanggal = Carbon::parse($tanggal)->format('Y-m-d');
            }
            
            $waktu = !empty($validated['event_time']) ? $validated['event_time'] : '09:00';
            $eventDateTime = $tanggal . ' ' . $waktu . ':00';
            
            Log::info('Mengupdate event ID ' . $id . ':', [
                'tanggal_input' => $validated['event_date'],
                'waktu_input' => $validated['event_time'] ?? '09:00 (default)',
                'event_date_final' => $eventDateTime
            ]);

            $event->update([
                'event_name' => $validated['event_name'],
                'location' => $validated['location'],
                'event_date' => $eventDateTime,
                'description' => $validated['description'] ?? null,
                'is_active' => $validated['is_active'] ?? $event->is_active,
                'max_participants' => $validated['max_participants'] ?? null,
                'requires_registration' => $validated['requires_registration'] ?? false,
                'category' => $validated['category'] ?? null,
            ]);
            
            DB::commit();
            
            // Clear cache
            $this->clearEventCache($event);
            
            if (class_exists(ActivityLog::class)) {
                ActivityLog::create([
                    'user_id' => auth()->id(),
                    'activity' => 'Memperbarui acara: ' . $event->event_name,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            }
            
            return redirect()->route('events.show', $event->id)
                ->with('success', 'Event berhasil diperbarui! Waktu: ' . $event->event_date->format('d/m/Y H:i'));
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating event: ' . $e->getMessage());
            return back()
                ->withInput()
                ->with('error', 'Gagal memperbarui event. Silakan coba lagi.');
        }
    }

    /**
     * Remove the specified event from storage.
     */
    public function destroy($id)
    {
        $event = Event::findOrFail($id);
        
        try {
            if ($event->isArchived()) {
                return back()->with('error', 'Event yang sudah diarsipkan tidak dapat dihapus.');
            }
            
            if ($event->attendances()->exists()) {
                return back()->with('error', 'Tidak dapat menghapus event yang sudah memiliki data absensi. Gunakan fitur "Arsipkan" untuk menyimpan data.');
            }
            
            $eventName = $event->event_name;
            $event->delete();
            
            // Clear cache
            $this->clearEventCache($event);
            
            if (class_exists(ActivityLog::class)) {
                ActivityLog::create([
                    'user_id' => auth()->id(),
                    'activity' => 'Menghapus acara: ' . $eventName,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
            }
            
            return redirect()->route('events.index')
                ->with('success', 'Event "' . $eventName . '" berhasil dihapus!');
                
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus event. Silakan coba lagi.');
        }
    }

    /**
     * ==========================================================
     * QR CODE MANAGEMENT - VERSION FINAL DENGAN ALL FIX
     * ==========================================================
     */

    /**
     * Show QR Code page for event.
     */
    public function qrPage($id)
    {
        $event = Event::findOrFail($id);
        $attendanceUrl = route('attendance.form.public', $event->id);
        $qrUrl = $this->generateQRCodeUrl($event);
        $statistics = $this->getEventStatistics($event);
        
        return view('events.qr-code', array_merge(
            compact('event', 'qrUrl', 'attendanceUrl'), 
            $statistics
        ));
    }

    /**
     * Generate QR Code dengan semua FIX:
     * - Fix Error 405 (Method Not Allowed)
     * - Fix Error 508 (Timeout/Loop)
     * - Fix Error 0 (Connection)
     */
    public function generateQRCode(Request $request, $id)
    {
        // ============================================================
        // FIX 1: Validasi Method - Mencegah Error 405
        // ============================================================
        if (!$request->isMethod('post')) {
            Log::warning('Generate QR Code: Invalid method used', [
                'method' => $request->method(),
                'event_id' => $id,
                'ip' => $request->ip()
            ]);
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Method not allowed. Please use POST.',
                    'error_code' => 'METHOD_NOT_ALLOWED'
                ], 405);
            }
            
            return redirect()->back()
                ->with('error', 'Metode request tidak valid. Gunakan POST.');
        }
        
        // ============================================================
        // FIX 2: Set Timeout Lebih Panjang - Mencegah Error 508
        // ============================================================
        set_time_limit(60);
        
        // ============================================================
        // FIX 3: Validasi Request ID
        // ============================================================
        if (!is_numeric($id) || $id <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'ID event tidak valid.'
            ], 400);
        }
        
        try {
            // ============================================================
            // FIX 4: Gunakan findOrFail dengan try-catch
            // ============================================================
            $event = Event::findOrFail($id);
            
            // Cek apakah event diarsipkan
            if ($event->isArchived()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat generate QR untuk event yang sudah diarsipkan.',
                    'error_code' => 'EVENT_ARCHIVED'
                ], 403);
            }
            
            // Cek apakah event masih aktif
            if (!$event->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Event tidak aktif. Aktifkan event terlebih dahulu.',
                    'error_code' => 'EVENT_INACTIVE'
                ], 403);
            }
            
            $newHash = Str::uuid()->toString();
            
            // ============================================================
            // FIX 5: Gunakan transaction dengan timeout handling
            // ============================================================
            DB::beginTransaction();
            
            try {
                // Update dengan format datetime yang benar
                $event->update([
                    'qr_code_hash' => $newHash,
                    'qr_code_generated_at' => now()->format('Y-m-d H:i:s'),
                ]);
                
                // Generate QR URL (external call - bisa timeout)
                $qrUrl = $this->generateQRCodeUrl($event);
                
                // Update QR URL
                $event->update(['qr_code_url' => $qrUrl]);
                
                DB::commit();
                
                // Log success
                Log::info('QR Code generated successfully', [
                    'event_id' => $id,
                    'event_name' => $event->event_name,
                    'user_id' => auth()->id()
                ]);
                
                // Clear cache jika ada
                Cache::forget("event_qr_{$id}");
                
            } catch (\Exception $e) {
                DB::rollBack();
                
                // ============================================================
                // FIX 6: Deteksi Error 508 (Timeout)
                // ============================================================
                if (strpos($e->getMessage(), 'timeout') !== false || 
                    strpos($e->getMessage(), 'timed out') !== false) {
                    Log::error('QR Generation Timeout', [
                        'event_id' => $id,
                        'error' => $e->getMessage()
                    ]);
                    
                    return response()->json([
                        'success' => false,
                        'message' => 'Generate QR timeout. Silakan coba lagi.',
                        'error_code' => 'TIMEOUT'
                    ], 408); // 408 Request Timeout
                }
                
                throw $e; // Re-throw untuk ditangani di catch luar
            }
            
            // Response untuk AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'QR Code berhasil digenerate!',
                    'qr_url' => $qrUrl,
                    'qr_code_hash' => $newHash,
                    'event_name' => $event->event_name
                ]);
            }
            
            return redirect()->route('events.qr.page', $event->id)
                ->with('success', 'QR Code berhasil digenerate!')
                ->with('qr_url', $qrUrl);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Generate QR Code: Event not found', ['id' => $id]);
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Event tidak ditemukan.',
                    'error_code' => 'EVENT_NOT_FOUND'
                ], 404);
            }
            
            return redirect()->back()
                ->with('error', 'Event tidak ditemukan.');
                
        } catch (\Exception $e) {
            // Rollback sudah dilakukan di dalam
            Log::error('Error generating QR code: ' . $e->getMessage(), [
                'event_id' => $id,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // ============================================================
            // FIX 7: Deteksi Error 0 (Connection)
            // ============================================================
            $errorMessage = $e->getMessage();
            $errorCode = 'SERVER_ERROR';
            $statusCode = 500;
            
            if (strpos($errorMessage, 'Connection') !== false || 
                strpos($errorMessage, 'cURL') !== false) {
                $errorCode = 'CONNECTION_ERROR';
                $statusCode = 503; // Service Unavailable
            } elseif (strpos($errorMessage, 'Invalid datetime') !== false) {
                $errorCode = 'INVALID_DATETIME';
                $statusCode = 500;
            }
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal generate QR Code: ' . $e->getMessage(),
                    'error_code' => $errorCode
                ], $statusCode);
            }
            
            return redirect()->back()
                ->with('error', 'Gagal generate QR Code: ' . $e->getMessage());
        }
    }

    /**
     * Refresh/regenerate QR Code (AJAX).
     */
    public function refreshQRCode(Request $request, $id)
    {
        // Validasi method - FIX ERROR 405
        if (!$request->isMethod('post')) {
            return response()->json([
                'success' => false,
                'message' => 'Method not allowed. Please use POST.',
                'error_code' => 'METHOD_NOT_ALLOWED'
            ], 405);
        }
        
        set_time_limit(60);
        
        try {
            $event = Event::findOrFail($id);
            
            // Cek apakah event diarsipkan
            if ($event->isArchived()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat refresh QR untuk event yang sudah diarsipkan.',
                    'error_code' => 'EVENT_ARCHIVED'
                ], 403);
            }
            
            // Cek apakah event masih aktif
            if (!$event->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Event tidak aktif. Aktifkan event terlebih dahulu.',
                    'error_code' => 'EVENT_INACTIVE'
                ], 403);
            }
            
            $newHash = Str::uuid()->toString();
            
            DB::beginTransaction();
            
            try {
                $event->update([
                    'qr_code_hash'         => $newHash,
                    'qr_code_generated_at' => now()->format('Y-m-d H:i:s'),
                    'qr_expired_at'        => now()->addDay()->format('Y-m-d H:i:s'),
                ]);
                
                $qrUrl = $this->generateQRCodeUrl($event);
                $event->update(['qr_code_url' => $qrUrl]);
                
                DB::commit();
                
                // Clear cache
                Cache::forget("event_qr_{$id}");
                
            } catch (\Exception $e) {
                DB::rollBack();
                
                if (strpos($e->getMessage(), 'timeout') !== false) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Refresh QR timeout. Silakan coba lagi.',
                        'error_code' => 'TIMEOUT'
                    ], 408);
                }
                
                throw $e;
            }
            
            if (class_exists(ActivityLog::class)) {
                ActivityLog::create([
                    'user_id' => auth()->id(),
                    'activity' => 'Refresh QR Code untuk acara: ' . $event->event_name,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            }
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'qr_url' => $qrUrl,
                    'qr_code_hash' => $newHash,
                    'message' => 'QR Code berhasil diperbarui'
                ]);
            }
            
            return redirect()->route('events.qr.page', $event->id)
                ->with('success', 'QR Code berhasil diperbarui')
                ->with('qr_url', $qrUrl);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Refresh QR Code: Event not found', ['id' => $id]);
            
            return response()->json([
                'success' => false,
                'message' => 'Event tidak ditemukan.',
                'error_code' => 'EVENT_NOT_FOUND'
            ], 404);
            
        } catch (\Exception $e) {
            Log::error('Error refreshing QR code: ' . $e->getMessage(), [
                'event_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            
            $errorCode = 'SERVER_ERROR';
            $statusCode = 500;
            
            if (strpos($e->getMessage(), 'Connection') !== false) {
                $errorCode = 'CONNECTION_ERROR';
                $statusCode = 503;
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui QR Code: ' . $e->getMessage(),
                'error_code' => $errorCode
            ], $statusCode);
        }
    }

    /**
     * Toggle event active status (AJAX compatible).
     */
    public function toggleStatus(Request $request, $id)
    {
        try {
            $event = Event::findOrFail($id);
            
            $newStatus = !$event->is_active;
            $event->update(['is_active' => $newStatus]);
            
            // Clear cache
            $this->clearEventCache($event);
            
            $message = $newStatus 
                ? 'Akses absensi berhasil dibuka' 
                : 'Akses absensi berhasil ditutup';
            
            if (class_exists(ActivityLog::class)) {
                ActivityLog::create([
                    'user_id' => auth()->id(),
                    'activity' => $message . ' untuk acara: ' . $event->event_name,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            }
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'is_active' => $newStatus,
                    'message' => $message,
                    'event_id' => $id,
                    'event_name' => $event->event_name
                ]);
            }
            
            return redirect()->back()->with('success', $message);
            
        } catch (\Exception $e) {
            Log::error('Error toggling event status: ' . $e->getMessage());
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * ==========================================================
     * ATTENDANCE MANAGEMENT
     * ==========================================================
     */

    /**
     * Public Attendance Form (for QR code scanning).
     */
    public function attendanceFormPublic($id)
    {
        $event = Event::where('is_active', true)
            ->notArchived()
            ->findOrFail($id);
        
        if (Carbon::parse($event->event_date)->isPast() && !Carbon::parse($event->event_date)->isToday()) {
            return view('attendance.event-ended', compact('event'));
        }
        
        if ($event->max_participants) {
            $currentParticipants = $event->attendances()->count();
            if ($currentParticipants >= $event->max_participants) {
                return view('attendance.event-full', compact('event'));
            }
        }
        
        return view('attendance.form-public', compact('event'));
    }

    /**
     * Display event attendances with filters.
     * OPTIMASI: Menggunakan index yang sudah dibuat
     */
    public function attendances($id, Request $request)
    {
        $event = Event::findOrFail($id);
        
        $query = $event->attendances()
            ->with('participant:id,name,address,phone')
            ->with('event:id,event_name,location,event_date');
        
        if ($request->has('search') && $request->filled('search')) {
            $search = $request->get('search');
            $query->whereHas('participant', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }
        
        if ($request->has('date') && $request->filled('date')) {
            $query->whereDate('attendance_time', $request->date);
        }
        
        if ($request->has('address') && $request->filled('address')) {
            $query->whereHas('participant', function($q) use ($request) {
                $q->where('address', 'like', "%{$request->address}%");
            });
        }
        
        $showDuplicates = $request->boolean('show_duplicates', false);
        if ($showDuplicates) {
            $query->where('is_duplicate', true);
        }
        
        // OPTIMASI: Gunakan index idx_attendance_event_time_desc
        $attendances = $query->latest('attendance_time')->paginate(50);
        
        // OPTIMASI: Cache daftar address untuk 1 jam
        $addresses = Cache::remember("event_addresses_{$id}", 3600, function() use ($event) {
            return DB::table('attendance')
                ->join('participants', 'attendance.participant_id', '=', 'participants.id')
                ->where('attendance.event_id', $event->id)
                ->select('participants.address')
                ->distinct()
                ->pluck('address')
                ->filter();
        });
        
        $events = Event::orderBy('event_date', 'desc')->get(['id', 'event_name', 'event_date']);
        $totalAttendances = $event->attendances()->count();
        $todayAttendances = $event->attendances()->whereDate('attendance_time', today())->count();
        $uniqueParticipants = $event->attendances()->distinct('participant_id')->count('participant_id');
        $duplicateCount = $event->attendances()->where('is_duplicate', true)->count();
        $selectedEvent = $event->id;
        
        return view('attendances.index', compact(
            'event', 
            'attendances', 
            'addresses',
            'events',
            'totalAttendances',
            'todayAttendances',
            'uniqueParticipants',
            'duplicateCount',
            'selectedEvent',
            'showDuplicates'
        ));
    }

    /**
     * ==========================================================
     * ARCHIVE FEATURES
     * ==========================================================
     */

    /**
     * Display listing of archived events (READ-ONLY)
     */
    public function archived(Request $request)
    {
        $query = Event::archived()
            ->withCount('attendances')
            ->with('creator:id,name');
        
        if ($request->has('search') && $request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('event_name', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        if ($request->has('date_from') && $request->filled('date_from')) {
            $query->whereDate('event_date', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->filled('date_to')) {
            $query->whereDate('event_date', '<=', $request->date_to);
        }
        
        $sort = $request->get('sort', 'date_desc');
        switch ($sort) {
            case 'oldest': $query->oldest(); break;
            case 'name_asc': $query->orderBy('event_name', 'asc'); break;
            case 'name_desc': $query->orderBy('event_name', 'desc'); break;
            case 'date_asc': $query->orderBy('event_date', 'asc'); break;
            case 'date_desc': $query->orderBy('event_date', 'desc'); break;
            default: $query->orderBy('event_date', 'desc');
        }
        
        $archivedEvents = $query->paginate(20)->withQueryString();
        
        return view('events.archived', compact('archivedEvents'));
    }

    /**
     * Show archived event details (READ-ONLY)
     */
    public function showArchived($id)
    {
        $event = Event::archived()
            ->with([
                'attendances' => function($query) {
                    $query->with(['participant:id,name,address,phone'])
                          ->latest('attendance_time');
                },
                'creator:id,name,email,username,role'
            ])
            ->withCount('attendances')
            ->findOrFail($id);
        
        $statistics = $this->getEventStatistics($event);
        
        return view('events.show_archived', compact('event', 'statistics'));
    }

    /**
     * Archive an event
     */
    public function archive($id)
    {
        $event = Event::notArchived()->findOrFail($id);
        
        try {
            if (!$event->attendances()->exists()) {
                return back()->with('warning', 
                    'Event "' . $event->event_name . '" tidak memiliki data absensi.');
            }
            
            DB::beginTransaction();
            
            $event->update([
                'status' => 'archived',
                'is_active' => false
            ]);
            
            // Clear cache
            $this->clearEventCache($event);
            
            if (class_exists(ActivityLog::class)) {
                ActivityLog::create([
                    'user_id' => auth()->id(),
                    'activity' => 'Mengarsipkan acara: ' . $event->event_name,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
            }
            
            DB::commit();
            
            return redirect()->route('events.index')
                ->with('success', 'Event "' . $event->event_name . '" berhasil diarsipkan.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to archive event: ' . $e->getMessage());
            return back()->with('error', 'Gagal mengarsipkan event. Silakan coba lagi.');
        }
    }

    /**
     * Restore archived event
     */
    public function restore($id)
    {
        $event = Event::archived()->findOrFail($id);
        
        try {
            DB::beginTransaction();
            
            $event->update([
                'status' => 'active',
                'is_active' => false
            ]);
            
            // Clear cache
            $this->clearEventCache($event);
            
            if (class_exists(ActivityLog::class)) {
                ActivityLog::create([
                    'user_id' => auth()->id(),
                    'activity' => 'Merestore acara dari arsip: ' . $event->event_name,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
            }
            
            DB::commit();
            
            return redirect()->route('events.show', $event->id)
                ->with('success', 'Event "' . $event->event_name . '" berhasil direstore.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to restore event: ' . $e->getMessage());
            return back()->with('error', 'Gagal restore event. Silakan coba lagi.');
        }
    }

    /**
     * ==========================================================
     * EXPORT, DUPLICATE, BULK ACTIONS
     * ==========================================================
     */

    /**
     * Export event data (CSV).
     */
    public function export($id, Request $request = null)
    {
        $event = Event::with(['attendances.participant'])->findOrFail($id);
        
        $dateFrom = $request ? $request->get('date_from') : null;
        $dateTo = $request ? $request->get('date_to') : null;
        
        $query = $event->attendances()->with('participant');
        
        if ($dateFrom) {
            $query->whereDate('attendance_time', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('attendance_time', '<=', $dateTo);
        }
        
        $attendances = $query->get();
        
        $fileName = 'event_' . $event->id . '_attendances_' . date('Y-m-d') . '.csv';
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use($event, $attendances) {
            $file = fopen('php://output', 'w');
            fwrite($file, "\xEF\xBB\xBF");
            
            fputcsv($file, ['No', 'Nama', 'Alamat', 'No. Telepon', 'Waktu Absensi', 'Status']);
            
            $no = 1;
            foreach ($attendances as $attendance) {
                $participant = $attendance->participant;
                fputcsv($file, [
                    $no++,
                    $participant->name ?? '',
                    $participant->address ?? '',
                    $participant->phone ?? '',
                    $attendance->created_at->format('Y-m-d H:i:s'),
                    $attendance->status ?? 'Hadir'
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Duplicate event.
     */
    public function duplicate($id)
    {
        $originalEvent = Event::findOrFail($id);
        
        try {
            DB::beginTransaction();
            
            $newEvent = $originalEvent->replicate();
            $newEvent->event_name = $originalEvent->event_name . ' (Salinan)';
            $newEvent->qr_code_hash = Str::uuid()->toString();
            $newEvent->qr_code_generated_at = now();
            $newEvent->status = 'active';
            
            if ($this->hasRegistrationCodeColumn()) {
                $newEvent->registration_code = 'EV-' . strtoupper(Str::random(6));
            }
            
            $newEvent->created_by = Auth::id() ?? 1;
            $newEvent->is_active = false;
            $newEvent->save();
            
            $qrUrl = $this->generateQRCodeUrl($newEvent);
            $newEvent->update(['qr_code_url' => $qrUrl]);
            
            DB::commit();
            
            return redirect()->route('events.show', $newEvent->id)
                ->with('success', 'Event berhasil diduplikasi!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menduplikasi event. Silakan coba lagi.');
        }
    }

    /**
     * Bulk actions with validation.
     */
    public function bulkActions(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete,export,archive',
            'selected' => 'required|array|min:1',
            'selected.*' => 'exists:events,id'
        ]);
        
        $selected = $request->selected;
        $action = $request->action;
        
        switch ($action) {
            case 'activate':
                Event::whereIn('id', $selected)
                    ->notArchived()
                    ->update(['is_active' => true]);
                $message = count($selected) . ' event berhasil diaktifkan.';
                break;
                
            case 'deactivate':
                Event::whereIn('id', $selected)
                    ->notArchived()
                    ->update(['is_active' => false]);
                $message = count($selected) . ' event berhasil dinonaktifkan.';
                break;
                
            case 'archive':
                return $this->handleBulkArchive($selected);
                
            case 'delete':
                $archivedEvents = Event::whereIn('id', $selected)
                    ->archived()
                    ->count();
                if ($archivedEvents > 0) {
                    return back()->with('error', 'Tidak dapat menghapus event yang sudah diarsipkan.');
                }
                
                $eventsWithAttendances = Event::whereIn('id', $selected)
                    ->has('attendances')
                    ->count();
                if ($eventsWithAttendances > 0) {
                    return back()->with('error', 'Tidak dapat menghapus event yang sudah memiliki data absensi. Gunakan fitur Archive.');
                }
                
                Event::whereIn('id', $selected)
                    ->notArchived()
                    ->delete();
                $message = count($selected) . ' event berhasil dihapus.';
                break;
                
            case 'export':
                return $this->handleBulkExport($selected);
                
            default:
                return back()->with('error', 'Aksi tidak valid.');
        }
        
        return back()->with('success', $message);
    }

    /**
     * ==========================================================
     * CALENDAR & STATISTICS
     * ==========================================================
     */

    /**
     * Get calendar events (JSON endpoint).
     */
    public function getCalendarEvents(Request $request)
    {
        $query = Event::query()->notArchived();
        
        if ($request->has('start') && $request->filled('start')) {
            $query->whereDate('event_date', '>=', $request->start);
        }
        if ($request->has('end') && $request->filled('end')) {
            $query->whereDate('event_date', '<=', $request->end);
        }
        if ($request->has('category') && $request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->get('active_only', true)) {
            $query->where('is_active', true);
        }
        
        $events = $query->get(['id', 'event_name', 'event_date', 'location', 'description', 'is_active'])
            ->map(function($event) {
                return [
                    'id' => $event->id,
                    'title' => $event->event_name . (!$event->is_active ? ' (Nonaktif)' : ''),
                    'start' => $event->event_date,
                    'url' => route('events.show', $event->id),
                    'location' => $event->location,
                    'description' => $event->description,
                    'extendedProps' => [
                        'is_active' => $event->is_active,
                        'attendees_count' => $event->attendances()->count(),
                    ]
                ];
            });
        
        return response()->json($events);
    }

    /**
     * Calendar view.
     */
    public function calendar()
    {
        $categories = Event::select('category')
            ->whereNotNull('category')
            ->distinct()
            ->pluck('category')
            ->filter();
        
        return view('events.calendar', compact('categories'));
    }

    /**
     * Event statistics and analytics.
     */
    public function analytics($id)
    {
        $event = Event::findOrFail($id);
        $statistics = $this->getEventStatistics($event);
        $analytics = $this->getEventAnalytics($event);
        
        return view('events.analytics', compact('event', 'statistics', 'analytics'));
    }

    /**
     * Print event data.
     */
    public function print($id, Request $request = null)
    {
        $event = Event::with(['attendances.participant'])
                     ->withCount('attendances')
                     ->findOrFail($id);
        
        if ($request && $request->has('date') && $request->filled('date')) {
            $event->attendances = $event->attendances()
                ->whereDate('attendance_time', $request->date)
                ->get();
        }
        
        $printType = $request ? $request->get('type', 'list') : 'list';
        
        return view('events.print', compact('event', 'printType'));
    }

    /**
     * Search events.
     */
    public function search(Request $request)
    {
        $search = $request->get('search');
        
        $events = Event::where('event_name', 'like', "%$search%")
            ->orWhere('location', 'like', "%$search%")
            ->orWhere('description', 'like', "%$search%")
            ->withCount('attendances')
            ->notArchived()
            ->latest()
            ->paginate(20);
        
        return view('events.index', compact('events', 'search'));
    }

    /**
     * Dashboard statistics.
     */
    public function dashboardStats()
    {
        $stats = [
            'totalEvents' => Event::notArchived()->count(),
            'activeEvents' => Event::where('is_active', true)->notArchived()->count(),
            'archivedEvents' => Event::archived()->count(),
            'todayEvents' => Event::whereDate('event_date', Carbon::today())->notArchived()->count(),
            'upcomingEvents' => Event::where('event_date', '>', Carbon::now())->notArchived()->count(),
            'pastEvents' => Event::where('event_date', '<', Carbon::now())->notArchived()->count(),
            'eventsWithAttendances' => Event::has('attendances')->notArchived()->count(),
        ];
        
        return response()->json($stats);
    }
}