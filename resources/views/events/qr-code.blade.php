@extends('layouts.app')

@section('title', "QR Code - {$event->event_name} - Sistem Absensi QR Code DPRD Kota Batam")
@section('page-title', 'QR Code Absensi')
@section('page-subtitle', "Sistem Absensi QR Code DPRD Kota Batam")

@push('styles')
<style>
    /* Animasi spinner */
    .fa-spinner {
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    /* Print styles */
    @media print {
        .no-print, 
        button, 
        .notification-toast,
        .xl\:col-span-2 .space-y-6 > section:last-child {
            display: none !important;
        }
        
        body {
            background: white !important;
            padding: 20px !important;
            margin: 0 !important;
        }
        
        #qrImage {
            width: 300px !important;
            height: 300px !important;
            border: 2px solid #000 !important;
        }
        
        .bg-white {
            background: white !important;
            border: 1px solid #ddd !important;
        }
        
        .shadow-sm, .shadow-lg {
            box-shadow: none !important;
        }
        
        /* Print header */
        @page {
            margin: 1cm;
        }
        
        body::before {
            content: "QR Code Absensi - {{ $event->event_name }}";
            display: block;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 20px;
            text-align: center;
        }
        
        body::after {
            content: "Dicetak pada: {{ now()->format('d/m/Y H:i') }}";
            display: block;
            font-size: 10px;
            text-align: center;
            margin-top: 20px;
            color: #666;
        }
    }

    /* Smooth transitions */
    button, a, .notification-toast, #qrImage {
        transition: all 0.3s ease;
    }

    /* Focus styles */
    button:focus-visible {
        outline: 2px solid #3b82f6;
        outline-offset: 2px;
    }

    /* QR Code loading state */
    #qrImage.opacity-50 {
        opacity: 0.5;
    }
    
    #qrLoading {
        transition: opacity 0.3s ease;
    }
</style>
@endpush

@section('content')
<!-- Breadcrumb -->
<nav aria-label="Breadcrumb" class="mb-6 no-print">
    <div class="flex items-center text-sm text-gray-500">
        <a href="{{ route('dashboard') }}" class="hover:text-blue-600 transition duration-200">Dashboard</a>
        <i class="fas fa-chevron-right mx-2 text-xs" aria-hidden="true"></i>
        <a href="{{ route('events.index') }}" class="hover:text-blue-600 transition duration-200">Kelola Acara</a>
        <i class="fas fa-chevron-right mx-2 text-xs" aria-hidden="true"></i>
        <span class="text-gray-700 font-medium" aria-current="page">QR Code - {{ $event->event_name }}</span>
    </div>
</nav>

<!-- Page Header -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
    <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
        <div class="flex-1">
            <div class="flex items-center mb-2">
                <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg flex items-center justify-center mr-3" aria-hidden="true">
                    <i class="fas fa-qrcode text-white text-lg"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">QR Code Absensi</h1>
                    <p class="text-gray-600">{{ $event->event_name }}</p>
                </div>
            </div>
            <div class="flex flex-wrap gap-4 text-sm text-gray-500">
                <div class="flex items-center">
                    <i class="fas fa-map-marker-alt mr-2 text-blue-500" aria-hidden="true"></i>
                    <span>{{ $event->location }}</span>
                </div>
                <div class="flex items-center">
                    <i class="fas fa-calendar-alt mr-2 text-green-500" aria-hidden="true"></i>
                    <span>{{ $event->event_date->format('d F Y') }}</span>
                </div>
                <div class="flex items-center">
                    <i class="fas fa-clock mr-2 text-purple-500" aria-hidden="true"></i>
                    <span>Dibuat: {{ now()->format('d/m/Y H:i') }}</span>
                </div>
                @if($event->qr_code_generated_at)
                <div class="flex items-center">
                    <i class="fas fa-sync-alt mr-2 text-orange-500" aria-hidden="true"></i>
                    <span>QR Terakhir: {{ $event->qr_code_generated_at->format('d/m/Y H:i') }}</span>
                </div>
                @endif
            </div>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('events.index') }}" 
               class="bg-gray-100 text-gray-700 px-5 py-2.5 rounded-lg hover:bg-gray-200 transition duration-200 flex items-center font-medium">
                <i class="fas fa-arrow-left mr-2" aria-hidden="true"></i>Kembali
            </a>
            <button onclick="window.print()" 
                    class="bg-blue-500 text-white px-5 py-2.5 rounded-lg hover:bg-blue-600 transition duration-200 flex items-center font-medium">
                <i class="fas fa-print mr-2" aria-hidden="true"></i>Print
            </button>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
    <!-- QR Code & Main Content -->
    <div class="xl:col-span-2 space-y-6">
        <!-- QR Code Card -->
        <section aria-labelledby="qr-code-title" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 id="qr-code-title" class="text-xl font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-qrcode mr-3 text-blue-500" aria-hidden="true"></i>
                    Kode QR Absensi
                </h2>
                <div class="flex items-center space-x-3">
                    <span id="access-badge" class="px-3 py-1 rounded-full text-xs font-medium {{ $event->is_active ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                        <i class="fas {{ $event->is_active ? 'fa-check-circle' : 'fa-exclamation-circle' }} mr-1" aria-hidden="true"></i>
                        {{ $event->is_active ? 'Bisa Absen' : 'Tidak Bisa Absen' }}
                    </span>
                    <button id="toggle-access-btn" 
                            onclick="toggleAbsensiAccess(event)"
                            class="px-4 py-2 rounded-lg text-sm font-medium transition duration-200 flex items-center {{ $event->is_active ? 'bg-yellow-500 hover:bg-yellow-600 text-white' : 'bg-green-500 hover:bg-green-600 text-white' }}"
                            aria-label="{{ $event->is_active ? 'Tutup akses absensi' : 'Buka akses absensi' }}">
                        <i class="fas {{ $event->is_active ? 'fa-lock' : 'fa-unlock' }} mr-2" aria-hidden="true"></i>
                        {{ $event->is_active ? 'Tutup Akses Absensi' : 'Buka Akses Absensi' }}
                    </button>
                </div>
            </div>
            
            <!-- QR Code Display -->
            <div class="flex flex-col items-center mb-8">
                <div class="bg-white p-6 rounded-2xl shadow-inner border-2 border-dashed border-blue-100 mb-4">
                    <div class="bg-white p-4 rounded-xl shadow-lg border border-gray-200 relative">
                        <!-- QR Code Image -->
                        <img src="{{ $qrUrl }}" 
                             alt="QR Code untuk {{ $event->event_name }}" 
                             class="w-64 h-64 mx-auto transition-opacity duration-300"
                             id="qrImage"
                             loading="eager">
                        
                        <!-- Loading overlay -->
                        <div id="qrLoading" class="hidden absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center rounded-lg">
                            <div class="text-center">
                                <i class="fas fa-spinner fa-spin text-blue-500 text-3xl mb-2"></i>
                                <p class="text-gray-600">Memperbarui QR Code...</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- QR Code Actions -->
                <div class="flex flex-wrap gap-3 justify-center">
                    <button onclick="downloadQRCode(event)" 
                            class="bg-green-500 text-white px-5 py-2.5 rounded-lg hover:bg-green-600 transition duration-200 flex items-center font-medium"
                            aria-label="Download QR Code">
                        <i class="fas fa-download mr-2" aria-hidden="true"></i>Download QR
                    </button>
                    <button onclick="refreshQRCode(event)" 
                            class="bg-blue-500 text-white px-5 py-2.5 rounded-lg hover:bg-blue-600 transition duration-200 flex items-center font-medium"
                            aria-label="Refresh QR Code"
                            id="refreshQrBtn">
                        <i class="fas fa-sync-alt mr-2" aria-hidden="true"></i>Refresh QR
                    </button>
                </div>
                
                @if($event->qr_code_generated_at)
                <p class="text-sm text-gray-500 mt-2">
                    Terakhir diperbarui: {{ $event->qr_code_generated_at->format('d/m/Y H:i') }}
                </p>
                @endif
            </div>

            <!-- URL Section -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-3 flex items-center">
                    <i class="fas fa-link mr-2 text-purple-500" aria-hidden="true"></i>
                    URL Absensi
                </h3>
                <div class="bg-gray-50 border border-gray-200 rounded-xl p-4">
                    <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                        <code class="text-sm break-all flex-1 bg-white p-3 rounded-lg border font-mono">
                            {{ $attendanceUrl }}
                        </code>
                        <button onclick="copyUrl(event)" 
                                class="bg-purple-500 text-white px-4 py-2.5 rounded-lg hover:bg-purple-600 transition duration-200 flex items-center font-medium whitespace-nowrap"
                                aria-label="Salin URL ke clipboard">
                            <i class="fas fa-copy mr-2" aria-hidden="true"></i>Copy URL
                        </button>
                    </div>
                    <div id="access-message" class="mt-3 flex items-center {{ $event->is_active ? 'text-green-600' : 'text-yellow-600' }} text-sm">
                        <i class="fas {{ $event->is_active ? 'fa-check-circle' : 'fa-exclamation-triangle' }} mr-2" aria-hidden="true"></i>
                        <span>
                            {{ $event->is_active ? 
                                'Peserta bisa melakukan absensi melalui QR Code ini' : 
                                'Peserta tidak bisa melakukan absensi saat ini' 
                            }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Instructions -->
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-5">
                <h3 class="text-lg font-semibold text-blue-800 mb-3 flex items-center">
                    <i class="fas fa-info-circle mr-2" aria-hidden="true"></i>
                    Petunjuk Penggunaan
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex items-start">
                        <div class="bg-blue-100 text-blue-600 rounded-full p-2 mr-3 mt-1" aria-hidden="true">
                            <i class="fas fa-print text-sm"></i>
                        </div>
                        <div>
                            <h4 class="font-medium text-blue-800">Cetak QR Code</h4>
                            <p class="text-sm text-blue-700">Cetak dan tempel di lokasi yang mudah diakses peserta</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="bg-blue-100 text-blue-600 rounded-full p-2 mr-3 mt-1" aria-hidden="true">
                            <i class="fas fa-toggle-on text-sm"></i>
                        </div>
                        <div>
                            <h4 class="font-medium text-blue-800">Kontrol Akses</h4>
                            <p class="text-sm text-blue-700">Atur kapan peserta bisa melakukan absensi</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="bg-blue-100 text-blue-600 rounded-full p-2 mr-3 mt-1" aria-hidden="true">
                            <i class="fas fa-mobile-alt text-sm"></i>
                        </div>
                        <div>
                            <h4 class="font-medium text-blue-800">Scan QR Code</h4>
                            <p class="text-sm text-blue-700">Peserta scan menggunakan kamera smartphone</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="bg-blue-100 text-blue-600 rounded-full p-2 mr-3 mt-1" aria-hidden="true">
                            <i class="fas fa-database text-sm"></i>
                        </div>
                        <div>
                            <h4 class="font-medium text-blue-800">Data Tersimpan</h4>
                            <p class="text-sm text-blue-700">Data kehadiran tersimpan otomatis di sistem</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Statistics Card -->
        <section aria-labelledby="statistics-title" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 id="statistics-title" class="text-xl font-semibold text-gray-800 mb-6 flex items-center">
                <i class="fas fa-chart-bar mr-3 text-green-500" aria-hidden="true"></i>
                Statistik Kehadiran
            </h2>
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
            <div id="statistics-message" class="mt-4 p-3 {{ $event->is_active ? 'bg-green-50 border border-green-200' : 'bg-yellow-50 border border-yellow-200' }} rounded-lg">
                <div class="flex items-center">
                    <i class="fas {{ $event->is_active ? 'fa-check-circle text-green-500' : 'fa-exclamation-circle text-yellow-500' }} mr-2" aria-hidden="true"></i>
                    <span class="text-sm {{ $event->is_active ? 'text-green-700' : 'text-yellow-700' }}">
                        {{ $event->is_active ? 
                            'Peserta bisa melakukan absensi - statistik akan bertambah' : 
                            'Peserta tidak bisa melakukan absensi - statistik tidak bertambah' 
                        }}
                    </span>
                </div>
            </div>
        </section>
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">
        <!-- Event Info Card -->
        <section aria-labelledby="event-status-title" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 id="event-status-title" class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-info-circle mr-2 text-blue-500" aria-hidden="true"></i>
                Status Akses Absensi
            </h2>
            <div class="space-y-4">
                <div class="flex justify-between items-center pb-3 border-b border-gray-100">
                    <span class="text-gray-600">Akses Absensi</span>
                    <div class="flex items-center space-x-2">
                        <span id="access-dot" class="w-3 h-3 rounded-full {{ $event->is_active ? 'bg-green-500' : 'bg-yellow-500' }}"></span>
                        <span id="access-text" class="font-semibold {{ $event->is_active ? 'text-green-600' : 'text-yellow-600' }}">
                            {{ $event->is_active ? 'Dibuka' : 'Ditutup' }}
                        </span>
                    </div>
                </div>
                <div class="flex justify-between items-center pb-3 border-b border-gray-100">
                    <span class="text-gray-600">Tanggal Event</span>
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
            
            <!-- Access Control Info -->
            <div id="access-info" class="mt-6 p-3 {{ $event->is_active ? 'bg-green-50 border border-green-200' : 'bg-yellow-50 border border-yellow-200' }} rounded-lg">
                <div class="flex items-start">
                    <i class="fas {{ $event->is_active ? 'fa-unlock text-green-500' : 'fa-lock text-yellow-500' }} mt-0.5 mr-2" aria-hidden="true"></i>
                    <div>
                        <h4 class="font-medium {{ $event->is_active ? 'text-green-800' : 'text-yellow-800' }} mb-1">
                            {{ $event->is_active ? 'Akses Absensi Dibuka' : 'Akses Absensi Ditutup' }}
                        </h4>
                        <p class="text-sm {{ $event->is_active ? 'text-green-700' : 'text-yellow-700' }}">
                            {{ $event->is_active ? 
                                'Peserta bisa melakukan scan QR Code untuk absensi' : 
                                'Peserta tidak bisa melakukan absensi melalui QR Code ini' 
                            }}
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Quick Actions -->
        <section aria-labelledby="quick-actions-title" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 id="quick-actions-title" class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-bolt mr-2 text-yellow-500" aria-hidden="true"></i>
                Akses Cepat
            </h2>
            <div class="space-y-3">
                <a href="{{ route('events.show', $event) }}" 
                   class="w-full bg-blue-50 text-blue-700 p-3 rounded-lg hover:bg-blue-100 transition duration-200 flex items-center font-medium">
                    <i class="fas fa-calendar-alt mr-3 text-blue-500" aria-hidden="true"></i>
                    Detail Event
                </a>
                <a href="{{ route('attendances.index', ['event_id' => $event->id]) }}" 
                   class="w-full bg-green-50 text-green-700 p-3 rounded-lg hover:bg-green-100 transition duration-200 flex items-center font-medium">
                    <i class="fas fa-list-alt mr-3 text-green-500" aria-hidden="true"></i>
                    Lihat Rekap
                </a>
                <a href="{{ route('attendances.index', ['event_id' => $event->id]) }}" 
                   class="w-full bg-purple-50 text-purple-700 p-3 rounded-lg hover:bg-purple-100 transition duration-200 flex items-center font-medium">
                    <i class="fas fa-file-export mr-3 text-purple-500" aria-hidden="true"></i>
                    Export Data
                </a>
            </div>
        </section>

        <!-- Tips Card -->
        <section aria-labelledby="tips-title" class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl shadow-sm p-6 text-white">
            <h2 id="tips-title" class="text-lg font-semibold mb-3 flex items-center">
                <i class="fas fa-lightbulb mr-2" aria-hidden="true"></i>
                Tips Kontrol Akses
            </h2>
            <ul class="space-y-2 text-sm">
                <li class="flex items-start">
                    <i class="fas fa-check-circle mr-2 mt-0.5 text-yellow-300" aria-hidden="true"></i>
                    <span>Buka akses 30 menit sebelum acara dimulai</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check-circle mr-2 mt-0.5 text-yellow-300" aria-hidden="true"></i>
                    <span>Tutup akses setelah acara selesai</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check-circle mr-2 mt-0.5 text-yellow-300" aria-hidden="true"></i>
                    <span>QR Code tetap bisa dilihat oleh peserta</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check-circle mr-2 mt-0.5 text-yellow-300" aria-hidden="true"></i>
                    <span>Fungsi QR Code tidak dimatikan, hanya aksesnya</span>
                </li>
            </ul>
        </section>
    </div>
</div>

@push('scripts')
<script>
    // === GLOBAL STATE ===
    let isLoading = false;
    let qrRefreshCount = 0;
    const EVENT_ID = {{ $event->id }};

    // === NOTIFICATION FUNCTION ===
    function showNotification(message, type = 'info') {
        // Remove old notification
        const oldNotification = document.querySelector('.notification-toast');
        if (oldNotification) {
            oldNotification.remove();
        }
        
        // Create new notification
        const notification = document.createElement('div');
        notification.className = `notification-toast fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg text-white font-medium transform transition-all duration-300`;
        
        // Set color based on type
        const colors = {
            success: 'bg-green-500',
            error: 'bg-red-500',
            warning: 'bg-yellow-500',
            info: 'bg-blue-500'
        };
        
        const icons = {
            success: 'fa-check-circle',
            error: 'fa-exclamation-circle',
            warning: 'fa-exclamation-triangle',
            info: 'fa-info-circle'
        };
        
        notification.className += ` ${colors[type] || 'bg-blue-500'}`;
        notification.style.transform = 'translateX(100%)';
        
        notification.innerHTML = `
            <div class="flex items-center">
                <i class="fas ${icons[type] || 'fa-info-circle'} mr-3" aria-hidden="true"></i>
                <span>${message}</span>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Animate in
        setTimeout(() => {
            notification.style.transform = 'translateX(0)';
        }, 10);
        
        // Auto remove after 3 seconds
        setTimeout(() => {
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }, 3000);
    }

    // === REFRESH QR CODE FUNCTION ===
    async function refreshQRCode(event) {
        if (event) event.preventDefault();
        
        const qrImage = document.getElementById('qrImage');
        const refreshBtn = document.getElementById('refreshQrBtn');
        const qrLoading = document.getElementById('qrLoading');
        
        // Show loading state
        if (qrLoading) qrLoading.classList.remove('hidden');
        qrImage.classList.add('opacity-50');
        qrRefreshCount++;
        
        if (refreshBtn) {
            const originalHTML = refreshBtn.innerHTML;
            refreshBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Memperbarui...';
            refreshBtn.disabled = true;
        }
        
        showNotification('Memperbarui QR Code...', 'info');
        
        try {
            const response = await fetch("{{ route('events.qr.refresh', $event) }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    _token: '{{ csrf_token() }}'
                })
            });
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }
            
            const data = await response.json();
            
            if (data.success && data.qr_url) {
                // Preload image before updating
                const img = new Image();
                img.onload = () => {
                    // Update QR image with new URL
                    qrImage.src = data.qr_url + '?t=' + Date.now();
                    qrImage.classList.remove('opacity-50');
                    if (qrLoading) qrLoading.classList.add('hidden');
                    
                    // Show success message
                    showNotification('QR Code berhasil diperbarui!', 'success');
                    
                    // Update last updated time
                    const now = new Date();
                    const timeStr = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
                    const dateStr = now.toLocaleDateString('id-ID');
                    document.querySelector('p.text-gray-500.mt-2').textContent = 
                        `Terakhir diperbarui: ${dateStr} ${timeStr}`;
                };
                img.onerror = () => {
                    qrImage.classList.remove('opacity-50');
                    if (qrLoading) qrLoading.classList.add('hidden');
                    showNotification('Gagal memuat QR Code baru', 'error');
                };
                img.src = data.qr_url + '?t=' + Date.now();
                
            } else {
                throw new Error(data.message || 'Gagal memperbarui QR Code');
            }
            
        } catch (error) {
            console.error('Error refreshing QR:', error);
            
            // Fallback: Simple cache busting
            const currentSrc = qrImage.src.split('?')[0];
            qrImage.src = currentSrc + '?refresh=' + Date.now();
            qrImage.classList.remove('opacity-50');
            if (qrLoading) qrLoading.classList.add('hidden');
            
            showNotification('QR Code diperbarui dengan cache busting', 'warning');
        } finally {
            if (refreshBtn) {
                refreshBtn.innerHTML = '<i class="fas fa-sync-alt mr-2" aria-hidden="true"></i>Refresh QR';
                refreshBtn.disabled = false;
            }
        }
    }

    // === TOGGLE ACCESS FUNCTION ===
    async function toggleAbsensiAccess(event) {
        if (event) event.preventDefault();
        
        if (isLoading) {
            showNotification('Sedang memproses...', 'warning');
            return;
        }
        
        const currentStatus = document.getElementById('access-text').textContent.trim();
        const isAccessOpen = currentStatus === 'Dibuka';
        const action = isAccessOpen ? 'menutup' : 'membuka';
        
        // Confirmation
        if (!confirm(`Apakah Anda yakin ingin ${action} akses absensi?\nPeserta ${isAccessOpen ? 'tidak' : 'akan'} bisa melakukan absensi.`)) {
            return;
        }
        
        // Set loading state
        isLoading = true;
        const button = document.getElementById('toggle-access-btn');
        const originalHTML = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...';
        button.disabled = true;
        
        try {
            const response = await fetch("{{ route('events.toggle-status', $event) }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    _token: '{{ csrf_token() }}'
                })
            });
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }
            
            const data = await response.json();
            
            if (data.success) {
                // Update UI
                updateAccessStatus(data.is_active);
                
                // Show success notification
                const message = data.is_active 
                    ? 'Akses absensi berhasil dibuka!' 
                    : 'Akses absensi berhasil ditutup!';
                
                showNotification(message, 'success');
                
            } else {
                throw new Error(data.message || 'Gagal mengubah akses');
            }
            
        } catch (error) {
            console.error('Error:', error);
            showNotification('Gagal mengubah akses absensi', 'error');
        } finally {
            // Reset button
            button.innerHTML = originalHTML;
            button.disabled = false;
            isLoading = false;
        }
    }

    // === UPDATE ACCESS STATUS FUNCTION ===
    function updateAccessStatus(isActive) {
        const config = {
            active: {
                badge: {
                    text: 'Bisa Absen',
                    icon: 'fa-check-circle',
                    classes: 'bg-green-100 text-green-800'
                },
                button: {
                    text: 'Tutup Akses Absensi',
                    icon: 'fa-lock',
                    classes: 'bg-yellow-500 hover:bg-yellow-600 text-white'
                },
                dot: 'bg-green-500',
                text: {
                    content: 'Dibuka',
                    classes: 'text-green-600'
                },
                message: {
                    icon: 'fa-check-circle',
                    text: 'Peserta bisa melakukan absensi melalui QR Code ini',
                    classes: 'text-green-600'
                },
                info: {
                    icon: 'fa-unlock',
                    title: 'Akses Absensi Dibuka',
                    description: 'Peserta bisa melakukan scan QR Code untuk absensi',
                    classes: 'bg-green-50 border border-green-200',
                    textColor: {
                        title: 'text-green-800',
                        description: 'text-green-700'
                    }
                },
                statistics: {
                    icon: 'fa-check-circle',
                    text: 'Peserta bisa melakukan absensi - statistik akan bertambah',
                    classes: 'bg-green-50 border border-green-200',
                    textColor: 'text-green-700'
                }
            },
            inactive: {
                badge: {
                    text: 'Tidak Bisa Absen',
                    icon: 'fa-exclamation-circle',
                    classes: 'bg-yellow-100 text-yellow-800'
                },
                button: {
                    text: 'Buka Akses Absensi',
                    icon: 'fa-unlock',
                    classes: 'bg-green-500 hover:bg-green-600 text-white'
                },
                dot: 'bg-yellow-500',
                text: {
                    content: 'Ditutup',
                    classes: 'text-yellow-600'
                },
                message: {
                    icon: 'fa-exclamation-triangle',
                    text: 'Peserta tidak bisa melakukan absensi saat ini',
                    classes: 'text-yellow-600'
                },
                info: {
                    icon: 'fa-lock',
                    title: 'Akses Absensi Ditutup',
                    description: 'Peserta tidak bisa melakukan absensi melalui QR Code ini',
                    classes: 'bg-yellow-50 border border-yellow-200',
                    textColor: {
                        title: 'text-yellow-800',
                        description: 'text-yellow-700'
                    }
                },
                statistics: {
                    icon: 'fa-exclamation-circle',
                    text: 'Peserta tidak bisa melakukan absensi - statistik tidak bertambah',
                    classes: 'bg-yellow-50 border border-yellow-200',
                    textColor: 'text-yellow-700'
                }
            }
        };
        
        const statusConfig = isActive ? config.active : config.inactive;
        
        // Update all UI elements
        const accessBadge = document.getElementById('access-badge');
        if (accessBadge) {
            accessBadge.innerHTML = `<i class="fas ${statusConfig.badge.icon} mr-1" aria-hidden="true"></i>${statusConfig.badge.text}`;
            accessBadge.className = `px-3 py-1 rounded-full text-xs font-medium ${statusConfig.badge.classes}`;
        }
        
        const button = document.getElementById('toggle-access-btn');
        if (button) {
            button.innerHTML = `<i class="fas ${statusConfig.button.icon} mr-2" aria-hidden="true"></i>${statusConfig.button.text}`;
            button.className = `px-4 py-2 rounded-lg text-sm font-medium transition duration-200 flex items-center ${statusConfig.button.classes}`;
            button.setAttribute('aria-label', statusConfig.button.text);
        }
        
        const accessDot = document.getElementById('access-dot');
        const accessText = document.getElementById('access-text');
        if (accessDot) accessDot.className = `w-3 h-3 rounded-full ${statusConfig.dot}`;
        if (accessText) {
            accessText.textContent = statusConfig.text.content;
            accessText.className = `font-semibold ${statusConfig.text.classes}`;
        }
        
        const accessMessage = document.getElementById('access-message');
        if (accessMessage) {
            accessMessage.innerHTML = `<i class="fas ${statusConfig.message.icon} mr-2" aria-hidden="true"></i><span>${statusConfig.message.text}</span>`;
            accessMessage.className = `mt-3 flex items-center ${statusConfig.message.classes} text-sm`;
        }
        
        const accessInfo = document.getElementById('access-info');
        if (accessInfo) {
            accessInfo.className = `mt-6 p-3 ${statusConfig.info.classes} rounded-lg`;
            accessInfo.innerHTML = `
                <div class="flex items-start">
                    <i class="fas ${statusConfig.info.icon} ${statusConfig.info.textColor.title} mt-0.5 mr-2" aria-hidden="true"></i>
                    <div>
                        <h4 class="font-medium ${statusConfig.info.textColor.title} mb-1">${statusConfig.info.title}</h4>
                        <p class="text-sm ${statusConfig.info.textColor.description}">${statusConfig.info.description}</p>
                    </div>
                </div>
            `;
        }
        
        const statisticsMessage = document.getElementById('statistics-message');
        if (statisticsMessage) {
            statisticsMessage.className = `mt-4 p-3 ${statusConfig.statistics.classes} rounded-lg`;
            statisticsMessage.innerHTML = `
                <div class="flex items-center">
                    <i class="fas ${statusConfig.statistics.icon} ${statusConfig.statistics.textColor} mr-2" aria-hidden="true"></i>
                    <span class="text-sm ${statusConfig.statistics.textColor}">${statusConfig.statistics.text}</span>
                </div>
            `;
        }
    }

    // === DOWNLOAD QR CODE ===
    function downloadQRCode(event) {
        if (event) event.preventDefault();
        
        try {
            const qrImage = document.getElementById('qrImage');
            const link = document.createElement('a');
            link.href = qrImage.src;
            link.download = `qr-code-absensi-{{ \Illuminate\Support\Str::slug($event->event_name) }}-{{ now()->format('Y-m-d') }}.png`;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            
            showNotification('QR Code berhasil diunduh!', 'success');
        } catch (error) {
            showNotification('Gagal mengunduh QR Code', 'error');
        }
    }

    // === COPY URL ===
    async function copyUrl(event) {
        if (event) event.preventDefault();
        
        const url = '{{ $attendanceUrl }}';
        
        try {
            if (navigator.clipboard && navigator.clipboard.writeText) {
                await navigator.clipboard.writeText(url);
                showNotification('URL berhasil disalin!', 'success');
            } else {
                // Fallback method
                const textArea = document.createElement('textarea');
                textArea.value = url;
                textArea.style.position = 'fixed';
                textArea.style.opacity = '0';
                textArea.style.left = '-999999px';
                textArea.style.top = '-999999px';
                document.body.appendChild(textArea);
                textArea.focus();
                textArea.select();
                
                const success = document.execCommand('copy');
                document.body.removeChild(textArea);
                
                if (success) {
                    showNotification('URL berhasil disalin!', 'success');
                } else {
                    throw new Error('Copy failed');
                }
            }
        } catch (error) {
            showNotification('Gagal menyalin URL', 'error');
        }
    }

    // === KEYBOARD SHORTCUTS ===
    document.addEventListener('keydown', function(e) {
        // Skip if user is typing
        if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA' || e.target.isContentEditable) {
            return;
        }
        
        // Ctrl/Cmd + R = Refresh QR
        if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'r') {
            e.preventDefault();
            refreshQRCode(e);
            return;
        }
        
        // Ctrl/Cmd + D = Download QR
        if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'd') {
            e.preventDefault();
            downloadQRCode(e);
            return;
        }
        
        // Ctrl/Cmd + T = Toggle access
        if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 't') {
            e.preventDefault();
            toggleAbsensiAccess(e);
            return;
        }
        
        // Ctrl/Cmd + C = Copy URL
        if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'c') {
            e.preventDefault();
            copyUrl(e);
            return;
        }
    });

    // === QR CODE ERROR HANDLING ===
    document.getElementById('qrImage').addEventListener('error', function() {
        console.error('QR Code gagal dimuat');
        const qrImage = document.getElementById('qrImage');
        const qrLoading = document.getElementById('qrLoading');
        
        if (qrLoading) qrLoading.classList.remove('hidden');
        
        // Try to reload after 2 seconds
        setTimeout(() => {
            const currentSrc = qrImage.src.split('?')[0];
            qrImage.src = currentSrc + '?retry=' + Date.now();
            if (qrLoading) qrLoading.classList.add('hidden');
            showNotification('Memuat ulang QR Code...', 'warning');
        }, 2000);
    });

    // === INITIALIZATION ===
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Halaman QR Code siap digunakan');
        
        // Ensure all buttons have proper type
        document.querySelectorAll('button').forEach(btn => {
            if (!btn.type) {
                btn.type = 'button';
            }
        });
        
        // Preload QR code
        const qrImage = document.getElementById('qrImage');
        const img = new Image();
        img.src = qrImage.src;
    });
</script>
@endpush
@endsection