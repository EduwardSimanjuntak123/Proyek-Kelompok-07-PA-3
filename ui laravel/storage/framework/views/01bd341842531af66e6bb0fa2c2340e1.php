<?php $__env->startSection('title', 'Dashboard'); ?>

<?php $__env->startSection('content'); ?>
    <section class="section">
        <div class="section-header">
            <h1>Dashboard Penguji</h1>
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
                                <h4>Jumlah Kelompok Diujikan</h4>
                            </div>
                            <div class="card-body">
                                <?php echo e($jumlah_kelompok); ?>

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
                                <?php echo e($jumlah_pengumuman); ?>

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
                                <?php echo e($jumlah_tugas); ?>

                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card shadow-sm border-0 penguji-groups-card">
                        <div class="card-header bg-white border-bottom-0 pb-0">
                            <h4 class="mb-1">Daftar Kelompok yang Diuji</h4>
                            <p class="text-muted mb-0">Lihat kelompok yang menjadi tanggung jawab pengujian Anda beserta
                                daftar mahasiswa di dalamnya.</p>
                        </div>
                        <div class="card-body">
                            <?php if($kelompokList->isEmpty()): ?>
                                <div class="alert alert-light border mb-0">
                                    Belum ada kelompok yang ditugaskan untuk Anda pada konteks prodi, kategori PA, dan tahun
                                    masuk saat ini.
                                </div>
                            <?php else: ?>
                                <div class="row">
                                    <?php $__currentLoopData = $kelompokList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kelompok): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="col-lg-6 col-12 mb-4">
                                            <div class="penguji-group-item h-100">
                                                <div class="d-flex justify-content-between align-items-start mb-3">
                                                    <div>
                                                        <h6 class="mb-1 penguji-group-title">Kelompok
                                                            <?php echo e($kelompok['nomor_kelompok']); ?></h6>
                                                        <span
                                                            class="badge badge-light"><?php echo e($kelompok['status_kelompok']); ?></span>
                                                    </div>
                                                    <div class="text-right">
                                                        <span
                                                            class="badge badge-success px-3 py-2"><?php echo e($kelompok['jumlah_anggota']); ?>

                                                            Mahasiswa</span>
                                                        <?php if($kelompok['posisi_penguji']): ?>
                                                            <div class="mt-2">
                                                                <span class="badge badge-info">Penguji
                                                                    <?php echo e($kelompok['posisi_penguji']); ?></span>
                                                                <?php if($kelompok['jumlah_penguji'] > 1): ?>
                                                                    <small class="d-block mt-1 text-muted">dari
                                                                        <?php echo e($kelompok['jumlah_penguji']); ?>

                                                                        penguji</small>
                                                                <?php endif; ?>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>

                                                <div class="penguji-meta mb-3">
                                                    <div class="meta-item meta-item-full">
                                                        <span class="meta-label">Jadwal Seminar</span>
                                                        <span class="meta-value">
                                                            <?php echo e($kelompok['jadwal'] ? \Carbon\Carbon::parse($kelompok['jadwal']['waktu_mulai'])->format('d M Y H:i') . ' - ' . \Carbon\Carbon::parse($kelompok['jadwal']['waktu_selesai'])->format('H:i') : 'Belum dijadwalkan'); ?>

                                                        </span>
                                                    </div>
                                                </div>

                                                <div>
                                                    <strong class="d-block mb-2">Daftar Mahasiswa</strong>
                                                    <?php if(collect($kelompok['anggota'])->isEmpty()): ?>
                                                        <p class="text-muted mb-0">Belum ada mahasiswa dalam kelompok ini.
                                                        </p>
                                                    <?php else: ?>
                                                        <ul class="list-unstyled mb-0 mahasiswa-list">
                                                            <?php $__currentLoopData = $kelompok['anggota']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $anggota): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                <li>
                                                                    <span class="mahasiswa-avatar">
                                                                        <i class="fas fa-user-graduate"></i>
                                                                    </span>
                                                                    <div>
                                                                        <div class="mahasiswa-nama"><?php echo e($anggota['nama']); ?>

                                                                        </div>
                                                                        <small class="text-muted">NIM:
                                                                            <?php echo e($anggota['nim']); ?>

                                                                            · Angkatan: <?php echo e($anggota['angkatan']); ?></small>
                                                                    </div>
                                                                </li>
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                        </ul>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            <?php endif; ?>
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
                            <?php if($pengumuman->isEmpty()): ?>
                                <p class="text-muted">Belum ada pengumuman.</p>
                            <?php else: ?>
                                <ul class="list-group list-group-flush">
                                    <?php $__currentLoopData = $pengumuman; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li class="list-group-item">
                                            <strong><?php echo e($index + 1); ?>.</strong>
                                            <a href="<?php echo e(route('pengumuman.penguji.show', $item->id)); ?>">
                                                <?php echo e($item->judul); ?>

                                            </a>
                                        </li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('style'); ?>
    <style>
        .penguji-groups-card {
            border-radius: 14px;
        }

        .penguji-group-item {
            border: 1px solid #e9ecef;
            border-radius: 12px;
            padding: 16px;
            background: linear-gradient(180deg, #ffffff 0%, #fafcff 100%);
            transition: all 0.2s ease;
        }

        .penguji-group-item:hover {
            border-color: #d5e3ff;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06);
            transform: translateY(-2px);
        }

        .penguji-group-title {
            font-size: 1rem;
            font-weight: 700;
            color: #2f3b52;
        }

        .penguji-meta {
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
<?php $__env->stopPush(); ?>
<?php $__env->startPush('script'); ?>
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
                events: <?php echo json_encode($events, 15, 512) ?>, // events: array of { title, start, end }
                eventDisplay: 'block', // tampilkan seluruh title
            });

            calendar.render();
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.main', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\Semester 6\PA III\Project\Proyek-Kelompok-07-PA-3\ui laravel\resources\views/pages/Penguji/dashboard.blade.php ENDPATH**/ ?>