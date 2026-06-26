<?php $__env->startSection('title', 'Detail Administratif'); ?>

<?php $__env->startSection('content'); ?>
    <section class="section">

        
        <div class="section-header">
            <div>
                <h1>Detail Status Administratif</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item">
                        <a href="<?php echo e(route('dashboard.koordinator')); ?>">
                            Dashboard
                        </a>
                    </div>
                    <div class="breadcrumb-item">
                        Detail Administratif
                    </div>
                </div>
            </div>
        </div>

        <div class="section-body">

            
            <div class="row mb-4">

                <div class="col-lg-4 col-md-6 col-sm-6">
                    <div class="card card-statistic-1 shadow-sm">
                        <div class="card-icon bg-primary">
                            <i class="fas fa-users"></i>
                        </div>

                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Total Kelompok</h4>
                            </div>

                            <div class="card-body">
                                <?php echo e($jumlah_kelompok); ?>

                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 col-sm-6">
                    <div class="card card-statistic-1 shadow-sm">
                        <div class="card-icon bg-success">
                            <i class="fas fa-check-circle"></i>
                        </div>

                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Selesai</h4>
                            </div>
                            <div class="card-body">
                                <?php echo e($selesai); ?>

                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 col-sm-6">
                    <div class="card card-statistic-1 shadow-sm">
                        <div class="card-icon bg-warning">
                            <i class="fas fa-clock"></i>
                        </div>

                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Berlangsung</h4>
                            </div>
                            <div class="card-body">
                                <?php echo e($berlangsung); ?>

                            </div>
                        </div>
                    </div>
                </div>
            </div>


            
            
            <div class="card shadow-sm border-0 mb-4">

                <div class="card-body py-3">

                    <form method="GET" action="<?php echo e(url()->current()); ?>">

                        <div class="row align-items-end">

                            
                            <div class="col-lg-4 col-md-6 mb-3">

                                <label class="font-weight-bold text-dark mb-2">
                                    Status Kelompok
                                </label>

                                <select name="status" class="form-control shadow-sm">

                                    <option value="">
                                        Semua Status
                                    </option>

                                    <option value="Selesai" <?php echo e(request('status') == 'Selesai' ? 'selected' : ''); ?>>
                                        Selesai
                                    </option>

                                    <option value="Berlangsung" <?php echo e(request('status') == 'Berlangsung' ? 'selected' : ''); ?>>
                                        Berlangsung
                                    </option>

                                </select>

                            </div>

                            
                            <div class="col-lg-4 col-md-6 mb-3">

                                <label class="font-weight-bold text-dark mb-2">
                                    Urutkan Data
                                </label>

                                <select name="sort" class="form-control shadow-sm">

                                    <option value="">
                                        Default
                                    </option>

                                    <option value="tinggi" <?php echo e(request('sort') == 'tinggi' ? 'selected' : ''); ?>>
                                        Progress Tertinggi
                                    </option>

                                    <option value="rendah" <?php echo e(request('sort') == 'rendah' ? 'selected' : ''); ?>>
                                        Progress Terendah
                                    </option>

                                    <option value="terbaru" <?php echo e(request('sort') == 'terbaru' ? 'selected' : ''); ?>>
                                        Terbaru
                                    </option>

                                </select>

                            </div>

                            
                            <div class="col-lg-4 col-md-12 mb-3">

                                <div class="d-flex flex-wrap align-items-center" style="gap:10px;">

                                    
                                    <button type="submit" class="btn btn-primary shadow-sm px-4">

                                        <i class="fas fa-search mr-1"></i>
                                        Cari

                                    </button>

                                    
                                    <a href="<?php echo e(url()->current()); ?>" class="btn btn-light border shadow-sm px-4">

                                        <i class="fas fa-redo-alt mr-1"></i>
                                        Reset

                                    </a>

                                </div>

                            </div>

                        </div>

                    </form>

                </div>

            </div>


            
            <div class="card shadow-sm">

                <div class="card-header">
                    <h4>Monitoring Administrasi Kelompok</h4>
                </div>

                <div class="card-body p-0">

                    <div class="table-responsive">

                        <table class="table table-hover">

                            <thead class="thead-light">

                                <tr>
                                    <th>Kelompok</th>
                                    <th>Jumlah Anggota</th>
                                    <th>Bimbingan</th>
                                    <th>Submit Artefak</th>
                                    <th>Maju Seminar</th>
                                    <th>Progress</th>
                                    <th>Status</th>
                                </tr>

                            </thead>

                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $daftar_kelompok; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kelompok): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>

                                        <td>
                                            <div class="font-weight-bold text-primary">
                                                <?php echo e($kelompok->nomor_kelompok); ?>

                                            </div>

                                            <small class="text-muted">
                                                PA-<?php echo e($KPA_id); ?> Tahun 2025/2026
                                            </small>
                                        </td>

                                        <td>
                                            <?php echo e($kelompok->jumlah_anggota ?? 0); ?> Mahasiswa
                                        </td>

                                        <td>

                                            <?php
                                                $jumlahSesi = $kelompok->jumlah_bimbingan_selesai ?? 0;
                                                $persentase = min(($jumlahSesi / 8) * 100, 100);
                                            ?>

                                            <div class="progress" data-height="10">

                                                <div class="progress-bar bg-primary" style="width: <?php echo e($persentase); ?>%">
                                                </div>

                                            </div>



                                            <small>
                                                <?php echo e($kelompok->jumlah_bimbingan_selesai ?? 0); ?>/8 sesi
                                            </small>

                                        </td>

                                        <td>

                                            <?php if($kelompok->jumlah_artefak_submit > 0): ?>
                                                <span class="badge badge-success">
                                                    Lengkap
                                                </span>
                                            <?php else: ?>
                                                <span class="badge badge-danger">
                                                    Belum
                                                </span>
                                            <?php endif; ?>

                                        </td>

                                        <td>

                                            <?php if($kelompok->sudah_memiliki_jadwal): ?>
                                                <span class="badge badge-info">
                                                    Terjadwal
                                                </span>
                                            <?php else: ?>
                                                <span class="badge badge-warning">
                                                    Belum Terjadwal
                                                </span>
                                            <?php endif; ?>

                                        </td>

                                        <td>

                                            <?php
                                                $progressCount = 0;

                                                // 1. Bimbingan minimal 8x
                                                if (($kelompok->jumlah_bimbingan_selesai ?? 0) >= 8) {
                                                    $progressCount++;
                                                }

                                                // 2. Artefak sudah submit
                                                if (($kelompok->jumlah_artefak_submit ?? 0) > 0) {
                                                    $progressCount++;
                                                }

                                                // 3. Seminar sudah terjadwal
                                                if ($kelompok->sudah_memiliki_jadwal) {
                                                    $progressCount++;
                                                }

                                                $persentaseProgress = ($progressCount / 3) * 100;
                                            ?>

                                            <div class="d-flex align-items-center">

                                                <div class="progress flex-grow-1 mr-2" data-height="8">

                                                    <div class="progress-bar 
            <?php if($progressCount == 3): ?> bg-success
            <?php elseif($progressCount == 2): ?>
                bg-warning
            <?php else: ?>
                bg-danger <?php endif; ?>
        "
                                                        style="width: <?php echo e($persentaseProgress); ?>%">
                                                    </div>

                                                </div>

                                                <small>
                                                    <?php echo e($progressCount); ?>/3
                                                </small>

                                            </div>

                                        </td>

                                        <td>

                                            <?php if($progressCount >= 3): ?>
                                                <span class="badge badge-success">
                                                    Selesai
                                                </span>
                                            <?php else: ?>
                                                <span class="badge badge-warning">
                                                    Berlangsung
                                                </span>
                                            <?php endif; ?>

                                        </td>

                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">
                                            Belum ada data kelompok
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>

                    </div>

                </div>


                
                <div class="card-footer bg-white border-0 py-3">

                    <div class="d-flex justify-content-between align-items-center flex-wrap">

                        <small class="text-muted mb-2 mb-md-0">
                            Menampilkan
                            <?php echo e($daftar_kelompok->firstItem() ?? 0); ?>

                            -
                            <?php echo e($daftar_kelompok->lastItem() ?? 0); ?>


                            dari

                            <?php echo e($daftar_kelompok->total()); ?>

                            kelompok
                        </small>

                        <div>
                            <?php echo e($daftar_kelompok->withQueryString()->links()); ?>

                        </div>

                    </div>

                </div>

            </div>

    </section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.main', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\Semester 6\PA III\Project\Proyek-Kelompok-07-PA-3\ui laravel\resources\views/pages/Koordinator/Detail/detail-administratif.blade.php ENDPATH**/ ?>