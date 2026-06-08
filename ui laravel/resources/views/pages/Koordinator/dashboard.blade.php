@extends('layouts.main')
@section('title', 'Dashboard')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>
                Dashboard Dosen Koordinator —
                <span style="color: #4C9BC8;">
                    {{ str_replace('PA-', 'PA ', $kpa->kategori_pa) }}
                </span>
            </h1>
        </div>

        <div class="section-body">

            {{-- ══════════════════════════════════════════════════════════
                 HERO AGENT PA — CTA UTAMA
            ═══════════════════════════════════════════════════════════ --}}
            <div class="row mb-4">
                <div class="col-12">

                    @if (session('success'))
                        <script>
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: '{{ session('success') }}'
                            });
                        </script>
                    @endif

                    {{-- ── Hero Card ── --}}
                    <div class="hero-agent-card mb-3">
                        {{-- Dekorasi latar --}}
                        <div class="hero-blob hero-blob-1"></div>
                        <div class="hero-blob hero-blob-2"></div>
                        <div class="hero-blob hero-blob-3"></div>

                        <div class="hero-inner">

                            {{-- Kiri: teks + CTA --}}
                            <div class="hero-left">
                                <span class="hero-badge">
                                    <i class="fas fa-sparkles"></i>
                                    Fitur baru — Agent PA
                                </span>

                                <h2 class="hero-title">
                                    Kelola seminar<br>
                                    <span class="hero-title-accent">lebih cepat &amp; cerdas</span><br>
                                    dengan Agent PA
                                </h2>

                                <p class="hero-sub">
                                    Agent PA mengotomatiskan seluruh alur koordinasi — dari cek kelengkapan
                                    nilai, rekap akhir, hingga notifikasi ke semua pihak.
                                    <strong>Satu klik, semua beres.</strong>
                                </p>

                                <div class="hero-cta-row">
                                    <a href="{{ route('ai.kelompok') }}" class="btn-agent-primary">
                                        <span class="pulse-dot"></span>
                                        <i class="fas fa-robot"></i>
                                        Buka Agent PA sekarang
                                    </a>
                                    {{-- <a href="#" class="btn-agent-ghost">
                                        <i class="fas fa-info-circle"></i>
                                        Pelajari dulu
                                    </a> --}}
                                </div>

                                <div class="hero-tags">
                                    <span class="hero-tag tag-teal">
                                        <i class="fas fa-check-circle"></i> Bebas input manual
                                    </span>
                                    <span class="hero-tag tag-blue">
                                        <i class="fas fa-bolt"></i> Respons instan
                                    </span>
                                    <span class="hero-tag tag-purple">
                                        <i class="fas fa-shield-alt"></i> Terintegrasi sistem
                                    </span>
                                </div>

                                <div class="hero-stats">
                                    <div class="hero-stat-item">
                                        <span class="hero-stat-val">3×</span>
                                        <span class="hero-stat-lbl">lebih cepat</span>
                                    </div>
                                    <div class="hero-stat-divider"></div>
                                    <div class="hero-stat-item">
                                        <span class="hero-stat-val">0</span>
                                        <span class="hero-stat-lbl">rekap manual</span>
                                    </div>
                                    <div class="hero-stat-divider"></div>
                                    <div class="hero-stat-item">
                                        <span class="hero-stat-val">100%</span>
                                        <span class="hero-stat-lbl">otomatis</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Kanan: feature pills --}}
                            <div class="hero-pills">
                                @foreach ([['icon' => 'fa-chart-bar', 'cls' => 'purple', 'title' => 'Rekap nilai otomatis', 'desc' => 'Semua komponen dihitung instan'], ['icon' => 'fa-users', 'cls' => 'teal', 'title' => 'Cek kelengkapan penilai', 'desc' => 'Tahu siapa yang belum mengisi'], ['icon' => 'fa-whatsapp fab', 'cls' => 'amber', 'title' => 'Kirim notifikasi WA', 'desc' => 'Langsung dari satu tempat'], ['icon' => 'fa-file-check', 'cls' => 'blue', 'title' => 'Validasi dokumen', 'desc' => 'Terdeteksi otomatis sebelum sidang']] as $f)
                                    <div class="hero-pill-item">
                                        <div class="pill-icon pill-{{ $f['cls'] }}">
                                            <i class="fas {{ $f['icon'] }}"></i>
                                        </div>
                                        <div>
                                            <p class="pill-title">{{ $f['title'] }}</p>
                                            <p class="pill-desc">{{ $f['desc'] }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                        </div>
                    </div>

                    {{-- ══════════════════════════════════════════════════════════
                         VERIFICATION FLOW
                    ═══════════════════════════════════════════════════════════ --}}
                    <div class="card mb-0">
                        <div class="card-body">
                            <div class="verification-flow-container d-flex align-items-stretch justify-content-between flex-wrap"
                                style="gap: 12px;">

                                {{-- Step 1: Kelompok --}}
                                <div class="verification-step flex-fill" data-step="kelompok"
                                    data-status="{{ $verification_status['kelompok'] ?? 'pending' }}"
                                    style="min-width: 180px;">
                                    <div class="step-circle">
                                        <span class="step-number">1</span>
                                        <svg class="status-checkmark" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="3" stroke-linecap="round">
                                            <polyline points="20 6 9 17 4 12"></polyline>
                                        </svg>
                                    </div>
                                    <div class="step-label">
                                        <p class="step-title">Pembagian Kelompok</p>
                                        <p class="step-desc">Buat &amp; generate kelompok</p>
                                        <div class="wa-btn-wrapper" style="margin-top: 8px; display: none;">
                                            <form action="{{ route('whatsapp.sendtoMahasiswa') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="pesan"
                                                    value="📢 *PENGUMUMAN PROYEK AKHIR*\n\nHalo Mahasiswa/Dosen,\n\nKelompok Proyek Akhir telah berhasil di-generate oleh sistem.\n\nSilakan cek detail pengumuman dan pembagian kelompok pada website *Vokasi Tera*.\n\nTerima kasih">
                                                <button type="submit" class="btn-wa">
                                                    <i class="fab fa-whatsapp"></i> Kirim Notifikasi WA
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <div class="verification-arrow align-self-center">→</div>

                                {{-- Step 2: Pembimbing --}}
                                <div class="verification-step flex-fill" data-step="pembimbing"
                                    data-status="{{ $verification_status['pembimbing'] ?? 'pending' }}"
                                    style="min-width: 180px;">
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
                                            <form action="{{ route('whatsapp.sendtoPembimbing') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="pesan"
                                                    value="📢 *PENGUMUMAN PROYEK AKHIR*\n\nHalo Mahasiswa/Dosen,\n\nKelompok Proyek Akhir telah berhasil di-generate oleh sistem.\n\nSilakan cek detail pengumuman dan pembagian kelompok pada website *Vokasi Tera*.\n\nTerima kasih">
                                                <button type="submit" class="btn-wa">
                                                    <i class="fab fa-whatsapp"></i> Kirim Notifikasi WA
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <div class="verification-arrow align-self-center">→</div>

                                {{-- Step 3: Penguji --}}
                                <div class="verification-step flex-fill" data-step="penguji"
                                    data-status="{{ $verification_status['penguji'] ?? 'pending' }}"
                                    style="min-width: 180px;">
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
                                            <form action="{{ route('whatsapp.sendtoPenguji') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="pesan"
                                                    value="📢 *PENGUMUMAN PROYEK AKHIR*\n\nHalo Mahasiswa/Dosen,\n\nKelompok Proyek Akhir telah berhasil di-generate oleh sistem.\n\nSilakan cek detail pengumuman dan pembagian kelompok pada website *Vokasi Tera*.\n\nTerima kasih">
                                                <button type="submit" class="btn-wa">
                                                    <i class="fab fa-whatsapp"></i> Kirim Notifikasi WA
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <div class="verification-arrow align-self-center">→</div>

                                {{-- Step 4: Jadwal --}}
                                <div class="verification-step flex-fill" data-step="jadwal"
                                    data-status="{{ $verification_status['jadwal'] ?? 'pending' }}"
                                    style="min-width: 180px;">
                                    <div class="step-circle">
                                        <span class="step-number">4</span>
                                        <svg class="status-checkmark" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="3" stroke-linecap="round">
                                            <polyline points="20 6 9 17 4 12"></polyline>
                                        </svg>
                                    </div>
                                    <div class="step-label">
                                        <p class="step-title">Assign Jadwal Seminar</p>
                                        <p class="step-desc">Tentukan waktu seminar</p>
                                        <div class="wa-btn-wrapper" style="margin-top: 8px; display: none;">
                                            <a href="#" class="btn-wa" onclick="kirimWA('jadwal'); return false;">
                                                <i class="fab fa-whatsapp"></i> Kirim Notifikasi WA
                                            </a>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            {{-- Status Legend --}}
                            <div class="verification-legend mt-4 pt-3 border-top">
                                <div class="legend-row d-flex" style="gap: 24px;">
                                    <div class="legend-item d-flex align-items-center" style="gap: 8px;">
                                        <span class="legend-badge"
                                            style="display:inline-block;width:14px;height:14px;border-radius:50%;background:#e8f1f7;border:2px solid #4c9bc8;"></span>
                                        <span>Pending — Belum diproses</span>
                                    </div>
                                    <div class="legend-item d-flex align-items-center" style="gap: 8px;">
                                        <span class="legend-badge"
                                            style="display:inline-block;width:14px;height:14px;border-radius:50%;background:#d4f1e0;border:2px solid #22c55e;"></span>
                                        <span>Success — Sudah selesai</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            {{-- ══════════════════════════════════════════════════════════
                 STAT CARDS
            ═══════════════════════════════════════════════════════════ --}}
            <div class="row mb-4">
                <div class="col-lg-3 col-md-6 col-sm-6 col-12 mb-3">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-primary"><i class="fas fa-users"></i></div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Jumlah Mahasiswa</h4>
                            </div>
                            <div class="card-body">{{ $jumlah_mahasiswa }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 col-12 mb-3">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-success"><i class="fas fa-bullhorn"></i></div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Pengumuman</h4>
                            </div>
                            <div class="card-body">{{ $jumlah_pengumuman }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 col-12 mb-3">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-info"><i class="fas fa-chalkboard-teacher"></i></div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Jumlah Dosen</h4>
                            </div>
                            <div class="card-body">{{ $jumlah_dosen }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 col-12 mb-3">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-warning"><i class="fas fa-tasks"></i></div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Jumlah Tugas</h4>
                            </div>
                            <div class="card-body">{{ $jumlah_tugas }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ══════════════════════════════════════════════════════════
                 BAR CHART BIMBINGAN
            ═══════════════════════════════════════════════════════════ --}}
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="mb-0">Jumlah Proses Bimbingan Tiap Kelompok</h4>
                            <span class="badge badge-info">Live Updates</span>
                        </div>
                        <div class="card-body">
                            <canvas id="barBimbingan" height="120"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ══════════════════════════════════════════════════════════
                 HISTOGRAM NILAI & DONUT STATUS ADMINISTRASI
            ═══════════════════════════════════════════════════════════ --}}
            <div class="row mb-4">
                <div class="col-lg-6 col-md-12 mb-3 mb-lg-0">
                    <div class="card h-100">
                        <div class="card-header">
                            <h4 class="mb-0">Distribusi Nilai Proyek</h4>
                        </div>
                        <div class="card-body">
                            <canvas id="histogramNilai" height="160"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-12">
                    <div class="card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="mb-0">Status Administrasi</h4>
                            <a href="{{ route('detail.administratif') }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-eye mr-1"></i> Detail
                            </a>
                        </div>
                        <div class="card-body d-flex flex-column justify-content-center">
                            <div class="dashboard-donut-wrap">
                                <canvas id="donutChart" height="220"></canvas>
                            </div>
                            <div class="dashboard-donut-legend mt-4">
                                <div class="donut-legend-item d-flex align-items-center mb-2">
                                    <span class="donut-legend-dot mr-2" style="background:#10b981;"></span>
                                    <span>Selesai</span>
                                    <span class="ml-auto font-weight-bold">{{ $stat_lengkap ?? 78 }}</span>
                                </div>
                                <div class="donut-legend-item d-flex align-items-center mb-2">
                                    <span class="donut-legend-dot mr-2" style="background:#f59e0b;"></span>
                                    <span>Sedang Progress</span>
                                    <span class="ml-auto font-weight-bold">{{ $stat_menunggu ?? 32 }}</span>
                                </div>
                                <div class="donut-legend-item d-flex align-items-center">
                                    <span class="donut-legend-dot mr-2" style="background:#ef4444;"></span>
                                    <span>Belum Ada Progress</span>
                                    <span class="ml-auto font-weight-bold">{{ $stat_belum ?? 14 }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ══════════════════════════════════════════════════════════
                 PERBANDINGAN NILAI AKHIR KELOMPOK
            ═══════════════════════════════════════════════════════════ --}}
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="mb-0">Perbandingan Nilai Akhir Kelompok (Teratas)</h4>
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
            </div>

            {{-- ══════════════════════════════════════════════════════════
                 TABEL MONITORING PRIORITAS
            ═══════════════════════════════════════════════════════════ --}}
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div>
                                <h4 class="mb-0">Monitoring Prioritas</h4>
                                <small class="text-muted">Detail kelompok yang membutuhkan perhatian khusus</small>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0" id="tabelMonitoring">
                                    <thead class="thead-light">
                                        <tr>
                                            <th style="width:25%;">Kelompok</th>
                                            <th style="width:22%;">Status Administrasi</th>
                                            <th class="text-center" style="width:18%;">Bimbingan</th>
                                            <th class="text-center" style="width:15%;">Nilai Akhir</th>
                                            <th style="width:20%;">Status Sidang</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($daftar_kelompok as $kelompok)
                                            <tr>
                                                <td class="font-weight-600 py-3">
                                                    Kelompok {{ $kelompok->nomor_kelompok }}
                                                </td>
                                                <td class="py-3">
                                                    <span class="badge badge-success px-3 py-2"
                                                        style="font-size:11px;border-radius:20px;">
                                                        {{ $kelompok->status }}
                                                    </span>
                                                </td>
                                                <td class="text-center py-3">
                                                    {{ $kelompok->jumlah_bimbingan_selesai }} / 12
                                                </td>
                                                <td class="text-center py-3">
                                                    {{ number_format($kelompok->rata_nilai_akhir ?? 0, 1) }}
                                                </td>
                                                <td class="py-3">
                                                    @if ($kelompok->jadwal)
                                                        <span style="color:#16a34a;font-size:12px;font-weight:600;">
                                                            <i class="fas fa-circle mr-1" style="font-size:7px;"></i>
                                                            {{ \Carbon\Carbon::parse($kelompok->jadwal->waktu_mulai)->format('d M Y H:i') }}
                                                        </span>
                                                    @else
                                                        <span style="color:#6b7280;font-size:12px;">
                                                            <i class="fas fa-circle mr-1" style="font-size:7px;"></i>
                                                            Belum Terjadwal
                                                        </span>
                                                    @endif
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

            {{-- ══════════════════════════════════════════════════════════
                 KALENDER & PENGUMUMAN
            ═══════════════════════════════════════════════════════════ --}}
            <div class="row mb-4">
                <div class="col-lg-8 col-md-12 mb-4">
                    <h2 class="text-center mb-4">Kalender Jadwal Seminar</h2>
                    <div class="card shadow h-100">
                        <div class="card-body">
                            <div id="calendar"></div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-12 mb-4">
                    <h2 class="text-center mb-4">Pengumuman</h2>
                    <div class="card shadow">
                        <div class="card-body">
                            @if ($pengumuman->isEmpty())
                                <p class="text-muted text-center">Belum ada pengumuman.</p>
                            @else
                                <ul class="list-group list-group-flush">
                                    @foreach ($pengumuman as $index => $item)
                                        <li class="list-group-item px-0">
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

        </div>{{-- /section-body --}}
    </section>
@endsection

@push('script')
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <!-- FullCalendar -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.js"></script>

    @php
        $dist_nilai = $dist_nilai ?? [62, 80, 28, 8];
        $tren_labels = $tren_labels ?? [
            'Minggu 1',
            'Minggu 2',
            'Minggu 3',
            'Minggu 4',
            'Minggu 5',
            'Minggu 6',
            'Minggu 7',
        ];
        $tren_data = $tren_data ?? [72, 74, 71, 78, 80, 76, 83];
        $bar_labels = $bar_labels ?? [
            'K-01',
            'K-02',
            'K-03',
            'K-04',
            'K-05',
            'K-06',
            'K-07',
            'K-08',
            'K-09',
            'K-10',
            'K-11',
            'K-12',
        ];
        $bar_data = $bar_data ?? [12, 10, 14, 2, 11, 1, 9, 13, 6, 2, 15, 10];
        $events = $events ?? [];
    @endphp

    <script>
        /* ── Verification status helpers ── */
        function applyVerificationStatus(data) {
            ['kelompok', 'pembimbing', 'penguji', 'jadwal'].forEach(function(step) {
                var status = data[step] || 'pending';
                var el = document.querySelector('.verification-step[data-step="' + step + '"]');
                if (!el) return;
                el.setAttribute('data-status', status);
                var wa = el.querySelector('.wa-btn-wrapper');
                if (wa) wa.style.display = (status === 'success') ? 'block' : 'none';
            });
        }

        function refreshVerificationStatus() {
            fetch('{{ route('koordinator.getVerificationStatus') }}', {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(r => r.json())
                .then(json => {
                    if (json.success && json.data) applyVerificationStatus(json.data);
                })
                .catch(err => console.warn('Gagal refresh status verifikasi:', err));
        }

        document.addEventListener('DOMContentLoaded', function() {

            /* Apply status awal dari server */
            applyVerificationStatus(@json($verification_status ?? []));

            /* Auto-refresh setiap 60 detik */
            setInterval(refreshVerificationStatus, 60000);

            /* ── 1. Donut ── */
            var donutEl = document.getElementById('donutChart');
            if (donutEl) {
                new Chart(donutEl, {
                    type: 'doughnut',
                    data: {
                        labels: ['Selesai', 'Sedang Progress', 'Belum Ada Progress'],
                        datasets: [{
                            data: [{{ $stat_lengkap ?? 78 }}, {{ $stat_menunggu ?? 32 }},
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
            }

            /* ── 2. Bar Bimbingan ── */
            var barEl = document.getElementById('barBimbingan');
            if (barEl) {
                var barRaw = @json($bar_data);
                new Chart(barEl, {
                    type: 'bar',
                    data: {
                        labels: @json($bar_labels),
                        datasets: [{
                            label: 'Pertemuan Bimbingan',
                            data: barRaw,
                            backgroundColor: barRaw.map(v => v > 8 ? '#4C9BC8' : '#f59e0b'),
                            borderRadius: 4,
                            borderSkipped: false
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
                                max: 16,
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
            }

            /* ── 3. Histogram Nilai ── */
            var histEl = document.getElementById('histogramNilai');
            if (histEl) {
                new Chart(histEl, {
                    type: 'bar',
                    data: {
                        labels: ['A', 'B', 'C', 'D'],
                        datasets: [{
                            label: 'Jumlah Kelompok',
                            data: @json($dist_nilai),
                            backgroundColor: '#4C9BC8',
                            borderRadius: 6,
                            borderSkipped: false
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
            }

            /* ── 4. Line Tren ── */
            var lineEl = document.getElementById('lineNilai');
            if (lineEl) {
                new Chart(lineEl, {
                    type: 'line',
                    data: {
                        labels: @json($tren_labels),
                        datasets: [{
                            label: 'Rata-rata Nilai',
                            data: @json($tren_data),
                            borderColor: '#4C9BC8',
                            backgroundColor: 'rgba(76,155,200,0.08)',
                            borderWidth: 2.5,
                            pointBackgroundColor: '#4C9BC8',
                            pointRadius: 4,
                            tension: 0.35,
                            fill: true
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
            }

            /* ── 5. Filter Tabel ── */
            var filterStatus = document.getElementById('filterStatus');
            if (filterStatus) {
                filterStatus.addEventListener('change', function() {
                    var val = this.value.toLowerCase();
                    document.querySelectorAll('#tabelMonitoring tbody tr').forEach(function(row) {
                        var admin = (row.getAttribute('data-admin') || '').toLowerCase();
                        row.style.display = (!val || admin.includes(val)) ? '' : 'none';
                    });
                });
            }

            /* ── 6. FullCalendar ── */
            var calendarEl = document.getElementById('calendar');
            if (calendarEl) {
                var calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    themeSystem: 'bootstrap5',
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: ''
                    },
                    events: @json($events),
                    eventDisplay: 'block'
                });
                calendar.render();
            }
        });
    </script>

    {{-- ══════════════════════════════════════════════════════════
         STYLES — HERO AGENT PA
    ═══════════════════════════════════════════════════════════ --}}
    <style>
        /* ─── Hero Card ─────────────────────────────────────────── */
        .hero-agent-card {
            position: relative;
            overflow: hidden;
            border-radius: 14px;
            border: 1px solid #c8dff0;
            background: linear-gradient(135deg, #e8f4fb 0%, #eeedfe 55%, #e1f5ee 100%);
            padding: 2rem 2rem 1.75rem;
        }

        /* Dekorasi blob latar */
        .hero-blob {
            position: absolute;
            border-radius: 50%;
            pointer-events: none;
        }

        .hero-blob-1 {
            top: -70px;
            right: -70px;
            width: 280px;
            height: 280px;
            background: #534AB7;
            opacity: 0.07;
        }

        .hero-blob-2 {
            bottom: -50px;
            left: 25%;
            width: 200px;
            height: 200px;
            background: #0F6E56;
            opacity: 0.05;
        }

        .hero-blob-3 {
            top: 30px;
            left: -40px;
            width: 140px;
            height: 140px;
            background: #4C9BC8;
            opacity: 0.07;
        }

        /* Layout dalam hero */
        .hero-inner {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: flex-start;
            gap: 2rem;
            flex-wrap: wrap;
        }

        .hero-left {
            flex: 1;
            min-width: 240px;
        }

        /* Badge atas */
        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #EEEDFE;
            color: #3C3489;
            font-size: 11px;
            font-weight: 600;
            padding: 4px 12px;
            border-radius: 100px;
            border: 1px solid #AFA9EC;
            margin-bottom: 1rem;
        }

        /* Judul */
        .hero-title {
            font-size: 26px;
            font-weight: 700;
            line-height: 1.25;
            margin-bottom: .5rem;
            color: #1a2e3b;
        }

        .hero-title-accent {
            color: #534AB7;
        }

        /* Subjudul */
        .hero-sub {
            font-size: 13px;
            color: #4b6476;
            line-height: 1.65;
            max-width: 400px;
            margin-bottom: 1.25rem;
        }

        /* CTA row */
        .hero-cta-row {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 14px;
        }

        /* Tombol utama */
        .btn-agent-primary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            background: #534AB7;
            color: #fff;
            font-size: 13px;
            font-weight: 700;
            border-radius: 8px;
            border: none;
            text-decoration: none;
            cursor: pointer;
            box-shadow: 0 4px 14px rgba(83, 74, 183, .35);
            transition: background .15s ease, transform .1s ease, box-shadow .15s ease;
        }

        .btn-agent-primary:hover {
            background: #3C3489;
            color: #fff;
            text-decoration: none;
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(83, 74, 183, .45);
        }

        .btn-agent-primary:active {
            transform: translateY(0);
        }

        /* Pulse dot di dalam tombol */
        .pulse-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #9FE1CB;
            box-shadow: 0 0 0 3px rgba(159, 225, 203, .35);
            animation: pulse-ring 1.6s ease-in-out infinite;
            flex-shrink: 0;
        }

        @keyframes pulse-ring {

            0%,
            100% {
                box-shadow: 0 0 0 3px rgba(159, 225, 203, .35);
            }

            50% {
                box-shadow: 0 0 0 7px rgba(159, 225, 203, .08);
            }
        }

        /* Tombol ghost */
        .btn-agent-ghost {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 10px 16px;
            background: #fff;
            color: #5a7384;
            font-size: 13px;
            font-weight: 500;
            border-radius: 8px;
            border: 1px solid #c8dff0;
            text-decoration: none;
            cursor: pointer;
            transition: background .15s ease, color .15s ease;
        }

        .btn-agent-ghost:hover {
            background: #e8f4fb;
            color: #1a2e3b;
            text-decoration: none;
        }

        /* Tag baris */
        .hero-tags {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
            margin-bottom: 1rem;
        }

        .hero-tag {
            font-size: 11px;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 100px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            border: 1px solid;
        }

        .tag-teal {
            background: #E1F5EE;
            color: #0F6E56;
            border-color: #9FE1CB;
        }

        .tag-blue {
            background: #E6F1FB;
            color: #185FA5;
            border-color: #B5D4F4;
        }

        .tag-purple {
            background: #EEEDFE;
            color: #3C3489;
            border-color: #CECBF6;
        }

        /* Stat baris */
        .hero-stats {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            border-top: 1px solid #c8dff0;
            padding-top: 14px;
        }

        .hero-stat-item {
            display: flex;
            flex-direction: column;
            gap: 2px;
            flex: 1;
            min-width: 72px;
        }

        .hero-stat-val {
            font-size: 22px;
            font-weight: 700;
            color: #534AB7;
            line-height: 1;
        }

        .hero-stat-lbl {
            font-size: 11px;
            color: #6b8fa3;
        }

        .hero-stat-divider {
            width: 1px;
            background: #c8dff0;
            align-self: stretch;
        }

        /* Feature pills kanan */
        .hero-pills {
            min-width: 210px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .hero-pill-item {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #fff;
            border: 1px solid #dde8f0;
            border-radius: 10px;
            padding: 10px 14px;
            transition: transform .15s ease, border-color .15s ease, box-shadow .15s ease;
            cursor: default;
        }

        .hero-pill-item:hover {
            transform: translateX(4px);
            border-color: #4C9BC8;
            box-shadow: 0 2px 8px rgba(76, 155, 200, .12);
        }

        .pill-icon {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
        }

        .pill-purple {
            background: #EEEDFE;
            color: #534AB7;
        }

        .pill-teal {
            background: #E1F5EE;
            color: #0F6E56;
        }

        .pill-amber {
            background: #FAEEDA;
            color: #854F0B;
        }

        .pill-blue {
            background: #E6F1FB;
            color: #185FA5;
        }

        .pill-title {
            margin: 0;
            font-size: 12px;
            font-weight: 700;
            color: #1a2e3b;
        }

        .pill-desc {
            margin: 2px 0 0;
            font-size: 11px;
            color: #6b8fa3;
        }

        /* ─── Responsive ─────────────────────────────────────────── */
        @media (max-width: 768px) {
            .hero-inner {
                flex-direction: column;
            }

            .hero-title {
                font-size: 20px;
            }

            .hero-pills {
                width: 100%;
            }
        }
    </style>

    {{-- ══════════════════════════════════════════════════════════
         STYLES — VERIFICATION FLOW
    ═══════════════════════════════════════════════════════════ --}}
    <style>
        /* Tombol WhatsApp */
        .btn-wa {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            background: #25D366;
            color: #fff;
            font-size: 12px;
            font-weight: 600;
            border-radius: 6px;
            border: none;
            text-decoration: none;
            cursor: pointer;
            transition: background .2s ease, transform .15s ease;
            box-shadow: 0 2px 6px rgba(37, 211, 102, .35);
            line-height: 1.4;
        }

        .btn-wa i {
            font-size: 15px;
            flex-shrink: 0;
        }

        .btn-wa:hover {
            background: #128C4D;
            color: #fff;
            text-decoration: none;
            transform: translateY(-1px);
        }

        .btn-wa:active {
            background: #0e6b3b;
            transform: translateY(0);
        }

        /* Verification flow */
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
            transition: all .3s ease;
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
            transition: all .3s ease;
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
            animation: checkmarkDraw .5s ease forwards;
        }

        .verification-step[data-status="success"] .status-checkmark {
            display: block;
        }

        @keyframes checkmarkDraw {
            0% {
                stroke-dasharray: 24;
                stroke-dashoffset: 24;
                transform: scale(.8);
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
            margin: 2px 0 0;
        }

        .verification-arrow {
            font-size: 24px;
            color: #4c9bc8;
            font-weight: bold;
            flex: 0 0 auto;
            margin: 0 -5px;
            padding-top: 13px;
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

    {{-- ══════════════════════════════════════════════════════════
         STYLES — DASHBOARD CHART COMPONENTS
    ═══════════════════════════════════════════════════════════ --}}
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

        .card-header .d-flex {
            flex-wrap: wrap;
            gap: 8px;
        }
    </style>
@endpush
