@extends('layouts.app')

@section('title', 'Profile - Sistem Absensi QR Code DPRD Kota Batam')
@section('page-title', 'Profile')
@section('page-subtitle', 'Sistem Absensi QR Code DPRD Kota Batam')

@section('content')
<div class="max-w-4xl mx-auto">
    @if ($errors->any())
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg shadow-sm">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <div>
                    <p class="font-medium">Terjadi kesalahan:</p>
                    <ul class="mt-1 text-sm list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <!-- ================================================ -->
    <!-- FR-16: FOTO PROFIL - UPLOAD AVATAR -->
    <!-- ================================================ -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-8">
        <div class="p-6 border-b border-gray-100">
            <h3 class="text-lg font-semibold text-gray-800">Foto Profil</h3>
            <p class="text-sm text-gray-600 mt-1">Upload foto profil Anda</p>
        </div>
        
        <div class="p-6">
            <div class="flex flex-col md:flex-row items-start md:items-center gap-6">
                <!-- Avatar Preview -->
                <div class="flex-shrink-0">
                    @if($user->avatar)
                        <img src="{{ asset('storage/avatars/' . $user->avatar) }}" 
                             alt="Avatar" 
                             class="w-32 h-32 rounded-full object-cover border-4 border-gray-200 shadow-sm">
                    @else
                        <div class="w-32 h-32 rounded-full bg-gradient-to-r from-blue-500 to-blue-600 flex items-center justify-center border-4 border-gray-200 shadow-sm">
                            <span class="text-white text-3xl font-bold">
                                {{ strtoupper(substr($user->name ?? 'U', 0, 2)) }}
                            </span>
                        </div>
                    @endif
                </div>
                
                <!-- Upload Form -->
                <div class="flex-grow">
                    <form action="{{ route('profile.avatar.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="avatar" class="block text-sm font-medium text-gray-700 mb-2">
                                Pilih Foto Baru
                            </label>
                            <input type="file" 
                                   id="avatar" 
                                   name="avatar" 
                                   accept="image/jpeg,image/png,image/jpg"
                                   class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG. Maks: 2MB</p>
                            @error('avatar')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="flex gap-2">
                            <button type="submit" 
                                    class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-200 text-sm flex items-center">
                                <i class="fas fa-upload mr-1"></i> Upload
                            </button>
                            
                            @if($user->avatar)
                                <button type="button" 
                                        onclick="confirmDeleteAvatar()"
                                        class="bg-red-100 text-red-700 px-4 py-2 rounded-lg hover:bg-red-200 transition duration-200 text-sm flex items-center">
                                    <i class="fas fa-trash mr-1"></i> Hapus
                                </button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- ================================================ -->
    <!-- INFORMASI PROFILE -->
    <!-- ================================================ -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-8">
        <div class="p-6 border-b border-gray-100">
            <h3 class="text-lg font-semibold text-gray-800">Informasi Profile</h3>
            <p class="text-sm text-gray-600 mt-1">Update informasi profile Anda</p>
        </div>
        
        <div class="p-6">
            <form action="{{ route('profile.update') }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nama -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Lengkap <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               value="{{ old('name', $user->name ?? '') }}"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               value="{{ old('email', $user->email ?? '') }}"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               required>
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Username -->
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                            Username <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="username" 
                               name="username" 
                               value="{{ old('username', $user->username ?? '') }}"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               required>
                        @error('username')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Telepon -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                            Nomor Telepon
                        </label>
                        <input type="text" 
                               id="phone" 
                               name="phone" 
                               value="{{ old('phone', $user->phone ?? '') }}"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="08xxxxxxxxxx">
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Alamat -->
                    <div class="md:col-span-2">
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                            Alamat
                        </label>
                        <textarea id="address" 
                                  name="address" 
                                  rows="3"
                                  class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('address', $user->address ?? '') }}</textarea>
                        @error('address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div class="flex justify-end mt-6">
                    <button type="submit" 
                            class="bg-blue-600 text-white px-6 py-2.5 rounded-lg hover:bg-blue-700 transition duration-200 flex items-center">
                        <i class="fas fa-save mr-2"></i>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ================================================ -->
    <!-- UBAH PASSWORD -->
    <!-- ================================================ -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-8">
        <div class="p-6 border-b border-gray-100">
            <h3 class="text-lg font-semibold text-gray-800">Ubah Password</h3>
            <p class="text-sm text-gray-600 mt-1">Pastikan password Anda aman dan kuat</p>
        </div>
        
        <div class="p-6">
            <form action="{{ route('profile.password') }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Current Password -->
                    <div>
                        <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">
                            Password Saat Ini <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="password" 
                                   id="current_password" 
                                   name="current_password" 
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   required>
                            <button type="button" 
                                    onclick="togglePasswordVisibility('current_password')"
                                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        @error('current_password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- New Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            Password Baru <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="password" 
                                   id="password" 
                                   name="password" 
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   required>
                            <button type="button" 
                                    onclick="togglePasswordVisibility('password')"
                                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        
                        <!-- Password Strength Indicator -->
                        <div class="mt-3 password-strength-container">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-xs font-medium text-gray-600">Kekuatan Password:</span>
                                <span id="password-strength-label" class="text-xs font-medium text-gray-500">-</span>
                            </div>
                            <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden">
                                <div id="password-strength-bar" class="h-full transition-all duration-300" style="width: 0%;"></div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Confirm New Password -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                            Konfirmasi Password Baru <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="password" 
                                   id="password_confirmation" 
                                   name="password_confirmation" 
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   required>
                            <button type="button" 
                                    onclick="togglePasswordVisibility('password_confirmation')"
                                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Password Requirements - TYPO FIXED -->
                    <div class="md:col-span-2">
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                            <h4 class="text-sm font-medium text-gray-700 mb-2">Persyaratan Password:</h4>
                            <ul class="text-xs text-gray-600 space-y-1">
                                <li class="flex items-center">
                                    <i class="fas fa-check-circle text-green-500 mr-2 text-xs"></i>
                                    Minimal 8 karakter
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-check-circle text-green-500 mr-2 text-xs"></i>
                                    Mengandung huruf dan angka
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-check-circle text-green-500 mr-2 text-xs"></i>
                                    Gunakan kombinasi huruf besar dan kecil
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-end mt-6">
                    <button type="submit" 
                            class="bg-green-600 text-white px-6 py-2.5 rounded-lg hover:bg-green-700 transition duration-200 flex items-center">
                        <i class="fas fa-key mr-2"></i>
                        Ubah Password
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ================================================ -->
    <!-- INFORMASI AKUN -->
    <!-- ================================================ -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-8">
        <div class="p-6 border-b border-gray-100">
            <h3 class="text-lg font-semibold text-gray-800">Informasi Akun</h3>
            <p class="text-sm text-gray-600 mt-1">Detail akun dan status sistem</p>
        </div>
        
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- User Info -->
                <div>
                    <h4 class="text-sm font-medium text-gray-700 mb-3">Detail Akun</h4>
                    <div class="space-y-3">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-r from-blue-500 to-blue-600 flex items-center justify-center mr-3 shadow-sm">
                                @if($user->avatar)
                                    <img src="{{ asset('storage/avatars/' . $user->avatar) }}" 
                                         alt="Avatar" 
                                         class="w-10 h-10 rounded-full object-cover">
                                @else
                                    <span class="text-white font-semibold text-sm">
                                        {{ strtoupper(substr($user->name ?? 'UD', 0, 2)) }}
                                    </span>
                                @endif
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-800">{{ $user->name ?? 'User' }}</p>
                                <p class="text-xs text-gray-500">{{ $user->email ?? 'email@example.com' }}</p>
                            </div>
                        </div>
                        
                        <div class="text-sm text-gray-600">
                            <div class="flex items-center mb-1">
                                <i class="fas fa-user-tag mr-2 text-gray-400 text-xs"></i>
                                <span>Role: <span class="font-medium text-blue-600">{{ ucfirst($user->role ?? 'Administrator') }}</span></span>
                            </div>
                            <div class="flex items-center mb-1">
                                <i class="fas fa-user mr-2 text-gray-400 text-xs"></i>
                                <span>Username: <span class="font-medium">{{ $user->username ?? 'username' }}</span></span>
                            </div>
                            <div class="flex items-center mb-1">
                                <i class="fas fa-calendar-alt mr-2 text-gray-400 text-xs"></i>
                                <span>Bergabung: {{ $user->created_at ? $user->created_at->format('d F Y') : 'Belum diketahui' }}</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-clock mr-2 text-gray-400 text-xs"></i>
                                <span>Terakhir Login: {{ $user->last_login_at ? $user->last_login_at->format('d/m/Y H:i') : 'Belum pernah' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- System Info -->
                <div>
                    <h4 class="text-sm font-medium text-gray-700 mb-3">Status Sistem</h4>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Status Akun</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-circle text-xs mr-1"></i>
                                Aktif
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Verifikasi Email</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $user->email_verified_at ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                <i class="fas {{ $user->email_verified_at ? 'fa-check-circle' : 'fa-exclamation-circle' }} text-xs mr-1"></i>
                                {{ $user->email_verified_at ? 'Terverifikasi' : 'Belum diverifikasi' }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Aktivitas Terakhir</span>
                            <span class="text-xs text-gray-500">
                                {{ $user->updated_at ? $user->updated_at->diffForHumans() : 'Tidak diketahui' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ================================================ -->
    <!-- ZONA BERBAHAYA - SATU TOMBOL LOGOUT -->
    <!-- ================================================ -->
    <div class="bg-red-50 rounded-xl shadow-sm border border-red-200">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-red-800 mb-2">Zona Berbahaya</h3>
            <p class="text-sm text-red-600 mb-4">
                Tindakan di bawah ini bersifat permanen dan tidak dapat dibatalkan.
            </p>
            
            <div class="flex flex-wrap gap-4">
                <!-- SINGLE LOGOUT BUTTON - POST Method dengan CSRF -->
                <form id="logoutForm" method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" 
                            onclick="return confirm('Apakah Anda yakin ingin keluar dari sistem?')"
                            class="bg-red-600 text-white hover:bg-red-700 px-6 py-2.5 rounded-lg transition duration-200 flex items-center shadow-sm">
                        <i class="fas fa-sign-out-alt mr-2"></i>
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- ================================================ -->
<!-- JAVASCRIPT -->
<!-- ================================================ -->
<script>
// Password visibility toggle
function togglePasswordVisibility(fieldId) {
    const field = document.getElementById(fieldId);
    const button = field.nextElementSibling;
    const icon = button.querySelector('i');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// Confirm delete avatar
function confirmDeleteAvatar() {
    if (confirm('Apakah Anda yakin ingin menghapus foto profil?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("profile.avatar.delete") }}';
        form.innerHTML = '@csrf @method("DELETE")';
        document.body.appendChild(form);
        form.submit();
    }
}

// Password strength indicator
document.addEventListener('DOMContentLoaded', function() {
    const passwordField = document.getElementById('password');
    if (passwordField) {
        passwordField.addEventListener('input', function() {
            const password = this.value;
            const strengthLabel = document.getElementById('password-strength-label');
            const strengthBar = document.getElementById('password-strength-bar');
            
            if (!strengthLabel || !strengthBar) return;
            
            // Calculate strength
            let strength = 0;
            if (password.length >= 8) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[a-z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^A-Za-z0-9]/.test(password)) strength++;
            
            // Strength labels and colors
            const strengthLabels = ['Sangat Lemah', 'Lemah', 'Cukup', 'Kuat', 'Sangat Kuat'];
            const strengthColors = ['bg-red-500', 'bg-orange-500', 'bg-yellow-500', 'bg-blue-500', 'bg-green-500'];
            
            // Update UI
            const index = Math.min(strength - 1, 4);
            strengthLabel.textContent = strengthLabels[index] || 'Sangat Lemah';
            strengthBar.className = `h-full rounded-full ${strengthColors[index] || 'bg-red-500'}`;
            strengthBar.style.width = `${(strength / 5) * 100}%`;
            
            // Default if no password
            if (password.length === 0) {
                strengthLabel.textContent = '-';
                strengthBar.style.width = '0%';
            }
        });
    }
});
</script>

<style>
/* Password strength bar animation */
.password-strength-container {
    transition: all 0.3s ease;
}

#password-strength-bar {
    transition: width 0.3s, background-color 0.3s;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .max-w-4xl {
        padding-left: 1rem;
        padding-right: 1rem;
    }
}
</style>
@endsection