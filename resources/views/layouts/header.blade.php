<header class="bg-white shadow-sm border-b">
    <div class="container mx-auto px-4 py-4">
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-2">
                    <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-qrcode text-white text-lg"></i>
                    </div>
                    <span class="text-xl font-bold text-gray-800">QR Absensi</span>
                </a>
                <nav class="hidden md:flex space-x-4">
                    <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-blue-600 font-medium">Dashboard</a>
                    <a href="{{ route('events.index') }}" class="text-gray-600 hover:text-blue-600 font-medium">Acara</a>
                    <a href="{{ route('attendances.index') }}" class="text-gray-600 hover:text-blue-600 font-medium">Kehadiran</a>
                    <a href="{{ route('participants.index') }}" class="text-gray-600 hover:text-blue-600 font-medium">Peserta</a>
                </nav>
            </div>
            
            <div class="flex items-center space-x-4">
                <span class="text-gray-600">Hai, {{ auth()->user()->name }}</span>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition duration-200">
                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>