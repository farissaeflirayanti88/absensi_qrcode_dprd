<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Acara - {{ $event->event_name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    @include('layouts.header')

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-8">
        <!-- Breadcrumb -->
        <nav class="mb-6">
            <ol class="flex items-center space-x-2 text-sm">
                <li><a href="{{ route('dashboard') }}" class="text-blue-600 hover:text-blue-800">Dashboard</a></li>
                <li><i class="fas fa-chevron-right text-gray-400"></i></li>
                <li><a href="{{ route('events.index') }}" class="text-blue-600 hover:text-blue-800">Acara</a></li>
                <li><i class="fas fa-chevron-right text-gray-400"></i></li>
                <li class="text-gray-500">Detail Acara</li>
            </ol>
        </nav>

        <!-- Page Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Detail Acara</h1>
                <p class="text-gray-600">Informasi lengkap tentang acara</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('events.index') }}" 
                   class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition duration-200">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali
                </a>
                <a href="{{ route('events.edit', $event) }}" 
                   class="bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600 transition duration-200">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
                <a href="{{ route('events.qr-code', $event) }}" 
                   class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition duration-200">
                    <i class="fas fa-qrcode mr-2"></i>QR Code
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Info -->
            <div class="lg:col-span-2">
                <!-- Event Details Card -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h2 class="text-xl font-semibold mb-4 text-gray-800 border-b pb-2">
                        <i class="fas fa-info-circle mr-2 text-blue-500"></i>Informasi Acara
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Nama Acara</label>
                            <p class="mt-1 text-lg font-semibold text-gray-800">{{ $event->event_name }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Lokasi</label>
                            <p class="mt-1 text-lg font-semibold text-gray-800">
                                <i class="fas fa-map-marker-alt text-red-500 mr-2"></i>{{ $event->location }}
                            </p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Tanggal Acara</label>
                            <p class="mt-1 text-lg font-semibold text-gray-800">
                                <i class="fas fa-calendar-alt text-green-500 mr-2"></i>
                                {{ $event->event_date->format('d F Y') }}
                            </p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Status</label>
                            <p class="mt-1">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $event->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    <i class="fas fa-circle mr-2 text-{{ $event->is_active ? 'green' : 'red' }}-500"></i>
                                    {{ $event->is_active ? 'Aktif' : 'Tidak Aktif' }}
                                </span>
                            </p>
                        </div>
                        
                        @if($event->description)
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-600">Deskripsi</label>
                            <p class="mt-1 text-gray-700 bg-gray-50 p-3 rounded-lg">{{ $event->description }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <a href="{{ route('events.attendances', $event) }}" 
                       class="bg-indigo-500 text-white p-4 rounded-lg hover:bg-indigo-600 transition duration-200 text-center">
                        <i class="fas fa-users text-2xl mb-2"></i>
                        <h3 class="font-semibold">Lihat Daftar Hadir</h3>
                        <p class="text-sm opacity-90">{{ $attendancesCount }} peserta</p>
                    </a>
                    
                    <a href="{{ route('events.qr-code', $event) }}" 
                       class="bg-purple-500 text-white p-4 rounded-lg hover:bg-purple-600 transition duration-200 text-center">
                        <i class="fas fa-qrcode text-2xl mb-2"></i>
                        <h3 class="font-semibold">Generate QR Code</h3>
                        <p class="text-sm opacity-90">Untuk absensi peserta</p>
                    </a>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Statistics -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold mb-4 text-gray-800 border-b pb-2">
                        <i class="fas fa-chart-bar mr-2 text-green-500"></i>Statistik
                    </h3>
                    
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Total Kehadiran</span>
                            <span class="text-2xl font-bold text-blue-600">{{ $attendancesCount }}</span>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Dibuat Pada</span>
                            <span class="text-sm text-gray-500">{{ $event->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Terakhir Diupdate</span>
                            <span class="text-sm text-gray-500">{{ $event->updated_at->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold mb-4 text-gray-800 border-b pb-2">
                        <i class="fas fa-bolt mr-2 text-yellow-500"></i>Aksi Cepat
                    </h3>
                    
                    <div class="space-y-3">
                        <a href="{{ route('events.edit', $event) }}" 
                           class="w-full bg-yellow-500 text-white py-2 px-4 rounded-lg hover:bg-yellow-600 transition duration-200 flex items-center justify-center">
                            <i class="fas fa-edit mr-2"></i>Edit Acara
                        </a>
                        
                        <a href="{{ route('events.qr-code', $event) }}" 
                           class="w-full bg-blue-500 text-white py-2 px-4 rounded-lg hover:bg-blue-600 transition duration-200 flex items-center justify-center">
                            <i class="fas fa-qrcode mr-2"></i>QR Code
                        </a>
                        
                        <form action="{{ route('events.destroy', $event) }}" method="POST" class="w-full" 
                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus acara ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="w-full bg-red-500 text-white py-2 px-4 rounded-lg hover:bg-red-600 transition duration-200 flex items-center justify-center">
                                <i class="fas fa-trash mr-2"></i>Hapus Acara
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Event Status -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold mb-4 text-gray-800 border-b pb-2">
                        <i class="fas fa-toggle-on mr-2 text-purple-500"></i>Status Acara
                    </h3>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">Status Saat Ini:</span>
                        <span class="font-semibold {{ $event->is_active ? 'text-green-600' : 'text-red-600' }}">
                            {{ $event->is_active ? 'Aktif' : 'Tidak Aktif' }}
                        </span>
                    </div>
                    
                    <form action="{{ route('events.toggle-status', $event) }}" method="POST" class="mt-4">
                        @csrf
                        <button type="submit" 
                                class="w-full {{ $event->is_active ? 'bg-red-500 hover:bg-red-600' : 'bg-green-500 hover:bg-green-600' }} text-white py-2 px-4 rounded-lg transition duration-200 flex items-center justify-center">
                            <i class="fas fa-power-off mr-2"></i>
                            {{ $event->is_active ? 'Nonaktifkan Acara' : 'Aktifkan Acara' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Recent Attendances -->
        @if($event->attendances_count > 0)
        <div class="bg-white rounded-lg shadow-md p-6 mt-6">
            <h2 class="text-xl font-semibold mb-4 text-gray-800 border-b pb-2">
                <i class="fas fa-clock mr-2 text-orange-500"></i>Kehadiran Terbaru
            </h2>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Nama Peserta</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Waktu Hadir</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">No. Telepon</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($event->attendances->take(5) as $attendance)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm font-medium text-gray-900">
                                {{ $attendance->participant->name }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600">
                                {{ $attendance->attendance_time->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600">
                                {{ $attendance->participant->phone }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-4 py-4 text-center text-gray-500">
                                Belum ada data kehadiran
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($event->attendances_count > 5)
            <div class="mt-4 text-center">
                <a href="{{ route('events.attendances', $event) }}" 
                   class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    Lihat semua {{ $event->attendances_count }} kehadiran →
                </a>
            </div>
            @endif
        </div>
        @endif
    </div>

    <!-- Footer -->
    @include('layouts.footer')

    <script>
        // Confirmation for delete
        document.addEventListener('DOMContentLoaded', function() {
            const deleteForms = document.querySelectorAll('form[onsubmit]');
            deleteForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    if (!confirm('Apakah Anda yakin ingin menghapus acara ini?')) {
                        e.preventDefault();
                    }
                });
            });
        });
    </script>
</body>
</html>