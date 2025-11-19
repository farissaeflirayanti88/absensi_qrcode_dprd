<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Acara - Sistem Absensi QR Code DPRD Kota Batam</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
                <a href="{{ route('events.index') }}" class="flex items-center py-3 px-4 bg-blue-700 border-l-4 border-yellow-400">
                    <i class="fas fa-calendar-alt mr-3"></i>
                    <span>Kelola Acara</span>
                </a>
                <a href="{{ route('attendances.index') }}" class="flex items-center py-3 px-4 hover:bg-blue-700 border-l-4 border-transparent hover:border-blue-400 transition duration-200">
                    <i class="fas fa-list-alt mr-3"></i>
                    <span>Rekap Kehadiran</span>
                </a>
            </nav>
            
            <!-- Quick Actions -->
            <div class="mt-8 px-4">
                <h3 class="text-sm font-semibold text-blue-200 uppercase tracking-wider mb-3">Aksi Cepat</h3>
                <div class="space-y-2">
                    <a href="{{ route('events.create') }}" class="flex items-center text-sm bg-blue-700 hover:bg-blue-600 py-2 px-3 rounded transition duration-200">
                        <i class="fas fa-plus mr-2 text-xs"></i>
                        <span>Buat Acara Baru</span>
                    </a>
                    <a href="{{ route('attendances.export') }}" class="flex items-center text-sm bg-blue-700 hover:bg-blue-600 py-2 px-3 rounded transition duration-200">
                        <i class="fas fa-download mr-2 text-xs"></i>
                        <span>Export Data</span>
                    </a>
                </div>
            </div>
            
            <!-- System Status -->
            <div class="mt-8 px-4">
                <div class="bg-blue-900 rounded-lg p-3">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs text-blue-200">Status Sistem</span>
                        <span class="flex items-center text-green-400 text-xs">
                            <i class="fas fa-circle text-xs mr-1"></i>
                            Online
                        </span>
                    </div>
                    <div class="text-xs text-blue-300">
                        <div>Terakhir update: {{ now()->format('H:i') }}</div>
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
                        <h2 class="text-xl font-semibold text-gray-800">Tambah Acara Baru</h2>
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
                <!-- Breadcrumb -->
                <div class="mb-6 flex items-center text-sm text-gray-500">
                    <a href="{{ route('dashboard') }}" class="hover:text-blue-600 transition duration-200">Dashboard</a>
                    <i class="fas fa-chevron-right mx-2 text-xs"></i>
                    <a href="{{ route('events.index') }}" class="hover:text-blue-600 transition duration-200">Kelola Acara</a>
                    <i class="fas fa-chevron-right mx-2 text-xs"></i>
                    <span class="text-gray-700 font-medium">Tambah Acara Baru</span>
                </div>

                <!-- Form Section -->
                <div class="max-w-4xl mx-auto">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                        <!-- Form Header -->
                        <div class="bg-gradient-to-r from-blue-500 to-purple-600 px-6 py-4">
                            <h3 class="text-lg font-semibold text-white flex items-center">
                                <i class="fas fa-calendar-plus mr-2"></i>
                                Form Tambah Acara Baru
                            </h3>
                            <p class="text-blue-100 text-sm mt-1">
                                Isi informasi acara untuk membuat QR Code absensi
                            </p>
                        </div>

                        <!-- Form Content -->
                        <div class="p-6">
                            <form method="POST" action="{{ route('events.store') }}" class="space-y-6">
                                @csrf
                                
                                <!-- Nama Acara -->
                                <div>
                                    <label for="event_name" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                                        <i class="fas fa-heading mr-2 text-blue-500"></i>
                                        Nama Acara *
                                    </label>
                                    <input type="text" id="event_name" name="event_name" 
                                           class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                                           placeholder="Masukkan nama acara reses"
                                           required value="{{ old('event_name') }}">
                                    @error('event_name')
                                        <p class="text-red-500 text-sm mt-2 flex items-center">
                                            <i class="fas fa-exclamation-circle mr-1"></i>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <!-- Lokasi -->
                                <div>
                                    <label for="location" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                                        <i class="fas fa-map-marker-alt mr-2 text-green-500"></i>
                                        Lokasi *
                                    </label>
                                    <input type="text" id="location" name="location" 
                                           class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                                           placeholder="Masukkan lokasi acara"
                                           required value="{{ old('location') }}">
                                    @error('location')
                                        <p class="text-red-500 text-sm mt-2 flex items-center">
                                            <i class="fas fa-exclamation-circle mr-1"></i>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <!-- Tanggal Acara -->
                                <div>
                                    <label for="event_date" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                                        <i class="fas fa-calendar-alt mr-2 text-purple-500"></i>
                                        Tanggal Acara *
                                    </label>
                                    <input type="date" id="event_date" name="event_date" 
                                           class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                                           required value="{{ old('event_date') }}"
                                           min="{{ date('Y-m-d') }}">
                                    @error('event_date')
                                        <p class="text-red-500 text-sm mt-2 flex items-center">
                                            <i class="fas fa-exclamation-circle mr-1"></i>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <!-- Deskripsi -->
                                <div>
                                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                                        <i class="fas fa-align-left mr-2 text-orange-500"></i>
                                        Deskripsi Acara
                                    </label>
                                    <textarea id="description" name="description" rows="4"
                                              class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                                              placeholder="Tambahkan deskripsi atau informasi tambahan tentang acara ini">{{ old('description') }}</textarea>
                                    @error('description')
                                        <p class="text-red-500 text-sm mt-2 flex items-center">
                                            <i class="fas fa-exclamation-circle mr-1"></i>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                    <p class="text-gray-500 text-xs mt-2">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        Deskripsi opsional untuk memberikan informasi tambahan kepada peserta
                                    </p>
                                </div>

                                <!-- Status Acara -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                                        <i class="fas fa-toggle-on mr-2 text-red-500"></i>
                                        Status Acara
                                    </label>
                                    <div class="flex items-center space-x-4">
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="is_active" value="1" 
                                                   class="text-blue-600 focus:ring-blue-500" 
                                                   {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
                                            <span class="ml-2 text-sm text-gray-700">Aktif</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="is_active" value="0"
                                                   class="text-blue-600 focus:ring-blue-500"
                                                   {{ old('is_active') == '0' ? 'checked' : '' }}>
                                            <span class="ml-2 text-sm text-gray-700">Tidak Aktif</span>
                                        </label>
                                    </div>
                                    <p class="text-gray-500 text-xs mt-2">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        Acara aktif akan dapat diakses peserta untuk absensi
                                    </p>
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-4 pt-6 border-t border-gray-200">
                                    <a href="{{ route('events.index') }}" 
                                       class="bg-gray-500 text-white px-6 py-3 rounded-lg hover:bg-gray-600 transition duration-200 flex items-center justify-center order-2 sm:order-1">
                                        <i class="fas fa-arrow-left mr-2"></i>
                                        Kembali
                                    </a>
                                    <button type="submit" 
                                            class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition duration-200 flex items-center justify-center order-1 sm:order-2">
                                        <i class="fas fa-save mr-2"></i>
                                        Simpan Acara
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Info Card -->
                    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-xl p-6">
                        <h4 class="text-lg font-semibold text-blue-800 mb-3 flex items-center">
                            <i class="fas fa-lightbulb mr-2 text-yellow-500"></i>
                            Tips Membuat Acara
                        </h4>
                        <ul class="space-y-2 text-sm text-blue-700">
                            <li class="flex items-start">
                                <i class="fas fa-check-circle mr-2 mt-0.5 text-green-500"></i>
                                <span>Pastikan nama acara jelas dan mudah dipahami peserta</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle mr-2 mt-0.5 text-green-500"></i>
                                <span>Gunakan lokasi yang spesifik untuk memudahkan peserta menemukan tempat</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle mr-2 mt-0.5 text-green-500"></i>
                                <span>Setelah acara dibuat, QR Code akan otomatis digenerate</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle mr-2 mt-0.5 text-green-500"></i>
                                <span>Cetak QR Code dan tempel di lokasi yang mudah diakses peserta</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        // Set minimum date to today
        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date().toISOString().split('T')[0];
            const dateInput = document.getElementById('event_date');
            
            if (!dateInput.value) {
                dateInput.value = today;
            }
            
            // Real-time validation for event name
            const eventNameInput = document.getElementById('event_name');
            eventNameInput.addEventListener('input', function() {
                if (this.value.length > 0) {
                    this.classList.remove('border-red-300');
                    this.classList.add('border-green-300');
                } else {
                    this.classList.remove('border-green-300');
                }
            });

            // Real-time validation for location
            const locationInput = document.getElementById('location');
            locationInput.addEventListener('input', function() {
                if (this.value.length > 0) {
                    this.classList.remove('border-red-300');
                    this.classList.add('border-green-300');
                } else {
                    this.classList.remove('border-green-300');
                }
            });

            // Form submission confirmation
            const form = document.querySelector('form');
            form.addEventListener('submit', function(e) {
                const eventName = document.getElementById('event_name').value;
                if (!confirm(`Apakah Anda yakin ingin menyimpan acara "${eventName}"?`)) {
                    e.preventDefault();
                }
            });
        });
    </script>
</body>
</html>