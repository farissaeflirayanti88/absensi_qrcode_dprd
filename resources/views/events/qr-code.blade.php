<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code - {{ $event->event_name }} - Sistem Absensi QR Code DPRD Kota Batam</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
</head>
<body class="bg-gray-100">
    <div class="flex">
        <!-- Sidebar -->
        <div class="bg-blue-800 text-white w-64 min-h-screen">
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
            <nav class="mt-6">
                <a href="{{ route('dashboard') }}" class="flex items-center py-3 px-4 hover:bg-blue-700 border-l-4 border-transparent hover:border-blue-400 transition duration-200">
                    <i class="fas fa-tachometer-alt mr-3"></i>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('events.index') }}" class="flex items-center py-3 px-4 hover:bg-blue-700 border-l-4 border-transparent hover:border-blue-400 transition duration-200">
                    <i class="fas fa-calendar-alt mr-3"></i>
                    <span>Kelola Acara</span>
                </a>
                <a href="{{ route('attendances.index') }}" class="flex items-center py-3 px-4 hover:bg-blue-700 border-l-4 border-transparent hover:border-blue-400 transition duration-200">
                    <i class="fas fa-list-alt mr-3"></i>
                    <span>Rekap Kehadiran</span>
                </a>
            </nav>
            
            <!-- Quick Actions -->
            <div class="mt-8 px-4">
                <h3 class="text-sm font-semibold text-blue-200 uppercase tracking-wider mb-3">Aksi Cepat</h3>
                <div class="space-y-2">
                    <a href="{{ route('events.create') }}" class="flex items-center text-sm bg-blue-700 hover:bg-blue-600 py-2 px-3 rounded transition duration-200">
                        <i class="fas fa-plus mr-2 text-xs"></i>
                        <span>Buat Acara Baru</span>
                    </a>
                    <a href="{{ route('attendances.export') }}" class="flex items-center text-sm bg-blue-700 hover:bg-blue-600 py-2 px-3 rounded transition duration-200">
                        <i class="fas fa-download mr-2 text-xs"></i>
                        <span>Export Data</span>
                    </a>
                </div>
            </div>
            
            <!-- System Status -->
            <div class="mt-8 px-4">
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

        <!-- Main Content -->
        <div class="flex-1 flex flex-col">
            <!-- Header -->
            <header class="bg-white shadow-sm border-b">
                <div class="flex justify-between items-center px-6 py-4">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-800">QR Code Absensi</h2>
                        <p class="text-sm text-gray-600">Sistem Absensi QR Code DPRD Kota Batam</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="text-right">
                            <span class="block text-sm font-medium text-gray-700">{{ auth()->user()->name ?? 'Admin' }}</span>
                            <span class="block text-xs text-gray-500">Administrator</span>
                        </div>
                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-user text-blue-600"></i>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-gray-500 hover:text-red-600 transition duration-200">
                                <i class="fas fa-sign-out-alt"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            <!-- Content -->
            <main class="flex-1 p-6 overflow-auto">
                <!-- Breadcrumb -->
                <div class="mb-6 flex items-center text-sm text-gray-500">
                    <a href="{{ route('dashboard') }}" class="hover:text-blue-600 transition duration-200">Dashboard</a>
                    <i class="fas fa-chevron-right mx-2 text-xs"></i>
                    <a href="{{ route('events.index') }}" class="hover:text-blue-600 transition duration-200">Kelola Acara</a>
                    <i class="fas fa-chevron-right mx-2 text-xs"></i>
                    <span class="text-gray-700 font-medium">QR Code - {{ $event->event_name }}</span>
                </div>

                <!-- Page Header -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
                    <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
                        <div class="flex-1">
                            <div class="flex items-center mb-2">
                                <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-qrcode text-white text-lg"></i>
                                </div>
                                <div>
                                    <h1 class="text-2xl font-bold text-gray-800">QR Code Absensi</h1>
                                    <p class="text-gray-600">{{ $event->event_name }}</p>
                                </div>
                            </div>
                            <div class="flex flex-wrap gap-4 text-sm text-gray-500">
                                <div class="flex items-center">
                                    <i class="fas fa-map-marker-alt mr-2 text-blue-500"></i>
                                    <span>{{ $event->location }}</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-calendar-alt mr-2 text-green-500"></i>
                                    <span>{{ $event->event_date->format('d F Y') }}</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-clock mr-2 text-purple-500"></i>
                                    <span>Dibuat: {{ now()->format('d/m/Y H:i') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-wrap gap-3">
                            <a href="{{ route('events.index') }}" 
                               class="bg-gray-100 text-gray-700 px-5 py-2.5 rounded-lg hover:bg-gray-200 transition duration-200 flex items-center font-medium">
                                <i class="fas fa-arrow-left mr-2"></i>Kembali
                            </a>
                            <button onclick="window.print()" 
                                    class="bg-blue-500 text-white px-5 py-2.5 rounded-lg hover:bg-blue-600 transition duration-200 flex items-center font-medium">
                                <i class="fas fa-print mr-2"></i>Print
                            </button>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                    <!-- QR Code & Main Content -->
                    <div class="xl:col-span-2 space-y-6">
                        <!-- QR Code Card -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                            <div class="flex items-center justify-between mb-6">
                                <h2 class="text-xl font-semibold text-gray-800 flex items-center">
                                    <i class="fas fa-qrcode mr-3 text-blue-500"></i>
                                    Kode QR Absensi
                                </h2>
                                <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                    Aktif
                                </span>
                            </div>
                            
                            <!-- QR Code Display -->
                            <div class="flex flex-col items-center mb-8">
                                <div class="bg-white p-6 rounded-2xl shadow-inner border-2 border-dashed border-blue-100 mb-4">
                                    <div class="bg-white p-4 rounded-xl shadow-lg border border-gray-200">
                                        <img src="{{ $qrUrl }}" 
                                             alt="QR Code untuk {{ $event->event_name }}" 
                                             class="w-64 h-64 mx-auto"
                                             id="qrImage">
                                    </div>
                                </div>
                                
                                <!-- QR Code Actions -->
                                <div class="flex flex-wrap gap-3 justify-center">
                                    <button onclick="downloadQRCode()" 
                                            class="bg-green-500 text-white px-5 py-2.5 rounded-lg hover:bg-green-600 transition duration-200 flex items-center font-medium">
                                        <i class="fas fa-download mr-2"></i>Download QR
                                    </button>
                                    <button onclick="refreshQRCode()" 
                                            class="bg-blue-500 text-white px-5 py-2.5 rounded-lg hover:bg-blue-600 transition duration-200 flex items-center font-medium">
                                        <i class="fas fa-sync-alt mr-2"></i>Refresh
                                    </button>
                                </div>
                            </div>

                            <!-- URL Section -->
                            <div class="mb-6">
                                <h3 class="text-lg font-semibold text-gray-800 mb-3 flex items-center">
                                    <i class="fas fa-link mr-2 text-purple-500"></i>
                                    URL Absensi
                                </h3>
                                <div class="bg-gray-50 border border-gray-200 rounded-xl p-4">
                                    <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                                        <code class="text-sm break-all flex-1 bg-white p-3 rounded-lg border">
                                            {{ $attendanceUrl }}
                                        </code>
                                        <button onclick="copyUrl()" 
                                                class="bg-purple-500 text-white px-4 py-2.5 rounded-lg hover:bg-purple-600 transition duration-200 flex items-center font-medium whitespace-nowrap">
                                            <i class="fas fa-copy mr-2"></i>Copy URL
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Instructions -->
                            <div class="bg-blue-50 border border-blue-200 rounded-xl p-5">
                                <h3 class="text-lg font-semibold text-blue-800 mb-3 flex items-center">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    Petunjuk Penggunaan
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="flex items-start">
                                        <div class="bg-blue-100 text-blue-600 rounded-full p-2 mr-3 mt-1">
                                            <i class="fas fa-print text-sm"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-medium text-blue-800">Cetak QR Code</h4>
                                            <p class="text-sm text-blue-700">Cetak dan tempel di lokasi yang mudah diakses peserta</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start">
                                        <div class="bg-blue-100 text-blue-600 rounded-full p-2 mr-3 mt-1">
                                            <i class="fas fa-mobile-alt text-sm"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-medium text-blue-800">Scan QR Code</h4>
                                            <p class="text-sm text-blue-700">Peserta scan menggunakan kamera smartphone</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start">
                                        <div class="bg-blue-100 text-blue-600 rounded-full p-2 mr-3 mt-1">
                                            <i class="fas fa-edit text-sm"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-medium text-blue-800">Isi Form</h4>
                                            <p class="text-sm text-blue-700">Peserta mengisi data kehadiran secara online</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start">
                                        <div class="bg-blue-100 text-blue-600 rounded-full p-2 mr-3 mt-1">
                                            <i class="fas fa-database text-sm"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-medium text-blue-800">Data Tersimpan</h4>
                                            <p class="text-sm text-blue-700">Data kehadiran tersimpan otomatis di sistem</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Statistics Card -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                            <h3 class="text-xl font-semibold text-gray-800 mb-6 flex items-center">
                                <i class="fas fa-chart-bar mr-3 text-green-500"></i>
                                Statistik Kehadiran
                            </h3>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <div class="text-center p-4 bg-green-50 rounded-lg border border-green-100">
                                    <div class="text-2xl font-bold text-green-600 mb-1">{{ $event->attendances()->count() }}</div>
                                    <div class="text-sm text-green-800 font-medium">Total Hadir</div>
                                </div>
                                <div class="text-center p-4 bg-blue-50 rounded-lg border border-blue-100">
                                    <div class="text-2xl font-bold text-blue-600 mb-1">{{ $todayCount ?? 0 }}</div>
                                    <div class="text-sm text-blue-800 font-medium">Hadir Hari Ini</div>
                                </div>
                                <div class="text-center p-4 bg-purple-50 rounded-lg border border-purple-100">
                                    <div class="text-2xl font-bold text-purple-600 mb-1">{{ $uniqueParticipants ?? 0 }}</div>
                                    <div class="text-sm text-purple-800 font-medium">Peserta Unik</div>
                                </div>
                                <div class="text-center p-4 bg-orange-50 rounded-lg border border-orange-100">
                                    <div class="text-2xl font-bold text-orange-600 mb-1">{{ $last7Days ?? 0 }}</div>
                                    <div class="text-sm text-orange-800 font-medium">7 Hari Terakhir</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="space-y-6">
                        <!-- Event Info Card -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                <i class="fas fa-info-circle mr-2 text-blue-500"></i>
                                Informasi Event
                            </h3>
                            <div class="space-y-4">
                                <div class="flex justify-between items-center pb-3 border-b border-gray-100">
                                    <span class="text-gray-600">Status Event</span>
                                    <span class="font-semibold {{ $event->is_active ? 'text-green-600' : 'text-red-600' }} flex items-center">
                                        <i class="fas fa-circle text-xs mr-1"></i>
                                        {{ $event->is_active ? 'Aktif' : 'Tidak Aktif' }}
                                    </span>
                                </div>
                                <div class="flex justify-between items-center pb-3 border-b border-gray-100">
                                    <span class="text-gray-600">Tanggal</span>
                                    <span class="font-semibold text-gray-800">{{ $event->event_date->format('d/m/Y') }}</span>
                                </div>
                                <div class="flex justify-between items-center pb-3 border-b border-gray-100">
                                    <span class="text-gray-600">Lokasi</span>
                                    <span class="font-semibold text-gray-800 text-right">{{ Str::limit($event->location, 20) }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Dibuat</span>
                                    <span class="font-semibold text-gray-800">{{ $event->created_at->format('d/m/Y') }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                <i class="fas fa-bolt mr-2 text-yellow-500"></i>
                                Akses Cepat
                            </h3>
                            <div class="space-y-3">
                                <a href="{{ route('events.show', $event) }}" 
                                   class="w-full bg-blue-50 text-blue-700 p-3 rounded-lg hover:bg-blue-100 transition duration-200 flex items-center font-medium">
                                    <i class="fas fa-calendar-alt mr-3 text-blue-500"></i>
                                    Detail Event
                                </a>
                                <a href="{{ route('attendances.index', ['event_id' => $event->id]) }}" 
                                   class="w-full bg-green-50 text-green-700 p-3 rounded-lg hover:bg-green-100 transition duration-200 flex items-center font-medium">
                                    <i class="fas fa-list-alt mr-3 text-green-500"></i>
                                    Lihat Rekap
                                </a>
                                <a href="{{ route('attendances.export', ['event_id' => $event->id]) }}" 
                                   class="w-full bg-purple-50 text-purple-700 p-3 rounded-lg hover:bg-purple-100 transition duration-200 flex items-center font-medium">
                                    <i class="fas fa-file-export mr-3 text-purple-500"></i>
                                    Export Data
                                </a>
                            </div>
                        </div>

                        <!-- Tips Card -->
                        <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl shadow-sm p-6 text-white">
                            <h3 class="text-lg font-semibold mb-3 flex items-center">
                                <i class="fas fa-lightbulb mr-2"></i>
                                Tips Optimalisasi
                            </h3>
                            <ul class="space-y-2 text-sm">
                                <li class="flex items-start">
                                    <i class="fas fa-check-circle mr-2 mt-0.5 text-yellow-300"></i>
                                    <span>Cetak QR code dengan ukuran minimal 15x15cm</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-check-circle mr-2 mt-0.5 text-yellow-300"></i>
                                    <span>Tempel di area dengan pencahayaan cukup</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-check-circle mr-2 mt-0.5 text-yellow-300"></i>
                                    <span>Pastikan koneksi internet tersedia</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-check-circle mr-2 mt-0.5 text-yellow-300"></i>
                                    <span>Test QR code sebelum acara dimulai</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        function downloadQRCode() {
            const qrImage = document.getElementById('qrImage');
            const link = document.createElement('a');
            link.href = qrImage.src;
            link.download = 'qr-code-{{ \Illuminate\Support\Str::slug($event->event_name) }}-{{ now()->format('Y-m-d') }}.png';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            
            // Show success message
            showNotification('QR Code berhasil diunduh!', 'success');
        }

        function refreshQRCode() {
            const qrImage = document.getElementById('qrImage');
            const currentSrc = qrImage.src;
            // Add timestamp to prevent caching
            qrImage.src = currentSrc.split('?')[0] + '?refresh=' + new Date().getTime();
            
            // Show loading state
            showNotification('Memperbarui QR Code...', 'info');
            
            // Reset after load
            qrImage.onload = function() {
                showNotification('QR Code berhasil diperbarui!', 'success');
            };
        }

        function copyUrl() {
            const url = '{{ $attendanceUrl }}';
            navigator.clipboard.writeText(url).then(function() {
                showNotification('URL berhasil disalin ke clipboard!', 'success');
            }, function() {
                // Fallback for older browsers
                const textArea = document.createElement('textarea');
                textArea.value = url;
                document.body.appendChild(textArea);
                textArea.select();
                document.execCommand('copy');
                document.body.removeChild(textArea);
                showNotification('URL berhasil disalin!', 'success');
            });
        }

        function showNotification(message, type) {
            // Create notification element
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg text-white font-medium transform translate-x-full transition-transform duration-300 ${
                type === 'success' ? 'bg-green-500' : 
                type === 'error' ? 'bg-red-500' : 'bg-blue-500'
            }`;
            notification.innerHTML = `
                <div class="flex items-center">
                    <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'} mr-2"></i>
                    ${message}
                </div>
            `;
            
            document.body.appendChild(notification);
            
            // Animate in
            setTimeout(() => {
                notification.classList.remove('translate-x-full');
            }, 10);
            
            // Remove after 3 seconds
            setTimeout(() => {
                notification.classList.add('translate-x-full');
                setTimeout(() => {
                    document.body.removeChild(notification);
                }, 300);
            }, 3000);
        }

        // Auto-refresh QR code every 10 minutes to ensure validity
        setInterval(() => {
            refreshQRCode();
        }, 600000);

        // Add keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Ctrl+D or Cmd+D to download
            if ((e.ctrlKey || e.metaKey) && e.key === 'd') {
                e.preventDefault();
                downloadQRCode();
            }
            // Ctrl+C or Cmd+C to copy URL when not in input
            if ((e.ctrlKey || e.metaKey) && e.key === 'c' && e.target.tagName !== 'INPUT' && e.target.tagName !== 'TEXTAREA') {
                e.preventDefault();
                copyUrl();
            }
        });
    </script>

    <!-- Print Styles -->
    <style>
        @media print {
            body { 
                background: white !important; 
                padding: 0 !important;
                margin: 0 !important;
                font-size: 12pt;
            }
            .bg-gray-100 { background: white !important; }
            .shadow-sm, .shadow-lg { 
                box-shadow: none !important; 
                border: 1px solid #ddd !important; 
            }
            button, .flex-wrap, .xl\:col-span-2 .space-y-6 > div:last-child,
            .sidebar, .header, .breadcrumb { 
                display: none !important; 
            }
            .max-w-6xl { max-width: 100% !important; }
            .grid-cols-1 { grid-template-columns: 1fr !important; }
            .gap-6 { gap: 1rem !important; }
            .p-6 { padding: 1rem !important; }
            .text-2xl { font-size: 1.5rem !important; }
            .text-xl { font-size: 1.25rem !important; }
            
            /* Ensure QR code prints properly */
            #qrImage {
                width: 300px !important;
                height: 300px !important;
            }
            
            /* Hide sidebar in print */
            .xl\:col-span-3 > div:last-child {
                display: none !important;
            }
            
            /* Show only QR code card in print */
            .xl\:col-span-2 {
                width: 100% !important;
            }
        }
    </style>
</body>
</html>