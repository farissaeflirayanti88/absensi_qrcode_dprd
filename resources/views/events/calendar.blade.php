<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kalendar Acara</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
    <style>
        body { background-color: #f8f9fa; padding-top: 20px; }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 25px; border-radius: 10px; margin-bottom: 20px; }
        .calendar-container { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 20px rgba(0,0,0,0.1); }
        .stats-card { background: white; padding: 15px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); margin-bottom: 15px; }
        .stats-icon { font-size: 2rem; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-calendar-alt"></i> Kalendar Acara</h1>
            <p class="mb-0">Jadwal dan timeline semua acara</p>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row mb-4">
            <div class="col-md-4">
                <div class="stats-card text-center">
                    <div class="stats-icon text-primary">
                        <i class="fas fa-calendar"></i>
                    </div>
                    <h3>{{ $totalEvents ?? 0 }}</h3>
                    <p class="text-muted mb-0">Total Acara</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card text-center">
                    <div class="stats-icon text-success">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h3>{{ $activeEvents ?? 0 }}</h3>
                    <p class="text-muted mb-0">Acara Aktif</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card text-center">
                    <div class="stats-icon text-warning">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h3>{{ $upcomingEvents ?? 0 }}</h3>
                    <p class="text-muted mb-0">Acara Mendatang</p>
                </div>
            </div>
        </div>

        <div class="calendar-container">
            <div id="calendar"></div>
        </div>

        <div class="mt-4">
            <div class="alert alert-info">
                <h5><i class="fas fa-info-circle"></i> Informasi Kalendar</h5>
                <ul class="mb-0">
                    <li><span class="badge bg-success">Hijau</span> = Acara aktif</li>
                    <li><span class="badge bg-secondary">Abu-abu</span> = Acara nonaktif</li>
                    <li>Klik pada acara untuk melihat detail</li>
                </ul>
            </div>
        </div>

        <div class="mt-4 text-center">
            <a href="{{ route('events.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali ke Daftar Acara
            </a>
            <a href="{{ route('dashboard') }}" class="btn btn-primary">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/your-fontawesome-kit.js" crossorigin="anonymous"></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/id.min.js'></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'id',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                events: '{{ route("api.calendar.events") }}',
                eventClick: function(info) {
                    if (info.event.url) {
                        window.open(info.event.url, '_self');
                        return false;
                    }
                },
                eventDisplay: 'block',
                eventTimeFormat: { 
                    hour: '2-digit',
                    minute: '2-digit',
                    meridiem: false
                }
            });
            
            calendar.render();
            
            // Load events
            fetch('{{ route("api.calendar.events") }}')
                .then(response => response.json())
                .then(data => {
                    calendar.removeAllEvents();
                    calendar.addEventSource(data);
                    
                    // Show event count
                    console.log('Loaded ' + data.length + ' events');
                    
                    if (data.length === 0) {
                        console.log('No events found');
                    }
                })
                .catch(error => {
                    console.error('Error loading calendar events:', error);
                });
        });
    </script>
</body>
</html>