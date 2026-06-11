<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Kehadiran - {{ $event->event_name ?? 'Acara Reses' }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        :root {
            --primary: #1a56db;
            --primary-dark: #1e429f;
            --secondary: #7e3af2;
            --success: #0e9f6e;
            --danger: #f05252;
            --warning: #ff9800;
            --light: #f9fafb;
            --dark: #111928;
            --gray: #6b7280;
            --border: #e5e7eb;
            --radius: 12px;
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 25px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--dark);
            line-height: 1.5;
        }

        .container {
            width: 100%;
            max-width: 500px;
            animation: slideUp 0.5s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card {
            background: white;
            border-radius: var(--radius);
            overflow: hidden;
            box-shadow: var(--shadow-lg);
        }

        .card-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            padding: 32px 24px;
            text-align: center;
            position: relative;
        }

        .badge {
            position: absolute;
            top: 20px;
            right: 20px;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            backdrop-filter: blur(10px);
        }

        .header-icon {
            width: 64px;
            height: 64px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 28px;
        }

        .event-title {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .event-description {
            font-size: 15px;
            opacity: 0.9;
            margin-bottom: 16px;
        }

        .event-date {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.15);
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
        }

        .card-content {
            padding: 32px 24px;
        }

        .alert {
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .alert-error {
            background: #fef2f2;
            border-left: 4px solid var(--danger);
            color: var(--danger);
        }

        .alert-success {
            background: #f0fdf4;
            border-left: 4px solid var(--success);
            color: var(--success);
        }

        .alert-info {
            background: #eff6ff;
            border-left: 4px solid var(--primary);
            color: var(--primary);
        }

        .alert-icon {
            font-size: 20px;
            flex-shrink: 0;
        }

        .alert-content {
            flex: 1;
        }

        .alert-title {
            font-weight: 600;
            margin-bottom: 4px;
        }

        .alert-message {
            font-size: 14px;
        }

        .form-section {
            margin-bottom: 32px;
        }

        .form-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-title i {
            color: var(--primary);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            color: var(--dark);
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-label .required {
            color: var(--danger);
            font-weight: bold;
        }

        .form-control {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid var(--border);
            border-radius: 8px;
            font-size: 15px;
            font-family: inherit;
            transition: var(--transition);
            background: var(--light);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            background: white;
            box-shadow: 0 0 0 3px rgba(26, 86, 219, 0.1);
        }

        .form-control.error {
            border-color: var(--danger);
            background: #fef2f2;
        }

        .form-control.success {
            border-color: var(--success);
            background: #f0fdf4;
        }

        .error-message {
            color: var(--danger);
            font-size: 13px;
            margin-top: 6px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .hint {
            font-size: 12px;
            color: var(--gray);
            margin-top: 6px;
            display: block;
        }

        textarea.form-control {
            min-height: 100px;
            resize: vertical;
        }

        .checkbox-group {
            background: #f8fafc;
            border: 2px solid var(--border);
            border-radius: 8px;
            padding: 20px;
            margin-top: 24px;
            transition: var(--transition);
        }

        .checkbox-group:has(input:checked) {
            border-color: var(--primary);
            background: #f0f9ff;
        }

        .checkbox-wrapper {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            cursor: pointer;
        }

        .checkbox-wrapper input[type="checkbox"] {
            display: none;
        }

        .custom-checkbox {
            width: 20px;
            height: 20px;
            border: 2px solid var(--border);
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 2px;
            transition: var(--transition);
            flex-shrink: 0;
        }

        .checkbox-wrapper input:checked + .custom-checkbox {
            background: var(--primary);
            border-color: var(--primary);
        }

        .checkbox-wrapper input:checked + .custom-checkbox::after {
            content: '✓';
            color: white;
            font-size: 12px;
            font-weight: bold;
        }

        .checkbox-content {
            flex: 1;
        }

        .checkbox-title {
            font-weight: 500;
            margin-bottom: 4px;
            color: var(--dark);
        }

        .checkbox-description {
            font-size: 13px;
            color: var(--gray);
            line-height: 1.5;
        }

        .submit-btn {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 24px;
            box-shadow: 0 4px 6px rgba(26, 86, 219, 0.25);
        }

        .submit-btn:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(26, 86, 219, 0.3);
        }

        .submit-btn:active:not(:disabled) {
            transform: translateY(0);
        }

        .submit-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none !important;
        }

        .loading-spinner {
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .footer {
            text-align: center;
            padding: 24px;
            background: #f8fafc;
            border-top: 1px solid var(--border);
        }

        .institution-name {
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 8px;
            font-size: 16px;
        }

        .contact-info {
            display: flex;
            justify-content: center;
            gap: 24px;
            margin: 16px 0;
            flex-wrap: wrap;
        }

        .contact-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            color: var(--gray);
            text-decoration: none;
            transition: var(--transition);
        }

        .contact-item:hover {
            color: var(--primary);
        }

        .footer-links {
            margin-top: 20px;
            display: flex;
            justify-content: center;
            gap: 16px;
        }

        .footer-link {
            color: var(--primary);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: var(--transition);
            padding: 8px 12px;
            border-radius: 6px;
        }

        .footer-link:hover {
            background: rgba(26, 86, 219, 0.1);
        }

        .copyright {
            font-size: 12px;
            color: var(--gray);
            margin-top: 16px;
        }

        .shake {
            animation: shake 0.5s;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }

        @media (max-width: 640px) {
            body {
                padding: 16px;
            }
            
            .card-header {
                padding: 24px 20px;
            }
            
            .card-content {
                padding: 24px 20px;
            }
            
            .event-title {
                font-size: 20px;
            }
            
            .header-icon {
                width: 56px;
                height: 56px;
                font-size: 24px;
            }
            
            .contact-info {
                flex-direction: column;
                gap: 12px;
                align-items: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <!-- Header -->
            <div class="card-header">
                <div class="badge">Form Kehadiran</div>
                <div class="header-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <h1 class="event-title">{{ $event->event_name ?? 'Acara Reses' }}</h1>
                <p class="event-description">{{ $event->description ?? 'DPRD Kota Batam' }}</p>
                @if($event && $event->event_date)
                <div class="event-date">
                    <i class="far fa-calendar"></i>
                    {{ \Carbon\Carbon::parse($event->event_date)->translatedFormat('d F Y') }}
                </div>
                @else
                <div class="event-date">
                    <i class="far fa-calendar"></i>
                    {{ now()->translatedFormat('d F Y') }}
                </div>
                @endif
            </div>

            <!-- Alert Messages -->
            @if ($errors->any())
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle alert-icon"></i>
                    <div class="alert-content">
                        <div class="alert-title">Perhatian!</div>
                        <div class="alert-message">
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            @if (session('error_message'))
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle alert-icon"></i>
                    <div class="alert-content">
                        <div class="alert-title">Terjadi Kesalahan</div>
                        <div class="alert-message">{!! session('error_message') !!}</div>
                    </div>
                </div>
            @endif

            @if (session('success_message'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle alert-icon"></i>
                    <div class="alert-content">
                        <div class="alert-title">Berhasil!</div>
                        <div class="alert-message">{!! session('success_message') !!}</div>
                    </div>
                </div>
            @endif

            @if (session('info_message'))
                <div class="alert alert-info">
                    <i class="fas fa-info-circle alert-icon"></i>
                    <div class="alert-content">
                        <div class="alert-title">Informasi</div>
                        <div class="alert-message">{!! session('info_message') !!}</div>
                    </div>
                </div>
            @endif

            <!-- Form Content -->
            <div class="card-content">
                <form method="POST" action="{{ route('attendance.store.public', $event->id) }}" id="attendanceForm">
                    @csrf

                    <!-- Data Pribadi Section -->
                    <div class="form-section">
                        <h2 class="form-title">
                            <i class="fas fa-user-edit"></i> Data Pribadi
                        </h2>

                        <!-- Nama Lengkap -->
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-user"></i> Nama Lengkap <span class="required">*</span>
                            </label>
                            <input type="text" 
                                   name="nama" 
                                   value="{{ old('nama') }}"
                                   placeholder="Masukkan nama lengkap"
                                   class="form-control {{ $errors->has('nama') ? 'error' : (old('nama') ? 'success' : '') }}"
                                   maxlength="100"
                                   required>
                            @error('nama')
                                <div class="error-message">
                                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Alamat -->
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-map-marker-alt"></i> Alamat Lengkap <span class="required">*</span>
                            </label>
                            <textarea name="alamat" 
                                      placeholder="Masukkan alamat lengkap"
                                      class="form-control {{ $errors->has('alamat') ? 'error' : (old('alamat') ? 'success' : '') }}"
                                      maxlength="500"
                                      required>{{ old('alamat') }}</textarea>
                            @error('alamat')
                                <div class="error-message">
                                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Telepon -->
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-phone"></i> Nomor Telepon <span class="required">*</span>
                            </label>
                            <input type="tel" 
                                   name="telepon" 
                                   value="{{ old('telepon') }}"
                                   placeholder="081234567890"
                                   pattern="[0-9]{10,15}"
                                   class="form-control {{ $errors->has('telepon') ? 'error' : (old('telepon') ? 'success' : '') }}"
                                   maxlength="15"
                                   required>
                            <span class="hint">Contoh: 081234567890 (10-15 digit angka)</span>
                            @error('telepon')
                                <div class="error-message">
                                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                    <!-- Konfirmasi Section -->
                    <div class="form-section">
                        <div class="checkbox-group">
                            <label class="checkbox-wrapper">
                                <input type="checkbox" 
                                       name="konfirmasi" 
                                       value="1" 
                                       {{ old('konfirmasi') ? 'checked' : '' }}
                                       required>
                                <span class="custom-checkbox"></span>
                                <div class="checkbox-content">
                                    <div class="checkbox-title">
                                        Konfirmasi Kehadiran <span class="required">*</span>
                                    </div>
                                    <div class="checkbox-description">
                                        Saya menyatakan bahwa data yang diisi adalah benar dan akan digunakan untuk kepentingan dokumentasi resmi acara.
                                    </div>
                                </div>
                            </label>
                        </div>
                        @error('konfirmasi')
                            <div class="error-message">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="submit-btn" id="submitBtn">
                        <i class="fas fa-paper-plane"></i>
                        <span id="btnText">Simpan Kehadiran</span>
                    </button>
                </form>
            </div>

            <!-- Footer -->
            <div class="footer">
                <div class="institution-info">
                    <div class="institution-name">DPRD KOTA BATAM</div>
                    <p style="font-size: 14px; color: var(--gray); margin-bottom: 16px;">
                        Sistem Absensi Digital - Acara Reses
                    </p>
                </div>

                <div class="contact-info">
                    <a href="tel:0778123456" class="contact-item">
                        <i class="fas fa-phone"></i>
                        <span>(0778) 123456</span>
                    </a>
                    <a href="mailto:admin@dprd-batam.go.id" class="contact-item">
                        <i class="fas fa-envelope"></i>
                        <span>admin@dprd-batam.go.id</span>
                    </a>
                    <div class="contact-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>Kota Batam</span>
                    </div>
                </div>

                <div class="footer-links">
                    <a href="{{ url('/') }}" class="footer-link">
                        <i class="fas fa-home"></i> Beranda
                    </a>
                    <a href="#" onclick="window.print(); return false;" class="footer-link">
                        <i class="fas fa-print"></i> Cetak
                    </a>
                </div>

                <div class="copyright">
                    &copy; {{ date('Y') }} DPRD Kota Batam. Hak Cipta Dilindungi.
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('attendanceForm');
            const submitBtn = document.getElementById('submitBtn');
            const btnText = document.getElementById('btnText');
            
            // Format telepon (hanya angka)
            const teleponInput = form.querySelector('input[name="telepon"]');
            if (teleponInput) {
                teleponInput.addEventListener('input', function() {
                    this.value = this.value.replace(/\D/g, '');
                    validateField(this);
                });
            }

            // Auto kapital untuk nama
            const namaInput = form.querySelector('input[name="nama"]');
            if (namaInput) {
                namaInput.addEventListener('input', function() {
                    this.value = this.value.toUpperCase();
                    validateField(this);
                });
            }

            // Validasi real-time
            function validateField(field) {
                field.classList.remove('error', 'success');
                
                if (!field.value.trim()) {
                    return false;
                }

                if (field.name === 'telepon') {
                    if (field.value.length >= 10 && field.value.length <= 15) {
                        field.classList.add('success');
                        return true;
                    } else {
                        field.classList.add('error');
                        return false;
                    }
                }

                if (field.name === 'nama' && field.value.length >= 3) {
                    field.classList.add('success');
                    return true;
                }

                if (field.name === 'alamat' && field.value.length >= 10) {
                    field.classList.add('success');
                    return true;
                }

                return false;
            }

            // Form submission
            form.addEventListener('submit', function(e) {
                // Show loading state
                submitBtn.disabled = true;
                btnText.textContent = 'Menyimpan...';
                submitBtn.innerHTML = `
                    <div class="loading-spinner"></div>
                    <span id="btnText">Menyimpan...</span>
                `;
            });

            // Focus first field
            if (namaInput) {
                namaInput.focus();
            }
        });
    </script>
</body>
</html>