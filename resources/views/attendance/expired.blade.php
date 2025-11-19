<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acara Tidak Tersedia - Sistem Absensi QR Code</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-orange-50 to-red-100 min-h-screen">
    <div class="min-h-screen flex items-center justify-center py-8 px-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden text-center">
            <!-- Header -->
            <div class="bg-gradient-to-r from-orange-500 to-red-600 text-white p-8">
                <div class="w-20 h-20 bg-white bg-opacity-20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold mb-2">Acara Tidak Tersedia</h1>
                <p class="text-orange-100">Masa absensi untuk acara ini telah berakhir</p>
            </div>

            <!-- Content -->
            <div class="p-6">
                <div class="mb-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-2">{{ $event->event_name }}</h2>
                    <p class="text-gray-600 text-sm mb-1">{{ $event->location }}</p>
                    <p class="text-gray-500 text-xs">Tanggal: {{ $event->event_date->format('d F Y') }}</p>
                </div>

                <div class="bg-orange-50 border border-orange-200 rounded-lg p-4 mb-6">
                    <p class="text-orange-700 text-sm">
                        Maaf, masa absensi untuk acara ini sudah berakhir. 
                        Acara ini diselenggarakan pada <strong>{{ $event->event_date->format('d F Y') }}</strong> 
                        dan periode absensi sudah ditutup.
                    </p>
                </div>

                <button onclick="window.close()" 
                        class="w-full bg-gray-600 text-white py-3 px-4 rounded-lg font-semibold hover:bg-gray-700 transition duration-200">
                    Tutup Halaman
                </button>
            </div>

            <!-- Footer -->
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                <p class="text-xs text-center text-gray-500">
                    Sistem Absensi QR Code DPRD Kota Batam
                </p>
            </div>
        </div>
    </div>
</body>
</html>