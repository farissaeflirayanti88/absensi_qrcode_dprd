<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { font-size: 18px; margin-bottom: 5px; }
        .header p { margin: 3px 0; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background: #4a5568; color: white; padding: 10px; text-align: left; }
        td { border: 1px solid #ddd; padding: 8px; }
        tr:nth-child(even) { background: #f9f9f9; }
        .footer { margin-top: 30px; text-align: right; font-size: 10px; color: #666; }
        .badge { background: #48bb78; color: white; padding: 2px 8px; border-radius: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        @if($dateRange)
            <p>{{ $dateRange }}</p>
        @endif
        <p>Tanggal Export: {{ $exportDate }}</p>
        <p>Total Data: {{ $totalAttendances }} kehadiran</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Peserta</th>
                <th>No. Telepon</th>
                <th>Alamat</th>
                <th>Acara</th>
                <th>Tanggal Acara</th>
                <th>Waktu Absen</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($attendances as $index => $attendance)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $attendance->participant->name ?? '-' }}</td>
                <td>{{ $attendance->participant->phone ?? '-' }}</td>
                <td>{{ Str::limit($attendance->participant->address ?? '-', 30) }}</td>
                <td>{{ $attendance->event->event_name ?? '-' }}</td>
                <td>{{ $attendance->event->event_date ? $attendance->event->event_date->format('d/m/Y') : '-' }}</td>
                <td>{{ $attendance->created_at->format('d/m/Y H:i:s') }}</td>
                <td><span class="badge">Hadir</span></td>
            </tr>
            @endforeach
        </tbody>
    </table>

    @if($totalAttendances == 0)
        <p style="text-align: center; margin-top: 50px; color: #999;">Tidak ada data kehadiran</p>
    @endif

    <div class="footer">
        Dicetak oleh: {{ auth()->user()->name ?? 'System' }} | {{ now()->format('d/m/Y H:i:s') }}
    </div>
</body>
</html>