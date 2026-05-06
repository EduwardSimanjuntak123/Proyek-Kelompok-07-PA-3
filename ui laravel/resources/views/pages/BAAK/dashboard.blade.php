@extends('layouts.main')
@section('title', 'Dashboard')

@section('content')
    <section class="section py-3">
        <div class="section-header mb-3">
            <h1 class="mb-0">Dashboard BAAK</h1>
        </div>
        <div class="section-body pt-0">
            <div class="row g-3">

                <!-- Jumlah Mahasiswa -->
                <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-wrap">
                            <div class="card-icon bg-primary">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="card-content">
                                <div class="card-header">
                                    <h4>Jumlah Mahasiswa</h4>
                                </div>
                            </div>
                            <div class="card-body">
                                {{ $jumlah_mahasiswa }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pengumuman -->
                <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-wrap">
                            <div class="card-icon bg-success">
                                <i class="fas fa-bullhorn"></i>
                            </div>
                            <div class="card-content">
                                <div class="card-header">
                                    <h4>Pengumuman</h4>
                                </div>
                            </div>
                            <div class="card-body">
                                {{ $jumlah_pengumuman }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Jumlah Dosen -->
                <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-wrap">
                            <div class="card-icon bg-info">
                                <i class="fas fa-chalkboard-teacher"></i>
                            </div>
                            <div class="card-content">
                                <div class="card-header">
                                    <h4>Jumlah Dosen</h4>
                                </div>
                            </div>
                            <div class="card-body">
                                {{ $jumlah_dosen }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kalender Jadwal Seminar -->
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-body py-3">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h2 class="h5 mb-0">Kalender Jadwal Seminar</h2>
                            </div>
                            <div id="calendar"></div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
@endsection

@push('style')
    <style>
        .section {
            padding-top: 0.75rem;
            padding-bottom: 0.75rem;
        }

        .section-header h1 {
            font-size: 1.75rem;
            margin-bottom: 0.5rem;
        }

        .section-body {
            padding-top: 0;
        }

        #calendar {
            min-height: 380px;
            height: 420px;
        }

        .card.card-statistic-1 {
            margin-bottom: 0.75rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: transform 0.2s ease;
        }

        .card.card-statistic-1:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }

        .card.card-statistic-1 .card-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            margin-right: 0.75rem;
            flex-shrink: 0;
        }

        .card.card-statistic-1 .card-wrap {
            display: grid;
            grid-template-columns: auto minmax(140px, 1fr) auto;
            align-items: center;
            gap: 1rem;
            padding: 0.75rem 1rem;
            min-height: 80px;
        }

        .card.card-statistic-1 .card-content {
            display: flex;
            flex-direction: column;
            justify-content: center;
            min-width: 0;
        }

        .card.card-statistic-1 .card-header {
            margin-bottom: 0.25rem;
            min-width: 0;
        }

        .card.card-statistic-1 .card-header h4 {
            font-size: 0.95rem;
            font-weight: 600;
            margin-bottom: 0;
            color: #495057;
            line-height: 1.2;
            white-space: normal;
            overflow: visible;
            text-overflow: unset;
            word-break: break-word;
            max-width: 100%;
        }

        .card.card-statistic-1 .card-body {
            font-size: 1.5rem;
            font-weight: 700;
            color: #212529;
            padding: 0;
            line-height: 1.2;
            justify-self: end;
            min-width: 70px;
            white-space: nowrap;
        }


        .card.shadow-sm {
            margin-bottom: 0;
        }
    </style>
@endpush

@push('script')
    <!-- FullCalendar JS -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
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
                height: 420,
                contentHeight: 420,
                dayMaxEventRows: 3,
                eventDisplay: 'block', // tampilkan seluruh title
            });

            calendar.render();
        });
    </script>
@endpush
