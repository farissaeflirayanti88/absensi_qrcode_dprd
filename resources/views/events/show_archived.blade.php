@extends('layouts.app')

@section('title', "Detail Acara Arsip - {$event->event_name} - Sistem Absensi QR Code DPRD Kota Batam")
@section('page-title', 'Detail Acara Arsip')
@section('page-subtitle', $event->event_name)

@section('content')
<!-- Breadcrumb -->
<div class="mb-6 flex items-center text-sm text-gray-500">
    <a href="{{ route('dashboard') }}" class="hover:text-blue-600 transition duration-200">Dashboard</a>
    <i class="fas fa-chevron-right mx-2 text-xs"></i>
    <a href="{{ route('events.archived') }}" class="hover:text-blue-600 transition duration-200">Arsip Acara</a>
    <i class="fas fa-chevron-right mx-2 text-xs"></i>
    <span class="text-gray-700 font-medium">Detail Arsip</span>
</div>

<!-- Warning Message -->
<div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6 rounded-r-lg">
    <div class="flex">
        <div class="flex-shrink-0">
            <i class="fas fa-exclamation-triangle text-yellow-400 text-xl"></i>
        </div>
        <div class="ml-3">
            <h3 class="text-sm font-medium text-yellow-800">Status: Arsip</h3>
            <div class="mt-2 text-sm text-yellow-700">
                <p>Data ini bersifat <strong>read-only</strong>. Anda dapat melihat dan meng-export data, tetapi tidak dapat mengubah atau menghapusnya.</p>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="max-w-6xl mx-auto">
    <!-- Main Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
        <!-- Card Header dengan warna arsip -->
        <div class="bg-gradient-to-r from-yellow-500 to-amber-600 px-6 py-4">
            <h3 class="text-lg font-semibold text-white flex items-center">
                <i class="fas fa-archive mr-2"></i>
                Informasi Detail Acara (Arsip)
            </h3>
            <p class="text-yellow-100 text-sm mt-1">
                Data acara yang telah diarsipkan - Read Only
            </p>
        </div>

        <!-- Card Content -->
        <div class="p-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Left Column: Event Details -->
                <div>
                    <div class="bg-yellow-50 rounded-lg p-5 mb-6 border border-yellow-100">
                        <h4 class="text-lg font-semibold text-yellow-800 mb-4 flex items-center">
                            <i class="fas fa-info-circle mr-2"></i>
                            Informasi Acara
                        </h4>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center border-b border-yellow-100 pb-3">
                                <span class="text-sm font-medium text-yellow-600">Nama Acara</span>
                                <span class="font-semibold text-gray-800">{{ $event->event_name }}</span>
                            </div>
                            <div class="flex justify-between items-center border-b border-yellow-100 pb-3">
                                <span class="text-sm font-medium text-yellow-600">Lokasi</span>
                                <span class="font-semibold text-gray-800">{{ $event->location }}</span>
                            </div>
                            <div class="flex justify-between items-center border-b border-yellow-100 pb-3">
                                <span class="text-sm font-medium text-yellow-600">Tanggal Acara</span>
                                <span class="font-semibold text-gray-800">
                                    {{ \Carbon\Carbon::parse($event->event_date)->format('d F Y H:i') }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center border-b border-yellow-100 pb-3">
                                <span class="text-sm font-medium text-yellow-600">Status</span>
                                <span>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-archive mr-1"></i>
                                        Diarsipkan
                                    </span>
                                </span>
                            </div>
                            <div class="flex justify-between items-center border-b border-yellow-100 pb-3">
                                <span class="text-sm font-medium text-yellow-600">Dibuat Oleh</span>
                                <span class="font-semibold text-gray-800">{{ $event->creator->name ?? 'System' }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-yellow-600">Diarsipkan Pada</span>
                                <span class="font-semibold text-gray-800">
                                    {{ $event->deleted_at ? $event->deleted_at->format('d/m/Y H:i') : '-' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    @if($event->description)
                    <div class="bg-blue-50 rounded-lg p-5 border border-blue-100">
                        <h4 class="text-lg font-semibold text-blue-800 mb-4 flex items-center">
                            <i class="fas fa-align-left mr-2"></i>
                            Deskripsi Acara
                        </h4>
                        <div class="text-gray-700 leading-relaxed whitespace-pre-line">
                            {{ $event->description }}
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Right Column: Statistics & Actions -->
                <div>
                    <!-- Statistics -->
                    <div class="bg-gray-50 rounded-lg p-5 border border-gray-200 mb-6">
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
                                        <p class="text-2xl font-bold">{{ $statistics['totalAttendances'] ?? 0 }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-lg p-4 shadow">
                                <div class="flex items-center">
                                    <div class="bg-purple-400 bg-opacity-20 p-2 rounded-lg mr-3">
                                        <i class="fas fa-user-check text-xl"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm opacity-90">Peserta Unik</p>
                                        <p class="text-2xl font-bold">{{ $statistics['uniqueParticipants'] ?? 0 }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm">
                        <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-cogs mr-2 text-blue-500"></i>
                            Aksi Arsip
                        </h4>
                        <div class="space-y-3">
                            <!-- Restore Button -->
                            <form method="POST" action="{{ route('events.restore', $event->id) }}" 
                                  onsubmit="return confirmRestore()" 
                                  class="w-full">
                                @csrf
                                <button type="submit" 
                                        class="w-full bg-yellow-500 hover:bg-yellow-600 text-white py-3 px-4 rounded-lg font-semibold transition duration-200 flex items-center justify-center">
                                    <i class="fas fa-undo mr-2"></i>
                                    Restore ke Acara Aktif
                                </button>
                            </form>
                            
                            <!-- Export Button -->
                            <a href="{{ route('events.export', $event->id) }}" 
                               class="w-full bg-green-500 hover:bg-green-600 text-white py-3 px-4 rounded-lg font-semibold transition duration-200 flex items-center justify-center">
                                <i class="fas fa-file-export mr-2"></i>
                                Export Data CSV
                            </a>
                            
                            <!-- Print Button -->
                            <a href="{{ route('events.print', $event->id) }}" target="_blank"
                               class="w-full bg-blue-500 hover:bg-blue-600 text-white py-3 px-4 rounded-lg font-semibold transition duration-200 flex items-center justify-center">
                                <i class="fas fa-print mr-2"></i>
                                Cetak Laporan
                            </a>
                            
                            <!-- Back Button -->
                            <a href="{{ route('events.archived') }}" 
                               class="w-full bg-gray-500 hover:bg-gray-600 text-white py-3 px-4 rounded-lg font-semibold transition duration-200 flex items-center justify-center">
                                <i class="fas fa-arrow-left mr-2"></i>
                                Kembali ke Daftar Arsip
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Attendances -->
    @if($event->attendances()->count() > 0)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-purple-500 to-indigo-600 px-6 py-4">
            <h3 class="text-lg font-semibold text-white flex items-center">
                <i class="fas fa-users mr-2"></i>
                Daftar Peserta Hadir
            </h3>
            <p class="text-purple-100 text-sm mt-1">
                {{ $event->attendances()->count() }} peserta tercatat hadir
            </p>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Peserta</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu Absensi</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alamat</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Telepon</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($event->attendances()->with('participant')->latest()->take(10)->get() as $index => $attendance)
                        <tr class="hover:bg-gray-50 transition duration-150">
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $index + 1 }}</td>
                            <td class="px-4 py-3">
                                <span class="font-medium text-gray-800">
                                    {{ $attendance->participant->name ?? '-' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600">
                                {{ $attendance->created_at->format('H:i:s') }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600">
                                {{ Str::limit($attendance->participant->address ?? '-', 30) }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600">
                                {{ $attendance->participant->phone ?? '-' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($event->attendances()->count() > 10)
            <div class="mt-4 text-center">
                <p class="text-sm text-gray-500">
                    Menampilkan 10 dari {{ $event->attendances()->count() }} peserta
                </p>
            </div>
            @endif
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
    // Confirm restore function
    function confirmRestore() {
        return confirm('Anda akan mengembalikan acara ini ke daftar aktif.\nAcara akan memiliki status "Nonaktif" setelah direstore.\nLanjutkan?');
    }
</script>
@endpush
@endsection