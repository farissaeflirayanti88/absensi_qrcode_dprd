@extends('layouts.app')

@section('title', 'Dashboard - Sistem Absensi QR Code DPRD Kota Batam')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Sistem Absensi QR Code DPRD Kota Batam')

@section('content')
<!-- ============================================= -->
<!-- SECTION 1: 4 STATISTIK UTAMA (CARD BESAR) -->
<!-- OPSI 6: ACARA DENGAN PESERTA -->
<!-- ============================================= -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Card 1: Total Acara -->
    <div class="bg-gradient-to-br from-blue-600 to-blue-800 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition duration-300">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-blue-200 text-sm font-medium uppercase tracking-wider">Total Acara</p>
                <p class="text-4xl font-bold mt-2">{{ $totalEvents ?? 0 }}</p>
                <div class="flex items-center mt-2 text-blue-200">
                    <i class="fas fa-check-circle text-xs mr-1"></i>
                    <span class="text-sm">{{ $acaraSelesai ?? 0 }} acara selesai</span>
                </div>
            </div>
            <div class="bg-white bg-opacity-20 rounded-lg p-3">
                <i class="fas fa-calendar-alt text-2xl"></i>
            </div>
        </div>
        <div class="mt-4 text-xs text-blue-200">
            <i class="fas fa-clock mr-1"></i> Update: {{ now()->format('H:i') }}
        </div>
    </div>

    <!-- Card 2: Total Kehadiran -->
    <div class="bg-gradient-to-br from-green-600 to-green-800 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition duration-300">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-green-200 text-sm font-medium uppercase tracking-wider">Total Kehadiran</p>
                <p class="text-4xl font-bold mt-2">{{ number_format($totalKehadiran ?? 0) }}</p>
                <div class="flex items-center mt-2 text-green-200">
                    <i class="fas fa-users text-xs mr-1"></i>
                    <span class="text-sm">akumulasi semua acara</span>
                </div>
            </div>
            <div class="bg-white bg-opacity-20 rounded-lg p-3">
                <i class="fas fa-user-check text-2xl"></i>
            </div>
        </div>
        <div class="mt-4 text-xs text-green-200">
            <i class="fas fa-calendar-day mr-1"></i> Hari ini: {{ $todayAttendances ?? 0 }}
        </div>
    </div>

    <!-- Card 3: Acara Aktif & Mendatang -->
    <div class="bg-gradient-to-br from-purple-600 to-purple-800 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition duration-300">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-purple-200 text-sm font-medium uppercase tracking-wider">Acara Aktif</p>
                <p class="text-4xl font-bold mt-2">{{ $activeEvents ?? 0 }}</p>
                <div class="flex items-center mt-2 text-purple-200">
                    <i class="fas fa-calendar-plus text-xs mr-1"></i>
                    <span class="text-sm">{{ $upcomingEvents ?? 0 }} akan datang</span>
                </div>
            </div>
            <div class="bg-white bg-opacity-20 rounded-lg p-3">
                <i class="fas fa-calendar-check text-2xl"></i>
            </div>
        </div>
        <div class="mt-4 text-xs text-purple-200">
            <i class="fas fa-calendar-day mr-1"></i> Hari ini: {{ $todayActiveEvents ?? 0 }}
        </div>
    </div>

    <!-- Card 4: ACARA DENGAN PESERTA (OPSI 6) -->
    <div class="bg-gradient-to-br from-orange-600 to-orange-800 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition duration-300">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-orange-200 text-sm font-medium uppercase tracking-wider">Acara dengan Peserta</p>
                <p class="text-4xl font-bold mt-2">{{ $acaraDenganPeserta ?? 0 }}</p>
                <div class="flex items-center mt-2 text-orange-200">
                    <i class="fas fa-check-circle text-xs mr-1"></i>
                    <span class="text-sm">dari {{ $totalEvents ?? 0 }} total acara</span>
                </div>
            </div>
            <div class="bg-white bg-opacity-20 rounded-lg p-3">
                <i class="fas fa-calendar-check text-2xl"></i>
            </div>
        </div>
        <div class="mt-4 text-xs text-orange-200">
            @php
                $persentase = $totalEvents > 0 ? round(($acaraDenganPeserta / $totalEvents) * 100) : 0;
            @endphp
            <i class="fas fa-chart-pie mr-1"></i> {{ $persentase }}% dari total acara
        </div>
    </div>
</div>

<!-- ============================================= -->
<!-- SECTION 2: INFORMASI PENTING (RINGKASAN EKSEKUTIF) -->
<!-- ============================================= -->
<div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6 mb-8">
    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
        <i class="fas fa-info-circle text-blue-500 mr-2"></i>
        Ringkasan Eksekutif
    </h3>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Kolom 1: Acara -->
        <div class="space-y-3">
            <div class="flex justify-between items-center p-3 bg-blue-50 rounded-lg">
                <span class="text-sm font-medium text-gray-700">Total Acara</span>
                <span class="text-lg font-bold text-blue-600">{{ $totalEvents ?? 0 }}</span>
            </div>
            <div class="flex justify-between items-center p-3 bg-green-50 rounded-lg">
                <span class="text-sm font-medium text-gray-700">Acara dengan Peserta</span>
                <span class="text-lg font-bold text-green-600">{{ $acaraDenganPeserta ?? 0 }}</span>
            </div>
            <div class="flex justify-between items-center p-3 bg-yellow-50 rounded-lg">
                <span class="text-sm font-medium text-gray-700">Acara Tanpa Peserta</span>
                <span class="text-lg font-bold text-yellow-600">{{ ($totalEvents - $acaraDenganPeserta) }}</span>
            </div>
        </div>
        
        <!-- Kolom 2: Kehadiran -->
        <div class="space-y-3">
            <div class="flex justify-between items-center p-3 bg-purple-50 rounded-lg">
                <span class="text-sm font-medium text-gray-700">Total Kehadiran</span>
                <span class="text-lg font-bold text-purple-600">{{ number_format($totalKehadiran ?? 0) }}</span>
            </div>
            <div class="flex justify-between items-center p-3 bg-indigo-50 rounded-lg">
                <span class="text-sm font-medium text-gray-700">Konstituen Aktif</span>
                <span class="text-lg font-bold text-indigo-600">{{ number_format($totalParticipants ?? 0) }}</span>
            </div>
            <div class="flex justify-between items-center p-3 bg-pink-50 rounded-lg">
                <span class="text-sm font-medium text-gray-700">Rata-rata per Acara</span>
                <span class="text-lg font-bold text-pink-600">{{ $rataPesertaPerAcara ?? 0 }}</span>
            </div>
        </div>
        
        <!-- Kolom 3: Periode -->
        <div class="space-y-3">
            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                <span class="text-sm font-medium text-gray-700">Kehadiran Hari Ini</span>
                <span class="text-lg font-bold text-gray-800">{{ $todayAttendances ?? 0 }}</span>
            </div>
            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                <span class="text-sm font-medium text-gray-700">Kehadiran Minggu Ini</span>
                <span class="text-lg font-bold text-gray-800">{{ $attendancesThisWeek ?? 0 }}</span>
            </div>
            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                <span class="text-sm font-medium text-gray-700">Acara Bulan Ini</span>
                <span class="text-lg font-bold text-gray-800">{{ $eventsThisMonth ?? 0 }}</span>
            </div>
        </div>
    </div>
    
    <!-- Penjelasan Singkat -->
    <div class="mt-4 p-4 bg-blue-50 rounded-lg text-sm text-blue-800">
        <i class="fas fa-lightbulb mr-2 text-yellow-500"></i>
        <strong>Catatan:</strong> Dari {{ $totalEvents ?? 0 }} total acara, 
        <strong>{{ $acaraDenganPeserta ?? 0 }} acara</strong> ({{ $persentase ?? 0 }}%) 
        telah dihadiri oleh <strong>{{ number_format($totalParticipants ?? 0 )}} konstituen</strong> 
        dengan total <strong>{{ number_format($totalKehadiran ?? 0) }} kehadiran</strong>.
        Rata-rata <strong>{{ $rataPesertaPerAcara ?? 0 }} peserta per acara</strong>.
    </div>
</div>

<!-- ============================================= -->
<!-- SECTION 3: TOP ACARA & TREN KEHADIRAN -->
<!-- ============================================= -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Top 5 Acara dengan Kehadiran Terbanyak -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-trophy text-yellow-500 mr-2"></i>
            Top 5 Acara Terpadat
        </h3>
        
        @if($topAcara && $topAcara->count() > 0)
        <div class="space-y-4">
            @foreach($topAcara as $index => $acara)
            <div class="flex items-center p-3 {{ $index % 2 == 0 ? 'bg-gray-50' : 'bg-white' }} rounded-lg">
                <div class="w-8 h-8 rounded-full 
                    {{ $index == 0 ? 'bg-yellow-100 text-yellow-700' : 
                       ($index == 1 ? 'bg-gray-200 text-gray-700' : 
                       ($index == 2 ? 'bg-orange-100 text-orange-700' : 'bg-blue-50 text-blue-600')) }} 
                    flex items-center justify-center mr-3 font-bold">
                    {{ $index + 1 }}
                </div>
                <div class="flex-1">
                    <p class="font-medium text-gray-800">{{ Str::limit($acara->event_name, 35) }}</p>
                    <div class="flex items-center text-xs text-gray-500 mt-1">
                        <i class="fas fa-map-marker-alt mr-1"></i>
                        {{ Str::limit($acara->location, 20) }}
                        <i class="fas fa-calendar-alt ml-3 mr-1"></i>
                        {{ $acara->event_date ? $acara->event_date->format('d/m/Y') : '-' }}
                    </div>
                </div>
                <div class="text-right">
                    <span class="inline-flex items-center px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">
                        <i class="fas fa-user-check mr-1"></i>
                        {{ $acara->attendances_count ?? 0 }}
                    </span>
                </div>
            </div>
            @endforeach
        </div>
        
        <div class="mt-4 text-right">
            <a href="{{ route('events.index') }}" class="text-sm text-blue-600 hover:text-blue-800">
                Lihat semua acara <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        @else
        <div class="text-center py-8 text-gray-500">
            <i class="fas fa-calendar-times text-4xl mb-3 text-gray-300"></i>
            <p>Belum ada data acara</p>
        </div>
        @endif
    </div>

    <!-- Tren Kehadiran 7 Hari Terakhir -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-chart-line text-green-500 mr-2"></i>
            Tren Kehadiran 7 Hari Terakhir
        </h3>
        
        @if($trendMingguan && count($trendMingguan) > 0)
        <div class="space-y-3">
            @foreach($trendMingguan as $trend)
            <div class="flex items-center">
                <div class="w-24 text-sm">
                    <span class="font-medium text-gray-700">{{ $trend['hari'] }}</span>
                    <span class="text-xs text-gray-400 ml-1">{{ $trend['tanggal'] }}</span>
                </div>
                <div class="flex-1 mx-3">
                    <div class="relative h-7">
                        @php
                            $maxValue = max(array_column($trendMingguan, 'jumlah')) ?: 1;
                            $width = ($trend['jumlah'] / $maxValue) * 100;
                        @endphp
                        <div class="absolute bottom-0 left-0 h-full bg-gradient-to-r from-green-400 to-green-600 rounded-lg"
                             style="width: {{ $width }}%">
                            <span class="absolute right-2 top-1/2 transform -translate-y-1/2 text-white text-xs font-medium">
                                {{ $trend['jumlah'] }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        
        <div class="grid grid-cols-3 gap-3 mt-6 pt-4 border-t border-gray-200">
            <div class="text-center">
                <p class="text-xs text-gray-500">Total 7 Hari</p>
                <p class="text-xl font-bold text-gray-800">{{ array_sum(array_column($trendMingguan, 'jumlah')) }}</p>
            </div>
            <div class="text-center">
                <p class="text-xs text-gray-500">Rata-rata</p>
                <p class="text-xl font-bold text-gray-800">{{ round(array_sum(array_column($trendMingguan, 'jumlah')) / 7, 1) }}</p>
            </div>
            <div class="text-center">
                <p class="text-xs text-gray-500">Hari Ini</p>
                <p class="text-xl font-bold text-green-600">{{ $todayAttendances ?? 0 }}</p>
            </div>
        </div>
        @else
        <div class="text-center py-8 text-gray-500">
            <i class="fas fa-chart-line text-4xl mb-3 text-gray-300"></i>
            <p>Belum ada data 7 hari terakhir</p>
        </div>
        @endif
    </div>
</div>

<!-- ============================================= -->
<!-- SECTION 4: SEBARAN WILAYAH & AKTIVITAS -->
<!-- ============================================= -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- Sebaran Wilayah -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-map-marker-alt text-red-500 mr-2"></i>
            Top 5 Wilayah Asal Peserta
        </h3>
        
        @if($statistikWilayah && $statistikWilayah->count() > 0)
        <div class="space-y-4">
            @foreach($statistikWilayah as $index => $wilayah)
            <div>
                <div class="flex justify-between items-center mb-1">
                    <span class="text-sm font-medium text-gray-700">
                        {{ $index + 1 }}. {{ Str::limit($wilayah->address, 25) }}
                    </span>
                    <span class="text-sm font-semibold text-blue-600">{{ $wilayah->total }}</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    @php
                        $totalKehadiran = $totalKehadiran ?? 1;
                        $percentage = $totalKehadiran > 0 ? min(($wilayah->total / $totalKehadiran) * 100, 100) : 0;
                    @endphp
                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                </div>
                <p class="text-xs text-gray-400 mt-1">{{ round($percentage, 1) }}% dari total kehadiran</p>
            </div>
            @endforeach
        </div>
        
        <div class="mt-4 text-right">
            <a href="{{ route('participants.index') }}" class="text-sm text-blue-600 hover:text-blue-800">
                Lihat semua peserta <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        @else
        <div class="text-center py-8 text-gray-500">
            <i class="fas fa-map-marked-alt text-4xl mb-3 text-gray-300"></i>
            <p>Belum ada data wilayah</p>
        </div>
        @endif
    </div>

    <!-- Aktivitas Terbaru (2 kolom) -->
    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-history text-blue-500 mr-2"></i>
                    Aktivitas Terbaru
                </h3>
                <span class="text-xs text-gray-500">
                    <i class="fas fa-clock mr-1"></i> Real-time
                </span>
            </div>
        </div>
        
        <div class="p-4 max-h-80 overflow-y-auto">
            @if($recentActivities && $recentActivities->count() > 0)
            <div class="space-y-3">
                @foreach($recentActivities as $activity)
                <div class="flex items-start p-3 hover:bg-gray-50 rounded-lg transition">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center">
                            <i class="fas fa-user text-blue-600 text-xs"></i>
                        </div>
                    </div>
                    <div class="ml-3 flex-1">
                        <div class="flex justify-between">
                            <p class="text-sm font-medium text-gray-800">
                                {{ $activity->user->name ?? 'System' }}
                            </p>
                            <span class="text-xs text-gray-400">
                                {{ $activity->created_at->diffForHumans() }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-600 mt-1">
                            {{ $activity->description ?? $activity->activity }}
                        </p>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-8 text-gray-500">
                <i class="fas fa-history text-4xl mb-3 text-gray-300"></i>
                <p>Belum ada aktivitas</p>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- ============================================= -->
<!-- SECTION 5: ACARA TERBARU & AKSI CEPAT -->
<!-- ============================================= -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Acara Terbaru (2 kolom) -->
    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-calendar-plus text-blue-500 mr-2"></i>
                    Acara Terbaru
                </h3>
                <a href="{{ route('events.index') }}" class="text-sm text-blue-600 hover:text-blue-800">
                    Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>
        
        <div class="p-4">
            @if($recentEvents && $recentEvents->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @foreach($recentEvents as $event)
                <div class="p-4 border border-gray-200 rounded-lg hover:shadow-md transition">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="font-medium text-gray-800">{{ Str::limit($event->event_name, 25) }}</p>
                            <p class="text-xs text-gray-500 mt-1">
                                <i class="fas fa-map-marker-alt mr-1"></i> {{ Str::limit($event->location, 20) }}
                            </p>
                        </div>
                        @if($event->is_active)
                            <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs">Aktif</span>
                        @else
                            <span class="px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs">Nonaktif</span>
                        @endif
                    </div>
                    <div class="flex justify-between items-center mt-3 text-xs">
                        <span class="text-gray-400">
                            <i class="fas fa-calendar mr-1"></i> {{ $event->event_date ? $event->event_date->format('d/m/Y') : '-' }}
                        </span>
                        <span class="text-blue-600">
                            <i class="fas fa-users mr-1"></i> {{ $event->attendances_count ?? 0 }} peserta
                        </span>
                    </div>
                    <div class="mt-3 flex justify-end space-x-2">
                        <a href="{{ route('events.show', $event) }}" class="text-xs text-blue-600 hover:underline">Detail</a>
                        <a href="{{ route('events.qr.page', $event) }}" class="text-xs text-green-600 hover:underline">QR</a>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-6 text-gray-500">
                <i class="fas fa-calendar-times text-3xl mb-2 text-gray-300"></i>
                <p>Belum ada acara terbaru</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Aksi Cepat & Info Sistem -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-bolt text-yellow-500 mr-2"></i>
            Aksi Cepat
        </h3>
        
        <div class="grid grid-cols-2 gap-3">
            <a href="{{ route('events.create') }}" class="bg-blue-50 hover:bg-blue-100 p-4 rounded-lg text-center transition group">
                <i class="fas fa-plus text-blue-600 text-xl mb-2 group-hover:scale-110 transition"></i>
                <p class="text-xs font-medium text-blue-800">Buat Acara</p>
            </a>
            
            <a href="{{ route('events.index') }}" class="bg-green-50 hover:bg-green-100 p-4 rounded-lg text-center transition group">
                <i class="fas fa-calendar-alt text-green-600 text-xl mb-2 group-hover:scale-110 transition"></i>
                <p class="text-xs font-medium text-green-800">Kelola Acara</p>
            </a>
            
            <a href="{{ route('attendances.index') }}" class="bg-purple-50 hover:bg-purple-100 p-4 rounded-lg text-center transition group">
                <i class="fas fa-list-alt text-purple-600 text-xl mb-2 group-hover:scale-110 transition"></i>
                <p class="text-xs font-medium text-purple-800">Rekap Hadir</p>
            </a>
            
            <a href="{{ route('participants.index') }}" class="bg-orange-50 hover:bg-orange-100 p-4 rounded-lg text-center transition group">
                <i class="fas fa-users text-orange-600 text-xl mb-2 group-hover:scale-110 transition"></i>
                <p class="text-xs font-medium text-orange-800">Peserta</p>
            </a>
            
            <a href="{{ route('events.archived') }}" class="bg-yellow-50 hover:bg-yellow-100 p-4 rounded-lg text-center transition group col-span-2">
                <i class="fas fa-archive text-yellow-600 text-xl mb-2 group-hover:scale-110 transition"></i>
                <p class="text-xs font-medium text-yellow-800">Lihat Arsip Acara</p>
            </a>
        </div>
        
        <!-- Info Sistem -->
        <div class="mt-6 pt-4 border-t border-gray-100">
            <h4 class="text-sm font-medium text-gray-700 mb-3">Status Sistem</h4>
            <div class="space-y-2">
                <div class="flex justify-between text-xs">
                    <span class="text-gray-500">Database:</span>
                    <span class="text-green-600 font-medium"><i class="fas fa-circle text-xs mr-1"></i> Terhubung</span>
                </div>
                <div class="flex justify-between text-xs">
                    <span class="text-gray-500">QR Code Files:</span>
                    <span class="text-gray-700">{{ $fileCount ?? 0 }} file</span>
                </div>
                <div class="flex justify-between text-xs">
                    <span class="text-gray-500">PHP Version:</span>
                    <span class="text-gray-700">{{ phpversion() }}</span>
                </div>
                <div class="flex justify-between text-xs">
                    <span class="text-gray-500">Last Update:</span>
                    <span class="text-gray-700" id="footerTime">{{ now()->format('H:i:s') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ============================================= -->
<!-- FOOTER -->
<!-- ============================================= -->
<div class="mt-6 text-center text-xs text-gray-400">
    <p>© {{ date('Y') }} Sekretariat DPRD Kota Batam - Sistem Absensi QR Code</p>
    <p class="mt-1">Data diperbarui secara real-time • {{ now()->format('d/m/Y H:i:s') }}</p>
</div>

@endsection

@section('scripts')
<script>
// Live clock
function updateClock() {
    const now = new Date();
    const timeString = now.toLocaleTimeString('id-ID');
    const footerTime = document.getElementById('footerTime');
    if (footerTime) {
        footerTime.textContent = timeString;
    }
}
setInterval(updateClock, 1000);

// Inisialisasi
document.addEventListener('DOMContentLoaded', function() {
    updateClock();
    console.log('Dashboard siap - ' + new Date().toLocaleString());
});
</script>
@endsection