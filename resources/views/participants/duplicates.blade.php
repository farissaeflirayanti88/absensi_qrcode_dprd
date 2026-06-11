@extends('layouts.app')

@section('title', 'Deteksi Duplikat Peserta - Sistem Absensi QR Code DPRD Kota Batam')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Header --}}
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">
                        <i class="fas fa-copy text-yellow-500 mr-3"></i>
                        Deteksi Duplikat Peserta
                    </h1>
                    <p class="mt-2 text-gray-600">
                        Menampilkan data peserta dengan kemungkinan duplikasi berdasarkan nama yang mirip
                    </p>
                </div>
                <div>
                    <a href="{{ route('participants.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                        <i class="fas fa-arrow-left mr-2"></i>Kembali ke Daftar Peserta
                    </a>
                </div>
            </div>
        </div>

        {{-- Statistics Duplicates --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Total Data Terduplikasi</p>
                        <p class="text-3xl font-bold text-yellow-600">{{ $totalDuplicates ?? 0 }}</p>
                    </div>
                    <div class="p-4 bg-yellow-100 rounded-full">
                        <i class="fas fa-copy text-2xl text-yellow-600"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Total Peserta Unik</p>
                        <p class="text-3xl font-bold text-green-600">{{ $uniqueParticipants ?? 0 }}</p>
                    </div>
                    <div class="p-4 bg-green-100 rounded-full">
                        <i class="fas fa-users text-2xl text-green-600"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Potensi Duplikasi</p>
                        <p class="text-3xl font-bold text-red-600">{{ $potentialDuplicates ?? 0 }}</p>
                    </div>
                    <div class="p-4 bg-red-100 rounded-full">
                        <i class="fas fa-exclamation-triangle text-2xl text-red-600"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filter Options --}}
        <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
            <form method="GET" action="{{ route('participants.duplicates') }}" id="filterForm">
                <div class="flex flex-wrap items-center gap-4">
                    <span class="text-sm font-medium text-gray-700">Filter:</span>
                    
                    <button type="submit" name="filter" value="all" 
                        class="px-3 py-1 rounded-full text-sm transition duration-200
                        {{ request('filter') == 'all' || !request('filter') ? 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        Semua Duplikat
                    </button>
                    
                    <button type="submit" name="filter" value="exact" 
                        class="px-3 py-1 rounded-full text-sm transition duration-200
                        {{ request('filter') == 'exact' ? 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        Nama Sama Persis
                    </button>
                    
                    <button type="submit" name="filter" value="similar" 
                        class="px-3 py-1 rounded-full text-sm transition duration-200
                        {{ request('filter') == 'similar' ? 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        Nama Mirip
                    </button>
                    
                    <button type="submit" name="filter" value="phone" 
                        class="px-3 py-1 rounded-full text-sm transition duration-200
                        {{ request('filter') == 'phone' ? 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        Nomor Telepon Sama
                    </button>
                    
                    <input type="hidden" name="search" value="{{ request('search') }}">
                    
                    <div class="flex-1 text-right">
                        <span class="text-sm text-gray-500">
                            <i class="fas fa-info-circle mr-1"></i>
                            Menampilkan data dengan kemiripan >80%
                        </span>
                    </div>
                </div>
            </form>
        </div>

        {{-- Search --}}
        <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
            <form method="GET" action="{{ route('participants.duplicates') }}" class="flex gap-4">
                <div class="flex-1">
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ request('search') }}"
                        placeholder="Cari berdasarkan nama atau telepon..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                    >
                </div>
                <input type="hidden" name="filter" value="{{ request('filter', 'all') }}">
                <button type="submit" class="px-6 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">
                    <i class="fas fa-search mr-2"></i>Cari
                </button>
                @if(request('search') || request('filter'))
                <a href="{{ route('participants.duplicates') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                    <i class="fas fa-times mr-2"></i>Reset
                </a>
                @endif
            </form>
        </div>

        {{-- System Status --}}
        <div class="bg-white rounded-lg shadow-sm p-4 mb-6 flex items-center">
            <span class="flex items-center text-green-600 font-medium mr-4">
                <i class="fas fa-circle text-xs mr-2"></i> Status Sistem: Online
            </span>
            <span class="text-gray-500 text-sm">Terakhir update: {{ now()->format('H:i') }}</span>
        </div>

        {{-- Duplicates Table --}}
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NO</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NAMA PESERTA</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">KONTAK</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">DUPLIKAT DENGAN</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">TINGKAT KEMIRIPAN</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">AKSI</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($duplicates ?? [] as $index => $duplicate)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $index + 1 }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $duplicate->name }}
                                </div>
                                @if($duplicate->address)
                                <div class="text-xs text-gray-500">
                                    <i class="fas fa-map-marker-alt mr-1"></i>
                                    {{ Str::limit($duplicate->address, 30) }}
                                </div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">
                                    <i class="fas fa-phone text-gray-400 mr-2"></i>
                                    {{ $duplicate->phone ?? '-' }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">
                                    <a href="#" class="text-blue-600 hover:underline">
                                        {{ $duplicate->duplicate_with_name }}
                                    </a>
                                </div>
                                <div class="text-xs text-gray-500">
                                    ID: {{ $duplicate->duplicate_with_id }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $duplicate->similarity >= 90 ? 'bg-red-100 text-red-800' : 
                                       ($duplicate->similarity >= 70 ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800') }}">
                                    {{ $duplicate->similarity }}% mirip
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex gap-2">
                                    <button onclick="showMergeModal({{ $duplicate->id }}, {{ $duplicate->duplicate_with_id }})" 
                                            class="px-3 py-1 bg-green-600 text-white rounded-lg hover:bg-green-700 text-xs">
                                        <i class="fas fa-compress-alt mr-1"></i> Gabung
                                    </button>
                                    <button onclick="showKeepModal({{ $duplicate->id }})" 
                                            class="px-3 py-1 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-xs">
                                        <i class="fas fa-check mr-1"></i> Pertahankan
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center text-gray-400">
                                    <i class="fas fa-check-circle text-6xl mb-4 text-green-400"></i>
                                    <p class="text-lg font-medium text-gray-600">Tidak Ditemukan Duplikat</p>
                                    <p class="text-sm text-gray-500">Semua data peserta sudah unik</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if(isset($duplicates) && method_exists($duplicates, 'hasPages') && $duplicates->hasPages())
            <div class="bg-white px-6 py-4 border-t border-gray-200">
                {{ $duplicates->appends(request()->query())->links() }}
            </div>
            @endif
        </div>

        {{-- Footer Info --}}
        <div class="mt-4 text-center text-sm text-gray-500">
            <p>
                Menampilkan {{ count($duplicates ?? []) }} data terduplikasi • 
                {{ $uniqueParticipants ?? 0 }} peserta unik • 
                {{ $potentialDuplicates ?? 0 }} potensi duplikasi
            </p>
        </div>

        {{-- Copyright --}}
        <div class="mt-6 text-center text-xs text-gray-400">
            © {{ date('Y') }} Sistem Absensi QR Code DPRD Kota Batam. Hak Cipta Dilindungi.
        </div>

    </div>
</div>

{{-- Merge Modal --}}
<div id="mergeModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
        <div class="p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Gabungkan Data Peserta</h3>
            <p class="text-sm text-gray-600 mb-4">
                Data yang digabungkan akan menyatukan riwayat kehadiran dan mempertahankan data terpilih.
            </p>
            
            <div class="mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                <div class="flex items-start">
                    <i class="fas fa-exclamation-triangle text-yellow-600 mt-1 mr-2"></i>
                    <span class="text-sm text-yellow-700">
                        Tindakan ini tidak dapat dibatalkan. Pastikan Anda memilih data yang benar.
                    </span>
                </div>
            </div>

            <div class="flex justify-end gap-2">
                <button onclick="closeMergeModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                    Batal
                </button>
                <button id="confirmMergeBtn" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    <i class="fas fa-compress-alt mr-2"></i>Gabungkan
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let mergeData = {
        id1: null,
        id2: null
    };

    function showMergeModal(id1, id2) {
        mergeData.id1 = id1;
        mergeData.id2 = id2;
        document.getElementById('mergeModal').classList.remove('hidden');
        document.getElementById('mergeModal').classList.add('flex');
    }

    function closeMergeModal() {
        document.getElementById('mergeModal').classList.add('hidden');
        document.getElementById('mergeModal').classList.remove('flex');
        mergeData = { id1: null, id2: null };
    }

    function showKeepModal(id) {
        if (confirm('Tandai data ini bukan duplikat?')) {
            // Implementasi logika untuk menandai bukan duplikat
            window.location.href = "{{ route('participants.duplicates') }}?ignore=" + id;
        }
    }

    document.getElementById('confirmMergeBtn').addEventListener('click', function() {
        if (mergeData.id1 && mergeData.id2) {
            // Implementasi logika penggabungan
            window.location.href = "{{ route('participants.duplicates') }}?merge1=" + mergeData.id1 + "&merge2=" + mergeData.id2;
            closeMergeModal();
        }
    });
</script>
@endpush
@endsection