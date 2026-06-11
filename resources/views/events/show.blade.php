@extends('layouts.app')

@section('title', "Detail Acara - {$event->event_name} - Sistem Absensi QR Code DPRD Kota Batam")
@section('page-title', 'Detail Acara')
@section('page-subtitle', $event->event_name)

@section('content')
<!-- Breadcrumb -->
<div class="mb-6 flex items-center text-sm text-gray-500">
    <a href="{{ route('dashboard') }}" class="hover:text-blue-600 transition duration-200">Dashboard</a>
    <i class="fas fa-chevron-right mx-2 text-xs"></i>
    <a href="{{ route('events.index') }}" class="hover:text-blue-600 transition duration-200">Kelola Acara</a>
    <i class="fas fa-chevron-right mx-2 text-xs"></i>
    <span class="text-gray-700 font-medium">Detail Acara</span>
</div>

<div class="max-w-6xl mx-auto">
    <!-- Main Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
        <!-- Card Header -->
        <div class="bg-gradient-to-r from-blue-500 to-purple-600 px-6 py-4">
            <h3 class="text-lg font-semibold text-white flex items-center">
                <i class="fas fa-calendar-alt mr-2"></i>
                Informasi Detail Acara
            </h3>
            <p class="text-blue-100 text-sm mt-1">
                Data lengkap acara dan statistik kehadiran
            </p>
        </div>

        <!-- Card Content -->
        <div class="p-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Left Column: Event Details -->
                <div>
                    <div class="bg-blue-50 rounded-lg p-5 mb-6">
                        <h4 class="text-lg font-semibold text-blue-800 mb-4 flex items-center">
                            <i class="fas fa-info-circle mr-2"></i>
                            Informasi Acara
                        </h4>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center border-b border-blue-100 pb-3">
                                <span class="text-sm font-medium text-blue-600">Nama Acara</span>
                                <span class="font-semibold text-gray-800">{{ $event->event_name }}</span>
                            </div>
                            <div class="flex justify-between items-center border-b border-blue-100 pb-3">
                                <span class="text-sm font-medium text-blue-600">Lokasi</span>
                                <span class="font-semibold text-gray-800">{{ $event->location }}</span>
                            </div>
                            
                            {{-- ========================================= --}}
                            {{-- BAGIAN TANGGAL & WAKTU - YANG DIREVISI --}}
                            {{-- ========================================= --}}
                            <div class="flex justify-between items-center border-b border-blue-100 pb-3">
                                <span class="text-sm font-medium text-blue-600">Tanggal & Waktu</span>
                                <div class="text-right">
                                    @if($event->event_date)
                                        <span class="font-semibold text-gray-800">
                                            {{ $event->event_date->translatedFormat('l, d F Y') }}
                                        </span>
                                        <br>
                                        <span class="text-sm font-semibold {{ $event->has_custom_time ? 'text-blue-600' : 'text-gray-500' }}">
                                            <i class="fas fa-clock mr-1"></i>
                                            {{ $event->event_date->format('H:i') }} WIB
                                            
                                            {{-- Informasi default time --}}
                                            @if($event->is_default_time)
                                                <span class="text-xs text-gray-400 ml-1">(default 09:00)</span>
                                            @endif
                                            
                                            {{-- Tampilkan warning jika masih 00:00 (data lama yang belum diperbaiki) --}}
                                            @if($event->event_date->format('H:i') == '00:00')
                                                <span class="text-xs text-red-500 ml-1" title="Waktu belum diset, akan menggunakan default 09:00">
                                                    <i class="fas fa-exclamation-triangle"></i> perlu diperbaiki
                                                </span>
                                            @endif
                                        </span>
                                    @else
                                        <span class="font-semibold text-gray-800">-</span>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="flex justify-between items-center border-b border-blue-100 pb-3">
                                <span class="text-sm font-medium text-blue-600">Status Acara</span>
                                <span>
                                    @if($event->is_active)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-circle text-xs mr-1"></i>
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                            <i class="fas fa-circle text-xs mr-1"></i>
                                            Tidak Aktif
                                        </span>
                                    @endif
                                    
                                    {{-- Tampilkan badge arsip jika sudah diarsipkan --}}
                                    @if($event->isArchived())
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800 ml-2">
                                            <i class="fas fa-archive text-xs mr-1"></i>
                                            Diarsipkan
                                        </span>
                                    @endif
                                </span>
                            </div>
                            
                            {{-- Perbaikan: Menggunakan relasi creator() bukan user() --}}
                            <div class="flex justify-between items-center border-b border-blue-100 pb-3">
                                <span class="text-sm font-medium text-blue-600">Dibuat Oleh</span>
                                <span class="font-semibold text-gray-800">{{ $event->creator->name ?? 'System' }}</span>
                            </div>
                            
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-blue-600">Dibuat Tanggal</span>
                                <span class="font-semibold text-gray-800">
                                    @if($event->created_at)
                                        {{ $event->created_at->format('d/m/Y H:i') }}
                                    @else
                                        -
                                    @endif
                                </span>
                            </div>
                            
                            {{-- Tampilkan informasi event_date asli dari database untuk debugging (opsional, bisa dihapus) --}}
                            @if(config('app.debug') && auth()->user() && auth()->user()->isAdmin())
                            <div class="mt-4 p-2 bg-gray-100 rounded text-xs text-gray-600">
                                <i class="fas fa-database mr-1"></i> 
                                Raw data: {{ \Carbon\Carbon::parse($event->getRawOriginal('event_date'))->format('Y-m-d H:i:s') }}
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Description -->
                    @if($event->description)
                    <div class="bg-yellow-50 rounded-lg p-5">
                        <h4 class="text-lg font-semibold text-yellow-800 mb-4 flex items-center">
                            <i class="fas fa-align-left mr-2"></i>
                            Deskripsi Acara
                        </h4>
                        <div class="text-gray-700 leading-relaxed whitespace-pre-line">
                            {{ $event->description }}
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Right Column: Statistics -->
                <div>
                    <!-- Statistics -->
                    <div class="bg-gray-50 rounded-lg p-5 border border-gray-200">
                        <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-chart-bar mr-2 text-green-500"></i>
                            Statistik Kehadiran
                        </h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg p-4 shadow">
                                <div class="flex items-center">
                                    <div class="bg-blue-400 bg-opacity-20 p-2 rounded-lg mr-3">
                                        <i class="fas fa-users text-xl"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm opacity-90">Total Kehadiran</p>
                                        <p class="text-2xl font-bold">
                                            {{ $event->attendances()->count() }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gradient-to-r from-green-500 to-green-600 text-white rounded-lg p-4 shadow">
                                <div class="flex items-center">
                                    <div class="bg-green-400 bg-opacity-20 p-2 rounded-lg mr-3">
                                        <i class="fas fa-calendar-check text-xl"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm opacity-90">Status Acara</p>
                                        <p class="text-lg font-bold">
                                            @if($event->is_active)
                                                @if($event->event_date && $event->event_date->isFuture())
                                                    <span>Akan Datang</span>
                                                @elseif($event->event_date && $event->event_date->isToday())
                                                    <span>Sedang Berlangsung</span>
                                                @else
                                                    <span>Berlangsung</span>
                                                @endif
                                            @else
                                                <span>Selesai</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Additional Stats -->
                        @php
                            try {
                                $todayAttendanceCount = $event->attendances()
                                    ->whereDate('created_at', today())
                                    ->count();
                                
                                $last7DaysCount = $event->attendances()
                                    ->where('created_at', '>=', now()->subDays(7))
                                    ->count();
                                
                                $uniqueParticipants = $event->attendances()
                                    ->distinct('participant_id')
                                    ->count('participant_id');
                            } catch (Exception $e) {
                                $todayAttendanceCount = 0;
                                $last7DaysCount = 0;
                                $uniqueParticipants = 0;
                            }
                        @endphp
                        
                        <div class="mt-4 grid grid-cols-2 gap-3">
                            <div class="bg-white border border-gray-200 rounded-lg p-3">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-xs text-gray-500">Hari Ini</p>
                                        <p class="text-lg font-semibold text-gray-800">{{ $todayAttendanceCount }}</p>
                                    </div>
                                    <div class="text-green-500">
                                        <i class="fas fa-sun"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white border border-gray-200 rounded-lg p-3">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-xs text-gray-500">7 Hari Terakhir</p>
                                        <p class="text-lg font-semibold text-gray-800">{{ $last7DaysCount }}</p>
                                    </div>
                                    <div class="text-purple-500">
                                        <i class="fas fa-chart-line"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        {{-- Tambahan statistik peserta unik --}}
                        <div class="mt-3 p-3 bg-white border border-gray-200 rounded-lg">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs text-gray-500">Peserta Unik</p>
                                    <p class="text-lg font-semibold text-gray-800">{{ $uniqueParticipants }}</p>
                                </div>
                                <div class="text-indigo-500">
                                    <i class="fas fa-user-check"></i>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Quick Links -->
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <h5 class="text-sm font-semibold text-gray-700 mb-3">Aksi Cepat</h5>
                            <div class="space-y-3">
                                <a href="{{ route('events.qr.page', $event->id) }}" 
                                   class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2.5 px-4 rounded-lg font-medium transition duration-200 flex items-center justify-center"
                                   target="_blank">
                                    <i class="fas fa-qr-code mr-2"></i>
                                    Lihat QR Code Absensi
                                </a>
                                <a href="{{ route('attendances.index', ['event_id' => $event->id]) }}" 
                                   class="w-full bg-purple-600 hover:bg-purple-700 text-white py-2.5 px-4 rounded-lg font-medium transition duration-200 flex items-center justify-center">
                                    <i class="fas fa-list mr-2"></i>
                                    Lihat Daftar Hadir Lengkap
                                </a>
                                
                                {{-- Tombol Export khusus acara ini --}}
                                <a href="{{ route('events.export', $event->id) }}" 
                                   class="w-full bg-green-600 hover:bg-green-700 text-white py-2.5 px-4 rounded-lg font-medium transition duration-200 flex items-center justify-center">
                                    <i class="fas fa-download mr-2"></i>
                                    Export Data Kehadiran
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-8 pt-6 border-t border-gray-200">
                <div class="flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-4">
                    <a href="{{ route('events.index') }}" 
                       class="bg-gray-500 hover:bg-gray-600 text-white py-3 px-6 rounded-lg font-semibold transition duration-200 flex items-center justify-center">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Kembali ke Daftar
                    </a>
                    
                    @if(!$event->isArchived())
                        <a href="{{ route('events.edit', $event->id) }}" 
                           class="bg-yellow-500 hover:bg-yellow-600 text-white py-3 px-6 rounded-lg font-semibold transition duration-200 flex items-center justify-center">
                            <i class="fas fa-edit mr-2"></i>
                            Edit Acara
                        </a>
                    @endif
                    
                    @if($event->canBeArchived())
                        <form method="POST" action="{{ route('events.archive', $event->id) }}" class="inline-block">
                            @csrf
                            <button type="submit" 
                                    class="bg-gray-600 hover:bg-gray-700 text-white py-3 px-6 rounded-lg font-semibold transition duration-200 flex items-center justify-center"
                                    onclick="return confirm('Arsipkan acara ini? Data kehadiran akan tetap tersimpan.')">
                                <i class="fas fa-archive mr-2"></i>
                                Arsipkan Acara
                            </button>
                        </form>
                    @endif
                    
                    @if($event->canBeDeleted())
                        <form method="POST" action="{{ route('events.destroy', $event->id) }}" 
                              onsubmit="return confirmDelete(event)" 
                              class="inline-block" id="deleteForm">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="bg-red-500 hover:bg-red-600 text-white py-3 px-6 rounded-lg font-semibold transition duration-200 flex items-center justify-center">
                                <i class="fas fa-trash mr-2"></i>
                                Hapus Acara
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Attendances -->
    @php
        try {
            $recentAttendances = $event->attendances()
                ->with('participant')
                ->latest()
                ->take(5)
                ->get();
        } catch (Exception $e) {
            $recentAttendances = collect([]);
        }
        
        $attendanceCount = $event->attendances()->count();
    @endphp
    
    @if($recentAttendances->count() > 0)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-green-500 to-teal-600 px-6 py-4">
            <h3 class="text-lg font-semibold text-white flex items-center">
                <i class="fas fa-users mr-2"></i>
                Kehadiran Terbaru
            </h3>
            <p class="text-green-100 text-sm mt-1">
                {{ $recentAttendances->count() }} peserta terbaru dari total {{ $attendanceCount }} kehadiran
            </p>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Peserta</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Telepon</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu Absensi</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($recentAttendances as $index => $attendance)
                        <tr class="hover:bg-gray-50 transition duration-150">
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $index + 1 }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                        <i class="fas fa-user text-blue-600 text-xs"></i>
                                    </div>
                                    <div>
                                        <span class="font-medium text-gray-800 block">
                                            {{ $attendance->participant->name ?? 'Unknown' }}
                                        </span>
                                        @if($attendance->participant_id)
                                        <span class="text-xs text-gray-500">
                                            ID: {{ $attendance->participant_id }}
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600">
                                {{ $attendance->participant->phone ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600">
                                @if($attendance->created_at)
                                    {{ $attendance->created_at->format('d/m/Y H:i:s') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle text-xs mr-1"></i>
                                    Hadir
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($attendanceCount > 5)
            <div class="mt-4 text-center">
                <a href="{{ route('attendances.index', ['event_id' => $event->id]) }}" 
                   class="text-blue-600 hover:text-blue-800 font-medium text-sm inline-flex items-center">
                    <i class="fas fa-eye mr-1"></i>
                    Lihat semua {{ $attendanceCount }} kehadiran
                </a>
            </div>
            @endif
        </div>
    </div>
    @elseif($attendanceCount == 0)
    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6 text-center">
        <div class="text-yellow-600 text-4xl mb-4">
            <i class="fas fa-users-slash"></i>
        </div>
        <h4 class="text-lg font-semibold text-yellow-800 mb-2">Belum Ada Kehadiran</h4>
        <p class="text-yellow-700 mb-4">Belum ada peserta yang melakukan absensi pada acara ini.</p>
        <div class="flex flex-wrap justify-center gap-3">
            <a href="{{ route('events.qr.page', $event->id) }}" 
               class="inline-flex items-center bg-yellow-500 hover:bg-yellow-600 text-white py-2.5 px-5 rounded-lg font-medium transition duration-200">
                <i class="fas fa-qr-code mr-2"></i>
                Buka Halaman QR Code
            </a>
            @if(!$event->isArchived())
            <a href="{{ route('events.edit', $event->id) }}" 
               class="inline-flex items-center bg-blue-500 hover:bg-blue-600 text-white py-2.5 px-5 rounded-lg font-medium transition duration-200">
                <i class="fas fa-edit mr-2"></i>
                Edit Acara
            </a>
            @endif
        </div>
    </div>
    @endif
</div>

@push('scripts')
@if(!$event->isArchived())
<script>
    // Confirm delete function
    function confirmDelete(event) {
        event.preventDefault();
        
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Acara ini akan dihapus beserta semua data kehadiran yang terkait. Tindakan ini tidak dapat dibatalkan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('deleteForm').submit();
            }
        });
    }
</script>
@endif
@endpush
@endsection