@extends('layouts.main')
@section('title', 'Dashboard')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Dashboard Pembimbing</h1>
        </div>

        <div class="section-body">

            {{-- =============================================
             STAT CARDS
        ============================================= --}}
            <div class="row mb-5">

                <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-primary">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Kelompok</h4>
                            </div>
                            <div class="card-body">
                                {{ $jumlah_kelompok }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-info">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Mahasiswa</h4>
                            </div>
                            <div class="card-body">
                                {{ $jumlahMahasiswa ?? 0 }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-success">
                            <i class="fas fa-history"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Bimbingan</h4>
                            </div>
                            <div class="card-body">
                                {{ $jumlah_bimbingan ?? 0 }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            {{-- =============================================
             KALENDER + PENGUMUMAN
        ============================================= --}}


            {{-- =============================================
             VISUALIZATION GRID ROW 1
        ============================================= --}}
            <div class="row">

                <div class="col-lg-8 col-12 mb-4">
                    <div class="card shadow-sm border-0 h-100">

                        <div class="card-header bg-white d-flex justify-content-between align-items-center">

                            <h4 class="mb-0">
                                Aktivitas Bimbingan
                            </h4>

                            <select class="form-control form-control-sm w-auto">
                                <option>Per Semester</option>
                                <option>Per Bulan</option>
                            </select>

                        </div>

                        <div class="card-body">

                            <div style="height:260px">

                                <canvas id="chartAktivitas"></canvas>

                            </div>

                        </div>

                    </div>
                </div>


                <div class="col-lg-4 col-12 mb-4">

                    <div class="card shadow-sm border-0 h-100">

                        <div class="card-header bg-white">

                            <h4 class="mb-0">
                                Status Submitan Artefak
                            </h4>

                        </div>

                        <div class="card-body">

                            <div style="height:260px">

                                <canvas id="chartDonut"></canvas>

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            {{-- =============================================
             VISUALIZATION GRID ROW 2
        ============================================= --}}
            <div class="row">

                <div class="col-lg-4 mb-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white">
                            <h4>Jumlah Bimbingan per Kelompok</h4>
                        </div>

                        <div class="card-body">
                            <div style="height:220px">
                                <canvas id="chartBarBimbingan"></canvas>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="col-lg-4 mb-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white">
                            <h4>Nilai Bimbingan Kelompok</h4>
                        </div>

                        <div class="card-body">
                            <div style="height:220px">
                                <canvas id="chartHorizontalBar"></canvas>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="col-lg-4 mb-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white">
                            <h4>Nilai Pameran VS Bimbingan VS Administrasi Tiap Kelompok</h4>
                        </div>

                        <div class="card-body">
                            <div style="height:220px">
                                <canvas id="chartGroupedBar"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- =============================================
                     PROGRESS TIMELINE
                ============================================= --}}
            {{-- <div class="col-12 mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-bottom-0 text-center">
                        <h4 class="mb-0">Alur Progress Bimbingan</h4>
                    </div>
                    <div class="card-body">
                        <div class="progress-timeline">
                            <div class="pt-connector"></div>
                            <div class="pt-connector-fill" style="width:33%"></div>
                            @php
                                $steps = [
                                    ['icon' => 'fa-file-alt', 'label' => 'Proposal', 'done' => true],
                                    ['icon' => 'fa-comments', 'label' => 'Bimbingan', 'done' => true],
                                    [
                                        'icon' => 'fa-cloud-upload-alt',
                                        'label' => 'Artefak',
                                        'done' => false,
                                        'active' => true,
                                    ],
                                    ['icon' => 'fa-chalkboard-teacher', 'label' => 'Seminar', 'done' => false],
                                    ['icon' => 'fa-edit', 'label' => 'Revisi', 'done' => false],
                                    ['icon' => 'fa-check-double', 'label' => 'Selesai', 'done' => false],
                                ];
                            @endphp
                            @foreach ($steps as $step)
                                <div
                                    class="pt-step {{ $step['done'] ? 'done' : '' }} {{ isset($step['active']) && $step['active'] ? 'active' : '' }}">
                                    <div class="pt-icon">
                                        <i class="fas {{ $step['icon'] }}"></i>
                                    </div>
                                    <span class="pt-label">{{ $step['label'] }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div> --}}

            {{-- =============================================
                     MONITORING TABLE
                ============================================= --}}
            <div class="col-12 mb-4">
                <div class="card shadow-sm border-0">
                    <div
                        class="card-header bg-white border-bottom-0 d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <h4 class="mb-0">Monitoring Progress Kelompok</h4>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-filter mr-1"></i> Filter
                            </button>
                            <button class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-sort mr-1"></i> Urutkan
                            </button>
                            <button class="btn btn-primary btn-sm">
                                <i class="fas fa-download mr-1"></i> Export Data
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 monitoring-table">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Kelompok</th>
                                        <th>Jumlah Bimbingan</th>
                                        <th>Status Artefak</th>
                                        <th>Jadwal Seminar</th>
                                        <th>Status Akhir</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($kelompokList as $kelompok)
                                        <tr class="monitoring-row">
                                            <td>
                                                <p class="mb-0 font-weight-bold text-primary">Kelompok
                                                    {{ $kelompok['nomor_kelompok'] }}</p>
                                                <small class="text-muted">{{ $kelompok['status_kelompok'] }}</small>
                                            </td>
                                            <td>
                                                <span
                                                    class="{{ $kelompok['total_sesi_bimbingan'] < 5 ? 'text-danger font-weight-bold' : '' }}">
                                                    {{ $kelompok['total_sesi_bimbingan'] }} Sesi
                                                </span>
                                            </td>
                                            <td>
                                                @if ($kelompok['jumlah_artefak_submit'] > 0)
                                                    <span class="badge badge-success">
                                                        Lengkap
                                                    </span>
                                                @else
                                                    <span class="badge badge-danger">
                                                        Belum Submit
                                                    </span>
                                                @endif
                                                {{-- <span class="badge {{ $badgeClass }}">{{ $badgeLabel }}</span> --}}
                                            </td>

                                            <td>
                                                @if (!empty($kelompok['jadwal_seminar']))
                                                    {{ \Carbon\Carbon::parse($kelompok['jadwal_seminar'])->format('d M Y H:i') }}
                                                @else
                                                    <span class="text-muted">Belum Dijadwalkan</span>
                                                @endif
                                            </td>

                                            <td>
                                                @if ($kelompok['status_monitoring'] === 'Selesai')
                                                    <span class="badge badge-success">
                                                        Selesai
                                                    </span>
                                                @else
                                                    <span class="badge badge-warning">
                                                        Berlangsung
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-right">
                                                <i class="fas fa-chevron-right text-muted"></i>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4">
                                                Belum ada kelompok bimbingan.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- =============================================
                     DAFTAR KELOMPOK BIMBINGAN & ANGGOTA (existing)
                ============================================= --}}
            <div class="col-12 mb-4">
                <div class="card shadow-sm border-0 kelompok-card-wrapper">
                    <div class="card-header bg-white border-bottom-0 pb-0">
                        <h4 class="mb-1">Daftar Kelompok Bimbingan dan Anggota</h4>
                        <p class="text-muted mb-0">Pantau semua kelompok bimbingan beserta progres dan daftar
                            mahasiswa.</p>
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
                                                <div class="text-right">
                                                    <span
                                                        class="badge badge-info px-3 py-2">{{ $kelompok['jumlah_anggota'] }}
                                                        Mahasiswa</span>
                                                    {{-- @if ($kelompok['posisi_pembimbing'])
                                                        <div class="mt-2">
                                                            <span class="badge badge-warning">Pembimbing
                                                                {{ $kelompok['posisi_pembimbing'] }}</span>
                                                            @if ($kelompok['jumlah_pembimbing'] > 1)
                                                                <small class="d-block mt-1 text-muted">dari
                                                                    {{ $kelompok['jumlah_pembimbing'] }}
                                                                    pembimbing</small>
                                                            @endif
                                                        </div>
                                                    @endif --}}
                                                </div>
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
            <div class="row mb-5">

                <div class="col-lg-8 col-md-7 col-sm-12 mb-4">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="col-12">
                            <h2 class="text-center mb-4"><br>Kalender Jadwal Seminar</h2>
                            <div class="card shadow">
                                <div class="card-body">
                                    <div id="calendar"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-5 col-sm-12 mb-4">

                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-white">
                            <h4 class="mb-0">
                                Pengumuman
                            </h4>
                        </div>

                        <div class="card-body">

                            @if ($pengumuman->isEmpty())
                                <p class="text-muted">
                                    Belum ada pengumuman.
                                </p>
                            @else
                                <ul class="list-group list-group-flush">

                                    @foreach ($pengumuman as $index => $item)
                                        <li class="list-group-item">

                                            <strong>
                                                {{ $index + 1 }}.
                                            </strong>

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

        </div>{{-- /row --}}
        </div>{{-- /section-body --}}
    </section>
@endsection

@push('style')
    {{-- FullCalendar CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css">

    <style>
        /* ---- Stat card overrides ---- */
        .stat-active .card-icon {
            background: #16a34a !important;
        }

        .stat-danger .card-icon {
            background: #dc2626 !important;
        }

        .stat-pending .card-icon {
            background: #d97706 !important;
        }

        /* ---- Donut chart ---- */
        .donut-legend {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            font-size: 0.78rem;
            color: #555;
        }

        .donut-legend span {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .legend-dot {
            width: 10px;
            height: 10px;
            border-radius: 3px;
            display: inline-block;
            flex-shrink: 0;
        }

        .donut-center-label {
            position: absolute;
            inset: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            pointer-events: none;
        }

        .donut-pct {
            font-size: 1.4rem;
            font-weight: 700;
            color: #1a365d;
            line-height: 1.1;
        }

        .donut-sub {
            font-size: 0.7rem;
            color: #74777f;
            text-transform: uppercase;
            letter-spacing: .05em;
        }

        /* ---- Progress Timeline ---- */
        .progress-timeline {
            position: relative;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 12px 8px 0;
            max-width: 900px;
            margin: 0 auto;
        }

        .pt-connector,
        .pt-connector-fill {
            position: absolute;
            top: 29px;
            left: 32px;
            right: 32px;
            height: 2px;
            border-radius: 1px;
            z-index: 0;
        }

        .pt-connector {
            background: #e2e8f0;
        }

        .pt-connector-fill {
            background: #1a365d;
            width: 33%;
        }

        .pt-step {
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            flex: 1;
        }

        .pt-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
            background: #e2e8f0;
            color: #74777f;
            border: 2px solid #fff;
            box-shadow: 0 0 0 3px #e2e8f0;
            transition: all .2s;
        }

        .pt-step.done .pt-icon {
            background: #1a365d;
            color: #fff;
            box-shadow: 0 0 0 3px #c7d8f5;
        }

        .pt-step.active .pt-icon {
            background: #2d4a8a;
            color: #fff;
            box-shadow: 0 0 0 3px #bfd3f9;
        }

        .pt-label {
            font-size: 0.72rem;
            font-weight: 600;
            color: #74777f;
            text-align: center;
        }

        .pt-step.done .pt-label {
            color: #1a365d;
        }

        .pt-step.active .pt-label {
            color: #2d4a8a;
        }

        /* ---- Monitoring Table ---- */
        .monitoring-table th {
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: .05em;
            color: #74777f;
            font-weight: 600;
            border-top: 0;
        }

        .monitoring-row {
            cursor: pointer;
            transition: background .15s;
        }

        .monitoring-row:hover {
            background: #f6f8fc;
        }

        /* ---- Kelompok cards (existing styles preserved) ---- */
        .kelompok-card-wrapper {
            border-radius: 14px;
        }

        .kelompok-item {
            border: 1px solid #e9ecef;
            border-radius: 12px;
            padding: 16px;
            background: linear-gradient(180deg, #ffffff 0%, #fafbff 100%);
            transition: all .2s ease;
        }

        .kelompok-item:hover {
            border-color: #cfd8ff;
            box-shadow: 0 8px 20px rgba(0, 0, 0, .06);
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
            font-size: .72rem;
            color: #74829a;
            text-transform: uppercase;
            letter-spacing: .03em;
            margin-bottom: 2px;
        }

        .meta-value {
            font-size: .9rem;
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
            font-size: .8rem;
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
    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    {{-- FullCalendar --}}
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            /* -----------------------------------------------
               Shared color palette
            ----------------------------------------------- */
            const BLUE_DARK = '#1a365d';
            const BLUE_MID = '#2d4a8a';
            const BLUE_LIGHT = '#86a0cd';
            const BLUE_PALE = '#c7d8f5';
            const GRAY = '#74777f';
            const RED = '#ba1a1a';
            const GREEN = '#16a34a';
            const detailChart = @json($detailChart);
            /* -----------------------------------------------
               1. LINE CHART — Aktivitas Bimbingan
            ----------------------------------------------- */
            new Chart(document.getElementById('chartAktivitas'), {
                type: 'line',
                data: {
                    labels: ['Bulan 1', 'Bulan 2', 'Bulan 3', 'Bulan 4', 'Bulan 5', 'Bulan 6'],
                    datasets: [{
                        label: 'Sesi Bimbingan',
                        data: @json($dataChart),
                        borderColor: BLUE_DARK,
                        backgroundColor: BLUE_PALE + '55',
                        borderWidth: 2,
                        pointRadius: 3,
                        pointBackgroundColor: BLUE_DARK,
                        fill: true,
                        tension: 0.4,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            callbacks: {
                                afterBody: function(context) {

                                    const bulan = context[0].dataIndex + 1;

                                    if (!detailChart[bulan]) {
                                        return [];
                                    }

                                    return detailChart[bulan].map(item =>
                                        `${item.kelompok}: ${item.total} sesi`
                                    );
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: '#f0f0f0'
                            },
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });
            /* ---         2. DONUT CHART — Status Artefak
            ----------------------------------------------- */
            new Chart(document.getElementById('chartDonut'), {
                type: 'doughnut',
                data: {
                    labels: ['Disetujui', 'Revisi', 'Belum', 'Lainnya'],
                    datasets: [{
                        data: [75, 40, 15, 10],
                        backgroundColor: [BLUE_DARK, '#003765', RED, GRAY],
                        borderWidth: 0,
                        hoverOffset: 6,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '72%',
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });

            /* -----------------------------------------------
               3. VERTICAL BAR — Jumlah Bimbingan per Kelompok
            ----------------------------------------------- */
            const bimbinganLabels = @json($chart_kelompok_labels);
            const bimbinganData = @json($chart_bimbingan_data);

            const maxData = Math.max(...bimbinganData);

            new Chart(document.getElementById('chartBarBimbingan'), {
                type: 'bar',
                data: {
                    labels: bimbinganLabels,
                    datasets: [{
                        label: 'Sesi',
                        data: bimbinganData,
                        backgroundColor: bimbinganData.map(v => v < 8 ? RED : GREEN),
                        borderRadius: 6,
                        borderSkipped: false,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            }
                        },
                        y: {
                            beginAtZero: true,
                            suggestedMax: Math.max(10, maxData),
                            ticks: {
                                stepSize: 1
                            },
                            grid: {
                                color: '#f0f0f0'
                            }
                        }
                    }
                }
            });
            /* -----------------------------------------------
               4. HORIZONTAL BAR — Nilai Rata-rata Kelompok
            ----------------------------------------------- */
            new Chart(document.getElementById('chartHorizontalBar'), {
                type: 'bar',
                data: {
                    labels: @json($chart_kelompok_labels),
                    datasets: [{
                        label: 'Nilai',
                        data: @json($chart_nilai_bimbingan),
                        backgroundColor: BLUE_DARK,
                        borderRadius: 4,
                        borderSkipped: false,
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: ctx => ctx.parsed.x + '/100'
                            }
                        }
                    },
                    scales: {
                        x: {
                            min: 0,
                            max: 100,
                            grid: {
                                color: '#f0f0f0'
                            },
                            ticks: {
                                font: {
                                    size: 10
                                },
                                callback: v => v + '%'
                            }
                        },
                        y: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    size: 10
                                }
                            }
                        }
                    }
                }
            });

            /* -----------------------------------------------
               5. GROUPED BAR — Nilai Tiap Anggota
            ----------------------------------------------- */
            new Chart(document.getElementById('chartGroupedBar'), {
                type: 'bar',
                data: {
                    labels: @json($chart_kelompok_labels),
                    datasets: [{
                            label: 'Seminar',
                            data: @json($chart_nilai_seminar),
                            backgroundColor: BLUE_DARK,
                            borderRadius: 3,
                        },
                        {
                            label: 'Pameran',
                            data: @json($chart_nilai_pameran),
                            backgroundColor: BLUE_LIGHT,
                            borderRadius: 3,
                        },
                        {
                            label: 'Bimbingan',
                            data: @json($chart_nilai_bimbingan),
                            backgroundColor: GRAY,
                            borderRadius: 3,
                        },
                        {
                            label: 'Administrasi',
                            data: @json($chart_nilai_administrasi),
                            backgroundColor: '#16a34a',
                            borderRadius: 3,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    size: 10
                                }
                            }
                        },
                        y: {
                            beginAtZero: true,
                            max: 100,
                            grid: {
                                color: '#f0f0f0'
                            },
                            ticks: {
                                font: {
                                    size: 10
                                }
                            }
                        }
                    }
                }
            });

            /* -----------------------------------------------
               6. FULLCALENDAR (existing)
            ----------------------------------------------- */
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                themeSystem: 'bootstrap5',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: ''
                },
                events: @json($events),
                eventDisplay: 'block',
            });
            calendar.render();
        });
    </script>
@endpush
