@extends('layouts.app')

@section('title', 'Activity Log - Sistem Absensi DPRD Batam')

@section('content')
<div class="p-6">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-history mr-2 text-blue-600"></i>Activity Log
            </h1>
            <p class="text-sm text-gray-500 mt-1">Riwayat seluruh aktivitas dalam sistem</p>
        </div>
        {{-- Tombol Bersihkan Log Lama --}}
        <form action="{{ route('activity-logs.clear-old') }}" method="POST"
              onsubmit="return confirm('Hapus log lebih dari berapa hari?')">
            @csrf
            <div class="flex items-center gap-2">
                <input type="number" name="days" value="90" min="7" max="365"
                       class="w-20 px-2 py-1 text-sm border border-gray-300 rounded">
                <span class="text-sm text-gray-500">hari</span>
                <button type="submit"
                        class="bg-red-500 hover:bg-red-600 text-white text-sm px-3 py-1.5 rounded transition">
                    <i class="fas fa-trash-alt mr-1"></i>Bersihkan Log Lama
                </button>
            </div>
        </form>
    </div>

    {{-- Notifikasi --}}
    @if(session('success'))
        <div class="bg-green-50 border border-green-300 text-green-700 px-4 py-3 rounded mb-4 flex items-center">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        </div>
    @endif

    {{-- Statistik Ringkas --}}
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-blue-500">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Total Log</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ number_format($totalLog) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-green-500">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Hari Ini</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ number_format($todayLog) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-yellow-500">
            <p class="text-xs text-gray-500 uppercase tracking-wide">7 Hari Terakhir</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ number_format($weekLog) }}</p>
        </div>
    </div>

    {{-- Filter --}}
    <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
        <form method="GET" action="{{ route('activity-logs.index') }}"
              class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Kata Kunci</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Cari aktivitas..."
                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">User</label>
                <select name="user_id"
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua User</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Dari Tanggal</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Sampai Tanggal</label>
                <div class="flex gap-2">
                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm transition whitespace-nowrap">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </form>
        @if(request()->hasAny(['search','user_id','date_from','date_to']))
            <div class="mt-2">
                <a href="{{ route('activity-logs.index') }}"
                   class="text-xs text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times mr-1"></i>Reset Filter
                </a>
            </div>
        @endif
    </div>

    {{-- Tabel Log --}}
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 w-40">Waktu</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 w-36">User</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Aktivitas</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 w-32">IP Address</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600 w-20">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($logs as $log)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-3 text-xs text-gray-500 whitespace-nowrap">
                                {{ $log->created_at->format('d/m/Y') }}<br>
                                <span class="font-medium text-gray-700">{{ $log->created_at->format('H:i:s') }}</span>
                            </td>
                            <td class="px-4 py-3">
                                @if($log->user)
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-full bg-blue-600 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                            {{ strtoupper(substr($log->user->name, 0, 1)) }}
                                        </div>
                                        <span class="text-xs text-gray-700 font-medium">{{ $log->user->name }}</span>
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400 italic">Sistem / Publik</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-gray-800">{{ $log->activity }}</span>
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-500 font-mono">
                                {{ $log->ip_address ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-1">
                                    <a href="{{ route('activity-logs.show', $log->id) }}"
                                       class="text-blue-600 hover:text-blue-800 p-1"
                                       title="Detail">
                                        <i class="fas fa-eye text-xs"></i>
                                    </a>
                                    <form action="{{ route('activity-logs.destroy', $log->id) }}"
                                          method="POST"
                                          onsubmit="return confirm('Hapus log ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700 p-1" title="Hapus">
                                            <i class="fas fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-12 text-center text-gray-400">
                                <i class="fas fa-history text-4xl mb-3 opacity-30"></i>
                                <p class="text-sm">Tidak ada log yang ditemukan</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($logs->hasPages())
            <div class="px-4 py-3 border-t border-gray-200 bg-gray-50">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
