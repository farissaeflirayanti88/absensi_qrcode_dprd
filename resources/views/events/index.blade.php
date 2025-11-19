<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Acara - Sistem Absensi QR Code DPRD Kota Batam</title>
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
                        <h2 class="text-xl font-semibold text-gray-800">Kelola Acara</h2>
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
                @if (session('success'))
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg shadow-sm">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle mr-2"></i>
                            <span>{{ session('success') }}</span>
                        </div>
                    </div>
                @endif

                @if (session('error'))
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg shadow-sm">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            <span>{{ session('error') }}</span>
                        </div>
                    </div>
                @endif

                <!-- Header dengan Statistik -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Total Acara</p>
                                <p class="text-3xl font-bold text-gray-800 mt-1">{{ $events->count() ?? 0 }}</p>
                            </div>
                            <div class="p-3 bg-blue-100 text-blue-600 rounded-lg">
                                <i class="fas fa-calendar-alt text-xl"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Acara Aktif</p>
                                <p class="text-3xl font-bold text-gray-800 mt-1">{{ $activeEvents ?? 0 }}</p>
                            </div>
                            <div class="p-3 bg-green-100 text-green-600 rounded-lg">
                                <i class="fas fa-calendar-check text-xl"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Acara Mendatang</p>
                                <p class="text-3xl font-bold text-gray-800 mt-1">{{ $upcomingEvents ?? 0 }}</p>
                            </div>
                            <div class="p-3 bg-purple-100 text-purple-600 rounded-lg">
                                <i class="fas fa-calendar-day text-xl"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Total Peserta</p>
                                <p class="text-3xl font-bold text-gray-800 mt-1">{{ $totalParticipants ?? 0 }}</p>
                            </div>
                            <div class="p-3 bg-orange-100 text-orange-600 rounded-lg">
                                <i class="fas fa-users text-xl"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Header Tabel -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">Daftar Acara</h3>
                        <p class="text-gray-600 mt-1">Kelola semua acara reses DPRD Kota Batam</p>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('events.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-200 flex items-center">
                            <i class="fas fa-plus mr-2"></i>
                            Tambah Acara Baru
                        </a>
                    </div>
                </div>

                <!-- Tabel Acara -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    @if($events->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Acara</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Peserta</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($events as $event)
                                <tr class="hover:bg-gray-50 transition duration-150">
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $event->event_name }}</div>
                                        <div class="text-sm text-gray-500">{{ Str::limit($event->description, 50) }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <div class="flex items-center">
                                            <i class="fas fa-map-marker-alt mr-2 text-gray-400"></i>
                                            {{ $event->location }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <div class="flex items-center">
                                            <i class="fas fa-calendar-alt mr-2 text-gray-400"></i>
                                            {{ $event->event_date->format('d/m/Y') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <div class="flex items-center">
                                            <i class="fas fa-users mr-2 text-gray-400"></i>
                                            {{ $event->attendances_count ?? 0 }} peserta
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($event->is_active)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-circle text-xs mr-1"></i>
                                            Aktif
                                        </span>
                                        @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <i class="fas fa-circle text-xs mr-1"></i>
                                            Tidak Aktif
                                        </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('events.qr-code', $event) }}" 
                                               class="text-blue-600 hover:text-blue-900 transition duration-200 p-2 bg-blue-50 rounded-lg"
                                               title="Generate QR Code">
                                                <i class="fas fa-qrcode"></i>
                                            </a>
                                            <a href="{{ route('events.edit', $event) }}" 
                                               class="text-green-600 hover:text-green-900 transition duration-200 p-2 bg-green-50 rounded-lg"
                                               title="Edit Acara">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('events.show', $event) }}" 
                                               class="text-purple-600 hover:text-purple-900 transition duration-200 p-2 bg-purple-50 rounded-lg"
                                               title="Detail Acara">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <form action="{{ route('events.destroy', $event) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="text-red-600 hover:text-red-900 transition duration-200 p-2 bg-red-50 rounded-lg"
                                                        onclick="return confirm('Apakah Anda yakin ingin menghapus acara ini?')"
                                                        title="Hapus Acara">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="px-6 py-12 text-center">
                        <div class="mx-auto w-24 h-24 mb-4 rounded-full bg-gray-100 flex items-center justify-center">
                            <i class="fas fa-calendar-times text-3xl text-gray-400"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada acara</h3>
                        <p class="text-gray-500 mb-6">Mulai dengan membuat acara pertama Anda</p>
                        <a href="{{ route('events.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200">
                            <i class="fas fa-plus mr-2"></i>
                            Buat Acara Pertama
                        </a>
                    </div>
                    @endif
                </div>

                <!-- Info Jumlah Data -->
                @if($events->count() > 0)
                <div class="mt-4 bg-white px-4 py-3 rounded-lg shadow-sm border border-gray-100">
                    <p class="text-sm text-gray-600">
                        Menampilkan <span class="font-semibold">{{ $events->count() }}</span> acara
                    </p>
                </div>
                @endif
            </main>
        </div>
    </div>

    <script>
        // Confirmation for delete
        document.addEventListener('DOMContentLoaded', function() {
            const deleteForms = document.querySelectorAll('form[action*="destroy"]');
            deleteForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    const eventName = this.closest('tr').querySelector('.text-sm.font-medium').textContent;
                    if (!confirm(`Apakah Anda yakin ingin menghapus acara "${eventName}"?`)) {
                        e.preventDefault();
                    }
                });
            });

            // Auto refresh setiap 2 menit untuk update statistik
            setInterval(() => {
                window.location.reload();
            }, 120000);
        });
    </script>
</body>
</html>