<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistem Absensi QR Code DPRD Kota Batam</title>
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
                <a href="{{ route('dashboard') }}" class="flex items-center py-3 px-4 bg-blue-700 border-l-4 border-yellow-400">
                    <i class="fas fa-tachometer-alt mr-3"></i>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('events.index') }}" class="flex items-center py-3 px-4 hover:bg-blue-700 border-l-4 border-transparent hover:border-blue-400 transition duration-200">
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
                        <h2 class="text-xl font-semibold text-gray-800">Dashboard</h2>
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
                <!-- Stats Overview -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Total Acara</p>
                                <p class="text-3xl font-bold text-gray-800 mt-1">{{ $totalEvents ?? 0 }}</p>
                            </div>
                            <div class="p-3 bg-blue-100 text-blue-600 rounded-lg">
                                <i class="fas fa-calendar-alt text-xl"></i>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center text-sm text-gray-500">
                            <i class="fas fa-arrow-up text-green-500 mr-1"></i>
                            <span>{{ $activeEvents ?? 0 }} acara aktif</span>
                        </div>
                    </div>
                    
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Kehadiran Hari Ini</p>
                                <p class="text-3xl font-bold text-gray-800 mt-1">{{ $todayAttendances ?? 0 }}</p>
                            </div>
                            <div class="p-3 bg-green-100 text-green-600 rounded-lg">
                                <i class="fas fa-user-check text-xl"></i>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center text-sm text-gray-500">
                            <i class="fas fa-clock text-blue-500 mr-1"></i>
                            <span>Update terakhir: {{ now()->format('H:i') }}</span>
                        </div>
                    </div>
                    
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Total Kehadiran</p>
                                <p class="text-3xl font-bold text-gray-800 mt-1">{{ $totalAttendances ?? 0 }}</p>
                            </div>
                            <div class="p-3 bg-purple-100 text-purple-600 rounded-lg">
                                <i class="fas fa-users text-xl"></i>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center text-sm text-gray-500">
                            <i class="fas fa-chart-line text-purple-500 mr-1"></i>
                            <span>{{ $attendanceGrowth ?? 0 }}% dari bulan lalu</span>
                        </div>
                    </div>
                    
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Peserta Unik</p>
                                <p class="text-3xl font-bold text-gray-800 mt-1">{{ $uniqueParticipants ?? 0 }}</p>
                            </div>
                            <div class="p-3 bg-orange-100 text-orange-600 rounded-lg">
                                <i class="fas fa-user-friends text-xl"></i>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center text-sm text-gray-500">
                            <i class="fas fa-map-marker-alt text-orange-500 mr-1"></i>
                            <span>Dari berbagai lokasi</span>
                        </div>
                    </div>
                </div>

                <!-- Main Content Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Left Column -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Recent Activities -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-100">
                                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                                    <i class="fas fa-history mr-2 text-blue-500"></i>
                                    Aktivitas Terbaru
                                </h3>
                            </div>
                            <div class="divide-y divide-gray-100">
                                @forelse(($recentActivities ?? []) as $activity)
                                <div class="px-6 py-4 hover:bg-gray-50 transition duration-150">
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0 mt-1">
                                            @if(($activity->type ?? '') == 'attendance')
                                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                                <i class="fas fa-user-check text-green-600 text-xs"></i>
                                            </div>
                                            @elseif(($activity->type ?? '') == 'event')
                                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                                <i class="fas fa-calendar-plus text-blue-600 text-xs"></i>
                                            </div>
                                            @else
                                            <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                                                <i class="fas fa-info-circle text-gray-600 text-xs"></i>
                                            </div>
                                            @endif
                                        </div>
                                        <div class="ml-3 flex-1">
                                            <p class="text-sm font-medium text-gray-900">
                                                {{ $activity->description ?? 'Aktivitas sistem' }}
                                            </p>
                                            <p class="text-xs text-gray-500 mt-1">
                                                {{ isset($activity->created_at) ? $activity->created_at->diffForHumans() : 'Baru saja' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <div class="px-6 py-8 text-center text-gray-500">
                                    <i class="fas fa-inbox text-3xl mb-2 text-gray-300"></i>
                                    <p>Belum ada aktivitas</p>
                                </div>
                                @endforelse
                            </div>
                        </div>

                        <!-- Upcoming Events -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-100">
                                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                                    <i class="fas fa-calendar-day mr-2 text-green-500"></i>
                                    Acara Mendatang
                                </h3>
                            </div>
                            <div class="divide-y divide-gray-100">
                                @forelse(($upcomingEvents ?? []) as $event)
                                <div class="px-6 py-4 hover:bg-gray-50 transition duration-150">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h4 class="font-medium text-gray-900">{{ $event->event_name ?? 'Nama Acara' }}</h4>
                                            <div class="flex items-center mt-1 text-sm text-gray-500">
                                                <i class="fas fa-map-marker-alt mr-2 text-xs"></i>
                                                <span>{{ $event->location ?? 'Lokasi acara' }}</span>
                                            </div>
                                            <div class="flex items-center mt-1 text-sm text-gray-500">
                                                <i class="fas fa-clock mr-2 text-xs"></i>
                                                <span>{{ isset($event->event_date) ? $event->event_date->format('d M Y, H:i') : date('d M Y, H:i') }}</span>
                                            </div>
                                        </div>
                                        <div class="flex space-x-2">
                                            <a href="{{ isset($event) ? route('events.show', $event) : '#' }}" 
                                               class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                                Detail
                                            </a>
                                            <a href="{{ isset($event) ? route('events.qr', $event) : '#' }}" 
                                               class="text-green-600 hover:text-green-800 text-sm font-medium">
                                                QR Code
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <div class="px-6 py-8 text-center text-gray-500">
                                    <i class="fas fa-calendar-times text-3xl mb-2 text-gray-300"></i>
                                    <p>Tidak ada acara mendatang</p>
                                    <a href="{{ route('events.create') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium mt-2 inline-block">
                                        Buat Acara Baru
                                    </a>
                                </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-6">
                        <!-- Quick Stats -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                <i class="fas fa-chart-pie mr-2 text-purple-500"></i>
                                Statistik Cepat
                            </h3>
                            <div class="space-y-4">
                                <div>
                                    <div class="flex justify-between text-sm mb-1">
                                        <span class="text-gray-600">Kehadiran 7 Hari Terakhir</span>
                                        <span class="font-medium text-gray-900">{{ $last7Days ?? 0 }}</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        @php
                                            $last7DaysWidth = min((($last7Days ?? 0) / max(($last7Days ?? 1), 1)) * 100, 100);
                                        @endphp
                                        <div class="bg-green-600 h-2 rounded-full" style="width: {{ $last7DaysWidth }}%"></div>
                                    </div>
                                </div>
                                <div>
                                    <div class="flex justify-between text-sm mb-1">
                                        <span class="text-gray-600">Rata-rata Kehadiran/Acara</span>
                                        <span class="font-medium text-gray-900">{{ $avgAttendancePerEvent ?? 0 }}</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        @php
                                            $avgWidth = min((($avgAttendancePerEvent ?? 0) / max(($avgAttendancePerEvent ?? 1), 1)) * 10, 100);
                                        @endphp
                                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $avgWidth }}%"></div>
                                    </div>
                                </div>
                                <div>
                                    <div class="flex justify-between text-sm mb-1">
                                        <span class="text-gray-600">Tingkat Kehadiran</span>
                                        <span class="font-medium text-gray-900">{{ $attendanceRate ?? 0 }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-purple-600 h-2 rounded-full" style="width: {{ $attendanceRate ?? 0 }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- System Health -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                <i class="fas fa-heartbeat mr-2 text-red-500"></i>
                                Kesehatan Sistem
                            </h3>
                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Status Server</span>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-circle text-xs mr-1"></i>
                                        Online
                                    </span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Penggunaan Database</span>
                                    <span class="text-sm font-medium text-gray-900">{{ $dbUsage ?? 0 }}%</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Response Time</span>
                                    <span class="text-sm font-medium text-gray-900">{{ $responseTime ?? 0 }}ms</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Aktivitas Hari Ini</span>
                                    <span class="text-sm font-medium text-gray-900">{{ $todayActivities ?? 0 }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl shadow-sm p-6 text-white">
                            <h3 class="text-lg font-semibold mb-4">Aksi Cepat</h3>
                            <div class="space-y-3">
                                <a href="{{ route('events.create') }}" 
                                   class="flex items-center justify-between p-3 bg-white bg-opacity-20 rounded-lg hover:bg-opacity-30 transition duration-200">
                                    <div class="flex items-center">
                                        <i class="fas fa-plus-circle mr-3"></i>
                                        <span>Buat Acara Baru</span>
                                    </div>
                                    <i class="fas fa-chevron-right text-sm"></i>
                                </a>
                                <a href="{{ route('events.index') }}" 
                                   class="flex items-center justify-between p-3 bg-white bg-opacity-20 rounded-lg hover:bg-opacity-30 transition duration-200">
                                    <div class="flex items-center">
                                        <i class="fas fa-qrcode mr-3"></i>
                                        <span>Generate QR Code</span>
                                    </div>
                                    <i class="fas fa-chevron-right text-sm"></i>
                                </a>
                                <a href="{{ route('attendances.export') }}" 
                                   class="flex items-center justify-between p-3 bg-white bg-opacity-20 rounded-lg hover:bg-opacity-30 transition duration-200">
                                    <div class="flex items-center">
                                        <i class="fas fa-file-export mr-3"></i>
                                        <span>Export Laporan</span>
                                    </div>
                                    <i class="fas fa-chevron-right text-sm"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        // Auto-refresh dashboard setiap 60 detik
        setInterval(() => {
            window.location.reload();
        }, 60000);
    </script>
</body>
</html>