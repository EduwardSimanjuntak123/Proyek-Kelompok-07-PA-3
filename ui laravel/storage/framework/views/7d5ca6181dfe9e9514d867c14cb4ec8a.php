<?php $__env->startSection('title', 'Tambah Kelompok'); ?>

<?php $__env->startSection('content'); ?>
<section class="section custom-section">
    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h4>Tambah Kelompok</h4>
                        <a class="btn btn-primary btn-sm" href="<?php echo e(route('kelompokMahasiswa.index', ['id' => $kelompok->id])); ?>">Kembali</a>
                    </div>  
                    <div class="card-body">

                        
                        <?php if($errors->any()): ?>
                            <div class="alert alert-danger alert-dismissible show fade">
                                <div class="alert-body">
                                    <button class="close" data-dismiss="alert"><span>&times;</span></button>
                                    <ul>
                                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <li><?php echo e($error); ?></li>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </ul>
                                </div>
                            </div>
                        <?php endif; ?>
                        <form method="POST" action="<?php echo e(route('kelompokMahasiswa.store')); ?>" enctype="multipart/form-data">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="kelompok_id" value="<?php echo e($kelompok->id); ?>">
                            <?php
                            $jumlahMin = 4;
                            $jumlahMax = 6;
                        
                            if (session('KPA_id') == 1) {
                                $jumlahMin = 3;
                                $jumlahMax = 5;
                            }
                        ?>
                       <div class="alert alert-info">
                        <strong>Petunjuk:</strong> Pilih minimal <strong><?php echo e($jumlahMin); ?></strong> mahasiswa dan maksimal <strong><?php echo e($jumlahMax); ?></strong> mahasiswa untuk dimasukkan ke dalam kelompok.
                    </div>

                    <form method="POST" action="<?php echo e(route('kelompokMahasiswa.store')); ?>" enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="kelompok_id" value="<?php echo e($kelompok->id); ?>">

                        <div class="row">
                            <?php $__empty_1 = true; $__currentLoopData = $mahasiswa; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <div class="col-md-6 col-lg-5 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="user_id[]" value="<?php echo e($item['user_id']); ?>" id="mhs<?php echo e($item['user_id']); ?>">
                                        <label class="form-check-label" for="mhs<?php echo e($item['user_id']); ?>">
                                            <?php echo e($item['nim']); ?> — <?php echo e($item['nama']); ?>

                                        </label>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <div class="col-12">
                                    <div class="alert alert-warning">
                                        Semua mahasiswa sudah memiliki kelompok.
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-plus-circle mr-1"></i> Tambah
                            </button>
                        </div>
                    </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>
    
<?php echo $__env->make('layouts.main', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\Semester 6\PA III\Project\Proyek-Kelompok-07-PA-3\ui laravel\resources\views/pages/Koordinator/kelompok-mahasiswa/create.blade.php ENDPATH**/ ?>