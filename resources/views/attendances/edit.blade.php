<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Kehadiran - Sistem Absensi QR Code DPRD Kota Batam</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .form-input:focus {
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="flex">
        <!-- Sidebar -->
        <div class="bg-blue-800 text-white w-64 min-h-screen">
            <div class="p-4 border-b border-blue-700">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-qrcode text-white"></i>
                    </div>
                    <div>
                        <h1 class="text-lg font-bold">Sistem Absensi</h1>
                        <p class="text-xs text-blue-200">DPRD Kota Batam</p>
                    </div>
                </div>
            </div>
            <nav class="mt-6">
                <a href="{{ route('dashboard') }}" class="flex items-center py-3 px-4 hover:bg-blue-700 border-l-4 border-transparent hover:border-blue-400 transition duration-200">
                    <i class="fas fa-tachometer-alt mr-3"></i>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('events.index') }}" class="flex items-center py-3 px-4 hover:bg-blue-700 border-l-4 border-transparent hover:border-blue-400 transition duration-200">
                    <i class="fas fa-calendar-alt mr-3"></i>
                    <span>Kelola Acara</span>
                </a>
                <a href="{{ route('attendances.index') }}" class="flex items-center py-3 px-4 bg-blue-700 border-l-4 border-yellow-400">
                    <i class="fas fa-list-alt mr-3"></i>
                    <span>Rekap Kehadiran</span>
                </a>
            </nav>
            
            <!-- Quick Actions -->
            <div class="mt-8 px-4">
                <h3 class="text-sm font-semibold text-blue-200 uppercase tracking-wider mb-3">Navigasi</h3>
                <div class="space-y-2">
                    <a href="{{ route('attendances.index') }}" class="flex items-center text-sm bg-blue-700 hover:bg-blue-600 py-2 px-3 rounded transition duration-200">
                        <i class="fas fa-arrow-left mr-2 text-xs"></i>
                        <span>Kembali ke Rekap</span>
                    </a>
                    <a href="{{ route('events.index') }}" class="flex items-center text-sm bg-blue-700 hover:bg-blue-600 py-2 px-3 rounded transition duration-200">
                        <i class="fas fa-calendar mr-2 text-xs"></i>
                        <span>Daftar Acara</span>
                    </a>
                </div>
            </div>
            
            <!-- System Status -->
            <div class="mt-8 px-4">
                <div class="bg-blue-900 rounded-lg p-3">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs text-blue-200">Status</span>
                        <span class="flex items-center text-green-400 text-xs">
                            <i class="fas fa-circle text-xs mr-1"></i>
                            Edit Mode
                        </span>
                    </div>
                    <div class="text-xs text-blue-300">
                        <div>ID: <span class="font-bold">{{ $attendance->id }}</span></div>
                        <div>Update: {{ now()->format('H:i') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col">
            <!-- Header -->
            <header class="bg-white shadow-sm border-b">
                <div class="flex justify-between items-center px-6 py-4">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-800">Edit Data Kehadiran</h2>
                        <p class="text-sm text-gray-600">Sistem Absensi QR Code DPRD Kota Batam</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="text-right">
                            <span class="block text-sm font-medium text-gray-700">{{ auth()->user()->name ?? 'Admin' }}</span>
                            <span class="block text-xs text-gray-500">Administrator</span>
                        </div>
                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-user text-blue-600"></i>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-gray-500 hover:text-red-600 transition duration-200">
                                <i class="fas fa-sign-out-alt"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            <!-- Content -->
            <main class="flex-1 p-6 overflow-auto">
                <!-- Page Header -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Edit Kehadiran</h1>
                        <p class="text-gray-600 mt-1">Perbarui data kehadiran peserta</p>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('attendances.index') }}" 
                           class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition duration-200 flex items-center">
                            <i class="fas fa-times mr-2"></i>Batal
                        </a>
                        <button type="submit" 
                                form="editForm"
                                class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-200 flex items-center">
                            <i class="fas fa-save mr-2"></i>Simpan Perubahan
                        </button>
                    </div>
                </div>

                <!-- Form Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
                    <!-- Form Header -->
                    <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-blue-100">
                        <div class="flex items-center">
                            <div class="p-3 bg-blue-100 text-blue-600 rounded-lg mr-4">
                                <i class="fas fa-user-edit text-xl"></i>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-gray-800">Form Edit Kehadiran</h2>
                                <p class="text-sm text-gray-600">Perbarui informasi kehadiran peserta</p>
                            </div>
                        </div>
                    </div>

                    <!-- Form Content -->
                    <form method="POST" action="{{ route('attendances.update', $attendance) }}" id="editForm">
                        @csrf
                        @method('PUT')
                        
                        <div class="p-6 space-y-6">
                            @if ($errors->any())
                            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                                <div class="flex items-center mb-2">
                                    <i class="fas fa-exclamation-circle text-red-600 text-xl mr-3"></i>
                                    <h3 class="text-lg font-semibold text-red-800">Perhatian!</h3>
                                </div>
                                <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif

                            <!-- Data Peserta -->
                            <div class="space-y-4">
                                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                                    <i class="fas fa-user text-blue-500 mr-2"></i>
                                    Data Peserta
                                </h3>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Nama -->
                                    <div>
                                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                                            <i class="fas fa-user-circle mr-1 text-blue-500"></i>
                                            Nama Lengkap <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" 
                                               id="name" 
                                               name="name" 
                                               value="{{ old('name', $attendance->participant->name ?? '') }}"
                                               class="w-full px-4 py-3 border border-gray-300 rounded-lg form-input focus:border-blue-500 focus:ring-blue-500 transition duration-200"
                                               placeholder="Masukkan nama lengkap"
                                               required
                                               minlength="3"
                                               maxlength="100">
                                        <p class="mt-1 text-xs text-gray-500">Minimal 3 karakter</p>
                                    </div>

                                    <!-- Telepon -->
                                    <div>
                                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">
                                            <i class="fas fa-phone mr-1 text-blue-500"></i>
                                            Nomor Telepon <span class="text-red-500">*</span>
                                        </label>
                                        <input type="tel" 
                                               id="phone" 
                                               name="phone" 
                                               value="{{ old('phone', $attendance->participant->phone ?? '') }}"
                                               class="w-full px-4 py-3 border border-gray-300 rounded-lg form-input focus:border-blue-500 focus:ring-blue-500 transition duration-200"
                                               placeholder="081234567890"
                                               pattern="[0-9]{10,15}"
                                               required
                                               maxlength="15">
                                        <p class="mt-1 text-xs text-gray-500">10-15 digit angka</p>
                                    </div>

                                    <!-- Alamat -->
                                    <div class="md:col-span-2">
                                        <label for="address" class="block text-sm font-medium text-gray-700 mb-1">
                                            <i class="fas fa-map-marker-alt mr-1 text-blue-500"></i>
                                            Alamat Lengkap <span class="text-red-500">*</span>
                                        </label>
                                        <textarea id="address" 
                                                  name="address" 
                                                  rows="3"
                                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg form-input focus:border-blue-500 focus:ring-blue-500 transition duration-200"
                                                  placeholder="Masukkan alamat lengkap"
                                                  required
                                                  minlength="10"
                                                  maxlength="255">{{ old('address', $attendance->participant->address ?? '') }}</textarea>
                                        <p class="mt-1 text-xs text-gray-500">Minimal 10 karakter</p>
                                    </div>

                                    <!-- Email -->
                                    <div class="md:col-span-2">
                                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                                            <i class="fas fa-envelope mr-1 text-blue-500"></i>
                                            Email (Opsional)
                                        </label>
                                        <input type="email" 
                                               id="email" 
                                               name="email" 
                                               value="{{ old('email', $attendance->participant->email ?? '') }}"
                                               class="w-full px-4 py-3 border border-gray-300 rounded-lg form-input focus:border-blue-500 focus:ring-blue-500 transition duration-200"
                                               placeholder="nama@contoh.com"
                                               maxlength="100">
                                    </div>
                                </div>
                            </div>

                            <!-- Data Kehadiran -->
                            <div class="space-y-4 pt-6 border-t border-gray-200">
                                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                                    <i class="fas fa-calendar-check text-blue-500 mr-2"></i>
                                    Data Kehadiran
                                </h3>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Acara -->
                                    <div>
                                        <label for="event_id" class="block text-sm font-medium text-gray-700 mb-1">
                                            <i class="fas fa-calendar-alt mr-1 text-blue-500"></i>
                                            Acara <span class="text-red-500">*</span>
                                        </label>
                                        <select id="event_id" 
                                                name="event_id" 
                                                class="w-full px-4 py-3 border border-gray-300 rounded-lg form-input focus:border-blue-500 focus:ring-blue-500 transition duration-200"
                                                required>
                                            <option value="">Pilih Acara</option>
                                            @foreach($events as $event)
                                                <option value="{{ $event->id }}" 
                                                        {{ old('event_id', $attendance->event_id) == $event->id ? 'selected' : '' }}>
                                                    {{ $event->event_name }} ({{ $event->event_date->format('d/m/Y') }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Waktu Hadir -->
                                    <div>
                                        <label for="attendance_time" class="block text-sm font-medium text-gray-700 mb-1">
                                            <i class="fas fa-clock mr-1 text-blue-500"></i>
                                            Waktu Hadir <span class="text-red-500">*</span>
                                        </label>
                                        <input type="datetime-local" 
                                               id="attendance_time" 
                                               name="attendance_time" 
                                               value="{{ old('attendance_time', $attendance->attendance_time ? $attendance->attendance_time->format('Y-m-d\TH:i') : '') }}"
                                               class="w-full px-4 py-3 border border-gray-300 rounded-lg form-input focus:border-blue-500 focus:ring-blue-500 transition duration-200"
                                               required>
                                        <p class="mt-1 text-xs text-gray-500">Tanggal dan waktu kehadiran</p>
                                    </div>

                                    <!-- Catatan -->
                                    <div class="md:col-span-2">
                                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">
                                            <i class="fas fa-sticky-note mr-1 text-blue-500"></i>
                                            Catatan (Opsional)
                                        </label>
                                        <textarea id="notes" 
                                                  name="notes" 
                                                  rows="2"
                                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg form-input focus:border-blue-500 focus:ring-blue-500 transition duration-200"
                                                  placeholder="Catatan tambahan tentang kehadiran"
                                                  maxlength="500">{{ old('notes', $attendance->notes ?? '') }}</textarea>
                                        <p class="mt-1 text-xs text-gray-500">Maksimal 500 karakter</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Status Duplikat Alert -->
                            @if($attendance->is_duplicate)
                            <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                                <div class="flex items-center">
                                    <i class="fas fa-exclamation-triangle text-yellow-600 text-xl mr-3"></i>
                                    <div>
                                        <h4 class="font-medium text-yellow-800">Status Duplikat</h4>
                                        <p class="text-sm text-yellow-700 mt-1">
                                            Data ini teridentifikasi sebagai duplikat. 
                                            Setelah diperbarui, status duplikat akan direset.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- Form Buttons -->
                            <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                                <a href="{{ route('attendances.index') }}" 
                                   class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition duration-200 font-medium flex items-center">
                                    <i class="fas fa-times mr-2"></i> Batal
                                </a>
                                <button type="submit" 
                                        id="submitBtn"
                                        class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200 font-medium flex items-center">
                                    <i class="fas fa-save mr-2"></i> Simpan Perubahan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Current Data Info Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="p-3 bg-gray-100 text-gray-600 rounded-lg mr-4">
                                    <i class="fas fa-info-circle text-xl"></i>
                                </div>
                                <div>
                                    <h2 class="text-xl font-bold text-gray-800">Data Saat Ini</h2>
                                    <p class="text-sm text-gray-600">Informasi kehadiran sebelum diubah</p>
                                </div>
                            </div>
                            <div class="text-sm text-gray-500">
                                ID: <span class="font-mono font-bold text-blue-600">{{ $attendance->id }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <!-- Data Peserta -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                    <i class="fas fa-user text-blue-500 mr-2"></i>
                                    Informasi Peserta
                                </h3>
                                <div class="space-y-4">
                                    <div class="flex items-start">
                                        <div class="w-1/3">
                                            <span class="text-sm text-gray-500">Nama Lengkap</span>
                                        </div>
                                        <div class="w-2/3">
                                            <p class="font-medium text-gray-900">{{ $attendance->participant->name ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start">
                                        <div class="w-1/3">
                                            <span class="text-sm text-gray-500">Nomor Telepon</span>
                                        </div>
                                        <div class="w-2/3">
                                            <p class="font-medium text-gray-900">{{ $attendance->participant->phone ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start">
                                        <div class="w-1/3">
                                            <span class="text-sm text-gray-500">Alamat</span>
                                        </div>
                                        <div class="w-2/3">
                                            <p class="font-medium text-gray-900">{{ $attendance->participant->address ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                    @if($attendance->participant->email)
                                    <div class="flex items-start">
                                        <div class="w-1/3">
                                            <span class="text-sm text-gray-500">Email</span>
                                        </div>
                                        <div class="w-2/3">
                                            <p class="font-medium text-gray-900">{{ $attendance->participant->email }}</p>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Data Kehadiran -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                    <i class="fas fa-calendar-check text-blue-500 mr-2"></i>
                                    Informasi Kehadiran
                                </h3>
                                <div class="space-y-4">
                                    <div class="flex items-start">
                                        <div class="w-1/3">
                                            <span class="text-sm text-gray-500">Acara</span>
                                        </div>
                                        <div class="w-2/3">
                                            <p class="font-medium text-gray-900">{{ $attendance->event->event_name ?? 'N/A' }}</p>
                                            <p class="text-xs text-gray-500 mt-1">
                                                <i class="fas fa-calendar-alt mr-1"></i>
                                                {{ $attendance->event->event_date->format('d/m/Y') ?? 'N/A' }}
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                <i class="fas fa-map-marker-alt mr-1"></i>
                                                {{ $attendance->event->location ?? 'N/A' }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex items-start">
                                        <div class="w-1/3">
                                            <span class="text-sm text-gray-500">Waktu Hadir</span>
                                        </div>
                                        <div class="w-2/3">
                                            <p class="font-medium text-gray-900">
                                                {{ $attendance->attendance_time->format('d/m/Y H:i') ?? 'N/A' }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex items-start">
                                        <div class="w-1/3">
                                            <span class="text-sm text-gray-500">Status</span>
                                        </div>
                                        <div class="w-2/3">
                                            @if($attendance->is_duplicate)
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                <i class="fas fa-exclamation-triangle mr-1"></i> Duplikat
                                            </span>
                                            @else
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <i class="fas fa-check mr-1"></i> Valid
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                    @if($attendance->notes)
                                    <div class="flex items-start">
                                        <div class="w-1/3">
                                            <span class="text-sm text-gray-500">Catatan</span>
                                        </div>
                                        <div class="w-2/3">
                                            <p class="text-sm text-gray-700 bg-gray-50 p-3 rounded-lg">
                                                {{ $attendance->notes }}
                                            </p>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('editForm');
            const submitBtn = document.getElementById('submitBtn');
            
            // Format telepon (hanya angka)
            const phoneInput = document.getElementById('phone');
            phoneInput.addEventListener('input', function() {
                this.value = this.value.replace(/\D/g, '');
            });
            
            // Format nama (auto kapital)
            const nameInput = document.getElementById('name');
            nameInput.addEventListener('input', function() {
                this.value = this.value.toUpperCase();
            });
            
            // Form validation dengan SweetAlert
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Validasi sisi klien
                const phone = phoneInput.value.trim();
                const name = nameInput.value.trim();
                const address = document.getElementById('address').value.trim();
                const eventId = document.getElementById('event_id').value;
                const attendanceTime = document.getElementById('attendance_time').value;
                
                let isValid = true;
                let errorMessage = '';
                
                if (!name || name.length < 3) {
                    isValid = false;
                    errorMessage = 'Nama lengkap minimal 3 karakter';
                    nameInput.focus();
                } else if (!address || address.length < 10) {
                    isValid = false;
                    errorMessage = 'Alamat minimal 10 karakter';
                    document.getElementById('address').focus();
                } else if (!phone || phone.length < 10 || phone.length > 15) {
                    isValid = false;
                    errorMessage = 'Nomor telepon harus 10-15 digit angka';
                    phoneInput.focus();
                } else if (!eventId) {
                    isValid = false;
                    errorMessage = 'Pilih acara terlebih dahulu';
                    document.getElementById('event_id').focus();
                } else if (!attendanceTime) {
                    isValid = false;
                    errorMessage = 'Pilih tanggal dan waktu kehadiran';
                    document.getElementById('attendance_time').focus();
                }
                
                if (!isValid) {
                    Swal.fire({
                        title: 'Validasi Gagal',
                        text: errorMessage,
                        icon: 'warning',
                        confirmButtonColor: '#3b82f6',
                    });
                    return false;
                }
                
                // Konfirmasi sebelum submit
                Swal.fire({
                    title: 'Simpan Perubahan?',
                    html: `Anda akan memperbarui data kehadiran:<br>
                           <strong>"${name}"</strong><br><br>
                           <span class="text-sm text-gray-600">Pastikan data yang diisi sudah benar</span>`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3b82f6',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Ya, Simpan',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show loading
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Menyimpan...';
                        
                        // Submit form
                        form.submit();
                    }
                });
            });
            
            // Set min/max date for attendance time
            const attendanceTimeInput = document.getElementById('attendance_time');
            const today = new Date();
            const minDate = new Date(today.getFullYear() - 1, today.getMonth(), today.getDate()); // 1 tahun lalu
            const maxDate = new Date(today.getFullYear() + 1, today.getMonth(), today.getDate()); // 1 tahun ke depan
            attendanceTimeInput.min = minDate.toISOString().slice(0, 16);
            attendanceTimeInput.max = maxDate.toISOString().slice(0, 16);
            
            // Auto focus first field
            nameInput.focus();
        });
    </script>
</body>
</html>