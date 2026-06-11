@extends('layouts.app')

@section('title', 'Kelola Acara - Sistem Absensi QR Code DPRD Kota Batam')
@section('page-title', 'Kelola Acara')
@section('page-subtitle', 'Sistem Absensi QR Code DPRD Kota Batam')

@section('content')
<!-- Navigation Tabs -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-1 mb-6 inline-flex">
    <a href="{{ route('events.index') }}" class="px-4 py-2 rounded-lg text-sm font-medium bg-blue-600 text-white transition">
        Semua Acara
    </a>
    <a href="{{ route('events.upcoming') }}" class="px-4 py-2 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-100 transition">
        Mendatang
    </a>
    <a href="{{ route('events.past') }}" class="px-4 py-2 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-100 transition">
        Sudah Lewat
    </a>
    <a href="{{ route('events.archived') }}" class="px-4 py-2 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-100 transition">
        Arsip
    </a>
</div>

<!-- Search and Filter -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">
    <form method="GET" action="{{ route('events.index') }}" class="space-y-4 md:space-y-0 md:flex md:space-x-4">
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
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-200 flex items-center">
                <i class="fas fa-search mr-2"></i>
                Cari
            </button>
            @if(request()->hasAny(['search', 'status']))
                <a href="{{ route('events.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition duration-200 flex items-center">
                    <i class="fas fa-times mr-2"></i>
                    Reset
                </a>
            @endif
        </div>
    </form>
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
                        @if($event->description)
                        <div class="text-sm text-gray-500">{{ Str::limit($event->description, 50) }}</div>
                        @endif
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
                            @if($event->event_date)
                                {{ \Carbon\Carbon::parse($event->event_date)->format('d/m/Y H:i') }}
                            @else
                                -
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <div class="flex items-center">
                            <i class="fas fa-users mr-2 text-gray-400"></i>
                            {{ $event->attendances_count ?? 0 }} peserta
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
                            
                            @if($event->isArchived())
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 ml-2">
                                <i class="fas fa-archive text-xs mr-1"></i>
                                Diarsipkan
                            </span>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex space-x-2">
                            <!-- Tombol QR -->
                            <form action="{{ route('events.qr.generate', $event) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" 
                                        class="text-blue-600 hover:text-blue-900 transition duration-200 p-2 bg-blue-50 rounded-lg"
                                        title="Generate QR Code"
                                        onclick="return confirm('Generate QR Code untuk acara ini?')">
                                    <i class="fas fa-qrcode"></i>
                                </button>
                            </form>
                            
                            <!-- Tombol Edit -->
                            <a href="{{ route('events.edit', $event) }}" 
                               class="text-green-600 hover:text-green-900 transition duration-200 p-2 bg-green-50 rounded-lg"
                               title="Edit Acara">
                                <i class="fas fa-edit"></i>
                            </a>
                            
                            <!-- Tombol Detail -->
                            <a href="{{ route('events.show', $event) }}" 
                               class="text-purple-600 hover:text-purple-900 transition duration-200 p-2 bg-purple-50 rounded-lg"
                               title="Detail Acara">
                                <i class="fas fa-eye"></i>
                            </a>
                            
                            <!-- Tombol Arsip (jika punya peserta dan belum diarsip) -->
                            @if(($event->attendances_count ?? 0) > 0 && !$event->isArchived())
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
                            @if(($event->attendances_count ?? 0) === 0 && !$event->isArchived())
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
    <div class="flex justify-between items-center">
        <p class="text-sm text-gray-600">
            Menampilkan <span class="font-semibold">{{ $events->count() }}</span> dari <span class="font-semibold">{{ $events->total() }}</span> acara
        </p>
        <a href="{{ route('events.archived') }}" class="text-sm text-yellow-600 hover:text-yellow-800 flex items-center">
            <i class="fas fa-archive mr-1"></i>
            Lihat Acara yang Diarsipkan
        </a>
    </div>
</div>
@endif

<!-- Info Fitur Arsip -->
<div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
    <div class="flex items-start">
        <div class="flex-shrink-0">
            <i class="fas fa-info-circle text-yellow-400 text-xl mt-1"></i>
        </div>
        <div class="ml-3">
            <h3 class="text-sm font-medium text-yellow-800">Informasi Fitur Arsip</h3>
            <div class="mt-2 text-sm text-yellow-700">
                <ul class="list-disc list-inside space-y-1">
                    <li>Acara yang sudah memiliki data peserta <strong>tidak dapat dihapus</strong></li>
                    <li>Gunakan fitur <strong>Arsipkan</strong> untuk menyimpan data secara permanen</li>
                    <li>Data yang diarsipkan dapat dilihat di halaman <strong>Arsip Acara</strong></li>
                    <li>Data arsip bersifat <strong>read-only</strong> (hanya dapat dilihat dan diexport)</li>
                </ul>
            </div>
        </div>
    </div>
</div>

@endsection