<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;

class EventController extends Controller
{
    // =========================
    // LIST EVENT
    // =========================
    public function index()
    {
        $events = Event::latest()->get();
        return view('events.index', compact('events'));
    }

    // =========================
    // FORM CREATE EVENT
    // =========================
    public function create()
    {
        return view('events.create');
    }

    // =========================
    // STORE EVENT
    // =========================
    public function store(Request $request)
    {
        $request->validate([
            'event_name' => 'required|string',
            'location'   => 'required|string',
            'event_date' => 'required|date',
        ]);

        Event::create($request->all());

        return redirect()->route('events.index')->with('success', 'Event berhasil dibuat!');
    }

    // =========================
    // SHOW EVENT DETAIL
    // =========================
    public function show($id)
    {
        $event = Event::findOrFail($id);
        return view('events.show', compact('event'));
    }

    // =========================
    // EDIT EVENT
    // =========================
    public function edit($id)
    {
        $event = Event::findOrFail($id);
        return view('events.edit', compact('event'));
    }

    // =========================
    // UPDATE EVENT
    // =========================
    public function update(Request $request, $id)
    {
        $request->validate([
            'event_name' => 'required|string',
            'location'   => 'required|string',
            'event_date' => 'required|date',
        ]);

        $event = Event::findOrFail($id);
        $event->update($request->all());

        return redirect()->route('events.index')->with('success', 'Event berhasil diperbarui!');
    }

    // =========================
    // DELETE EVENT
    // =========================
    public function destroy($id)
    {
        Event::destroy($id);
        return redirect()->route('events.index')->with('success', 'Event berhasil dihapus!');
    }



    // =========================================================
    //  HALAMAN QR CODE — INFORMASI & QR UTAMA
    // =========================================================
    public function qrPage($id)
    {
        $event = Event::findOrFail($id);

        $url = route('attendance.form.public', $event->id);

        // Provider list
        $providers = [
            [
                'name' => 'QuickChart.io (Main)',
                'url'  => 'https://quickchart.io/qr?text=' . urlencode($url) . '&size=400'
            ],
            [
                'name' => 'Google Chart API',
                'url'  => 'https://chart.googleapis.com/chart?chs=400x400&cht=qr&chl=' . urlencode($url)
            ],
            [
                'name' => 'QRServer',
                'url'  => 'https://api.qrserver.com/v1/create-qr-code/?size=400x400&data=' . urlencode($url)
            ]
        ];

        return view('events.qr', [
            'event'     => $event,
            'url'       => $url,
            'providers' => $providers,
            'qrCodeUrl' => $providers[0]['url']
        ]);
    }



    // =========================================================
    //  DOWNLOAD QR (QuickChart Version – TANPA LIBRARY)
    // =========================================================
    public function generateQRCode(Event $event)
{
    // URL form absensi
    $url = route('attendance.form.public', $event->id);

    // QuickChart QR (langsung embed)
    $qrUrl = "https://quickchart.io/qr?text=" . urlencode($url) . "&size=600";

    // Tampilkan halaman, BUKAN download
    return view('events.qr-code', [
        'event' => $event,
        'qrUrl' => $qrUrl,
        'attendanceUrl' => $url,
    ]);
}


    // =========================================================
    //  FORM ABSENSI PUBLIK (SCAN QR MASUK KE SINI)
    // =========================================================
    public function attendanceFormPublic($eventId)
    {
        $event = Event::findOrFail($eventId);
        return view('attendance.form-public', compact('event'));
    }
}
