<div class="bg-blue-800 text-white w-64 min-h-screen flex flex-col">
    <!-- Logo / Brand -->
    <div class="p-4 border-b border-blue-700">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
                <i class="fas fa-qrcode text-white"></i>
            </div>
            <div>
                <h1 class="text-lg font-bold">Sistem Absensi</h1>
                <p class="text-xs text-blue-200">DPRD Kota Batam</p>
            </div>
        </div>
    </div>
    
    <!-- Navigation -->
    <nav class="mt-6">
        <!-- Dashboard -->
        <a href="{{ route('dashboard') }}" 
           class="flex items-center py-3 px-4 hover:bg-blue-700 border-l-4 border-transparent hover:border-blue-400 transition duration-200 {{ Route::currentRouteName() == 'dashboard' ? 'bg-blue-700 border-yellow-400' : '' }}">
            <i class="fas fa-tachometer-alt mr-3 w-5"></i>
            <span>Dashboard</span>
        </a>
        
        <!-- Kelola Acara -->
        <a href="{{ route('events.index') }}" 
           class="flex items-center py-3 px-4 hover:bg-blue-700 border-l-4 border-transparent hover:border-blue-400 transition duration-200 {{ request()->routeIs('events.*') ? 'bg-blue-700 border-yellow-400' : '' }}">
            <i class="fas fa-calendar-alt mr-3 w-5"></i>
            <span>Kelola Acara</span>
        </a>
        
        <!-- Rekap Kehadiran -->
        <a href="{{ route('attendances.index') }}" 
           class="flex items-center py-3 px-4 hover:bg-blue-700 border-l-4 border-transparent hover:border-blue-400 transition duration-200 {{ request()->routeIs('attendances.*') ? 'bg-blue-700 border-yellow-400' : '' }}">
            <i class="fas fa-list-alt mr-3 w-5"></i>
            <span>Rekap Kehadiran</span>
        </a>

        <!-- Daftar Peserta -->
        <a href="{{ route('participants.index') }}" 
           class="flex items-center py-3 px-4 hover:bg-blue-700 border-l-4 border-transparent hover:border-blue-400 transition duration-200 {{ request()->routeIs('participants.index') || request()->routeIs('participants.show') ? 'bg-blue-700 border-yellow-400' : '' }}">
            <i class="fas fa-users mr-3 w-5"></i>
            <span>Daftar Peserta</span>
        </a>
        
        <!-- Activity Log -->
        <a href="{{ route('activity-logs.index') }}" 
           class="flex items-center py-3 px-4 hover:bg-blue-700 border-l-4 border-transparent hover:border-blue-400 transition duration-200 {{ request()->routeIs('activity-logs.*') ? 'bg-blue-700 border-yellow-400' : '' }}">
            <i class="fas fa-history mr-3 w-5"></i>
            <span>Activity Log</span>
        </a>
    </nav>
    
    <!-- Quick Actions -->
    <div class="mt-8 px-4">
        <h3 class="text-xs font-semibold text-blue-200 uppercase tracking-wider mb-3">Aksi Cepat</h3>
        <div class="space-y-2">
            <a href="{{ route('events.create') }}" 
               class="flex items-center text-sm bg-blue-700 hover:bg-blue-600 py-2 px-3 rounded transition duration-200">
                <i class="fas fa-plus mr-2 text-xs"></i>
                <span>Buat Acara Baru</span>
            </a>
            <a href="{{ route('attendances.index') }}" 
               class="flex items-center text-sm bg-blue-700 hover:bg-blue-600 py-2 px-3 rounded transition duration-200">
                <i class="fas fa-download mr-2 text-xs"></i>
                <span>Export Data</span>
            </a>
        </div>
    </div>
    
    <!-- System Status -->
    <div class="mt-8 px-4 pb-4">
        <div class="bg-blue-900 rounded-lg p-3">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs text-blue-200">Status Sistem</span>
                <span class="flex items-center text-green-400 text-xs">
                    <i class="fas fa-circle text-xs mr-1"></i>
                    Online
                </span>
            </div>
            <div class="text-xs text-blue-300">
                <div>Terakhir update: {{ now()->format('H:i') }}</div>
            </div>
        </div>
    </div>
</div>