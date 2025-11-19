<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Absensi Berhasil - {{ $event->event_name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-green-50 to-emerald-100 min-h-screen">
    <div class="min-h-screen flex items-center justify-center py-8 px-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden text-center">
            <!-- Success Icon -->
            <div class="bg-gradient-to-r from-green-500 to-emerald-600 text-white p-8">
                <div class="w-20 h-20 bg-white bg-opacity-20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold mb-2">Absensi Berhasil!</h1>
                <p class="text-green-100">Terima kasih atas kehadiran Anda</p>
            </div>

            <!-- Content -->
            <div class="p-6">
                <div class="mb-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-2">{{ $event->event_name }}</h2>
                    <p class="text-gray-600 text-sm mb-1">{{ $event->location }}</p>
                    <p class="text-gray-500 text-xs">{{ $event->event_date->format('d F Y') }}</p>
                </div>

                <!-- Attendance Details -->
                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div class="text-left">
                            <p class="text-gray-500">Nama</p>
                            <p class="font-semibold text-gray-800">{{ $attendance->participant->name }}</p>
                        </div>
                        <div class="text-left">
                            <p class="text-gray-500">Waktu</p>
                            <p class="font-semibold text-gray-800">{{ $attendance->attendance_time->format('H:i') }}</p>
                        </div>
                        <div class="text-left col-span-2">
                            <p class="text-gray-500">Tanggal</p>
                            <p class="font-semibold text-gray-800">{{ $attendance->attendance_time->format('d/m/Y') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Success Message -->
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                    <p class="text-green-700 text-sm">
                        <span class="font-semibold">Selamat!</span> Data kehadiran Anda telah tercatat dengan baik. 
                        Terima kasih telah berpartisipasi dalam acara ini.
                    </p>
                </div>

                <!-- Actions -->
                <div class="space-y-3">
                    <button onclick="window.close()" 
                            class="w-full bg-gray-600 text-white py-3 px-4 rounded-lg font-semibold hover:bg-gray-700 transition duration-200">
                        Tutup Halaman
                    </button>
                    <a href="{{ route('attendance.form.public', $event) }}" 
                       class="block w-full bg-green-600 text-white py-3 px-4 rounded-lg font-semibold hover:bg-green-700 transition duration-200">
                        Absensi Lagi
                    </a>
                </div>
            </div>

            <!-- Footer -->
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                <p class="text-xs text-center text-gray-500">
                    Sistem Absensi QR Code DPRD Kota Batam<br>
                    ID Absensi: {{ $attendance->id }}
                </p>
            </div>
        </div>
    </div>

    <script>
        // Auto close after 10 seconds
        setTimeout(() => {
            if (confirm('Halaman akan ditutup otomatis. Tutup sekarang?')) {
                window.close();
            }
        }, 10000);
    </script>
</body>
</html>