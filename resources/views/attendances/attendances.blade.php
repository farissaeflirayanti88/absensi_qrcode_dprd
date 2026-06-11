<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Kehadiran - {{ $event->event_name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container-fluid py-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">
                    <i class="fas fa-users me-2"></i>
                    Rekap Kehadiran: {{ $event->event_name }}
                </h4>
                <div>
                    <a href="{{ route('events.show', $event->id) }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Kembali
                    </a>
                    <a href="{{ route('events.export', $event->id) }}" class="btn btn-success">
                        <i class="fas fa-file-export me-1"></i> Export CSV
                    </a>
                </div>
            </div>
            
            <div class="card-body">
                <!-- Info Event -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="p-3 bg-light rounded">
                            <small class="text-muted">Tanggal</small>
                            <div class="fw-bold">{{ \Carbon\Carbon::parse($event->event_date)->translatedFormat('d F Y') }}</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 bg-light rounded">
                            <small class="text-muted">Lokasi</small>
                            <div class="fw-bold">{{ $event->location }}</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 bg-light rounded">
                            <small class="text-muted">Total Kehadiran</small>
                            <div class="fw-bold">{{ $attendances->total() }} Peserta</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 bg-light rounded">
                            <small class="text-muted">Status</small>
                            <div>
                                @if($event->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-secondary">Nonaktif</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Filters -->
                <form method="GET" class="row g-3 mb-4">
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control" 
                               placeholder="Cari nama/alamat/telepon..." 
                               value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <input type="date" name="date" class="form-control" 
                               value="{{ request('date') }}" 
                               placeholder="Filter tanggal">
                    </div>
                    @if($addresses->isNotEmpty())
                    <div class="col-md-3">
                        <select name="address" class="form-control">
                            <option value="">Semua Alamat</option>
                            @foreach($addresses as $address)
                                <option value="{{ $address }}" {{ request('address') == $address ? 'selected' : '' }}>
                                    {{ $address }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-filter me-1"></i> Filter
                        </button>
                    </div>
                </form>
                
                <!-- Data Table -->
                @if($attendances->isEmpty())
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i> Belum ada data kehadiran untuk acara ini.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Nama</th>
                                    <th>Alamat</th>
                                    <th>No. Telepon</th>
                                    <th>Waktu Absen</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($attendances as $index => $attendance)
                                @php
                                    $participant = $attendance->participant;
                                @endphp
                                <tr>
                                    <td>{{ $index + $attendances->firstItem() }}</td>
                                    <td>{{ $participant->name ?? '-' }}</td>
                                    <td>{{ $participant->address ?? '-' }}</td>
                                    <td>{{ $participant->phone ?? '-' }}</td>
                                    <td>
                                        {{ $attendance->attendance_time ? \Carbon\Carbon::parse($attendance->attendance_time)->format('d/m/Y H:i') : '-' }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $attendances->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>