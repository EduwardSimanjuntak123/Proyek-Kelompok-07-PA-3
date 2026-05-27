@extends('layouts.main')
@section('title', 'Dashboard')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Dashboard Koordinator</h1>
        </div>

        <div class="section-body">
            {{-- VERIFICATION FLOW --}}
            <div class="row mb-4">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4>List Task Koordinator PA</h4>
                            <button class="btn btn-sm btn-outline-secondary" id="btnRefreshStatus"
                                onclick="refreshVerificationStatus()">
                                <i class="fas fa-sync-alt mr-1"></i> Refresh Status
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="verification-flow-container">

                                {{-- Step 1: Kelompok --}}
                                <div class="verification-step" data-step="kelompok"
                                    data-status="{{ $verification_status['kelompok'] ?? 'pending' }}">
                                    <div class="step-circle">
                                        <span class="step-number">1</span>
                                        <svg class="status-checkmark" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="3" stroke-linecap="round">
                                            <polyline points="20 6 9 17 4 12"></polyline>
                                        </svg>
                                    </div>
                                    <div class="step-label">
                                        <p class="step-title">Pembagian Kelompok</p>
                                        <p class="step-desc">Buat & generate kelompok</p>
                                        <div class="wa-btn-wrapper" style="margin-top: 8px; display: none;">
                                            <a href="#" class="btn-wa" onclick="kirimWA('kelompok'); return false;">
                                                <i class="fab fa-whatsapp"></i>
                                                Kirim Notifikasi WA
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                {{-- Arrow --}}
                                <div class="verification-arrow">→</div>

                                {{-- Step 2: Pembimbing --}}
                                <div class="verification-step" data-step="pembimbing"
                                    data-status="{{ $verification_status['pembimbing'] ?? 'pending' }}">
                                    <div class="step-circle">
                                        <span class="step-number">2</span>
                                        <svg class="status-checkmark" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="3" stroke-linecap="round">
                                            <polyline points="20 6 9 17 4 12"></polyline>
                                        </svg>
                                    </div>
                                    <div class="step-label">
                                        <p class="step-title">Assign Dosen Pembimbing</p>
                                        <p class="step-desc">Tentukan dosen pembimbing</p>
                                        <div class="wa-btn-wrapper" style="margin-top: 8px; display: none;">
                                            <a href="#" class="btn-wa" onclick="kirimWA('pembimbing'); return false;">
                                                <i class="fab fa-whatsapp"></i>
                                                Kirim Notifikasi WA
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                {{-- Arrow --}}
                                <div class="verification-arrow">→</div>

                                {{-- Step 3: Penguji --}}
                                <div class="verification-step" data-step="penguji"
                                    data-status="{{ $verification_status['penguji'] ?? 'pending' }}">
                                    <div class="step-circle">
                                        <span class="step-number">3</span>
                                        <svg class="status-checkmark" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="3" stroke-linecap="round">
                                            <polyline points="20 6 9 17 4 12"></polyline>
                                        </svg>
                                    </div>
                                    <div class="step-label">
                                        <p class="step-title">Assign Dosen Penguji</p>
                                        <p class="step-desc">Tentukan dosen penguji</p>
                                        <div class="wa-btn-wrapper" style="margin-top: 8px; display: none;">
                                            <a href="#" class="btn-wa" onclick="kirimWA('penguji'); return false;">
                                                <i class="fab fa-whatsapp"></i>
                                                Kirim Notifikasi WA
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                {{-- Arrow --}}
                                <div class="verification-arrow">→</div>

                                {{-- Step 4: Jadwal --}}
                                <div class="verification-step" data-step="jadwal"
                                    data-status="{{ $verification_status['jadwal'] ?? 'pending' }}">
                                    <div class="step-circle">
                                        <span class="step-number">4</span>
                                        <svg class="status-checkmark" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="3" stroke-linecap="round">
                                            <polyline points="20 6 9 17 4 12"></polyline>
                                        </svg>
                                    </div>
                                    <div class="step-label">
                                        <p class="step-title">Jadwal Seminar</p>
                                        <p class="step-desc">Tentukan waktu seminar</p>
                                        <div class="wa-btn-wrapper" style="margin-top: 8px; display: none;">
                                            <a href="#" class="btn-wa" onclick="kirimWA('jadwal'); return false;">
                                                <i class="fab fa-whatsapp"></i>
                                                Kirim Notifikasi WA
                                            </a>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            {{-- Status Legend --}}
                            <div class="verification-legend mt-4 pt-3 border-top">
                                <div class="legend-row">
                                    <div class="legend-item">
                                        <span class="legend-badge"
                                            style="background: #e8f1f7; border: 2px solid #4c9bc8;"></span>
                                        <span>Pending - Belum diproses</span>
                                    </div>
                                    <div class="legend-item">
                                        <span class="legend-badge"
                                            style="background: #d4f1e0; border: 2px solid #22c55e;"></span>
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
            </div>

            {{-- ROW: Donut Chart & Bar Chart Bimbingan --}}
            <div class="row mt-4">
                <div class="col-lg-12 col-md-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4>Jumlah Proses Bimbingan Tiap Kelompok</h4>
                            <span class="badge badge-info">Live Updates</span>
                        </div>
                        <div class="card-body">
                            <canvas id="barBimbingan" height="120"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ROW: Histogram Nilai & Line Chart Tren --}}
            <div class="row mt-2">
                <div class="col-lg-6 col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Distribusi Nilai Proyek</h4>
                        </div>
                        <div class="card-body">
                            <canvas id="histogramNilai" height="160"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="mb-0">
                                Status Administrasi
                            </h4>
                            <a href="{{ route('detail.administratif') }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-eye mr-1"></i>
                                Detail
                            </a>
                        </div>
                        <div class="card-body">
                            <div class="dashboard-donut-wrap">
                                <canvas id="donutChart" height="220"></canvas>
                            </div>
                            <div class="dashboard-donut-legend mt-4">
                                <div class="donut-legend-item">
                                    <span class="donut-legend-dot" style="background:#10b981;"></span>
                                    <span>Selesai</span>
                                    <span class="ml-auto font-weight-bold">
                                        {{ $stat_lengkap ?? 78 }}
                                    </span>
                                </div>
                                <div class="donut-legend-item">
                                    <span class="donut-legend-dot" style="background:#f59e0b;"></span>
                                    <span>Sedang Progress</span>
                                    <span class="ml-auto font-weight-bold">
                                        {{ $stat_menunggu ?? 32 }}
                                    </span>
                                </div>
                                <div class="donut-legend-item">
                                    <span class="donut-legend-dot" style="background:#ef4444;"></span>
                                    <span>Belum Ada Progress</span>
                                    <span class="ml-auto font-weight-bold">
                                        {{ $stat_belum ?? 14 }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- <div class="col-lg-6 col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Tren Rata-rata Nilai Mata Kuliah</h4>
                        </div>
                        <div class="card-body">
                            <canvas id="lineNilai" height="160"></canvas>
                        </div>
                    </div>
                </div> --}}
            </div>

            {{-- ROW: Perbandingan Nilai & Timeline Tahapan --}}
            <div class="row mt-2">
                <div class="col-lg-12 col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Perbandingan Nilai Akhir Kelompok (Teratas)</h4>
                        </div>
                        <div class="card-body">
                            <div class="dashboard-progress-list">
                                @php
                                    $top_kelompok = isset($top_kelompok)
                                        ? $top_kelompok
                                        : [
                                            ['nama' => 'Kelompok K-22 (Smart Farm)', 'nilai' => 94.5],
                                            ['nama' => 'Kelompok K-12 (E-Logistics)', 'nilai' => 89.2],
                                            ['nama' => 'Kelompok K-05 (AI Tutor)', 'nilai' => 88.0],
                                            ['nama' => 'Kelompok K-31 (Health-Tech)', 'nilai' => 85.5],
                                        ];
                                @endphp
                                @foreach ($top_kelompok as $k)
                                    <div class="dashboard-progress-item mb-3">
                                        <div class="d-flex justify-content-between mb-1">
                                            <span style="font-size:13px;font-weight:600;">{{ $k['nama'] }}</span>
                                            <span style="font-size:13px;font-weight:700;">{{ $k['nilai'] }}</span>
                                        </div>
                                        <div class="progress" style="height:10px;border-radius:99px;">
                                            <div class="progress-bar bg-primary" role="progressbar"
                                                style="width:{{ $k['nilai'] }}%;border-radius:99px;"
                                                aria-valuenow="{{ $k['nilai'] }}" aria-valuemin="0"
                                                aria-valuemax="100">
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                {{-- <div class="col-lg-5 col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Monitoring Tahapan Proyek</h4>
                        </div>
                        <div class="card-body">
                            <div class="dashboard-timeline">
                                @php
                                    $tahapan = isset($tahapan)
                                        ? $tahapan
                                        : [
                                            [
                                                'label' => 'Submit Proposal',
                                                'desc' => 'Selesai pada 15 Feb 2024',
                                                'status' => 'done',
                                            ],
                                            [
                                                'label' => 'Verifikasi Dosen Pembimbing',
                                                'desc' => 'Selesai pada 20 Feb 2024',
                                                'status' => 'done',
                                            ],
                                            [
                                                'label' => 'Masa Bimbingan (Berjalan)',
                                                'desc' => 'Target Selesai: 30 April 2024',
                                                'status' => 'active',
                                            ],
                                            ['label' => 'Pendaftaran Sidang', 'desc' => '', 'status' => 'pending'],
                                            ['label' => 'Sidang Akhir', 'desc' => '', 'status' => 'pending'],
                                        ];
                                @endphp
                                @foreach ($tahapan as $t)
                                    <div class="dashboard-timeline-item {{ $t['status'] }}">
                                        <div class="timeline-dot">
                                            @if ($t['status'] === 'done')
                                                <i class="fas fa-check" style="font-size:10px;color:#fff;"></i>
                                            @elseif($t['status'] === 'active')
                                                <span class="timeline-pulse"></span>
                                            @endif
                                        </div>
                                        <div class="timeline-content">
                                            <p class="timeline-label">{{ $t['label'] }}</p>
                                            @if ($t['desc'])
                                                <p class="timeline-desc">{{ $t['desc'] }}</p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div> --}}
            </div>

            {{-- Tabel Monitoring Prioritas --}}
            <div class="row mt-2">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex flex-wrap justify-content-between align-items-start gap-2">
                            <div>
                                <h4 class="mb-0">Monitoring Prioritas</h4>
                                <small class="text-muted">Detail kelompok yang membutuhkan perhatian khusus</small>
                            </div>
                            <div class="d-flex gap-2 align-items-center" style="gap:8px;">
                                <select class="form-control form-control-sm" id="filterStatus" style="min-width:150px;">
                                    <option value="">Semua Status</option>
                                    <option value="Belum Lengkap">Belum Lengkap</option>
                                    <option value="Verifikasi">Verifikasi</option>
                                    <option value="Lengkap">Lengkap</option>
                                </select>
                                <button class="btn btn-primary btn-sm" onclick="window.print()">
                                    <i class="fas fa-download mr-1"></i> Export Data
                                </button>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0" id="tabelMonitoring">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Kelompok</th>
                                            <th>Status Administrasi</th>
                                            <th class="text-center">Bimbingan</th>
                                            <th class="text-right">Nilai Akhir</th>
                                            <th>Status Sidang</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($daftar_kelompok as $kelompok)
                                            <tr>
                                                <td style="font-weight:500;">
                                                    Kelompok {{ $kelompok->nomor_kelompok }}
                                                </td>

                                                <td>
                                                    <span class="badge badge-success" style="font-size:11px;">
                                                        {{ $kelompok->status }}
                                                    </span>
                                                </td>

                                                <td class="text-center" style="font-size:13px;">
                                                    {{ $kelompok->jumlah_bimbingan_selesai }} / 12
                                                </td>

                                                <td class="text-right" style="font-size:13px;font-weight:600;">
                                                    {{ number_format($kelompok->rata_nilai_akhir ?? 0, 1) }}
                                                </td>

                                                <td>
                                                    @if ($kelompok->jadwal)
                                                        <span style="color:#16a34a;font-size:12px;font-weight:600;">
                                                            <i class="fas fa-circle"
                                                                style="font-size:7px;vertical-align:middle;"></i>

                                                            {{ \Carbon\Carbon::parse($kelompok->jadwal->waktu_mulai)->format('d M Y H:i') }}
                                                        </span>
                                                    @else
                                                        <span style="color:#6b7280;font-size:12px;">
                                                            <i class="fas fa-circle"
                                                                style="font-size:7px;vertical-align:middle;"></i>

                                                            Belum Terjadwal
                                                        </span>
                                                    @endif
                                                </td>

                                                <td class="text-right">
                                                    <a href="#" class="text-primary"
                                                        style="font-size:12px;font-weight:600;">
                                                        Detail
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Kalender --}}
            <div class="row mt-2">
                <div class="col-12">
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
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <!-- FullCalendar JS -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.js"></script>

    @php
        $dist_nilai = isset($dist_nilai) ? $dist_nilai : [62, 80, 28, 8];

        $tren_labels = isset($tren_labels)
            ? $tren_labels
            : ['Minggu 1', 'Minggu 2', 'Minggu 3', 'Minggu 4', 'Minggu 5', 'Minggu 6', 'Minggu 7'];

        $tren_data = isset($tren_data) ? $tren_data : [72, 74, 71, 78, 80, 76, 83];

        $bar_labels = isset($bar_labels)
            ? $bar_labels
            : ['K-01', 'K-02', 'K-03', 'K-04', 'K-05', 'K-06', 'K-07', 'K-08', 'K-09', 'K-10', 'K-11', 'K-12'];

        $bar_data = isset($bar_data) ? $bar_data : [12, 10, 14, 2, 11, 1, 9, 13, 6, 2, 15, 10];

        $events = isset($events) ? $events : [];
    @endphp

    <script>
        // ============================================================
        // KONFIGURASI NOMOR & PESAN WA
        // Ganti nomor dengan format internasional tanpa tanda +
        // Contoh: 6281234567890
        // ============================================================
        const WA_CONFIG = {
            kelompok: '6281234567890',
            pembimbing: '6281234567890',
            penguji: '6281234567890',
            jadwal: '6281234567890',
        };

        const WA_MESSAGES = {
            kelompok: 'Halo, informasi bahwa pembagian kelompok PA telah selesai dilakukan. Silakan cek sistem untuk melihat detail kelompok masing-masing.',
            pembimbing: 'Halo, informasi bahwa assign dosen pembimbing PA telah selesai dilakukan. Silakan cek sistem untuk melihat dosen pembimbing kelompok Anda.',
            penguji: 'Halo, informasi bahwa assign dosen penguji PA telah selesai dilakukan. Silakan cek sistem untuk melihat dosen penguji kelompok Anda.',
            jadwal: 'Halo, informasi bahwa jadwal seminar PA telah ditetapkan. Silakan cek sistem untuk melihat jadwal seminar kelompok Anda.',
        };

        // ============================================================
        // FUNGSI KIRIM WA
        // ============================================================
        function kirimWA(step) {
            const nomor = WA_CONFIG[step];
            const pesan = encodeURIComponent(WA_MESSAGES[step]);
            window.open('https://wa.me/' + nomor + '?text=' + pesan, '_blank');
        }

        // ============================================================
        // APPLY STATUS KE DOM (tampilkan/sembunyikan tombol WA)
        // ============================================================
        function applyVerificationStatus(data) {
            var steps = ['kelompok', 'pembimbing', 'penguji', 'jadwal'];
            steps.forEach(function(step) {
                var status = data[step] || 'pending';
                var stepEl = document.querySelector('.verification-step[data-step="' + step + '"]');
                if (!stepEl) return;

                stepEl.setAttribute('data-status', status);

                var waBtn = stepEl.querySelector('.wa-btn-wrapper');
                if (waBtn) {
                    waBtn.style.display = (status === 'success') ? 'block' : 'none';
                }
            });
        }

        // ============================================================
        // REFRESH STATUS VIA AJAX
        // ============================================================
        function refreshVerificationStatus() {
            var btn = document.getElementById('btnRefreshStatus');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Memuat...';
            }

            fetch('{{ route('koordinator.getVerificationStatus') }}', {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    }
                })
                .then(function(res) {
                    return res.json();
                })
                .then(function(json) {
                    if (json.success && json.data) {
                        applyVerificationStatus(json.data);
                    }
                })
                .catch(function(err) {
                    console.warn('Gagal refresh status verifikasi:', err);
                })
                .finally(function() {
                    if (btn) {
                        btn.disabled = false;
                        btn.innerHTML = '<i class="fas fa-sync-alt mr-1"></i> Refresh Status';
                    }
                });
        }

        document.addEventListener('DOMContentLoaded', function() {

            // -- Apply status awal dari server (Blade) --
            var initialStatus = @json($verification_status ?? []);
            applyVerificationStatus(initialStatus);

            // -- Auto-refresh setiap 60 detik --
            setInterval(refreshVerificationStatus, 60000);

            // ── 1. Donut Chart Status Administrasi ──────────────────────
            new Chart(document.getElementById('donutChart'), {
                type: 'doughnut',
                data: {
                    labels: ['Lengkap', 'Menunggu Verifikasi', 'Belum Lengkap'],
                    datasets: [{
                        data: [
                            {{ $stat_lengkap ?? 78 }},
                            {{ $stat_menunggu ?? 32 }},
                            {{ $stat_belum ?? 14 }}
                        ],
                        backgroundColor: ['#10b981', '#f59e0b', '#ef4444'],
                        borderWidth: 0,
                        hoverOffset: 6
                    }]
                },
                options: {
                    cutout: '72%',
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: ctx => ' ' + ctx.label + ': ' + ctx.parsed
                            }
                        }
                    }
                }
            });

            // ── 2. Bar Chart Bimbingan Per Kelompok ─────────────────────
            var barDataRaw = @json($bar_data);
            var barColors = barDataRaw.map(v => v > 8 ? '#10b981' : '#f59e0b');

            new Chart(document.getElementById('barBimbingan'), {
                type: 'bar',
                data: {
                    labels: @json($bar_labels),
                    datasets: [{
                        label: 'Pertemuan Bimbingan',
                        data: barDataRaw,
                        backgroundColor: barColors,
                        borderRadius: 4,
                        borderSkipped: false,
                    }]
                },
                options: {
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 10,
                            grid: {
                                color: '#f0f0f0'
                            },
                            ticks: {
                                stepSize: 2
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });

            // ── 3. Histogram Distribusi Nilai ───────────────────────────
            new Chart(document.getElementById('histogramNilai'), {
                type: 'bar',
                data: {
                    labels: ['A', 'B', 'C', 'D'],
                    datasets: [{
                        label: 'Jumlah Kelompok',
                        data: @json($dist_nilai),
                        backgroundColor: '#002045',
                        borderRadius: 6,
                        borderSkipped: false,
                    }]
                },
                options: {
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: '#f0f0f0'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });

            // ── 4. Line Chart Tren Nilai ─────────────────────────────────
            new Chart(document.getElementById('lineNilai'), {
                type: 'line',
                data: {
                    labels: @json($tren_labels),
                    datasets: [{
                        label: 'Rata-rata Nilai',
                        data: @json($tren_data),
                        borderColor: '#002045',
                        backgroundColor: 'rgba(0,32,69,0.08)',
                        borderWidth: 2.5,
                        pointBackgroundColor: '#002045',
                        pointRadius: 4,
                        tension: 0.35,
                        fill: true,
                    }]
                },
                options: {
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: false,
                            grid: {
                                color: '#f0f0f0'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });

            // ── 5. Filter Tabel Monitoring ───────────────────────────────
            document.getElementById('filterStatus').addEventListener('change', function() {
                var val = this.value.toLowerCase();
                document.querySelectorAll('#tabelMonitoring tbody tr').forEach(function(row) {
                    var admin = (row.getAttribute('data-admin') || '').toLowerCase();
                    row.style.display = (!val || admin.includes(val)) ? '' : 'none';
                });
            });

            // ── 6. FullCalendar ──────────────────────────────────────────
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

    {{-- DASHBOARD CHART & COMPONENT STYLES --}}
    <style>
        .dashboard-donut-wrap {
            display: flex;
            justify-content: center;
            align-items: center;
            max-height: 220px;
        }

        .dashboard-donut-legend {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .donut-legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: #374151;
        }

        .donut-legend-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        /* Timeline Tahapan */
        .dashboard-timeline {
            display: flex;
            flex-direction: column;
            gap: 0;
            padding-left: 12px;
            border-left: 2px solid #e5e7eb;
            margin-left: 10px;
        }

        .dashboard-timeline-item {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            padding: 0 0 24px 0;
            position: relative;
        }

        .dashboard-timeline-item:last-child {
            padding-bottom: 0;
        }

        .timeline-dot {
            position: absolute;
            left: -22px;
            top: 2px;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 3px solid #fff;
            box-shadow: 0 0 0 2px #e5e7eb;
            background: #d1d5db;
            flex-shrink: 0;
        }

        .dashboard-timeline-item.done .timeline-dot {
            background: #10b981;
            box-shadow: 0 0 0 2px #10b981;
        }

        .dashboard-timeline-item.active .timeline-dot {
            background: #002045;
            box-shadow: 0 0 0 2px #002045;
        }

        .dashboard-timeline-item.pending {
            opacity: 0.45;
        }

        .timeline-pulse {
            width: 7px;
            height: 7px;
            background: #fff;
            border-radius: 50%;
            display: block;
            animation: tlpulse 1.4s ease-in-out infinite;
        }

        @keyframes tlpulse {

            0%,
            100% {
                opacity: 1;
                transform: scale(1);
            }

            50% {
                opacity: .6;
                transform: scale(.75);
            }
        }

        .timeline-content {
            padding-left: 6px;
        }

        .timeline-label {
            font-size: 12px;
            font-weight: 600;
            color: #002045;
            margin: 0;
        }

        .dashboard-timeline-item.pending .timeline-label {
            color: #374151;
        }

        .timeline-desc {
            font-size: 11px;
            color: #6b7280;
            margin: 2px 0 0;
        }

        /* Tabel Monitoring gap fix */
        .card-header .d-flex {
            flex-wrap: wrap;
            gap: 8px;
        }
    </style>

    {{-- VERIFICATION FLOW STYLES --}}
    <style>
        /* ── Tombol WhatsApp ─────────────────────────────────────────── */
        .btn-wa {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            background-color: #25D366;
            color: #fff;
            font-size: 12px;
            font-weight: 600;
            border-radius: 6px;
            border: none;
            text-decoration: none;
            cursor: pointer;
            transition: background-color 0.2s ease, box-shadow 0.2s ease, transform 0.15s ease;
            box-shadow: 0 2px 6px rgba(37, 211, 102, 0.35);
            line-height: 1.4;
        }

        .btn-wa i {
            font-size: 15px;
            flex-shrink: 0;
        }

        /* Hover: warna hijau tua yang kontras & masih terasa WA */
        .btn-wa:hover {
            background-color: #128C4D;
            color: #fff;
            text-decoration: none;
            box-shadow: 0 4px 10px rgba(18, 140, 77, 0.45);
            transform: translateY(-1px);
        }

        .btn-wa:active {
            background-color: #0e6b3b;
            transform: translateY(0);
            box-shadow: 0 2px 4px rgba(18, 140, 77, 0.3);
        }

        /* ── Verification Flow ───────────────────────────────────────── */
        .verification-flow-container {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 10px;
            flex-wrap: wrap;
            padding: 20px 0;
        }

        .verification-step {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 15px;
            border-radius: 10px;
            background: #f8fafb;
            border: 2px solid #e0e6ef;
            transition: all 0.3s ease;
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
            /* sejajarkan dengan tengah step-circle */
            padding-top: 13px;
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
                padding-top: 0;
            }

            .verification-step {
                width: 100%;
            }
        }
    </style>
@endpush
