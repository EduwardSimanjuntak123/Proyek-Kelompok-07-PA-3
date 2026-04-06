@extends('layouts.main')
@section('title', 'Dashboard')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Dashboard Penguji</h1>
        </div>

        <div class="section-body">
            <div class="row">

                <!-- Jumlah Mahasiswa -->
                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-primary">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Jumlah Kelompok Bimbingan</h4>
                            </div>
                            <div class="card-body">
                                {{ $jumlah_kelompok }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pengumuman -->
                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-success">
                            <i class="fas fa-bullhorn"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Pengumuman</h4>
                            </div>
                            <div class="card-body">
                                {{ $jumlah_pengumuman }}
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Jumlah Tugas -->
                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-warning">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Jumlah Tugas</h4>
                            </div>
                            <div class="card-body">
                                {{ $jumlah_tugas }}
                            </div>
                        </div>
                    </div>
                </div>
              <div class="container my-4">
                <div class="row align-items-start">
                    <!-- Kolom Kalender -->
                    <div class="col-lg-8 col-md-6 col-sm-12 mb-4">
                        <h2 class="text-center mb-4">Kalender Jadwal Seminar</h2>
                    <div class="card shadow h-100">
                        <div class="card-body">
                        
                        <div id="calendar"></div>
                        </div>
                    </div>
                    </div>

                    <!-- Kolom Konten Lain -->
                   <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                    <h2 class="text-center mb-4">Pengumuman</h2>
                    <div class="card h-100">
                        <div class="card-body">
                        @if($pengumuman->isEmpty())
                            <p class="text-muted">Belum ada pengumuman.</p>
                        @else
                            <ul class="list-group list-group-flush">
                            @foreach ($pengumuman as $index => $item)
                                <li class="list-group-item">
                                <strong>{{ $index + 1 }}.</strong>
                                <a href="{{ route('pengumuman.penguji.show', $item->id) }}">
                                    {{ $item->judul }}
                                </a>
                                </li>
                            @endforeach
                            </ul>
                        @endif
                        </div>
                    </div>
                    </div>
                        </div>
                        </div>
                    </div>
        </div>
    </section>
@endsection
@push('script')
    <!-- FullCalendar JS -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var calendarEl = document.getElementById('calendar');

            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                themeSystem: 'bootstrap5',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: ''
                },
                events: @json($events), // events: array of { title, start, end }
                eventDisplay: 'block', // tampilkan seluruh title
            });

            calendar.render();
        });
    </script>
@endpush