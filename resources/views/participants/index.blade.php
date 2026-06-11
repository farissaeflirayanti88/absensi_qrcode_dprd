@extends('layouts.app')

@section('title', 'Daftar Peserta - Sistem Absensi QR Code DPRD Kota Batam')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Header --}}
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">
                        <i class="fas fa-users text-blue-600 mr-3"></i>
                        Daftar Peserta
                    </h1>
                    <p class="mt-2 text-gray-600">
                        Kelola data peserta sistem absensi QR Code DPRD Kota Batam
                    </p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('attendances.export.pdf') }}" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                        <i class="fas fa-file-pdf mr-2"></i>Export Data
                    </a>
                </div>
            </div>
        </div>

        {{-- Search --}}
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <form method="GET" action="{{ route('participants.index') }}" class="flex flex-wrap gap-4">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-search mr-1"></i> Cari Peserta
                    </label>
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ request('search') }}"
                        placeholder="Nama, telepon, atau alamat..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        <i class="fas fa-search mr-2"></i>Cari
                    </button>
                    @if(request('search'))
                    <a href="{{ route('participants.index') }}" 
                       class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                        <i class="fas fa-times mr-2"></i>Reset
                    </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Statistics --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Total Peserta</p>
                        <p class="text-3xl font-bold text-blue-600">{{ $totalParticipants ?? $participants->total() }}</p>
                    </div>
                    <div class="p-4 bg-blue-100 rounded-full">
                        <i class="fas fa-users text-2xl text-blue-600"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Peserta Aktif</p>
                        <p class="text-3xl font-bold text-green-600">{{ $activeParticipants ?? $participants->where('attendances_count', '>', 0)->count() }}</p>
                    </div>
                    <div class="p-4 bg-green-100 rounded-full">
                        <i class="fas fa-user-check text-2xl text-green-600"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- System Status --}}
        <div class="bg-white rounded-lg shadow-sm p-4 mb-6 flex items-center">
            <span class="flex items-center text-green-600 font-medium mr-4">
                <i class="fas fa-circle text-xs mr-2"></i> Status Sistem: Online
            </span>
            <span class="text-gray-500 text-sm">Terakhir update: {{ now()->format('H:i') }}</span>
        </div>

        {{-- Participants Table --}}
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NO</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NAMA PESERTA</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">KONTAK</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">TOTAL KEHADIRAN</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">AKSI</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($participants as $index => $participant)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $participants->firstItem() + $index }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $participant->name }}
                                </div>
                                @if($participant->address)
                                <div class="text-xs text-gray-500">
                                    <i class="fas fa-map-marker-alt mr-1"></i>
                                    {{ Str::limit($participant->address, 30) }}
                                </div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">
                                    <i class="fas fa-phone text-gray-400 mr-2"></i>
                                    {{ $participant->phone ?? '-' }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $attendanceCount = $participant->attendances_count ?? 0;
                                    $lastAttendance = $participant->attendances()->latest()->first();
                                @endphp
                                
                                @if($attendanceCount > 0)
                                    <div class="text-sm text-gray-600">
                                        {{ $attendanceCount }} kali
                                        @if($lastAttendance)
                                            <div class="text-xs text-gray-500">
                                                Terakhir: {{ $lastAttendance->created_at->format('d M Y') }}
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <div class="text-sm text-gray-600">
                                        0 kali
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <form action="{{ route('participants.destroy', $participant->id) }}" 
                                      method="POST" 
                                      onsubmit="return confirm('Yakin hapus peserta {{ addslashes($participant->name) }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1 bg-red-600 text-white rounded-lg hover:bg-red-700 text-xs">
                                        <i class="fas fa-trash mr-1"></i> Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center text-gray-400">
                                    <i class="fas fa-users text-6xl mb-4 text-gray-300"></i>
                                    <p class="text-lg font-medium text-gray-600">Belum ada data peserta</p>
                                    <p class="text-sm text-gray-500">
                                        @if(request('search'))
                                            Tidak ditemukan peserta dengan kata kunci "{{ request('search') }}"
                                        @else
                                            Silakan tambahkan peserta baru melalui proses absensi
                                        @endif
                                    </p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($participants->hasPages())
            <div class="bg-white px-6 py-4 border-t border-gray-200">
                {{ $participants->appends(['search' => request('search')])->links() }}
            </div>
            @endif
        </div>

        {{-- Info Note --}}
        <div class="mt-4 bg-blue-50 border-l-4 border-blue-500 p-4 rounded-r-lg">
            <div class="flex items-start">
                <i class="fas fa-info-circle text-blue-500 mt-0.5 mr-3"></i>
                <div class="text-sm text-blue-700">
                    <p class="font-medium">Informasi Penghapusan Peserta:</p>
                    <p>Peserta hanya dapat dihapus jika <strong>belum pernah melakukan absensi</strong> di acara manapun. 
                       Jika peserta sudah memiliki riwayat kehadiran, data tidak dapat dihapus untuk menjaga integritas laporan.</p>
                </div>
            </div>
        </div>

        {{-- Footer Info --}}
        <div class="mt-4 text-center text-sm text-gray-500">
            <p>
                Total {{ $totalParticipants ?? $participants->total() }} peserta • 
                {{ $activeParticipants ?? $participants->where('attendances_count', '>', 0)->count() }} peserta aktif •
                {{ $totalAttendances ?? $participants->sum('attendances_count') }} total kehadiran
            </p>
        </div>

        {{-- Copyright --}}
        <div class="mt-6 text-center text-xs text-gray-400">
            © {{ date('Y') }} Sistem Absensi QR Code DPRD Kota Batam. Hak Cipta Dilindungi.
        </div>

    </div>
</div>
@endsection