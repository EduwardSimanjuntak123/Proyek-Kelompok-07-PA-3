@extends('layouts.main')
@section('title', 'Dashboard')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Dashboard Kordinator</h1>
        </div>

        <div class="section-body">
            {{-- VERIFICATION FLOW --}}
            <div class="row mb-4">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>📋 Alur Verifikasi PA</h4>
                        </div>
                        <div class="card-body">
                            <div class="verification-flow-container">
                                {{-- Step 1: Kelompok --}}
                                <div class="verification-step" data-step="kelompok" data-status="{{ $verification_status['kelompok'] ?? 'pending' }}">
                                    <div class="step-circle">
                                        <span class="step-number">1</span>
                                        <svg class="status-checkmark" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round">
                                            <polyline points="20 6 9 17 4 12"></polyline>
                                        </svg>
                                    </div>
                                    <div class="step-label">
                                        <p class="step-title">Pembagian Kelompok</p>
                                        <p class="step-desc">Buat & generate kelompok</p>
                                    </div>
                                </div>

                                {{-- Arrow --}}
                                <div class="verification-arrow">→</div>

                                {{-- Step 2: Pembimbing --}}
                                <div class="verification-step" data-step="pembimbing" data-status="{{ $verification_status['pembimbing'] ?? 'pending' }}">
                                    <div class="step-circle">
                                        <span class="step-number">2</span>
                                        <svg class="status-checkmark" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round">
                                            <polyline points="20 6 9 17 4 12"></polyline>
                                        </svg>
                                    </div>
                                    <div class="step-label">
                                        <p class="step-title">Verifikasi Pembimbing</p>
                                        <p class="step-desc">Tentukan dosen pembimbing</p>
                                    </div>
                                </div>

                                {{-- Arrow --}}
                                <div class="verification-arrow">→</div>

                                {{-- Step 3: Penguji --}}
                                <div class="verification-step" data-step="penguji" data-status="{{ $verification_status['penguji'] ?? 'pending' }}">
                                    <div class="step-circle">
                                        <span class="step-number">3</span>
                                        <svg class="status-checkmark" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round">
                                            <polyline points="20 6 9 17 4 12"></polyline>
                                        </svg>
                                    </div>
                                    <div class="step-label">
                                        <p class="step-title">Verifikasi Penguji</p>
                                        <p class="step-desc">Tentukan dosen penguji</p>
                                    </div>
                                </div>

                                {{-- Arrow --}}
                                <div class="verification-arrow">→</div>

                                {{-- Step 4: Jadwal --}}
                                <div class="verification-step" data-step="jadwal" data-status="{{ $verification_status['jadwal'] ?? 'pending' }}">
                                    <div class="step-circle">
                                        <span class="step-number">4</span>
                                        <svg class="status-checkmark" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round">
                                            <polyline points="20 6 9 17 4 12"></polyline>
                                        </svg>
                                    </div>
                                    <div class="step-label">
                                        <p class="step-title">Jadwal Seminar</p>
                                        <p class="step-desc">Tentukan waktu seminar</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Status Legend --}}
                            <div class="verification-legend mt-4 pt-3 border-top">
                                <div class="legend-row">
                                    <div class="legend-item">
                                        <span class="legend-badge" style="background: #e8f1f7; border: 2px solid #4c9bc8;"></span>
                                        <span>Pending - Belum diproses</span>
                                    </div>
                                    <div class="legend-item">
                                        <span class="legend-badge" style="background: #d4f1e0; border: 2px solid #22c55e;"></span>
                                        <span>Success - Sudah selesai</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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

    {{-- VERIFICATION FLOW STYLES & SCRIPTS --}}
    <style>
        .verification-flow-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            flex-wrap: wrap;
            padding: 20px 0;
        }

        .verification-step {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 15px;
            border-radius: 10px;
            background: #f8fafb;
            border: 2px solid #e0e6ef;
            transition: all 0.3s ease;
            min-width: fit-content;
            flex: 1;
            min-width: 200px;
        }

        .verification-step[data-status="success"] {
            background: #d4f1e0;
            border-color: #22c55e;
        }

        .verification-step[data-status="warning"] {
            background: #fef3c7;
            border-color: #f59e0b;
        }

        .step-circle {
            position: relative;
            width: 50px;
            height: 50px;
            min-width: 50px;
            border-radius: 50%;
            background: #fff;
            border: 2px solid #4c9bc8;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: #4c9bc8;
            flex-shrink: 0;
            transition: all 0.3s ease;
        }

        .verification-step[data-status="success"] .step-circle {
            background: #22c55e;
            border-color: #22c55e;
        }

        .verification-step[data-status="warning"] .step-circle {
            background: #f59e0b;
            border-color: #f59e0b;
        }

        .step-number {
            display: flex;
            align-items: center;
            justify-content: center;
            color: inherit;
        }

        .verification-step[data-status="success"] .step-number {
            display: none;
        }

        .status-checkmark {
            width: 24px;
            height: 24px;
            stroke: #fff;
            display: none;
            animation: checkmarkDraw 0.5s ease forwards;
        }

        .verification-step[data-status="success"] .status-checkmark {
            display: block;
        }

        @keyframes checkmarkDraw {
            0% {
                stroke-dasharray: 24;
                stroke-dashoffset: 24;
                transform: scale(0.8);
                opacity: 0;
            }
            100% {
                stroke-dasharray: 24;
                stroke-dashoffset: 0;
                transform: scale(1);
                opacity: 1;
            }
        }

        .step-label {
            flex: 1;
            text-align: left;
        }

        .step-title {
            font-weight: 600;
            font-size: 14px;
            color: #1a2e3b;
            margin: 0;
        }

        .step-desc {
            font-size: 12px;
            color: #6b8fa3;
            margin: 2px 0 0 0;
        }

        .verification-arrow {
            font-size: 24px;
            color: #4c9bc8;
            font-weight: bold;
            flex: 0 0 auto;
            margin: 0 -5px;
        }

        .verification-legend {
            display: flex;
            gap: 30px;
            flex-wrap: wrap;
        }

        .legend-row {
            display: flex;
            gap: 30px;
            flex-wrap: wrap;
            width: 100%;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
            color: #6b8fa3;
        }

        .legend-badge {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        @media (max-width: 768px) {
            .verification-flow-container {
                flex-direction: column;
                gap: 15px;
            }

            .verification-arrow {
                transform: rotate(90deg);
                margin: -5px 0;
            }

            .verification-step {
                width: 100%;
            }
        }
    </style>
@endpush