@extends('layouts.main')
@section('title', 'Dashboard')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Dashboard Kordinator</h1>
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
                                <h4>Jumlah Mahasiswa</h4>
                            </div>
                            <div class="card-body">
                                {{ $jumlah_mahasiswa }}
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

                <!-- Jumlah Dosen -->
                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-info">
                            <i class="fas fa-chalkboard-teacher"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Jumlah Dosen</h4>
                            </div>
                            <div class="card-body">
                                {{ $jumlah_dosen }}
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
                <div class="container">
                <h2 class="text-center mb-4">Kalender Jadwal Seminar</h2>
                <div class="card shadow">
                    <div class="card-body">
                        <div id="calendar"></div>
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