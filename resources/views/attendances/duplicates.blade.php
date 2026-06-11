@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Data Duplikat</h2>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No</th>
                <th>Nomor Telepon</th>
                <th>Event</th>
                <th>Tanggal</th>
                <th>Jumlah Duplikat</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($duplicates as $index => $duplicate)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $duplicate->phone }}</td>
                    <td>{{ $duplicate->event->name ?? 'N/A' }}</td>
                    <td>{{ $duplicate->date }}</td>
                    <td>{{ $duplicate->count }}</td>
                    <td>
                        <a href="{{ route('attendances.index', ['event' => $duplicate->event_id, 'phone' => $duplicate->phone, 'date' => $duplicate->date]) }}" class="btn btn-sm btn-info">Lihat Data</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection