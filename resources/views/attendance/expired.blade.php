<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Tidak Aktif - Sistem Absensi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white rounded-xl shadow-lg p-8">
        <div class="text-center mb-6">
            <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-calendar-times text-3xl text-red-600"></i>
            </div>
            <h1 class="text-2xl font-bold text-gray-800">Event Tidak Aktif</h1>
            <p class="text-gray-600 mt-2">Maaf, tidak dapat mengisi absensi untuk event ini.</p>
            
            {{-- Tampilkan pesan error jika ada --}}
            @if(isset($error) && $error)
                <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded">
                    <p class="text-yellow-700">{{ $error }}</p>
                </div>
            @endif
        </div>

        {{-- PERBAIKAN UTAMA: CEK APAKAH $event ADA SEBELUM MENGAKSES --}}
        @if(isset($event) && $event)
        <div class="bg-gray-50 rounded-lg p-6 mb-6">
            <h2 class="font-semibold text-gray-700 mb-4 flex items-center">
                <i class="fas fa-info-circle mr-2 text-blue-500"></i>
                Detail Event
            </h2>
            
            <div class="space-y-3">
                <div class="flex">
                    <span class="w-32 text-gray-600">Nama Event</span>
                    <span class="font-medium text-gray-800">{{ $event->event_name ?? 'N/A' }}</span>
                </div>
                <div class="flex">
                    <span class="w-32 text-gray-600">Lokasi</span>
                    <span class="font-medium text-gray-800">{{ $event->location ?? 'N/A' }}</span>
                </div>
                <div class="flex">
                    <span class="w-32 text-gray-600">Tanggal</span>
                    <span class="font-medium text-gray-800">
                        @if($event->event_date)
                            {{ \Carbon\Carbon::parse($event->event_date)->format('d F Y') }}
                        @else
                            N/A
                        @endif
                    </span>
                </div>
                <div class="flex">
                    <span class="w-32 text-gray-600">Status</span>
                    <span class="font-medium {{ $event->is_active ? 'text-green-600' : 'text-red-600' }}">
                        {{ $event->is_active ? 'AKTIF' : 'TIDAK AKTIF' }}
                    </span>
                </div>
            </div>
        </div>
        @else
        {{-- Tampilkan pesan jika event tidak ditemukan --}}
        <div class="bg-gray-50 rounded-lg p-6 mb-6">
            <p class="text-gray-600 text-center">
                <i class="fas fa-exclamation-triangle text-yellow-500 mr-2"></i>
                Event tidak ditemukan atau tidak tersedia.
            </p>
        </div>
        @endif

        <div class="space-y-3">
            {{-- Hanya tampilkan tombol "Coba Lagi" jika event ada dan aktif --}}
            @if(isset($event) && $event && $event->is_active)
                <a href="{{ route('attendance.form.public', $event) }}" 
                   class="block w-full bg-blue-600 text-white text-center py-3 rounded-lg hover:bg-blue-700 transition duration-200">
                    <i class="fas fa-refresh mr-2"></i>Coba Lagi
                </a>
            @endif
            
            <a href="{{ url('/') }}" 
               class="block w-full bg-gray-600 text-white text-center py-3 rounded-lg hover:bg-gray-700 transition duration-200">
                <i class="fas fa-home mr-2"></i>Kembali ke Beranda
            </a>
        </div>

        <div class="mt-6 pt-6 border-t border-gray-200 text-center text-sm text-gray-500">
            <p>Jika Anda merasa ini adalah kesalahan, silakan hubungi administrator event.</p>
            <p class="mt-2">
                <i class="fas fa-phone mr-1"></i> (0778) 123456 | 
                <i class="fas fa-envelope ml-2 mr-1"></i> admin@dprd-batam.go.id
            </p>
        </div>
    </div>
</body>
</html>