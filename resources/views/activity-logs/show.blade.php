@extends('layouts.app')

@section('title', 'Detail Log - Sistem Absensi DPRD Batam')

@section('content')
<div class="p-6 max-w-2xl">

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('activity-logs.index') }}"
           class="text-gray-500 hover:text-gray-700 transition">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="text-xl font-bold text-gray-800">
            <i class="fas fa-info-circle mr-2 text-blue-600"></i>Detail Log #{{ $activityLog->id }}
        </h1>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 space-y-4">

        <div class="grid grid-cols-3 gap-1 border-b pb-3">
            <span class="text-sm text-gray-500 font-medium">ID Log</span>
            <span class="text-sm text-gray-800 col-span-2 font-mono">#{{ $activityLog->id }}</span>
        </div>

        <div class="grid grid-cols-3 gap-1 border-b pb-3">
            <span class="text-sm text-gray-500 font-medium">Waktu</span>
            <span class="text-sm text-gray-800 col-span-2">
                {{ $activityLog->created_at->format('d/m/Y H:i:s') }}
                <span class="text-gray-400 ml-1">({{ $activityLog->created_at->diffForHumans() }})</span>
            </span>
        </div>

        <div class="grid grid-cols-3 gap-1 border-b pb-3">
            <span class="text-sm text-gray-500 font-medium">User</span>
            <div class="col-span-2">
                @if($activityLog->user)
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-white text-xs font-bold">
                            {{ strtoupper(substr($activityLog->user->name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-800">{{ $activityLog->user->name }}</p>
                            <p class="text-xs text-gray-500">{{ $activityLog->user->email }}</p>
                        </div>
                    </div>
                @else
                    <span class="text-sm text-gray-400 italic">Sistem / Publik (tidak ada user)</span>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-3 gap-1 border-b pb-3">
            <span class="text-sm text-gray-500 font-medium">Aktivitas</span>
            <span class="text-sm text-gray-800 col-span-2">{{ $activityLog->activity }}</span>
        </div>

        <div class="grid grid-cols-3 gap-1 border-b pb-3">
            <span class="text-sm text-gray-500 font-medium">IP Address</span>
            <span class="text-sm text-gray-800 col-span-2 font-mono">
                {{ $activityLog->ip_address ?? '-' }}
            </span>
        </div>

        <div class="grid grid-cols-3 gap-1">
            <span class="text-sm text-gray-500 font-medium">Device / Browser</span>
            <span class="text-sm text-gray-800 col-span-2 break-all">
                {{ $activityLog->user_agent ?? '-' }}
            </span>
        </div>

    </div>

    <div class="mt-4 flex gap-3">
        <a href="{{ route('activity-logs.index') }}"
           class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm transition">
            <i class="fas fa-arrow-left mr-1"></i>Kembali
        </a>
        <form action="{{ route('activity-logs.destroy', $activityLog->id) }}"
              method="POST" onsubmit="return confirm('Hapus log ini?')">
            @csrf @method('DELETE')
            <button type="submit"
                    class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm transition">
                <i class="fas fa-trash mr-1"></i>Hapus Log Ini
            </button>
        </form>
    </div>

</div>
@endsection
