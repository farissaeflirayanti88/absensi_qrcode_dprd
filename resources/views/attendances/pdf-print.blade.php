<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        /* Reset CSS untuk print */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            font-size: 14px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
            background: white;
        }

        /* Tombol hanya tampil di browser, tidak saat print */
        @media screen {
            .print-controls {
                text-align: center;
                margin-bottom: 20px;
                padding: 15px;
                background: #f8f9fa;
                border-radius: 8px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            }
            
            .print-btn {
                background: #007bff;
                color: white;
                border: none;
                padding: 12px 24px;
                border-radius: 5px;
                cursor: pointer;
                font-size: 14px;
                margin: 0 10px;
                transition: background 0.3s;
                text-decoration: none;
                display: inline-block;
            }
            
            .print-btn:hover {
                background: #0056b3;
            }
            
            .print-btn.close {
                background: #dc3545;
            }
            
            .print-btn.close:hover {
                background: #c82333;
            }

            .print-btn.back {
                background: #28a745;
            }
            
            .print-btn.back:hover {
                background: #218838;
            }
        }

        @media print {
            .print-controls {
                display: none;
            }
            
            body {
                padding: 0;
                margin: 0;
            }
            
            .page-break {
                page-break-after: always;
            }
        }

        /* Header */
        .header {
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 3px solid #2c5aa0;
        }
        
        .header h1 {
            font-size: 22px;
            color: #2c5aa0;
            margin-bottom: 8px;
            font-weight: bold;
        }
        
        .header .subtitle {
            font-size: 14px;
            color: #666;
            margin-bottom: 5px;
        }
        
        .header .date {
            font-size: 12px;
            color: #888;
        }

        /* Info Section */
        .info-section {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #2c5aa0;
        }
        
        .info-item {
            margin-bottom: 5px;
        }
        
        .info-label {
            font-weight: bold;
            color: #2c5aa0;
            display: inline-block;
            min-width: 120px;
        }

        /* Summary */
        .summary {
            background: #e7f3ff;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 15px;
            text-align: center;
            font-weight: bold;
            border: 1px solid #b8d4f0;
        }

        /* Table */
        .table-container {
            width: 100%;
            margin-top: 15px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }
        
        table th {
            background: #2c5aa0;
            color: white;
            padding: 10px 8px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #1e3d6d;
        }
        
        table td {
            padding: 8px;
            border: 1px solid #ddd;
            vertical-align: top;
        }
        
        table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        table tr:hover {
            background-color: #e9ecef;
        }

        /* Footer */
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        
        .footer p {
            margin: 3px 0;
        }

        /* Utility Classes */
        .text-center {
            text-align: center;
        }
        
        .text-right {
            text-align: right;
        }
        
        .mb-3 {
            margin-bottom: 15px;
        }
        
        .mt-3 {
            margin-top: 15px;
        }

        /* Page setup untuk print */
        @page {
            size: A4;
            margin: 1cm;
        }
        
        @media print {
            body {
                font-size: 10px;
            }
            
            table {
                font-size: 9px;
            }
            
            .header h1 {
                font-size: 18px;
            }
        }
    </style>
</head>
<body>
    <!-- Tombol kontrol hanya tampil di browser -->
    <div class="print-controls">
        <button class="print-btn" onclick="window.print()">
            🖨️ Cetak Dokumen
        </button>
        </a>
        <button class="print-btn close" onclick="safeCloseWindow()">
            ❌ Tutup Jendela
        </button>
        <p style="margin-top: 10px; color: #666; font-size: 12px;">
            Tips: Gunakan Ctrl+P untuk mencetak atau simpan sebagai PDF
        </p>
    </div>

    <!-- Kop Surat -->
    <div class="header">
        <h1>REKAP KEHADIRAN PESERTA</h1>
        <p class="subtitle">Sistem Absensi QR Code - DPRD Kota Batam</p>
        <p class="date">Dicetak pada: {{ $exportDate }}</p>
    </div>

    <!-- Informasi Acara -->
    <div class="info-section">
        @if($event)
            <div class="info-item">
                <span class="info-label">Nama Acara:</span>
                {{ $event->event_name }}
            </div>
            <div class="info-item">
                <span class="info-label">Lokasi:</span>
                {{ $event->location }}
            </div>
            <div class="info-item">
                <span class="info-label">Tanggal Acara:</span>
                {{ $event->event_date->format('d/m/Y') }}
            </div>
        @else
            <div class="info-item">
                <span class="info-label">Laporan:</span>
                Semua Acara
            </div>
        @endif
        <div class="info-item">
            <span class="info-label">Total Kehadiran:</span>
            {{ $totalAttendances }} peserta
        </div>
    </div>

    <!-- Ringkasan -->
    <div class="summary">
        Menampilkan {{ $attendances->count() }} data kehadiran
    </div>

    <!-- Tabel Data -->
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th style="width: 5%">No</th>
                    <th style="width: 20%">Nama Peserta</th>
                    <th style="width: 25%">Alamat</th>
                    <th style="width: 12%">No. Telepon</th>
                    <th style="width: 20%">Acara</th>
                    <th style="width: 10%">Waktu Hadir</th>
                    <th style="width: 8%">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($attendances as $attendance)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td><strong>{{ $attendance->participant->name }}</strong></td>
                    <td>{{ Str::limit($attendance->participant->address, 60) }}</td>
                    <td>{{ $attendance->participant->phone }}</td>
                    <td>
                        <strong>{{ $attendance->event->event_name }}</strong><br>
                        <small>📍 {{ $attendance->event->location }}</small><br>
                        <small>📅 {{ $attendance->event->event_date->format('d/m/Y') }}</small>
                    </td>
                    <td>{{ $attendance->attendance_time->format('d/m/Y H:i') }}</td>
                    <td class="text-center">
                        <span style="color: green; font-weight: bold;">✓ Hadir</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 20px;">
                        Tidak ada data kehadiran
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Dokumen ini dicetak secara otomatis dari Sistem Absensi QR Code DPRD Kota Batam</p>
        <p>Merupakan dokumen resmi yang dapat digunakan untuk pelaporan dan pertanggungjawaban</p>
        <p>© {{ date('Y') }} - DPRD Kota Batam. Hak Cipta Dilindungi.</p>
    </div>

    <script>
        // Fungsi aman untuk menutup jendela
        function safeCloseWindow() {
            // Coba tutup jendela
            try {
                window.close();
                // Jika masih terbuka setelah 1 detik, redirect ke halaman rekap
                setTimeout(function() {
                    if (!window.closed) {
                        window.location.href = "{{ route('attendances.index') }}";
                    }
                }, 1000);
            } catch (error) {
                // Jika error, redirect ke halaman rekap
                window.location.href = "{{ route('attendances.index') }}";
            }
        }

        // Auto focus dan siap untuk print
        window.onload = function() {
            // Optional: Auto print setelah halaman load
            // window.print();
        };

        // Handle sebelum print
        window.onbeforeprint = function() {
            console.log('Mempersiapkan dokumen untuk dicetak...');
        };

        // Handle setelah print
        window.onafterprint = function() {
            console.log('Pencetakan selesai');
            // Optional: Tampilkan pesan setelah print
            // setTimeout(() => {
            //     alert('Pencetakan selesai. Anda bisa menutup jendela ini.');
            // }, 500);
        };
    </script>
</body>
</html>