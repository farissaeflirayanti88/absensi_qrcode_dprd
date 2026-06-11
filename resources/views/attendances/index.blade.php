@extends('layouts.app')

@section('title', 'Rekap Kehadiran - Sistem Absensi QR Code DPRD Kota Batam')
@section('page-title', 'Rekap Kehadiran')
@section('page-subtitle', 'Data kehadiran peserta per acara')

@section('content')
<!-- Filter Section -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
    <form method="GET" action="{{ route('attendances.index') }}" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Filter Event -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Acara</label>
                <select name="event_id" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Acara</option>
                    @foreach($events as $ev)
                        <option value="{{ $ev->id }}" {{ $selectedEvent == $ev->id ? 'selected' : '' }}>
                            {{ $ev->event_name }} ({{ $ev->event_date->format('d/m/Y') }})
                        </option>
                    @endforeach
                </select>
            </div>
            
            <!-- Filter Tanggal Mulai -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
                <input type="date" name="date_from" value="{{ $dateFrom ?? '' }}" 
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <!-- Filter Tanggal Akhir -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Akhir</label>
                <input type="date" name="date_to" value="{{ $dateTo ?? '' }}" 
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <!-- Pencarian -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Cari Peserta</label>
                <input type="text" name="search" value="{{ $search ?? '' }}" 
                       placeholder="Nama / No. Telepon" 
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
        </div>
        
        <div class="flex justify-end space-x-3">
            <a href="{{ route('attendances.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition">
                <i class="fas fa-times mr-2"></i>Reset Filter
            </a>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-search mr-2"></i>Filter
            </button>
        </div>
    </form>
</div>

<!-- Export Buttons -->
<div class="flex justify-end mb-6 space-x-3">
    @if($selectedEvent)
        {{-- Tombol buka modal konfigurasi PDF (acara ini) --}}
        <button onclick="document.getElementById('modalExportPdf').classList.remove('hidden')"
                class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition flex items-center">
            <i class="fas fa-file-pdf mr-2"></i>Export PDF (Acara Ini)
        </button>
    @endif
    {{-- Export CSV langsung --}}
    <a href="{{ route('attendances.export.csv', request()->query()) }}"
       class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition flex items-center">
        <i class="fas fa-download mr-2"></i>Export CSV
    </a>
    {{-- Tombol buka modal konfigurasi PDF (semua data) --}}
    <button onclick="document.getElementById('modalExportPdf').classList.remove('hidden')"
            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center">
        <i class="fas fa-file-pdf mr-2"></i>Export PDF
    </button>
</div>

{{-- FR-09: Modal Konfigurasi Export PDF --}}
<div id="modalExportPdf"
     class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-md mx-4">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-bold text-gray-800">
                <i class="fas fa-file-pdf mr-2 text-red-500"></i>Konfigurasi Export PDF
            </h2>
            <button onclick="document.getElementById('modalExportPdf').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="formExportPdf" action="{{ route('attendances.export.pdf') }}" method="GET" target="_blank">
            {{-- Teruskan filter aktif --}}
            @if($selectedEvent)
                <input type="hidden" name="event_id" value="{{ $selectedEvent }}">
            @endif
            @if(request('date_from'))
                <input type="hidden" name="date_from" value="{{ request('date_from') }}">
            @endif
            @if(request('date_to'))
                <input type="hidden" name="date_to" value="{{ request('date_to') }}">
            @endif

            <div class="space-y-4">
                {{-- Orientasi --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Orientasi Halaman</label>
                    <div class="flex gap-4">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="orientation" value="portrait" checked
                                   class="text-blue-600">
                            <span class="text-sm text-gray-700">
                                <i class="fas fa-file mr-1"></i>Portrait
                            </span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="orientation" value="landscape"
                                   class="text-blue-600">
                            <span class="text-sm text-gray-700">
                                <i class="fas fa-file mr-1 rotate-90"></i>Landscape
                            </span>
                        </label>
                    </div>
                </div>

                {{-- Include QR Code --}}
                <div>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="include_qr" value="1"
                               class="w-4 h-4 text-blue-600 rounded">
                        <div>
                            <span class="text-sm font-medium text-gray-700">Sertakan QR Code</span>
                            <p class="text-xs text-gray-400">Tampilkan QR Code acara di header PDF</p>
                        </div>
                    </label>
                </div>

                {{-- Include Statistik --}}
                <div>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="include_stats" value="1" checked
                               class="w-4 h-4 text-blue-600 rounded">
                        <div>
                            <span class="text-sm font-medium text-gray-700">Sertakan Statistik</span>
                            <p class="text-xs text-gray-400">Total peserta, persentase kehadiran, dsb.</p>
                        </div>
                    </label>
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-100">
                <button type="button"
                        onclick="document.getElementById('modalExportPdf').classList.add('hidden')"
                        class="px-4 py-2 text-sm text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                    Batal
                </button>
                <button type="submit"
                        class="px-4 py-2 text-sm bg-red-600 hover:bg-red-700 text-white rounded-lg transition flex items-center gap-2">
                    <i class="fas fa-file-pdf"></i>Generate PDF
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4">
        <div class="flex items-center">
            <div class="bg-blue-100 rounded-lg p-3 mr-4">
                <i class="fas fa-calendar-check text-blue-600 text-xl"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500">Total Kehadiran</p>
                <p class="text-2xl font-bold text-gray-800">{{ $totalAttendances ?? 0 }}</p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4">
        <div class="flex items-center">
            <div class="bg-green-100 rounded-lg p-3 mr-4">
                <i class="fas fa-calendar-day text-green-600 text-xl"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500">Hari Ini</p>
                <p class="text-2xl font-bold text-gray-800">{{ $todayAttendances ?? 0 }}</p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4">
        <div class="flex items-center">
            <div class="bg-purple-100 rounded-lg p-3 mr-4">
                <i class="fas fa-users text-purple-600 text-xl"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500">Peserta Unik</p>
                <p class="text-2xl font-bold text-gray-800">{{ $uniqueParticipants ?? 0 }}</p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4">
        <div class="flex items-center">
            <div class="bg-yellow-100 rounded-lg p-3 mr-4">
                <i class="fas fa-clock text-yellow-600 text-xl"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500">Last Update</p>
                <p class="text-sm font-bold text-gray-800">{{ now()->format('H:i:s') }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Table -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Peserta</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Telepon</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alamat</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acara</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu Absen</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($attendances as $index => $attendance)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                        {{ $attendances->firstItem() + $index }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">
                            {{ $attendance->participant->name ?? '-' }}
                        </div>
                        @if($attendance->participant->email ?? false)
                            <div class="text-xs text-gray-500">{{ $attendance->participant->email }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                        {{ $attendance->participant->phone ?? '-' }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600 max-w-xs truncate">
                        {{ Str::limit($attendance->participant->address ?? '-', 30) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $attendance->event->event_name ?? '-' }}</div>
                        <div class="text-xs text-gray-500">{{ $attendance->event->event_date->format('d/m/Y') ?? '' }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                        {{ $attendance->created_at->format('d/m/Y H:i:s') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">
                            <i class="fas fa-check-circle mr-1"></i>Hadir
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                        <div class="mx-auto w-16 h-16 mb-4 rounded-full bg-gray-100 flex items-center justify-center">
                            <i class="fas fa-users-slash text-2xl text-gray-400"></i>
                        </div>
                        <p class="text-lg font-medium text-gray-700 mb-2">Belum ada data kehadiran</p>
                        <p class="text-sm text-gray-500 mb-4">Silakan pilih acara yang berbeda atau reset filter</p>
                        @if($selectedEvent)
                            <a href="{{ route('events.qr.page', $selectedEvent) }}" 
                               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                <i class="fas fa-qrcode mr-2"></i>Lihat QR Code Acara
                            </a>
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($attendances->hasPages())
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $attendances->withQueryString()->links() }}
    </div>
    @endif
</div>

<!-- Info Export -->
<div class="mt-4 text-sm text-gray-500 text-center">
    <i class="fas fa-info-circle mr-1"></i>
    @if($selectedEvent)
        Menampilkan data kehadiran untuk acara terpilih. 
        <a href="{{ route('attendances.export', ['event_id' => $selectedEvent]) }}" class="text-blue-600 hover:underline">
            Klik di sini untuk export PDF per acara
        </a>
    @else
        Menampilkan semua data kehadiran. Gunakan filter acara untuk melihat data per acara.
    @endif
</div>
@endsection