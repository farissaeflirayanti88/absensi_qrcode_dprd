<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Participant;
use App\Models\Attendance;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

class AttendanceController extends Controller
{
    /**
     * ==========================================================
     * KONSTANTA & PROPERTIES
     * ==========================================================
     */
    
    private const ERROR_MESSAGES = [
        'duplicate' => "ANDA SUDAH MELAKUKAN ABSENSI SEBELUMNYA!\n\nNama: %s\nNomor Telepon: %s\nAcara: %s\nWaktu Absensi Sebelumnya: %s\n\nSetiap peserta hanya dapat absen satu kali per acara.",
        'inactive' => 'Event sudah tidak aktif. Tidak dapat melakukan absensi.',
        'expired' => 'Event sudah berakhir pada %s.',
        'qr_expired' => 'QR Code sudah kadaluarsa. Silakan hubungi admin untuk memperbarui QR Code.',
        'not_found' => 'Event tidak ditemukan.',
        'system_error' => 'Terjadi kesalahan sistem. Silakan coba lagi atau hubungi administrator.',
        'success' => "KEHADIRAN BERHASIL DISIMPAN!\n\nTerima kasih %s,\nKehadiran Anda telah tercatat dengan:\n\nID Kehadiran: %s\nTanggal: %s\nWaktu: %s\nStatus: ✅ TERSIMPAN"
    ];

    private const SUCCESS_MESSAGES = [
        'store' => 'Data kehadiran berhasil disimpan.',
        'update' => 'Data kehadiran berhasil diperbarui.',
        'delete' => 'Data kehadiran berhasil dihapus.',
        'bulk_delete' => '%d data kehadiran berhasil dihapus.',
        'import' => 'Import berhasil! %d data baru, %d duplikat, %d error.'
    ];

    private $columnCache = [];

    /**
     * ==========================================================
     * PUBLIC AREA (SCAN QR → FORM ABSENSI)
     * ==========================================================
     */

    /**
     * Tampilkan form absensi publik berdasarkan event ID
     */
    public function showForm($eventId)
    {
        try {
            Log::info('showForm called', ['event_id' => $eventId]);
            
            $event = Event::findOrFail($eventId);
            
            // Validasi akses event
            $validationResult = $this->validateEventAccess($event);
            if ($validationResult !== true) {
                return $validationResult;
            }
            
            // Log akses ke form
            $this->logActivity('Mengakses form absensi event: ' . $event->event_name, null);
            
            Log::info('Showing attendance form for event ID: ' . $event->id);
            return view('attendance.attendance-form', compact('event'));
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Event not found: ' . $eventId);
            return view('attendance.expired', [
                'error' => self::ERROR_MESSAGES['not_found']
            ]);
        } catch (\Exception $e) {
            Log::error('Error in showForm: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'event_id' => $eventId
            ]);
            
            return view('attendance.expired', [
                'error' => self::ERROR_MESSAGES['system_error']
            ]);
        }
    }

    /**
     * Tampilkan form absensi publik berdasarkan QR HASH
     */
    public function showFormByHash($qr_hash)
    {
        try {
            Log::info('showFormByHash called', ['qr_hash' => $qr_hash]);
            
            $event = Event::where('qr_code_hash', $qr_hash)->first();
            
            if (!$event) {
                Log::error('QR hash not found: ' . $qr_hash);
                return view('attendance.expired', [
                    'error' => 'QR Code tidak valid atau sudah kadaluarsa.'
                ]);
            }
            
            // Validasi akses event
            $validationResult = $this->validateEventAccess($event, true);
            if ($validationResult !== true) {
                return $validationResult;
            }
            
            // Log akses ke form
            $this->logActivity('Mengakses form absensi via QR hash: ' . $event->event_name, null);
            
            Log::info('Showing attendance form for event via QR hash', ['event_id' => $event->id]);
            return view('attendance.attendance-form', compact('event'));
            
        } catch (\Exception $e) {
            Log::error('Error in showFormByHash: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'qr_hash' => $qr_hash
            ]);
            
            return view('attendance.expired', [
                'error' => self::ERROR_MESSAGES['system_error']
            ]);
        }
    }

    /**
     * Simpan data kehadiran (Publik) - versi event ID
     */
    public function store(Request $request, $eventId)
    {
        DB::beginTransaction();
        
        try {
            // Normalize input field names
            $normalizedData = $this->normalizeInput($request);
            
            // Validasi input
            $validator = $this->validateAttendanceInput($normalizedData);
            
            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }
            
            // Ambil data event
            $event = Event::findOrFail($eventId);
            
            // Validasi akses event
            if (!$this->isEventAccessible($event)) {
                return redirect()->back()
                    ->with('error_message', self::ERROR_MESSAGES['inactive'])
                    ->withInput();
            }
            
            // Proses kehadiran
            $result = $this->processAttendance($normalizedData, $event, $request);
            
            if ($result instanceof \Illuminate\Http\RedirectResponse) {
                DB::commit();
                return $result;
            }
            
            DB::commit();
            
            return redirect()->route('attendance.success', [
                'event' => $event->id,
                'attendance' => $result->id
            ])->with('success_message', sprintf(
                self::ERROR_MESSAGES['success'],
                e($normalizedData['nama']),
                $result->id,
                now()->format('d/m/Y'),
                now()->format('H:i:s')
            ));
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error_message', self::ERROR_MESSAGES['not_found'])
                ->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error menyimpan kehadiran: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'event_id' => $eventId,
                'input_data' => $request->except(['_token'])
            ]);
            
            return redirect()->back()
                ->with('error_message', self::ERROR_MESSAGES['system_error'])
                ->withInput();
        }
    }

    /**
     * Submit attendance by QR HASH
     */
    public function submitByHash(Request $request, $qr_hash)
    {
        DB::beginTransaction();
        
        try {
            // Normalize input field names
            $normalizedData = $this->normalizeInput($request);
            
            // Debug log
            Log::info('Attendance submission attempt:', [
                'qr_hash' => $qr_hash,
                'normalized_data' => $normalizedData
            ]);
            
            // Validasi input
            $validator = $this->validateAttendanceInput($normalizedData);
            
            if ($validator->fails()) {
                Log::warning('Validation failed:', $validator->errors()->toArray());
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }
            
            // Cari event berdasarkan QR hash
            $event = Event::where('qr_code_hash', $qr_hash)->first();
            
            if (!$event) {
                Log::warning('Event not found for QR hash:', ['qr_hash' => $qr_hash]);
                return redirect()->back()
                    ->with('error_message', 'QR Code tidak valid.')
                    ->withInput();
            }
            
            // Validasi akses event
            if (!$this->isEventAccessible($event)) {
                return redirect()->back()
                    ->with('error_message', self::ERROR_MESSAGES['inactive'])
                    ->withInput();
            }
            
            // Proses kehadiran
            $result = $this->processAttendance($normalizedData, $event, $request);
            
            if ($result instanceof \Illuminate\Http\RedirectResponse) {
                DB::commit();
                return $result;
            }
            
            DB::commit();
            
            return redirect()->route('attendance.success', [
                'event' => $event->id,
                'attendance' => $result->id
            ])->with('success_message', sprintf(
                self::ERROR_MESSAGES['success'],
                e($normalizedData['nama']),
                $result->id,
                now()->format('d/m/Y'),
                now()->format('H:i:s')
            ));
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error menyimpan kehadiran via QR hash: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'qr_hash' => $qr_hash,
                'input_data' => $request->except(['_token'])
            ]);
            
            return redirect()->back()
                ->with('error_message', self::ERROR_MESSAGES['system_error'])
                ->withInput();
        }
    }

    /**
     * Halaman sukses absensi
     */
    public function success($eventId, $attendanceId)
    {
        try {
            $attendance = Attendance::with(['participant', 'event'])->findOrFail($attendanceId);
            $event = Event::findOrFail($eventId);
            
            // Validasi kepemilikan
            if ($attendance->event_id != $eventId) {
                return redirect('/')
                    ->with('error_message', 'Data kehadiran tidak valid.');
            }
            
            return view('attendance.success', compact('event', 'attendance'));
            
        } catch (\Exception $e) {
            Log::error('Error in success page: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'event_id' => $eventId,
                'attendance_id' => $attendanceId
            ]);
            
            return redirect('/')
                ->with('error_message', 'Data kehadiran tidak ditemukan.');
        }
    }

    /**
     * ==========================================================
     * ADMIN AREA (REKAP, EDIT, EXPORT) - REVISI UTAMA
     * ==========================================================
     */

    /**
     * List kehadiran dengan filter
     * ✅ FR-08: Fitur filter (acara, tanggal, pencarian)
     */
    public function index(Request $request)
    {
        $selectedEvent = $request->event_id;
        $search = $request->search;
        $dateFrom = $request->date_from;
        $dateTo = $request->date_to;
        
        $query = Attendance::with(['event', 'participant'])
            ->orderBy('created_at', 'desc');

        // Filter event
        if ($selectedEvent) {
            $query->where('event_id', $selectedEvent);
        }

        // Filter pencarian
        if ($search) {
            $query->whereHas('participant', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter tanggal
        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $attendances = $query->paginate(50)->withQueryString();
        $events = Event::orderBy('event_date', 'desc')->get();
        
        // Statistik
        $totalAttendances = $selectedEvent 
            ? Attendance::where('event_id', $selectedEvent)->count()
            : Attendance::count();
            
        $todayAttendances = Attendance::whereDate('created_at', today())
            ->when($selectedEvent, function($q) use ($selectedEvent) {
                $q->where('event_id', $selectedEvent);
            })
            ->count();
            
        $uniqueParticipants = Participant::has('attendances')
            ->when($selectedEvent, function($q) use ($selectedEvent) {
                $q->whereHas('attendances', function($q2) use ($selectedEvent) {
                    $q2->where('event_id', $selectedEvent);
                });
            })
            ->count();

        // Log activity
        $this->logActivity('Melihat daftar kehadiran', auth()->id(), $request->ip(), $request->userAgent());

        return view('attendances.index', compact(
            'attendances', 
            'events', 
            'selectedEvent',
            'search',
            'dateFrom',
            'dateTo',
            'totalAttendances',
            'todayAttendances',
            'uniqueParticipants'
        ));
    }

    /**
     * Export data ke PDF (bisa per acara atau semua)
     * ✅ FR-09: Export laporan PDF + konfigurasi dari modal (orientasi, QR, statistik)
     */
    public function exportPdf(Request $request)
    {
        $selectedEvent = $request->event_id;
        $dateFrom      = $request->date_from;
        $dateTo        = $request->date_to;

        // Opsi konfigurasi dari modal (FR-09)
        $orientation  = $request->get('orientation', 'portrait');
        $includeQr    = $request->boolean('include_qr', false);
        $includeStats = $request->boolean('include_stats', true);
        
        $query = Attendance::with(['event', 'participant'])
            ->orderBy('created_at', 'desc');

        if ($selectedEvent) {
            $event = Event::find($selectedEvent);
            $query->where('event_id', $selectedEvent);
            $title = $event
                ? 'Rekap Kehadiran - ' . $event->event_name
                : 'Rekap Kehadiran Per Acara';
        } else {
            $event = null;
            $title = 'Rekap Kehadiran Semua Acara';
        }

        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $attendances = $query->get();

        $dateRange = '';
        if ($dateFrom && $dateTo) {
            $dateRange = "Periode: " . Carbon::parse($dateFrom)->format('d/m/Y') .
                         " s/d " . Carbon::parse($dateTo)->format('d/m/Y');
        } elseif ($dateFrom) {
            $dateRange = "Dari: " . Carbon::parse($dateFrom)->format('d/m/Y');
        } elseif ($dateTo) {
            $dateRange = "Sampai: " . Carbon::parse($dateTo)->format('d/m/Y');
        }

        // FR-09 Exception Scenario: tidak ada data
        if ($attendances->isEmpty()) {
            return back()->with('warning', 'Tidak ada data kehadiran untuk diexport.');
        }

        $data = [
            'attendances'  => $attendances,
            'event'        => $event,
            'totalAttendances' => $attendances->count(),
            'exportDate'   => now()->format('d/m/Y H:i:s'),
            'title'        => $title,
            'dateRange'    => $dateRange,
            'request'      => $request,
            'includeQr'    => $includeQr,
            'includeStats' => $includeStats,
        ];

        $pdf = Pdf::loadView('attendances.pdf-print', $data);

        // Set orientasi sesuai pilihan modal
        if ($orientation === 'landscape') {
            $pdf->setPaper('a4', 'landscape');
        } else {
            $pdf->setPaper('a4', 'portrait');
        }

        // FR-09 step 9: Nama file dinamis
        $fileName  = 'Laporan_Kehadiran_';
        $fileName .= $event ? Str::slug($event->event_name) . '_' : 'semua-acara_';
        $fileName .= now()->format('Ymd') . '.pdf';

        // Log activity (FR-09 step 12)
        $this->logActivity('Export PDF: ' . $title, auth()->id(), $request->ip(), $request->userAgent());

        return $pdf->download($fileName);
    }

    /**
     * Export ke Excel/CSV
     */
    public function exportCsv(Request $request)
    {
        $selectedEvent = $request->event_id;
        $dateFrom = $request->date_from;
        $dateTo = $request->date_to;
        
        $query = Attendance::with(['event', 'participant'])
            ->orderBy('created_at', 'desc');

        if ($selectedEvent) {
            $event = Event::find($selectedEvent);
            $query->where('event_id', $selectedEvent);
        } else {
            $event = null;
        }

        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $attendances = $query->get();

        $filename = 'rekap-kehadiran-';
        $filename .= $event ? Str::slug($event->event_name) . '-' : 'semua-acara-';
        $filename .= date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($attendances, $event) {
            $file = fopen('php://output', 'w');
            
            // BOM untuk UTF-8
            fwrite($file, "\xEF\xBB\xBF");
            
            // Header
            fputcsv($file, ['No', 'Nama', 'No. Telepon', 'Alamat', 'Email', 'Instansi', 'Acara', 'Tanggal Acara', 'Waktu Absen']);
            
            // Data
            foreach($attendances as $index => $att) {
                fputcsv($file, [
                    $index + 1,
                    $att->participant->name ?? '-',
                    $att->participant->phone ?? '-',
                    $att->participant->address ?? '-',
                    $att->participant->email ?? '-',
                    $att->participant->institution ?? '-',
                    $att->event->event_name ?? '-',
                    $att->event->event_date ? $att->event->event_date->format('d/m/Y') : '-',
                    $att->created_at->format('d/m/Y H:i:s')
                ]);
            }
            fclose($file);
        };
        
        // Log activity
        $this->logActivity('Export CSV: ' . ($event ? $event->event_name : 'Semua Acara'), 
                          auth()->id(), $request->ip(), $request->userAgent());
        
        return response()->stream($callback, 200, $headers);
    }

    /**
     * Tampilkan form edit kehadiran
     */
    public function edit(Attendance $attendance)
    {
        $attendance->load(['participant', 'event']);
        $events = Event::orderBy('event_date', 'desc')->get();

        return view('attendances.edit', compact('attendance', 'events'));
    }

    /**
     * Update data kehadiran
     */
    public function update(Request $request, Attendance $attendance)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|min:3',
            'address' => 'required|string|max:255|min:10',
            'phone' => [
                'required',
                'string',
                'regex:/^[0-9]{10,15}$/'
            ],
            'email' => 'nullable|email|max:100',
            'institution' => 'nullable|string|max:100',
            'event_id' => 'required|exists:events,id',
            'attendance_time' => 'required|date',
            'notes' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();

        try {
            $participant = $attendance->participant;

            // Update data peserta
            $participant->update([
                'name' => $this->formatName($validated['name']),
                'address' => trim($validated['address']),
                'phone' => preg_replace('/\D/', '', $validated['phone']),
                'email' => $validated['email'] ?? $participant->email,
                'institution' => $validated['institution'] ?? $participant->institution,
            ]);

            // Update data kehadiran
            $attendance->update([
                'event_id' => $validated['event_id'],
                'attendance_time' => $validated['attendance_time'],
                'notes' => $validated['notes'] ?? $attendance->notes,
            ]);

            // Log activity
            $this->logActivity('Memperbarui kehadiran: ' . $validated['name'] . ' - ID: ' . $attendance->id);

            DB::commit();

            return redirect()->route('attendances.index')
                ->with('success', self::SUCCESS_MESSAGES['update']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating attendance: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'attendance_id' => $attendance->id,
                'input_data' => $request->except(['_token', '_method'])
            ]);

            return back()->withInput()->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }

    /**
     * Hapus data kehadiran
     */
    public function destroy(Attendance $attendance)
    {
        DB::beginTransaction();
        
        try {
            $participant   = $attendance->participant;
            $participantName = $participant->name ?? 'Unknown';
            $eventName     = $attendance->event->event_name ?? 'Unknown';

            $attendance->delete();

            // FR-11 step 6-7: Cek apakah peserta masih punya kehadiran di acara lain
            // Jika tidak ada, soft delete peserta (isi deleted_at)
            if ($participant && $participant->attendances()->count() === 0) {
                $participant->delete(); // SoftDeletes akan mengisi deleted_at
                $this->logActivity('Menghapus kehadiran & soft delete peserta: ' . $participantName . ' - ' . $eventName);
            } else {
                $this->logActivity('Menghapus kehadiran: ' . $participantName . ' - ' . $eventName);
            }

            DB::commit();

            return redirect()->route('attendances.index')
                ->with('success', self::SUCCESS_MESSAGES['delete']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting attendance: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'attendance_id' => $attendance->id
            ]);

            return back()->with('error', 'Gagal menghapus data kehadiran: ' . $e->getMessage());
        }
    }

    /**
     * Bulk delete kehadiran
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'selected' => 'required|array',
            'selected.*' => 'exists:attendances,id'
        ]);

        DB::beginTransaction();

        try {
            $selectedIds = $request->selected;
            $deletedCount = Attendance::whereIn('id', $selectedIds)->delete();

            // Log activity
            $this->logActivity('Menghapus ' . $deletedCount . ' data kehadiran secara massal');

            DB::commit();

            return redirect()->route('attendances.index')
                ->with('success', sprintf(self::SUCCESS_MESSAGES['bulk_delete'], $deletedCount));

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error bulk deleting: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'selected_ids' => $request->selected
            ]);
            
            return back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    /**
     * ==========================================================
     * IMPORT/EXPORT & UTILITY FUNCTIONS
     * ==========================================================
     */

    /**
     * Import data kehadiran dari CSV
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:2048',
            'event_id' => 'required|exists:events,id'
        ]);
        
        DB::beginTransaction();
        
        try {
            $event = Event::findOrFail($request->event_id);
            
            // Validasi event aktif
            if (!$this->isEventAccessible($event)) {
                return back()->with('error', self::ERROR_MESSAGES['inactive']);
            }
            
            $file = $request->file('file');
            $importedCount = 0;
            $duplicateCount = 0;
            $errorCount = 0;
            $errors = [];
            
            if (($handle = fopen($file->getPathname(), 'r')) !== false) {
                // Lewati header
                $header = fgetcsv($handle);
                
                while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                    try {
                        if (count($data) < 3) {
                            $errorCount++;
                            continue;
                        }
                        
                        $name = trim($data[0]);
                        $phone = preg_replace('/\D/', '', $data[1]);
                        $address = trim($data[2]);
                        $email = isset($data[3]) ? trim($data[3]) : null;
                        $institution = isset($data[4]) ? trim($data[4]) : null;
                        
                        // Validasi data
                        if (empty($name) || empty($phone) || empty($address)) {
                            $errorCount++;
                            continue;
                        }
                        
                        if (!preg_match('/^[0-9]{10,15}$/', $phone)) {
                            $errorCount++;
                            $errors[] = "Nomor telepon tidak valid: $phone";
                            continue;
                        }
                        
                        // Cek duplikasi
                        $existing = $this->checkDuplicateAttendance($event->id, $phone);
                        if ($existing) {
                            $duplicateCount++;
                            continue;
                        }
                        
                        // Cari atau buat peserta
                        $participantData = [
                            'name' => $this->formatName($name),
                            'address' => $address,
                            'phone' => $phone,
                        ];
                        
                        if ($email) {
                            $participantData['email'] = $email;
                        }
                        
                        if ($institution) {
                            $participantData['institution'] = $institution;
                        }
                        
                        $participant = Participant::firstOrCreate(
                            ['phone' => $phone],
                            $participantData
                        );
                        
                        // Buat kehadiran
                        $attendanceData = [
                            'event_id' => $event->id,
                            'participant_id' => $participant->id,
                            'attendance_time' => now(),
                            'notes' => 'Imported from CSV',
                            'ip_address' => $request->ip(),
                            'user_agent' => $request->userAgent(),
                        ];
                        
                        Attendance::create($attendanceData);
                        
                        $importedCount++;
                        
                    } catch (\Exception $e) {
                        $errorCount++;
                        Log::error('Error importing row: ' . $e->getMessage(), [
                            'row_data' => $data
                        ]);
                    }
                }
                fclose($handle);
            }
            
            // Log activity
            $this->logActivity(sprintf(
                'Import data kehadiran: %d baru, %d duplikat, %d error',
                $importedCount,
                $duplicateCount,
                $errorCount
            ));
            
            DB::commit();
            
            $message = sprintf(self::SUCCESS_MESSAGES['import'], $importedCount, $duplicateCount, $errorCount);
            
            if (!empty($errors)) {
                $message .= '<br><small>' . implode('<br>', array_slice($errors, 0, 5)) . '</small>';
            }
            
            return back()->with('success', $message);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error importing data: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'event_id' => $request->event_id,
                'file_name' => $request->file('file')->getClientOriginalName()
            ]);
            
            return back()->with('error', 'Gagal mengimport data: ' . $e->getMessage());
        }
    }

    /**
     * Download template import
     */
    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="template_import_kehadiran.csv"',
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            
            // Tambahkan BOM untuk UTF-8
            fwrite($file, "\xEF\xBB\xBF");
            
            fputcsv($file, ['Nama Lengkap*', 'Nomor Telepon* (10-15 digit)', 'Alamat Lengkap*', 'Email (opsional)', 'Instansi (opsional)']);
            fputcsv($file, ['John Doe', '081234567890', 'Jl. Contoh No. 123, Kota Batam', 'john@example.com', 'PT. Contoh']);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Quick add attendance for admin
     */
    public function quickAdd(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'name' => 'required|string|min:3|max:100',
            'phone' => 'required|string|regex:/^[0-9]{10,15}$/',
            'address' => 'required|string|min:10|max:500',
            'email' => 'nullable|email|max:100',
            'institution' => 'nullable|string|max:100',
        ]);

        DB::beginTransaction();

        try {
            $event = Event::findOrFail($request->event_id);
            
            // Validasi event aktif
            if (!$this->isEventAccessible($event)) {
                return back()->with('error', self::ERROR_MESSAGES['inactive']);
            }
            
            // Bersihkan nomor telepon
            $phone = preg_replace('/\D/', '', $request->phone);
            
            // Cek duplikasi
            $existing = $this->checkDuplicateAttendance($event->id, $phone);
            if ($existing) {
                return back()->with('error', sprintf(
                    'Peserta sudah melakukan absensi untuk event ini pada %s.',
                    $existing->created_at->format('d/m/Y H:i:s')
                ));
            }
            
            // Cari atau buat peserta
            $participantData = [
                'name' => $this->formatName($request->name),
                'address' => trim($request->address),
                'phone' => $phone,
                'email' => $request->email,
                'institution' => $request->institution,
            ];
            
            $participant = Participant::firstOrCreate(
                ['phone' => $phone],
                $participantData
            );
            
            // Buat kehadiran
            $attendance = Attendance::create([
                'event_id' => $event->id,
                'participant_id' => $participant->id,
                'attendance_time' => now(),
                'notes' => 'Ditambahkan via quick add',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
            
            // Log activity
            $this->logActivity('Quick add kehadiran: ' . $request->name . ' - ' . $event->event_name);
            
            DB::commit();
            
            return back()->with('success', 'Kehadiran berhasil ditambahkan! ID: ' . $attendance->id);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error quick add: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'event_id' => $request->event_id,
                'input_data' => $request->except(['_token'])
            ]);
            
            return back()->with('error', 'Gagal menambahkan kehadiran: ' . $e->getMessage());
        }
    }

    /**
     * Get attendance statistics for dashboard
     */
    public function getStatistics(Request $request)
    {
        try {
            $eventId = $request->event_id;
            $startDate = $request->start_date ?? now()->subDays(30)->format('Y-m-d');
            $endDate = $request->end_date ?? now()->format('Y-m-d');
            
            $query = Attendance::whereBetween('created_at', [$startDate, $endDate]);
            
            if ($eventId) {
                $query->where('event_id', $eventId);
            }
            
            $total = $query->count();
            
            $today = Attendance::whereDate('created_at', today())
                ->when($eventId, function($q) use ($eventId) {
                    $q->where('event_id', $eventId);
                })
                ->count();
            
            // Group by date for chart
            $dailyStats = Attendance::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->when($eventId, function($q) use ($eventId) {
                    $q->where('event_id', $eventId);
                })
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->mapWithKeys(function($item) {
                    return [$item->date => $item->count];
                });
            
            // Group by event
            $eventStats = Attendance::selectRaw('events.id, events.event_name, COUNT(attendances.id) as count')
                ->join('events', 'attendances.event_id', '=', 'events.id')
                ->when($eventId, function($q) use ($eventId) {
                    $q->where('events.id', $eventId);
                })
                ->whereBetween('attendances.created_at', [$startDate, $endDate])
                ->groupBy('events.id', 'events.event_name')
                ->orderBy('count', 'desc')
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'total' => $total,
                    'today' => $today,
                    'daily_stats' => $dailyStats,
                    'event_stats' => $eventStats,
                    'date_range' => [
                        'start' => $startDate,
                        'end' => $endDate
                    ]
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting statistics: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil statistik',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * ==========================================================
     * PRIVATE HELPER METHODS
     * ==========================================================
     */

    /**
     * Validasi akses event
     */
    private function validateEventAccess(Event $event, $checkQrExpired = false)
    {
        // Cek 1: Event aktif?
        if (!$event->is_active) {
            return view('attendance.expired', [
                'event' => $event,
                'error' => self::ERROR_MESSAGES['inactive']
            ]);
        }
        
        // Cek 2: Tanggal event sudah lewat?
        $eventDate = Carbon::parse($event->event_date);
        $today = Carbon::today();
        
        if ($eventDate->lt($today)) {
            return view('attendance.expired', [
                'event' => $event,
                'error' => sprintf(self::ERROR_MESSAGES['expired'], $eventDate->format('d/m/Y'))
            ]);
        }
        
        // Cek 3: QR Code expired? (NFR-04)
        if ($checkQrExpired && $event->qr_expired_at) {
            $expiredAt = Carbon::parse($event->qr_expired_at);
            if ($expiredAt->isPast()) {
                return view('attendance.expired', [
                    'event' => $event,
                    'error' => self::ERROR_MESSAGES['qr_expired']
                ]);
            }
        }
        
        return true;
    }

    /**
     * Cek apakah event dapat diakses
     */
    private function isEventAccessible(Event $event)
    {
        if (!$event->is_active) {
            return false;
        }
        
        $eventDate = Carbon::parse($event->event_date);
        $today = Carbon::today();
        
        return !$eventDate->lt($today);
    }

    /**
     * Validasi input form absensi
     */
    private function validateAttendanceInput($data)
    {
        return Validator::make($data, [
            'nama' => 'required|string|min:3|max:100',
            'alamat' => 'required|string|min:10|max:500',
            'telepon' => [
                'required',
                'string',
                'regex:/^[0-9]{10,15}$/'
            ],
            'email' => 'nullable|email|max:100',
            'instansi' => 'nullable|string|max:100',
            'konfirmasi' => 'required|accepted',
        ], [
            'nama.required' => 'Nama lengkap wajib diisi',
            'nama.min' => 'Nama minimal 3 karakter',
            'nama.max' => 'Nama maksimal 100 karakter',
            'alamat.required' => 'Alamat lengkap wajib diisi',
            'alamat.min' => 'Alamat terlalu pendek (minimal 10 karakter)',
            'alamat.max' => 'Alamat terlalu panjang (maksimal 500 karakter)',
            'telepon.required' => 'Nomor telepon wajib diisi',
            'telepon.regex' => 'Nomor telepon harus 10-15 digit angka',
            'email.email' => 'Format email tidak valid',
            'konfirmasi.required' => 'Harap konfirmasi kehadiran',
            'konfirmasi.accepted' => 'Harap konfirmasi kehadiran',
        ]);
    }

    /**
     * Normalisasi input field names
     */
    private function normalizeInput(Request $request)
    {
        return [
            'nama' => $request->nama ?? $request->name ?? $request->input('nama_lengkap'),
            'alamat' => $request->alamat ?? $request->address,
            'telepon' => $request->telepon ?? $request->phone ?? $request->no_hp,
            'email' => $request->email,
            'instansi' => $request->instansi ?? $request->institution,
            'konfirmasi' => $request->konfirmasi ?? $request->confirmation,
        ];
    }

    /**
     * Format nama dengan title case yang cerdas
     */
    private function formatName($name)
    {
        $name = trim($name);
        
        // Jika semua huruf kapital, gunakan title case
        if (strtoupper($name) === $name && strlen($name) > 3) {
            return Str::title($name);
        }
        
        // Jika sudah mixed, biarkan apa adanya
        return $name;
    }

    /**
     * Cek duplikasi absensi
     */
    private function checkDuplicateAttendance($eventId, $phone)
    {
        $participant = Participant::where('phone', $phone)->first();

        if (!$participant) {
            return null;
        }

        // Cek apakah sudah pernah absen di event ini
        return Attendance::where('event_id', $eventId)
            ->where('participant_id', $participant->id)
            ->first();
    }

    /**
     * Cari atau buat peserta baru
     */
    private function findOrCreateParticipant($phone, $data)
    {
        // Validasi format nomor telepon
        if (!preg_match('/^[0-9]{10,15}$/', $phone)) {
            throw new \Exception('Nomor telepon tidak valid. Harus 10-15 digit angka.');
        }
        
        $participant = Participant::where('phone', $phone)->first();
        
        if (!$participant) {
            // Buat peserta baru
            $participantData = [
                'name' => $this->formatName($data['nama']),
                'address' => trim($data['alamat']),
                'phone' => $phone,
            ];
            
            // Tambahkan email jika ada
            if (isset($data['email']) && !empty($data['email'])) {
                $participantData['email'] = trim($data['email']);
            }
            
            // Tambahkan instansi jika ada
            if (isset($data['instansi']) && !empty($data['instansi'])) {
                $participantData['institution'] = trim($data['instansi']);
            }
            
            $participant = Participant::create($participantData);
            
            Log::info('Peserta baru dibuat', [
                'participant_id' => $participant->id,
                'name' => $participant->name,
                'phone' => $participant->phone
            ]);
            
        } else {
            // Update data peserta yang sudah ada - hanya field yang berubah
            $updateData = [
                'name' => $this->formatName($data['nama']),
                'address' => trim($data['alamat']),
            ];
            
            // Update email jika ada dan berbeda
            if (isset($data['email']) && !empty($data['email']) && $data['email'] !== $participant->email) {
                $updateData['email'] = trim($data['email']);
            }
            
            // Update instansi jika ada dan berbeda
            if (isset($data['instansi']) && !empty($data['instansi']) && $data['instansi'] !== $participant->institution) {
                $updateData['institution'] = trim($data['instansi']);
            }
            
            $participant->update($updateData);
            
            Log::info('Peserta diupdate', [
                'participant_id' => $participant->id,
                'updated_fields' => array_keys($updateData)
            ]);
        }
        
        return $participant;
    }

    /**
     * Proses kehadiran
     */
    private function processAttendance($normalizedData, Event $event, Request $request)
    {
        // Bersihkan nomor telepon
        $telepon_bersih = preg_replace('/\D/', '', $normalizedData['telepon']);
        
        // Validasi nomor telepon setelah dibersihkan
        if (!preg_match('/^[0-9]{10,15}$/', $telepon_bersih)) {
            return redirect()->back()
                ->with('error_message', 'Nomor telepon harus 10-15 digit angka.')
                ->withInput();
        }

        // CEK DUPLIKASI ABSENSI (FR-10)
        $existingAttendance = $this->checkDuplicateAttendance($event->id, $telepon_bersih);
        
        if ($existingAttendance) {
            // FR-10 Exception step 11: Catat activity log "Duplikasi terdeteksi"
            $this->logActivity(
                'Duplikasi terdeteksi: ' . $normalizedData['nama'] . ' (' . $telepon_bersih . ')' .
                ' mencoba absen ke acara: ' . $event->event_name .
                ' (sudah absen pada: ' . $existingAttendance->created_at->format('d/m/Y H:i:s') . ')',
                null,
                $request->ip(),
                $request->userAgent()
            );

            return redirect()->back()
                ->with('error_message', sprintf(
                    self::ERROR_MESSAGES['duplicate'],
                    e($normalizedData['nama']),
                    $telepon_bersih,
                    $event->event_name,
                    $existingAttendance->created_at->format('d/m/Y H:i:s')
                ))
                ->withInput();
        }

        // Cari atau buat peserta
        $participant = $this->findOrCreateParticipant($telepon_bersih, $normalizedData);
        
        // Simpan kehadiran
        $attendanceData = [
            'event_id' => $event->id,
            'participant_id' => $participant->id,
            'attendance_time' => now(),
            'notes' => 'Kehadiran via form publik',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ];
        
        $attendance = Attendance::create($attendanceData);

        // Log aktivitas
        $this->logActivity(
            'Kehadiran baru: ' . $normalizedData['nama'] . ' - ' . $event->event_name,
            null,
            $request->ip(),
            $request->userAgent()
        );

        return $attendance;
    }

    /**
     * Log aktivitas dengan caching class_exists
     */
    private function logActivity($activity, $userId = null, $ip = null, $agent = null)
    {
        static $activityLogExists = null;
        
        if ($activityLogExists === null) {
            $activityLogExists = class_exists(ActivityLog::class);
        }
        
        if (!$activityLogExists) {
            return;
        }
        
        try {
            ActivityLog::create([
                'user_id' => $userId ?? auth()->id(),
                'activity' => $activity,
                'ip_address' => $ip ?? request()->ip(),
                'user_agent' => $agent ?? request()->userAgent(),
                'created_at' => now()
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to log activity: ' . $e->getMessage(), [
                'activity' => $activity,
                'user_id' => $userId
            ]);
        }
    }

    /**
     * Cek apakah kolom ada di tabel (dengan cache)
     */
    private function checkIfColumnExists($tableName, $columnName)
    {
        $cacheKey = $tableName . '.' . $columnName;
        
        if (!isset($this->columnCache[$cacheKey])) {
            try {
                // Coba dengan nama tabel asli
                if (Schema::hasTable($tableName)) {
                    $this->columnCache[$cacheKey] = Schema::hasColumn($tableName, $columnName);
                } 
                // Coba dengan nama tabel alternatif
                else {
                    $alternative = $tableName === 'attendances' ? 'attendance' : 'attendances';
                    if (Schema::hasTable($alternative)) {
                        $this->columnCache[$cacheKey] = Schema::hasColumn($alternative, $columnName);
                    } else {
                        $this->columnCache[$cacheKey] = false;
                    }
                }
            } catch (\Exception $e) {
                Log::warning('Error checking column: ' . $e->getMessage(), [
                    'table' => $tableName,
                    'column' => $columnName
                ]);
                $this->columnCache[$cacheKey] = false;
            }
        }
        
        return $this->columnCache[$cacheKey];
    }
}