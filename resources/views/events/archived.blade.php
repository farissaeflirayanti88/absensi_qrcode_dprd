{{-- resources/views/events/archived.blade.php --}}
@extends('layouts.app')

@section('title', 'Arsip Acara - Sistem Absensi QR Code DPRD Kota Batam')
@section('page-title', 'Daftar Acara Arsip')
@section('page-subtitle', 'Menampilkan semua acara yang telah diarsipkan')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-6">
        <div class="mb-4 md:mb-0">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800">
                <i class="fas fa-archive text-yellow-500 mr-2"></i>
                Arsip Acara
            </h1>
            <p class="text-gray-600 mt-2">
                Menampilkan semua acara yang telah diarsipkan.
            </p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('events.index') }}" 
               class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-200 flex items-center">
                <i class="fas fa-calendar-alt mr-2"></i>
                Acara Aktif
            </a>
        </div>
    </div>

    <!-- Warning Alert -->
    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6 rounded-r-lg">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-yellow-400 text-xl"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-yellow-800">Informasi</h3>
                <div class="mt-2 text-sm text-yellow-700">
                    <p>Data arsip bersifat <strong>read-only</strong>. Anda dapat melihat dan meng-export data, tetapi tidak dapat mengubah atau menghapusnya.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-800">
                    <i class="fas fa-list text-gray-500 mr-2"></i>
                    Daftar Acara Arsip
                </h3>
                <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-sm">
                    {{ $archivedEvents->total() }} acara diarsipkan
                </span>
            </div>
        </div>
        
        @if($archivedEvents->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Acara</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Peserta</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Diarsipkan Pada</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($archivedEvents as $event)
                    <tr class="hover:bg-gray-50 transition duration-150">
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $event->event_name }}</div>
                            <div class="text-xs text-gray-500 mt-1">
                                ID: {{ $event->id }}
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ \Carbon\Carbon::parse($event->event_date)->format('d/m/Y') }}
                            <div class="text-xs text-gray-500">
                                {{ \Carbon\Carbon::parse($event->event_date)->format('H:i') }} WIB
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $event->location }}
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">
                                <i class="fas fa-users mr-1"></i>
                                {{ $event->attendances_count }} peserta
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            @if($event->deleted_at)
                                <div class="font-medium">{{ $event->deleted_at->format('d/m/Y') }}</div>
                                <div class="text-xs text-gray-500">{{ $event->deleted_at->format('H:i') }}</div>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <div class="flex items-center space-x-2">
                                <!-- Detail Button -->
                                <a href="{{ route('events.show.archived', $event->id) }}" 
                                   class="text-blue-600 hover:text-blue-900 transition duration-200 flex items-center"
                                   title="Lihat Detail">
                                    <i class="fas fa-eye mr-1"></i> Detail
                                </a>
                                
                                <!-- Restore Button -->
                                <form action="{{ route('events.restore', $event->id) }}" 
                                      method="POST" 
                                      class="inline-block"
                                      onsubmit="return confirm('Apakah Anda yakin ingin restore acara ini?\nAcara akan dikembalikan ke daftar aktif dengan status Nonaktif.')">
                                    @csrf
                                    <button type="submit" 
                                            class="text-green-600 hover:text-green-900 transition duration-200 flex items-center"
                                            title="Restore ke Acara Aktif">
                                        <i class="fas fa-undo mr-1"></i> Restore
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($archivedEvents->hasPages())
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-600">
                    Menampilkan {{ $archivedEvents->firstItem() }} - {{ $archivedEvents->lastItem() }} dari {{ $archivedEvents->total() }} acara
                </div>
                <div>
                    {{ $archivedEvents->links() }}
                </div>
            </div>
        </div>
        @endif
        @else
        <div class="text-center py-12">
            <div class="mx-auto w-20 h-20 mb-4 rounded-full bg-gray-100 flex items-center justify-center">
                <i class="fas fa-archive text-2xl text-gray-400"></i>
            </div>
            <h4 class="text-lg font-medium text-gray-700 mb-2">Belum ada arsip acara</h4>
            <p class="text-gray-500 mb-4">Semua acara masih dalam daftar aktif.</p>
            <a href="{{ route('events.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200">
                <i class="fas fa-calendar-alt mr-2"></i>
                Lihat Acara Aktif
            </a>
        </div>
        @endif
    </div>

    <!-- Footer Info -->
    <div class="mt-6 bg-gray-50 border border-gray-200 rounded-lg p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="text-center md:text-left">
                <h4 class="font-medium text-gray-700 mb-2 flex items-center justify-center md:justify-start">
                    <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                    Informasi
                </h4>
                <p class="text-sm text-gray-600">
                    Data arsip akan disimpan untuk keperluan pelaporan dan audit.
                </p>
            </div>
            
            <div class="text-center md:text-left">
                <h4 class="font-medium text-gray-700 mb-2 flex items-center justify-center md:justify-start">
                    <i class="fas fa-shield-alt text-green-500 mr-2"></i>
                    Keamanan Data
                </h4>
                <p class="text-sm text-gray-600">
                    Data terlindungi dari perubahan dan penghapusan.
                </p>
            </div>
            
            <div class="text-center md:text-left">
                <h4 class="font-medium text-gray-700 mb-2 flex items-center justify-center md:justify-start">
                    <i class="fas fa-database text-purple-500 mr-2"></i>
                    Status Sistem
                </h4>
                <p class="text-sm text-gray-600">
                    {{ now()->format('d/m/Y H:i') }} • {{ $archivedEvents->total() }} data arsip
                </p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Confirm sebelum restore
    document.addEventListener('DOMContentLoaded', function() {
        const restoreForms = document.querySelectorAll('form[action*="restore"]');
        restoreForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                if (!confirm('Anda akan mengembalikan acara ini ke daftar aktif.\nAcara akan memiliki status "Nonaktif" setelah direstore.\nLanjutkan?')) {
                    e.preventDefault();
                }
            });
        });
    });
</script>
@endpush
@endsection