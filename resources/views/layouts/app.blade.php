<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Google Site Verification -->
    <meta name="google-site-verification" content="Pnb21_XACyVDPpKoj4HS9Kw65KWGOHYItUbWJFZ5uI4" />
    
    <title>@yield('title', 'Sistem Absensi QR Code DPRD Kota Batam')</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Custom Tailwind Config -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                        }
                    }
                }
            }
        }
    </script>
    
    @stack('styles')
</head>
<body class="bg-gray-100">
    <!-- Include Header -->
    @include('layouts.header')
    
    <div class="flex">
        <!-- Include Sidebar -->
        @include('layouts.sidebar')

        <!-- Main Content -->
        <div class="flex-1 flex flex-col">
            <!-- Page Content -->
            <main class="flex-1 p-6 overflow-auto">
                @if (session('success'))
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg shadow-sm">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle mr-2"></i>
                            <span>{{ session('success') }}</span>
                        </div>
                    </div>
                @endif

                @if (session('error'))
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg shadow-sm">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            <span>{{ session('error') }}</span>
                        </div>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <!-- Include Footer -->
    @include('layouts.footer')

    <!-- JavaScript -->
    <script>
        // CSRF Token untuk AJAX
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Global toggle function untuk event status
        function toggleEventStatus(eventId) {
            if (!confirm('Ubah status acara ini?')) return;
            
            fetch(`/events/${eventId}/toggle-status`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const badge = document.getElementById(`status-badge-${eventId}`);
                    const btn = document.getElementById(`toggle-btn-${eventId}`);
                    
                    if (data.is_active) {
                        badge.innerHTML = '<i class="fas fa-circle text-xs mr-1"></i> Aktif';
                        badge.className = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800';
                        btn.innerHTML = '<i class="fas fa-toggle-on"></i>';
                        btn.title = 'Nonaktifkan Acara';
                    } else {
                        badge.innerHTML = '<i class="fas fa-circle text-xs mr-1"></i> Tidak Aktif';
                        badge.className = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800';
                        btn.innerHTML = '<i class="fas fa-toggle-off"></i>';
                        btn.title = 'Aktifkan Acara';
                    }
                    
                    alert(data.message || 'Status berhasil diubah!');
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    alert(data.message || 'Gagal mengubah status');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat mengubah status');
            });
        }
    </script>
    
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fadeIn {
            animation: fadeIn 0.3s ease-out;
        }
    </style>
    
    @stack('scripts')
</body>
</html>