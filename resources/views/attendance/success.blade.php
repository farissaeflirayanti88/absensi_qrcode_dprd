<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kehadiran Berhasil</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; }
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; }
            .print-content { border: none !important; box-shadow: none !important; }
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in {
            animation: fadeIn 0.6s ease-out;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-green-50 to-emerald-100 min-h-screen">
    @php
        // Helper untuk konversi waktu
        use Carbon\Carbon;
        
        // Jika timezone sudah Asia/Jakarta di .env, semua akan otomatis WIB
        // Tapi kita buat backup untuk data lama
        $attendanceTime = Carbon::parse($attendance->attendance_time);
        
        // Cek apakah perlu konversi (untuk data lama)
        $isUtc = $attendanceTime->timezoneName === 'UTC';
        if ($isUtc) {
            $attendanceTime->setTimezone('Asia/Jakarta');
        }
        
        $waktuAbsensi = $attendanceTime->format('H:i:s');
        $tanggalAbsensi = $attendanceTime->format('d/m/Y');
        
        // Waktu server sekarang
        $waktuSekarang = now()->format('H:i:s');
        $tanggalSekarang = now()->format('d/m/Y');
    @endphp
    
    <div class="min-h-screen flex items-center justify-center py-8 px-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden print-content animate-fade-in">
            <!-- Success Header -->
            <div class="bg-gradient-to-r from-green-500 to-emerald-600 text-white p-8 text-center">
                <div class="w-20 h-20 bg-white bg-opacity-20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-check-circle text-3xl"></i>
                </div>
                <h1 class="text-2xl font-bold mb-2">Kehadiran Tercatat!</h1>
                <p class="text-green-100">Terima kasih atas kehadiran Anda</p>
            </div>

            <!-- Content -->
            <div class="p-8">
                <!-- Success Message Alert -->
                @if(session('success_message'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-check-circle text-green-600 text-xl mr-3"></i>
                        <h2 class="text-lg font-semibold text-green-800">Berhasil!</h2>
                    </div>
                    <div class="text-sm text-green-700">
                        {!! nl2br(e(session('success_message'))) !!}
                    </div>
                </div>
                @endif

                <!-- Info Message Alert (for duplicate cases) -->
                @if(session('info_message'))
                <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-exclamation-triangle text-yellow-600 text-xl mr-3"></i>
                        <h2 class="text-lg font-semibold text-yellow-800">Informasi</h2>
                    </div>
                    <div class="text-sm text-yellow-700">
                        {!! nl2br(e(session('info_message'))) !!}
                    </div>
                </div>
                @endif

                <div class="text-center mb-6">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 rounded-full mb-4">
                        <i class="fas fa-user-check text-green-600 text-2xl"></i>
                    </div>
                    <h2 class="text-xl font-semibold text-gray-800 mb-2">
                        {{ $attendance->participant->name ?? 'N/A' }}
                    </h2>
                    <p class="text-gray-600 mb-4">
                        Kehadiran Anda telah berhasil dicatat pada sistem.
                    </p>
                    
                    <!-- Notification Badge -->
                    <div class="inline-flex items-center px-4 py-2 bg-green-100 border border-green-200 rounded-full">
                        <i class="fas fa-info-circle text-green-600 mr-2"></i>
                        <span class="text-sm font-medium text-green-800">Status: Tersimpan</span>
                    </div>
                </div>

                <!-- Attendance Details Card -->
                <div class="bg-gray-50 rounded-lg p-6 mb-6">
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-4 flex items-center">
                        <i class="fas fa-info-circle mr-2 text-blue-500"></i>
                        Detail Kehadiran
                    </h3>
                    
                    <div class="space-y-4">
                        <!-- Participant Information -->
                        <div class="border-b border-gray-200 pb-3">
                            <h4 class="text-sm font-medium text-gray-700 mb-2">Informasi Peserta</h4>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <span class="text-xs text-gray-500">Nama Lengkap</span>
                                    <p class="font-semibold text-gray-800">{{ $attendance->participant->name ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <span class="text-xs text-gray-500">Nomor Telepon</span>
                                    <p class="font-semibold text-gray-800">{{ $attendance->participant->phone ?? 'N/A' }}</p>
                                </div>
                                <div class="col-span-2">
                                    <span class="text-xs text-gray-500">Alamat</span>
                                    <p class="font-semibold text-gray-800">{{ $attendance->participant->address ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Event Information -->
                        <div class="border-b border-gray-200 pb-3">
                            <h4 class="text-sm font-medium text-gray-700 mb-2">Informasi Acara</h4>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <span class="text-xs text-gray-500">Nama Acara</span>
                                    <p class="font-semibold text-gray-800">{{ $attendance->event->event_name ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <span class="text-xs text-gray-500">Lokasi</span>
                                    <p class="font-semibold text-gray-800">{{ $attendance->event->location ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <span class="text-xs text-gray-500">Tanggal Acara</span>
                                    <p class="font-semibold text-gray-800">
                                        {{ $attendance->event->event_date->format('d/m/Y') ?? 'N/A' }}
                                    </p>
                                </div>
                                <div>
                                    <span class="text-xs text-gray-500">Status</span>
                                    <p class="font-semibold">
                                        @if($attendance->event->is_active ?? false)
                                            <span class="text-green-600">Aktif</span>
                                        @else
                                            <span class="text-red-600">Tidak Aktif</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Attendance Information -->
                        <div>
                            <h4 class="text-sm font-medium text-gray-700 mb-2">Informasi Absensi</h4>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <span class="text-xs text-gray-500">ID Kehadiran</span>
                                    <p class="font-semibold text-gray-800">{{ $attendance->id }}</p>
                                </div>
                                <div>
                                    <span class="text-xs text-gray-500">Tanggal Absensi</span>
                                    <p class="font-semibold text-gray-800">
                                        {{ $tanggalAbsensi }}
                                    </p>
                                </div>
                                <div>
                                    <span class="text-xs text-gray-500">Waktu Absensi</span>
                                    <p class="font-semibold text-gray-800">
                                        {{ $waktuAbsensi }} WIB
                                    </p>
                                </div>
                                <div>
                                    <span class="text-xs text-gray-500">Status</span>
                                    <p class="font-semibold text-green-600">
                                        <i class="fas fa-check-circle mr-1"></i> Berhasil
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- HAPUS VERIFICATION CODE SECTION - DIHAPUS -->
                <!-- 
                <div class="text-center mb-6">
                    <div class="inline-block p-4 bg-white border border-gray-200 rounded-lg">
                        <div class="text-xs text-gray-500 mb-2">Kode Verifikasi Kehadiran</div>
                        <div class="font-mono text-lg font-bold text-blue-600 tracking-wider">
                            {{ strtoupper(substr(md5($attendance->id . $attendance->participant_id), 0, 8)) }}
                        </div>
                        <div class="text-xs text-gray-400 mt-1">ID: {{ $attendance->id }}</div>
                        <div class="text-xs text-gray-500 mt-2">
                            <i class="fas fa-clock mr-1"></i>
                            Waktu Server: {{ $waktuSekarang }} WIB
                        </div>
                    </div>
                </div>
                -->

                <!-- Buttons - HANYA TAMPILAN, NO AUTO-PRINT -->
                <div class="space-y-4 no-print">
                    <!-- Notification bahwa print hanya manual -->
                    <div class="p-3 bg-blue-50 border border-blue-100 rounded-lg text-center">
                        <i class="fas fa-print text-blue-500 mr-2"></i>
                        <span class="text-sm text-blue-700">Cetak bukti hanya jika diperlukan</span>
                    </div>
                    
                    <button onclick="window.print()" 
                            class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg font-semibold hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-200 flex items-center justify-center">
                        <i class="fas fa-print mr-2"></i>
                        Cetak Bukti Kehadiran
                    </button>
                    
                    <div class="flex space-x-3">
                        <a href="{{ url('/') }}" 
                           class="flex-1 bg-gray-600 text-white py-3 px-4 rounded-lg font-semibold hover:bg-gray-700 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition duration-200 flex items-center justify-center">
                            <i class="fas fa-home mr-2"></i>
                            Halaman Utama
                        </a>
                        
                        <a href="{{ route('attendance.form.public', $event->id ?? '') }}" 
                           class="flex-1 bg-green-600 text-white py-3 px-4 rounded-lg font-semibold hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition duration-200 flex items-center justify-center">
                            <i class="fas fa-plus mr-2"></i>
                            Absen Lagi
                        </a>
                    </div>
                    
                    <div class="text-center">
                        <a href="{{ route('events.index') }}" 
                           class="inline-block text-blue-600 hover:text-blue-800 font-medium text-sm">
                            <i class="fas fa-calendar-alt mr-1"></i>
                            Lihat Daftar Acara Lainnya
                        </a>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                <p class="text-xs text-center text-gray-500">
                    <i class="fas fa-qrcode mr-1"></i>
                    Sistem Absensi QR Code DPRD Kota Batam<br>
                    <span class="text-xs text-gray-400">
                        Dicatat pada: {{ $tanggalSekarang }} {{ $waktuSekarang }} WIB
                    </span>
                </p>
                <div class="mt-2 flex justify-center space-x-4 text-xs text-gray-400">
                    <span><i class="fas fa-phone mr-1"></i> (0778) 123456</span>
                    <span><i class="fas fa-envelope mr-1"></i> admin@dprd-batam.go.id</span>
                </div>
            </div>
        </div>
    </div>

    <!-- HAPUS SEMUA SCRIPT AUTO-PRINT -->
    <script>
        // Hanya untuk animasi atau fungsi non-print
        document.addEventListener('DOMContentLoaded', function() {
            // Optional: Tambah efek confetti atau animasi sukses
            const successIcon = document.querySelector('.fa-check-circle');
            if (successIcon) {
                successIcon.parentElement.classList.add('animate-pulse');
            }
            
            // Tampilkan toast sukses
            showSuccessToast();
        });
        
        function showSuccessToast() {
            // Buat elemen toast
            const toast = document.createElement('div');
            toast.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg transform translate-x-full transition-transform duration-300 z-50';
            toast.innerHTML = `
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-3 text-xl"></i>
                    <div>
                        <p class="font-semibold">Kehadiran Tersimpan!</p>
                        <p class="text-sm opacity-90">Data telah disimpan ke database</p>
                    </div>
                </div>
            `;
            
            document.body.appendChild(toast);
            
            // Animasikan masuk
            setTimeout(() => {
                toast.classList.remove('translate-x-full');
                toast.classList.add('translate-x-0');
            }, 100);
            
            // Hapus setelah 3 detik
            setTimeout(() => {
                toast.classList.remove('translate-x-0');
                toast.classList.add('translate-x-full');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }
    </script>
</body>
</html>