@extends('layouts.app')

@section('title', 'Acara Mendatang - Sistem Absensi QR Code DPRD Kota Batam')
@section('page-title', 'Acara Mendatang')
@section('page-subtitle', 'Daftar acara yang akan datang')

@section('content')
<!-- Header dengan Statistik -->
<div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl shadow-lg p-6 mb-8 text-white">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
        <div>
            <h2 class="text-2xl font-bold mb-2 flex items-center">
                <i class="fas fa-calendar-alt mr-3"></i>
                Acara Mendatang
            </h2>
            <p class="text-blue-100">Menampilkan acara dengan jadwal mendatang</p>
        </div>
        <div class="mt-4 md:mt-0 flex space-x-4">
            <div class="bg-white bg-opacity-20 rounded-lg px-4 py-2 text-center">
                <p class="text-xs text-blue-200">Total Acara</p>
                <p class="text-2xl font-bold">{{ $totalUpcoming ?? 0 }}</p>
            </div>
            @if($nearestEvent)
            <div class="bg-white bg-opacity-20 rounded-lg px-4 py-2 text-center">
                <p class="text-xs text-blue-200">Acara Terdekat</p>
                <p class="text-sm font-bold">{{ \Carbon\Carbon::parse($nearestEvent->event_date)->diffForHumans() }}</p>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Navigation Tabs -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-1 mb-6 inline-flex">
    <a href="{{ route('events.index') }}" class="px-4 py-2 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-100 transition">
        Semua Acara
    </a>
    <a href="{{ route('events.upcoming') }}" class="px-4 py-2 rounded-lg text-sm font-medium bg-blue-600 text-white transition">
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
    <form method="GET" action="{{ route('events.upcoming') }}" class="space-y-4 md:space-y-0 md:flex md:space-x-4">
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
                <option value="date_asc" {{ request('sort') == 'date_asc' ? 'selected' : '' }}>Tanggal Terdekat</option>
                <option value="date_desc" {{ request('sort') == 'date_desc' ? 'selected' : '' }}>Tanggal Terjauh</option>
                <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Nama A-Z</option>
                <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Nama Z-A</option>
            </select>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center">
                <i class="fas fa-search mr-2"></i>
                Cari
            </button>
            @if(request()->hasAny(['search', 'status', 'sort']))
                <a href="{{ route('events.upcoming') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition flex items-center">
                    <i class="fas fa-times mr-2"></i>
                    Reset
                </a>
            @endif
        </div>
    </form>
</div>

<!-- Tombol Tambah Acara -->
<div class="mb-6 flex justify-end">
    <a href="{{ route('events.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center">
        <i class="fas fa-plus mr-2"></i>
        Tambah Acara Baru
    </a>
</div>

<!-- Tabel Acara Mendatang -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    @if($events->count() > 0)
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Acara</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
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
                        <div class="text-sm text-gray-500">{{ Str::limit($event->description, 30) }}</div>
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
                        @php
                            $daysDiff = \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($event->event_date), false);
                        @endphp
                        @if($daysDiff <= 7)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800 mt-1">
                                <i class="fas fa-clock mr-1"></i>
                                {{ $daysDiff == 0 ? 'Besok' : $daysDiff . ' hari lagi' }}
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ \Carbon\Carbon::parse($event->event_date)->format('H:i') }} WIB
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
                            
                            <!-- Tombol QR Page -->
                            <a href="{{ route('events.qr.page', $event) }}" 
                               class="text-indigo-600 hover:text-indigo-900 transition duration-200 p-2 bg-indigo-50 rounded-lg"
                               title="Halaman QR Code">
                                <i class="fas fa-qrcode"></i>
                            </a>
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
        <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada acara mendatang</h3>
        <p class="text-gray-500 mb-6">Belum ada jadwal acara untuk waktu mendatang</p>
        <a href="{{ route('events.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200">
            <i class="fas fa-plus mr-2"></i>
            Buat Acara Baru
        </a>
    </div>
    @endif
</div>

<!-- Info Tambahan -->
@if($events->count() > 0)
<div class="mt-4 bg-blue-50 border border-blue-200 rounded-lg p-4">
    <div class="flex items-start">
        <i class="fas fa-info-circle text-blue-500 mt-1 mr-3"></i>
        <div class="text-sm text-blue-800">
            <p class="font-medium mb-1">Informasi Acara Mendatang:</p>
            <ul class="list-disc list-inside space-y-1">
                <li>Acara dengan <span class="bg-yellow-100 text-yellow-800 px-2 py-0.5 rounded text-xs">label kuning</span> berarti dalam 7 hari ke depan</li>
                <li>Pastikan QR Code sudah digenerate sebelum acara dimulai</li>
                <li>Setatus "Aktif" harus diaktifkan agar peserta bisa absen</li>
            </ul>
        </div>
    </div>
</div>
@endif
@endsection