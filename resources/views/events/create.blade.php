@extends('layouts.app')

@section('title', 'Tambah Acara - Sistem Absensi QR Code DPRD Kota Batam')
@section('page-title', 'Tambah Acara Baru')
@section('page-subtitle', 'Sistem Absensi QR Code DPRD Kota Batam')

@section('content')
<!-- Breadcrumb -->
<div class="mb-6 flex items-center text-sm text-gray-500">
    <a href="{{ route('dashboard') }}" class="hover:text-blue-600 transition duration-200">Dashboard</a>
    <i class="fas fa-chevron-right mx-2 text-xs"></i>
    <a href="{{ route('events.index') }}" class="hover:text-blue-600 transition duration-200">Kelola Acara</a>
    <i class="fas fa-chevron-right mx-2 text-xs"></i>
    <span class="text-gray-700 font-medium">Tambah Acara Baru</span>
</div>

<!-- Debug Section -->
@if(session('error_debug'))
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg">
        <div class="flex items-center">
            <i class="fas fa-bug mr-2"></i>
            <span class="font-medium">Debug Error:</span>
        </div>
        <pre class="mt-2 text-sm whitespace-pre-wrap">{{ session('error_debug') }}</pre>
    </div>
@endif

<!-- Error Messages -->
@if ($errors->any())
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg shadow-sm">
        <div class="flex items-center">
            <i class="fas fa-exclamation-circle mr-2"></i>
            <span class="font-medium">Terjadi kesalahan validasi:</span>
        </div>
        <ul class="mt-2 ml-6 list-disc text-sm">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<!-- Success Message -->
@if (session('success_message'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg shadow-sm">
        <div class="flex items-center">
            <i class="fas fa-check-circle mr-2"></i>
            <span class="font-medium">{{ session('success_message') }}</span>
        </div>
    </div>
@endif

<!-- Form Section -->
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <!-- Form Header -->
        <div class="bg-gradient-to-r from-blue-500 to-purple-600 px-6 py-4">
            <h3 class="text-lg font-semibold text-white flex items-center">
                <i class="fas fa-calendar-plus mr-2"></i>
                Form Tambah Acara Baru
            </h3>
            <p class="text-blue-100 text-sm mt-1">
                Isi informasi acara untuk membuat QR Code absensi
            </p>
        </div>

        <!-- Form Content -->
        <div class="p-6">
            <form method="POST" action="{{ route('events.store') }}" id="eventForm" class="space-y-6">
                @csrf
                
                <!-- Nama Acara -->
                <div>
                    <label for="event_name" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                        <i class="fas fa-heading mr-2 text-blue-500"></i>
                        Nama Acara *
                    </label>
                    <input type="text" 
                           id="event_name" 
                           name="event_name" 
                           class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                           placeholder="Masukkan nama acara reses"
                           required 
                           value="{{ old('event_name') }}"
                           oninput="validateField(this, 'nama')">
                    <div id="event_name_error" class="text-red-500 text-sm mt-2 hidden">
                        <i class="fas fa-exclamation-circle mr-1"></i>
                        <span></span>
                    </div>
                </div>

                <!-- Lokasi -->
                <div>
                    <label for="location" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                        <i class="fas fa-map-marker-alt mr-2 text-green-500"></i>
                        Lokasi *
                    </label>
                    <input type="text" 
                           id="location" 
                           name="location" 
                           class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                           placeholder="Masukkan lokasi acara"
                           required 
                           value="{{ old('location') }}"
                           oninput="validateField(this, 'lokasi')">
                    <div id="location_error" class="text-red-500 text-sm mt-2 hidden">
                        <i class="fas fa-exclamation-circle mr-1"></i>
                        <span></span>
                    </div>
                </div>

                <!-- Tanggal dan Waktu Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Tanggal Acara -->
                    <div>
                        <label for="event_date" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                            <i class="fas fa-calendar-alt mr-2 text-purple-500"></i>
                            Tanggal Acara *
                        </label>
                        <input type="date" 
                               id="event_date" 
                               name="event_date" 
                               class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                               required 
                               value="{{ old('event_date', date('Y-m-d')) }}"
                               min="{{ date('Y-m-d') }}">
                        <div id="event_date_error" class="text-red-500 text-sm mt-2 hidden">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            <span></span>
                        </div>
                    </div>

                    <!-- Waktu Acara -->
                    <div>
                        <label for="event_time" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                            <i class="fas fa-clock mr-2 text-yellow-500"></i>
                            Waktu Acara
                        </label>
                        <input type="time" 
                               id="event_time" 
                               name="event_time" 
                               class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                               value="{{ old('event_time', '09:00') }}">
                        <p class="text-gray-500 text-xs mt-2">
                            <i class="fas fa-info-circle mr-1"></i>
                            Waktu mulai acara (format: HH:MM)
                        </p>
                    </div>
                </div>

                <!-- Deskripsi -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                        <i class="fas fa-align-left mr-2 text-orange-500"></i>
                        Deskripsi Acara
                    </label>
                    <textarea id="description" 
                              name="description" 
                              rows="4"
                              class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 resize-none"
                              placeholder="Tambahkan deskripsi atau informasi tambahan tentang acara ini">{{ old('description') }}</textarea>
                    <p class="text-gray-500 text-xs mt-2">
                        <i class="fas fa-info-circle mr-1"></i>
                        Deskripsi opsional untuk memberikan informasi tambahan kepada peserta
                    </p>
                </div>

                <!-- Status Acara -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                        <i class="fas fa-toggle-on mr-2 text-red-500"></i>
                        Status Acara *
                    </label>
                    <div class="flex items-center space-x-6">
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="radio" 
                                   name="is_active" 
                                   value="1" 
                                   class="w-4 h-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                   {{ old('is_active', '1') == '1' ? 'checked' : '' }}
                                   required>
                            <span class="ml-2 text-sm text-gray-700 font-medium">Aktif</span>
                        </label>
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="radio" 
                                   name="is_active" 
                                   value="0"
                                   class="w-4 h-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                   {{ old('is_active') == '0' ? 'checked' : '' }}
                                   required>
                            <span class="ml-2 text-sm text-gray-700 font-medium">Tidak Aktif</span>
                        </label>
                    </div>
                    <div id="is_active_error" class="text-red-500 text-sm mt-2 hidden">
                        <i class="fas fa-exclamation-circle mr-1"></i>
                        <span>Pilih status acara</span>
                    </div>
                    <p class="text-gray-500 text-xs mt-2">
                        <i class="fas fa-info-circle mr-1"></i>
                        Acara aktif akan dapat diakses peserta untuk absensi
                    </p>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-4 pt-6 border-t border-gray-200">
                    <a href="{{ route('events.index') }}" 
                       class="bg-gray-500 text-white px-6 py-3 rounded-lg hover:bg-gray-600 transition duration-200 flex items-center justify-center order-2 sm:order-1">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Kembali
                    </a>
                    <button type="submit" 
                            id="submitBtn"
                            class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition duration-200 flex items-center justify-center order-1 sm:order-2 disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fas fa-save mr-2"></i>
                        <span id="btnText">Simpan Acara</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Info Card -->
    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-xl p-6">
        <h4 class="text-lg font-semibold text-blue-800 mb-3 flex items-center">
            <i class="fas fa-lightbulb mr-2 text-yellow-500"></i>
            Tips Membuat Acara
        </h4>
        <ul class="space-y-2 text-sm text-blue-700">
            <li class="flex items-start">
                <i class="fas fa-check-circle mr-2 mt-0.5 text-green-500"></i>
                <span>Pastikan nama acara jelas dan mudah dipahami peserta</span>
            </li>
            <li class="flex items-start">
                <i class="fas fa-check-circle mr-2 mt-0.5 text-green-500"></i>
                <span>Gunakan lokasi yang spesifik untuk memudahkan peserta menemukan tempat</span>
            </li>
            <li class="flex items-start">
                <i class="fas fa-check-circle mr-2 mt-0.5 text-green-500"></i>
                <span>Setelah acara dibuat, QR Code akan otomatis digenerate</span>
            </li>
            <li class="flex items-start">
                <i class="fas fa-check-circle mr-2 mt-0.5 text-green-500"></i>
                <span>Cetak QR Code dan tempel di lokasi yang mudah diakses peserta</span>
            </li>
        </ul>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('eventForm');
        const submitBtn = document.getElementById('submitBtn');
        const btnText = document.getElementById('btnText');
        
        // Set tanggal default ke hari ini
        const dateInput = document.getElementById('event_date');
        if (!dateInput.value) {
            const today = new Date().toISOString().split('T')[0];
            dateInput.value = today;
        }
        
        // Set waktu default ke 09:00
        const timeInput = document.getElementById('event_time');
        if (!timeInput.value) {
            timeInput.value = '09:00';
        }
        
        // Real-time validation
        function validateField(field, type) {
            const errorElement = document.getElementById(field.id + '_error');
            const value = field.value.trim();
            
            // Reset error state
            field.classList.remove('border-red-300', 'border-green-300');
            errorElement.classList.add('hidden');
            
            if (field.hasAttribute('required') && !value) {
                showError(field, errorElement, `${type} harus diisi`);
                return false;
            }
            
            if (value) {
                field.classList.add('border-green-300');
            }
            
            return true;
        }
        
        function showError(field, errorElement, message) {
            field.classList.add('border-red-300');
            field.classList.remove('border-green-300');
            errorElement.querySelector('span').textContent = message;
            errorElement.classList.remove('hidden');
        }
        
        // Client-side form validation
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            let isValid = true;
            
            // Validate required fields
            const requiredFields = [
                { id: 'event_name', type: 'Nama acara' },
                { id: 'location', type: 'Lokasi' },
                { id: 'event_date', type: 'Tanggal acara' }
            ];
            
            requiredFields.forEach(field => {
                const input = document.getElementById(field.id);
                if (!validateField(input, field.type)) {
                    isValid = false;
                }
            });
            
            // Validate radio buttons
            const isActiveSelected = form.querySelector('input[name="is_active"]:checked');
            const isActiveError = document.getElementById('is_active_error');
            
            if (!isActiveSelected) {
                isActiveError.classList.remove('hidden');
                isValid = false;
            } else {
                isActiveError.classList.add('hidden');
            }
            
            if (!isValid) {
                // Show first error field
                const firstErrorField = form.querySelector('.border-red-300');
                if (firstErrorField) {
                    firstErrorField.focus();
                    firstErrorField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                
                // Show alert
                showNotification('Harap perbaiki kesalahan di form sebelum melanjutkan', 'error');
                return false;
            }
            
            // Show loading state
            submitBtn.disabled = true;
            btnText.textContent = 'Menyimpan...';
            submitBtn.innerHTML = `
                <div class="spinner"></div>
                <span id="btnText">Menyimpan...</span>
            `;
            
            // Submit form after confirmation
            const eventName = document.getElementById('event_name').value;
            const confirmation = confirm(`Apakah Anda yakin ingin menyimpan acara "${eventName}"?`);
            
            if (confirmation) {
                form.submit();
            } else {
                // Reset button state
                submitBtn.disabled = false;
                btnText.textContent = 'Simpan Acara';
                submitBtn.innerHTML = `
                    <i class="fas fa-save mr-2"></i>
                    <span id="btnText">Simpan Acara</span>
                `;
            }
        });
        
        // Notification function
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 px-6 py-4 rounded-lg shadow-lg z-50 ${type === 'error' ? 'bg-red-50 border border-red-200 text-red-700' : 'bg-blue-50 border border-blue-200 text-blue-700'}`;
            notification.innerHTML = `
                <div class="flex items-center">
                    <i class="fas fa-${type === 'error' ? 'exclamation-triangle' : 'info-circle'} mr-3"></i>
                    <span>${message}</span>
                </div>
            `;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.style.opacity = '0';
                notification.style.transform = 'translateY(-20px)';
                setTimeout(() => notification.remove(), 300);
            }, 4000);
        }
    });
</script>
@endpush
@endsection