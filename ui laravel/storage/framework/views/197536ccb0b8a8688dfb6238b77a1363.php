<?php $__env->startSection('title', 'Tambah Pembimbing'); ?>

<?php $__env->startSection('content'); ?>
    <section class="section custom-section">
        <div class="section-body">
            <div class="row">
                <div class="col-12">

                    <div class="card">

                        <div class="card-header d-flex justify-content-between">
                            <h4>Tambah Pembimbing Kelompok</h4>

                            <a class="btn btn-primary btn-sm" href="<?php echo e(route('pembimbing.index')); ?>">
                                Kembali
                            </a>

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


                            <form method="POST" action="<?php echo e(route('pembimbing.store')); ?>">
                                <?php echo csrf_field(); ?>

                                <input type="hidden" name="kelompok_id" value="<?php echo e($kelompok_id); ?>">

                                
                                <div class="form-group">

                                    <label>Pembimbing 1</label>

                                    <select name="pembimbing1" class="form-control select2">

                                        <option value="">-- Pilih Pembimbing 1 --</option>

                                        <?php $__currentLoopData = $dosen; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($item['user_id']); ?>">
                                                <?php echo e($item['nama']); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                    </select>

                                </div>

                                
                                <div class="form-group">

                                    <label>Pembimbing 2</label>

                                    <select name="pembimbing2" class="form-control select2">

                                        <option value="">-- Pilih Pembimbing 2 --</option>

                                        <?php $__currentLoopData = $dosen; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($item['user_id']); ?>">
                                                <?php echo e($item['nama']); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                    </select>

                                </div>

                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Simpan
                                </button>

                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.main', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\Semester 6\PA III\Project\Proyek-Kelompok-07-PA-3\ui laravel\resources\views/pages/Koordinator/pembimbing/create.blade.php ENDPATH**/ ?>