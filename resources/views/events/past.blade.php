@extends('layouts.app')

@section('title', 'Acara Lewat - Sistem Absensi QR Code DPRD Kota Batam')
@section('page-title', 'Acara Sudah Lewat')
@section('page-subtitle', 'Daftar acara yang sudah dilaksanakan')

@section('content')
<!-- Header dengan Statistik -->
<div class="bg-gradient-to-r from-gray-600 to-gray-800 rounded-xl shadow-lg p-6 mb-8 text-white">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
        <div>
            <h2 class="text-2xl font-bold mb-2 flex items-center">
                <i class="fas fa-history mr-3"></i>
                Acara Sudah Lewat
            </h2>
            <p class="text-gray-200">Menampilkan acara dengan jadwal sudah berlalu</p>
        </div>
        <div class="mt-4 md:mt-0 flex space-x-4">
            <div class="bg-white bg-opacity-20 rounded-lg px-4 py-2 text-center">
                <p class="text-xs text-gray-200">Total Acara</p>
                <p class="text-2xl font-bold">{{ $totalPast ?? 0 }}</p>
            </div>
            <div class="bg-white bg-opacity-20 rounded-lg px-4 py-2 text-center">
                <p class="text-xs text-gray-200">Total Kehadiran</p>
                <p class="text-2xl font-bold">{{ $totalAttendancesPast ?? 0 }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Navigation Tabs -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-1 mb-6 inline-flex">
    <a href="{{ route('events.index') }}" class="px-4 py-2 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-100 transition">
        Semua Acara
    </a>
    <a href="{{ route('events.upcoming') }}" class="px-4 py-2 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-100 transition">
        Mendatang
    </a>
    <a href="{{ route('events.past') }}" class="px-4 py-2 rounded-lg text-sm font-medium bg-blue-600 text-white transition">
        Sudah Lewat
    </a>
    <a href="{{ route('events.archived') }}" class="px-4 py-2 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-100 transition">
        Arsip
    </a>
</div>

<!-- Search and Filter -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">
    <form method="GET" action="{{ route('events.past') }}" class="space-y-4 md:space-y-0 md:flex md:space-x-4">
        <div class="flex-1">
            <input type="text" 
                   name="search" 
                   placeholder="Cari nama acara, lokasi..."
                   value="{{ request('search') }}"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div class="flex space-x-4">
            <select name="status" class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Semua Status</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
            </select>
            <select name="sort" class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="date_desc" {{ request('sort') == 'date_desc' ? 'selected' : '' }}>Terbaru</option>
                <option value="date_asc" {{ request('sort') == 'date_asc' ? 'selected' : '' }}>Terlama</option>
                <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Nama A-Z</option>
                <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Nama Z-A</option>
            </select>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center">
                <i class="fas fa-search mr-2"></i>
                Cari
            </button>
            @if(request()->hasAny(['search', 'status', 'sort']))
                <a href="{{ route('events.past') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition flex items-center">
                    <i class="fas fa-times mr-2"></i>
                    Reset
                </a>
            @endif
        </div>
    </form>
</div>

<!-- Tabel Acara Lewat -->
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
                        @if($event->attendances_count > 0)
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 mt-1">
                            <i class="fas fa-user-check mr-1"></i>
                            {{ $event->attendances_count }} peserta hadir
                        </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <div class="flex items-center">
                            <i class="fas fa-map-marker-alt mr-2 text-gray-400"></i>
                            {{ $event->location }}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">
                            {{ \Carbon\Carbon::parse($event->event_date)->format('d/m/Y') }}
                        </div>
                        <div class="text-xs text-gray-500">
                            {{ \Carbon\Carbon::parse($event->event_date)->diffForHumans() }}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <div class="flex items-center">
                            <i class="fas fa-users mr-2 text-gray-400"></i>
                            {{ $event->attendances_count ?? 0 }}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
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
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex space-x-2">
                            <!-- Tombol Detail -->
                            <a href="{{ route('events.show', $event) }}" 
                               class="text-purple-600 hover:text-purple-900 transition duration-200 p-2 bg-purple-50 rounded-lg"
                               title="Detail Acara">
                                <i class="fas fa-eye"></i>
                            </a>
                            
                            <!-- Tombol Rekap -->
                            <a href="{{ route('attendances.index', ['event_id' => $event->id]) }}" 
                               class="text-blue-600 hover:text-blue-900 transition duration-200 p-2 bg-blue-50 rounded-lg"
                               title="Lihat Rekap Kehadiran">
                                <i class="fas fa-list-alt"></i>
                            </a>
                            
                            <!-- Tombol Arsip (jika punya peserta) -->
                            @if($event->attendances_count > 0 && !$event->isArchived())
                            <form action="{{ route('events.archive', $event) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" 
                                        class="text-yellow-600 hover:text-yellow-900 transition duration-200 p-2 bg-yellow-50 rounded-lg"
                                        onclick="return confirm('Arsipkan acara ini? Data peserta akan disimpan sebagai arsip.')"
                                        title="Arsipkan Acara">
                                    <i class="fas fa-archive"></i>
                                </button>
                            </form>
                            @endif
                            
                            <!-- Tombol Hapus (jika tidak punya peserta) -->
                            @if($event->attendances_count === 0 && !$event->isArchived())
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
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    @if($events->hasPages())
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $events->withQueryString()->links() }}
    </div>
    @endif
    
    @else
    <div class="px-6 py-12 text-center">
        <div class="mx-auto w-24 h-24 mb-4 rounded-full bg-gray-100 flex items-center justify-center">
            <i class="fas fa-calendar-check text-3xl text-gray-400"></i>
        </div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada acara yang sudah lewat</h3>
        <p class="text-gray-500 mb-6">Semua acara masih berlangsung atau akan datang</p>
    </div>
    @endif
</div>

<!-- Info Tambahan -->
@if($events->count() > 0)
<div class="mt-4 bg-gray-50 border border-gray-200 rounded-lg p-4">
    <div class="flex items-start">
        <i class="fas fa-lightbulb text-yellow-500 mt-1 mr-3"></i>
        <div class="text-sm text-gray-700">
            <p class="font-medium mb-1">Rekomendasi:</p>
            <ul class="list-disc list-inside space-y-1">
                <li>Acara yang sudah selesai dapat <strong>diarsipkan</strong> untuk menyimpan data kehadiran</li>
                <li>Data kehadiran bisa diexport kapan saja melalui halaman detail</li>
                <li>Acara tanpa peserta dapat langsung dihapus</li>
            </ul>
        </div>
    </div>
</div>
@endif
@endsection