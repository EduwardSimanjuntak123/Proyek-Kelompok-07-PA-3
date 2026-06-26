<?php $__env->startSection('title', 'Dashboard'); ?>

<?php $__env->startSection('content'); ?>
    <section class="section">
        <div class="section-header">
            <h1>Dashboard Mahasiswa</h1>
        </div>

        <div class="section-body">

            
            <div class="row">
                
                <div class="col-lg-4 col-md-6 col-12">
                    <div class="card card-statistic-1 shadow-sm">
                        <div class="card-icon bg-primary text-white">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Jumlah Mahasiswa</h4>
                            </div>
                            <div class="card-body">
                                <?php echo e($mahasiswa_kelompok->count()); ?> Orang
                            </div>
                        </div>
                    </div>
                </div>

                
                <div class="col-lg-4 col-md-6 col-12">
                    <div class="card card-statistic-1 shadow-sm">
                        <div class="card-icon bg-success text-white">
                            <i class="fas fa-user-check"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Pembimbing</h4>
                            </div>
                            <div class="card-body">
                                <?php echo e($pembimbing->count()); ?> Orang
                            </div>
                        </div>
                    </div>
                </div>

                
                <div class="col-lg-4 col-md-6 col-12">
                    <div class="card card-statistic-1 shadow-sm">
                        <div class="card-icon bg-danger text-white">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Penguji</h4>
                            </div>
                            <div class="card-body">
                                <?php echo e($penguji->count()); ?> Orang
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row mt-5">
                
                <div class="col-lg-5">
                    <div class="card shadow-sm">
                        <div class="card-header bg-light">
                            <h4 class="mb-0">Anggota Kelompok</h4>
                        </div>
                        <div class="card-body p-3">
                            <?php $__empty_1 = true; $__currentLoopData = $mahasiswa_kelompok; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                                    <div>
                                        <strong><?php echo e($item->nama); ?></strong> <br>
                                        <small class="text-muted">NIM: <?php echo e($item->nim); ?> | Angkatan:
                                            <?php echo e($item->angkatan); ?></small>
                                    </div>
                                    <span class="badge badge-info">Mahasiswa</span>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <p class="text-muted">Belum ada anggota kelompok.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                
                <div class="col-lg-7">
                    
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-light">
                            <h4 class="mb-0">Dosen Pembimbing</h4>
                        </div>
                        <div class="card-body p-3">
                            <?php $__empty_1 = true; $__currentLoopData = $pembimbing; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                                    <div>
                                        <strong><?php echo e($item->nama); ?></strong>
                                    </div>
                                    <span class="badge badge-success">Pembimbing</span>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <p class="text-muted">Belum ada pembimbing.</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    
                    <div class="card shadow-sm">
                        <div class="card-header bg-light">
                            <h4 class="mb-0">Dosen Penguji</h4>
                        </div>
                        <div class="card-body p-3">
                            <?php $__empty_1 = true; $__currentLoopData = $penguji; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                                    <div>
                                        <strong><?php echo e($item->nama); ?></strong>
                                    </div>
                                    <span class="badge badge-danger">Penguji</span>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <p class="text-muted">Belum ada penguji.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            
            


        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.main', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\Semester 6\PA III\Project\Proyek-Kelompok-07-PA-3\ui laravel\resources\views/pages/Mahasiswa/dashboard.blade.php ENDPATH**/ ?>