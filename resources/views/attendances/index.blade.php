<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Kehadiran - Sistem Absensi QR Code DPRD Kota Batam</title>
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
                        <h2 class="text-xl font-semibold text-gray-800">Rekap Kehadiran</h2>
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
                        <h1 class="text-2xl font-bold text-gray-800">Rekap Kehadiran</h1>
                        <p class="text-gray-600 mt-1">Data kehadiran peserta semua acara</p>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('attendances.export', ['event_id' => $selectedEvent ?? '']) }}" 
                           class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition duration-200 flex items-center">
                            <i class="fas fa-file-csv mr-2"></i>Export CSV
                        </a>
                        <a href="{{ route('attendances.export-pdf', ['event_id' => $selectedEvent ?? '']) }}" 
                           target="_blank"
                           class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition duration-200 flex items-center">
                            <i class="fas fa-file-pdf mr-2"></i>Print PDF
                        </a>
                    </div>
                </div>

                <!-- Statistics -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Total Kehadiran</p>
                                <p class="text-3xl font-bold text-gray-800 mt-1">{{ $totalAttendances ?? 0 }}</p>
                            </div>
                            <div class="p-3 bg-blue-100 text-blue-600 rounded-lg">
                                <i class="fas fa-users text-xl"></i>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center text-sm text-gray-500">
                            <i class="fas fa-chart-line text-blue-500 mr-1"></i>
                            <span>Semua waktu</span>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Kehadiran Hari Ini</p>
                                <p class="text-3xl font-bold text-gray-800 mt-1">{{ $todayAttendances ?? 0 }}</p>
                            </div>
                            <div class="p-3 bg-green-100 text-green-600 rounded-lg">
                                <i class="fas fa-user-clock text-xl"></i>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center text-sm text-gray-500">
                            <i class="fas fa-calendar-day text-green-500 mr-1"></i>
                            <span>{{ now()->format('d M Y') }}</span>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Peserta Unik</p>
                                <p class="text-3xl font-bold text-gray-800 mt-1">{{ $uniqueParticipants ?? 0 }}</p>
                            </div>
                            <div class="p-3 bg-purple-100 text-purple-600 rounded-lg">
                                <i class="fas fa-user-check text-xl"></i>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center text-sm text-gray-500">
                            <i class="fas fa-id-card text-purple-500 mr-1"></i>
                            <span>Individu berbeda</span>
                        </div>
                    </div>
                </div>

                <!-- Filter dan Tabel -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <!-- Filter Section -->
                    <div class="p-6 border-b border-gray-100">
                        <form method="GET" action="{{ route('attendances.index') }}" class="flex flex-col md:flex-row gap-4 items-end">
                            <div class="flex-1">
                                <label for="event_id" class="block text-sm font-medium text-gray-700 mb-1">
                                    <i class="fas fa-filter mr-1 text-blue-500"></i>
                                    Filter Berdasarkan Acara
                                </label>
                                <select name="event_id" id="event_id" 
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Semua Acara</option>
                                    @foreach($events as $event)
                                        <option value="{{ $event->id }}" {{ ($selectedEvent ?? '') == $event->id ? 'selected' : '' }}>
                                            {{ $event->event_name }} ({{ $event->event_date->format('d/m/Y') }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex space-x-2">
                                <button type="submit" 
                                        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-200 flex items-center">
                                    <i class="fas fa-filter mr-2"></i>Filter
                                </button>
                                <a href="{{ route('attendances.index') }}" 
                                   class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition duration-200 flex items-center">
                                    <i class="fas fa-refresh mr-2"></i>Reset
                                </a>
                            </div>
                        </form>
                    </div>

                    <!-- Tabel Data -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        No
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Nama Peserta
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Acara
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Waktu Hadir
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($attendances as $attendance)
                                <tr class="hover:bg-gray-50 transition duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $loop->iteration }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $attendance->participant->name ?? 'N/A' }}</div>
                                        <div class="text-sm text-gray-500 flex items-center mt-1">
                                            <i class="fas fa-phone mr-2 text-gray-400 text-xs"></i>
                                            {{ $attendance->participant->phone ?? 'N/A' }}
                                        </div>
                                        <div class="text-xs text-gray-400 flex items-center mt-1">
                                            <i class="fas fa-map-marker-alt mr-2 text-gray-400 text-xs"></i>
                                            {{ Str::limit($attendance->participant->address ?? 'N/A', 50) }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $attendance->event->event_name ?? 'N/A' }}</div>
                                        <div class="text-sm text-gray-500 flex items-center mt-1">
                                            <i class="fas fa-map-marker-alt mr-2 text-gray-400 text-xs"></i>
                                            {{ $attendance->event->location ?? 'N/A' }}
                                        </div>
                                        <div class="text-xs text-gray-400 flex items-center mt-1">
                                            <i class="fas fa-calendar-alt mr-2 text-gray-400 text-xs"></i>
                                            {{ $attendance->event->event_date->format('d/m/Y') ?? 'N/A' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <div class="flex items-center">
                                            <i class="fas fa-clock mr-2 text-gray-400"></i>
                                            {{ $attendance->attendance_time->format('d/m/Y H:i') ?? 'N/A' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('attendances.edit', $attendance) }}" 
                                               class="text-yellow-600 hover:text-yellow-900 transition duration-200 p-2 bg-yellow-50 rounded-lg"
                                               title="Edit Kehadiran">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('attendances.destroy', $attendance) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="text-red-600 hover:text-red-900 transition duration-200 p-2 bg-red-50 rounded-lg"
                                                        title="Hapus Kehadiran">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center">
                                        <div class="mx-auto w-24 h-24 mb-4 rounded-full bg-gray-100 flex items-center justify-center">
                                            <i class="fas fa-inbox text-3xl text-gray-400"></i>
                                        </div>
                                        <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada data kehadiran</h3>
                                        <p class="text-gray-500 mb-4">Data kehadiran akan muncul setelah peserta melakukan absensi</p>
                                        <a href="{{ route('events.index') }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                            Lihat Daftar Acara
                                        </a>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Info Jumlah Data -->
                    @if($attendances->count() > 0)
                    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                        <p class="text-sm text-gray-600">
                            Menampilkan <span class="font-semibold">{{ $attendances->count() }}</span> data kehadiran
                        </p>
                    </div>
                    @endif
                </div>
            </main>
        </div>
    </div>

    <script>
        // Confirmation for delete
        document.addEventListener('DOMContentLoaded', function() {
            const deleteForms = document.querySelectorAll('form[action*="destroy"]');
            deleteForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    if (!confirm('Apakah Anda yakin ingin menghapus data kehadiran ini?')) {
                        e.preventDefault();
                    }
                });
            });

            // Auto refresh setiap 2 menit untuk update data terbaru
            setInterval(() => {
                window.location.reload();
            }, 120000);
        });
    </script>
</body>
</html>