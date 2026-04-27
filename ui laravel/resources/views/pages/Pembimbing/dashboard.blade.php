@extends('layouts.main')
@section('title', 'Dashboard')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Dashboard Pembimbing</h1>
        </div>

        <div class="section-body">
            <div class="row">

                <!-- Jumlah Mahasiswa -->
                <div class="col-lg-4 col-md-6 col-sm-6 col-12">
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
                <div class="col-lg-4 col-md-6 col-sm-6 col-12">
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
                <div class="col-lg-4 col-md-6 col-sm-6 col-12">
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

                <div class="col-12">
                    <div class="card shadow-sm border-0 kelompok-card-wrapper">
                        <div class="card-header bg-white border-bottom-0 pb-0">
                            <h4 class="mb-1">Daftar Kelompok Bimbingan dan Anggota</h4>
                            <p class="text-muted mb-0">Pantau semua kelompok bimbingan beserta progres dan daftar mahasiswa.
                            </p>
                        </div>
                        <div class="card-body">
                            @if ($kelompokList->isEmpty())
                                <div class="alert alert-light border mb-0">
                                    Belum ada kelompok bimbingan pada konteks prodi, kategori PA, dan tahun masuk saat ini.
                                </div>
                            @else
                                <div class="row">
                                    @foreach ($kelompokList as $kelompok)
                                        <div class="col-lg-6 col-12 mb-4">
                                            <div class="kelompok-item h-100">
                                                <div class="d-flex justify-content-between align-items-start mb-3">
                                                    <div>
                                                        <h6 class="mb-1 kelompok-title">Kelompok
                                                            {{ $kelompok['nomor_kelompok'] }}</h6>
                                                        <span
                                                            class="badge badge-light">{{ $kelompok['status_kelompok'] }}</span>
                                                    </div>
                                                    <span
                                                        class="badge badge-info px-3 py-2">{{ $kelompok['jumlah_anggota'] }}
                                                        Mahasiswa</span>
                                                </div>

                                                <div class="kelompok-meta mb-3">
                                                    <div class="meta-item">
                                                        <span class="meta-label">Status Bimbingan</span>
                                                        <span class="meta-value">{{ $kelompok['status_bimbingan'] }}</span>
                                                    </div>
                                                    <div class="meta-item">
                                                        <span class="meta-label">Total Sesi</span>
                                                        <span
                                                            class="meta-value">{{ $kelompok['total_sesi_bimbingan'] }}</span>
                                                    </div>
                                                    <div class="meta-item meta-item-full">
                                                        <span class="meta-label">Bimbingan Terakhir</span>
                                                        <span
                                                            class="meta-value">{{ $kelompok['terakhir_bimbingan'] ? \Carbon\Carbon::parse($kelompok['terakhir_bimbingan'])->format('d M Y') : '-' }}</span>
                                                    </div>
                                                </div>

                                                <div>
                                                    <strong class="d-block mb-2">Daftar Mahasiswa</strong>
                                                    @if (collect($kelompok['anggota'])->isEmpty())
                                                        <p class="text-muted mb-0">Belum ada mahasiswa dalam kelompok ini.
                                                        </p>
                                                    @else
                                                        <ul class="list-unstyled mb-0 mahasiswa-list">
                                                            @foreach ($kelompok['anggota'] as $anggota)
                                                                <li>
                                                                    <span class="mahasiswa-avatar">
                                                                        <i class="fas fa-user-graduate"></i>
                                                                    </span>
                                                                    <div>
                                                                        <div class="mahasiswa-nama">{{ $anggota['nama'] }}
                                                                        </div>
                                                                        <small class="text-muted">NIM:
                                                                            {{ $anggota['nim'] }}</small>
                                                                    </div>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
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
                            @if ($pengumuman->isEmpty())
                                <p class="text-muted">Belum ada pengumuman.</p>
                            @else
                                <ul class="list-group list-group-flush">
                                    @foreach ($pengumuman as $index => $item)
                                        <li class="list-group-item">
                                            <strong>{{ $index + 1 }}.</strong>
                                            <a href="{{ route('pengumuman.pembimbing.show', $item->id) }}">
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
    </section>
@endsection

@push('style')
    <style>
        .kelompok-card-wrapper {
            border-radius: 14px;
        }

        .kelompok-item {
            border: 1px solid #e9ecef;
            border-radius: 12px;
            padding: 16px;
            background: linear-gradient(180deg, #ffffff 0%, #fafbff 100%);
            transition: all 0.2s ease;
        }

        .kelompok-item:hover {
            border-color: #cfd8ff;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06);
            transform: translateY(-2px);
        }

        .kelompok-title {
            font-size: 1rem;
            font-weight: 700;
            color: #2f3b52;
        }

        .kelompok-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .meta-item {
            flex: 1 1 48%;
            background: #f6f8fc;
            border-radius: 8px;
            padding: 8px 10px;
            border: 1px solid #edf0f6;
        }

        .meta-item-full {
            flex: 1 1 100%;
        }

        .meta-label {
            display: block;
            font-size: 0.72rem;
            color: #74829a;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            margin-bottom: 2px;
        }

        .meta-value {
            font-size: 0.9rem;
            font-weight: 600;
            color: #27364b;
        }

        .mahasiswa-list li {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 0;
            border-bottom: 1px dashed #edf0f6;
        }

        .mahasiswa-list li:last-child {
            border-bottom: 0;
            padding-bottom: 0;
        }

        .mahasiswa-avatar {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #eef3ff;
            color: #3b5bdb;
            font-size: 0.8rem;
            flex-shrink: 0;
        }

        .mahasiswa-nama {
            font-weight: 600;
            color: #253247;
            line-height: 1.2;
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
                eventDisplay: 'block', // tampilkan seluruh title
            });

            calendar.render();
        });
    </script>
@endpush
