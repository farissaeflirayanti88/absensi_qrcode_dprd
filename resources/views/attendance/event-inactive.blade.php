<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Tidak Aktif - Sistem Absensi QR DPRD</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .icon-container {
            display: inline-block;
            padding: 20px;
            background-color: #dc3545;
            border-radius: 50%;
            margin-bottom: 20px;
        }
        .icon-container i {
            font-size: 48px;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card">
                    <div class="card-body text-center p-5">
                        <div class="icon-container">
                            <i class="fas fa-calendar-times"></i>
                        </div>
                        <h2 class="card-title text-danger mb-3">Event Tidak Aktif</h2>
                        <div class="alert alert-danger" role="alert">
                            <h4 class="alert-heading">Maaf!</h4>
                            <p>Event <strong>"{{ $event->event_name }}"</strong> saat ini tidak aktif atau sudah berakhir.</p>
                        </div>
                        <div class="card mt-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Detail Event</h5>
                            </div>
                            <div class="card-body text-start">
                                <table class="table table-bordered">
                                    <tr>
                                        <th width="40%">Nama Event</th>
                                        <td>{{ $event->event_name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Lokasi</th>
                                        <td>{{ $event->location }}</td>
                                    </tr>
                                    <tr>
                                        <th>Tanggal</th>
                                        <td>{{ \Carbon\Carbon::parse($event->event_date)->format('d F Y H:i') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Status</th>
                                        <td>
                                            @if($event->is_active)
                                                <span class="badge bg-success">Aktif</span>
                                            @else
                                                <span class="badge bg-danger">Tidak Aktif</span>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="mt-4">
                            <p class="text-muted">
                                Jika Anda merasa ini adalah kesalahan, silakan hubungi administrator event.
                            </p>
                        </div>
                        <div class="mt-4">
                            <a href="{{ url('/') }}" class="btn btn-primary">
                                <i class="fas fa-home"></i> Kembali ke Beranda
                            </a>
                            @if(auth()->check())
                                <a href="{{ route('events.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-calendar"></i> Lihat Event Lain
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</body>
</html>