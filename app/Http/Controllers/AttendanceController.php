<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Participant;
use App\Models\Attendance;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AttendanceController extends Controller
{
    /* ==========================================================
       PUBLIC AREA (SCAN QR → FORM ABSENSI)
    ========================================================== */

    // 🔥 TAMPILKAN FORM ABSENSI (PUBLIC)
    public function showForm(Event $event)
    {
        return view('attendance-form', compact('event'));
    }

    // 🔥 SIMPAN DATA KEHADIRAN (PUBLIC)
    public function store(Request $request, Event $event)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|min:3',
            'address' => 'required|string|max:255|min:10',
            'phone' => 'required|string|max:20|regex:/^[0-9+\-\s()]{10,15}$/',
            'notes' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            // Cek apakah peserta sudah ada
            $participant = Participant::firstOrCreate(
                ['phone' => preg_replace('/\D/', '', $validated['phone'])],
                [
                    'name' => trim(Str::title($validated['name'])),
                    'address' => trim($validated['address']),
                    'phone' => preg_replace('/\D/', '', $validated['phone']),
                ]
            );

            // Simpan kehadiran
            $attendance = Attendance::create([
                'event_id' => $event->id,
                'participant_id' => $participant->id,
                'attendance_time' => now(),
                'notes' => $validated['notes'],
            ]);

            DB::commit();

            return redirect()->route('attendance.success', [$event->id, $attendance->id]);

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Gagal menyimpan kehadiran.');
        }
    }

    // 🔥 HALAMAN SUKSES (PUBLIC)
    public function success(Event $event, Attendance $attendance)
    {
        return view('attendance-success', compact('event', 'attendance'));
    }

    /* ==========================================================
       ADMIN AREA (REKAP, EDIT, EXPORT, LIST)
    ========================================================== */

    // 🔥 LIST KEHADIRAN
    public function index(Request $request)
    {
        $selectedEvent = $request->event_id;
        
        $query = Attendance::with(['event', 'participant'])->latest();

        if ($selectedEvent) {
            $query->where('event_id', $selectedEvent);
        }

        $attendances = $query->get();
        $events = Event::all();
        
        // Statistik
        $totalAttendances = $attendances->count();
        $todayAttendances = Attendance::whereDate('created_at', today())->count();
        $uniqueParticipants = $attendances->unique('participant_id')->count();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'activity' => 'Melihat daftar kehadiran',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return view('attendances.index', compact(
            'attendances', 
            'events', 
            'selectedEvent',
            'totalAttendances',
            'todayAttendances',
            'uniqueParticipants'
        ));
    }

    // 🔥 EXPORT CSV
    public function export(Request $request)
    {
        $selectedEvent = $request->event_id;
        
        $query = Attendance::with(['event', 'participant']);

        if ($selectedEvent) {
            $query->where('event_id', $selectedEvent);
        }

        $attendances = $query->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="rekap_kehadiran_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function() use ($attendances) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'No',
                'Nama Peserta',
                'Alamat',
                'No. Telepon',
                'Acara',
                'Lokasi',
                'Tanggal Acara',
                'Waktu Hadir',
                'Catatan'
            ]);

            $no = 1;
            foreach ($attendances as $attendance) {
                fputcsv($file, [
                    $no++,
                    $attendance->participant->name,
                    $attendance->participant->address,
                    $attendance->participant->phone,
                    $attendance->event->event_name,
                    $attendance->event->location,
                    $attendance->event->event_date->format('d/m/Y'),
                    $attendance->attendance_time->format('d/m/Y H:i'),
                    $attendance->notes
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // 🔥 EXPORT PDF / PRINT VIEW
    public function exportPdf(Request $request)
    {
        $selectedEvent = $request->event_id;
        
        $query = Attendance::with(['event', 'participant']);

        if ($selectedEvent) {
            $event = Event::find($selectedEvent);
            $query->where('event_id', $selectedEvent);
        } else {
            $event = null;
        }

        $attendances = $query->get();

        $data = [
            'attendances' => $attendances,
            'event' => $event,
            'totalAttendances' => $attendances->count(),
            'exportDate' => now()->format('d/m/Y H:i'),
            'title' => $event
                ? 'Rekap Kehadiran - ' . $event->event_name
                : 'Rekap Kehadiran Semua Acara'
        ];

        return view('attendances.pdf-print', $data);
    }

    // 🔥 HAPUS KEHADIRAN
    public function destroy(Attendance $attendance)
    {
        DB::beginTransaction();
        
        try {
            $participantName = $attendance->participant->name;
            $eventName = $attendance->event->event_name;

            $attendance->delete();

            ActivityLog::create([
                'user_id' => auth()->id(),
                'activity' => 'Menghapus kehadiran: ' . $participantName . ' - ' . $eventName,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            DB::commit();

            return redirect()->route('attendances.index')
                ->with('success', 'Data kehadiran berhasil dihapus.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Gagal menghapus data kehadiran.');
        }
    }

    // 🔥 EDIT DATA KEHADIRAN
    public function edit(Attendance $attendance)
    {
        $attendance->load(['participant', 'event']);
        $events = Event::all();

        return view('attendances.edit', compact('attendance', 'events'));
    }

    // 🔥 UPDATE DATA KEHADIRAN
    public function update(Request $request, Attendance $attendance)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|min:3',
            'address' => 'required|string|max:255|min:10',
            'phone' => 'required|string|max:20|regex:/^[0-9+\-\s()]{10,15}$/',
            'event_id' => 'required|exists:events,id',
            'attendance_time' => 'required|date',
        ]);

        DB::beginTransaction();

        try {
            $participant = $attendance->participant;

            $participant->update([
                'name' => trim(Str::title($validated['name'])),
                'address' => trim($validated['address']),
                'phone' => preg_replace('/\D/', '', $validated['phone']),
            ]);

            $attendance->update([
                'event_id' => $validated['event_id'],
                'attendance_time' => $validated['attendance_time'],
            ]);

            DB::commit();

            return redirect()->route('attendances.index')
                ->with('success', 'Data kehadiran berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withInput()->with('error', 'Gagal memperbarui data.');
        }
    }
}
