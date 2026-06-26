<?php $__env->startSection('title', 'Edit Pembimbing'); ?>

<?php $__env->startSection('content'); ?>
    <section class="section">
        <div class="section-body">
            <div class="row">
                <div class="col-12">

                    <?php echo $__env->make('partials.alert', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

                    <div class="card">

                        <div class="card-header d-flex justify-content-between">
                            <h4>Edit Pembimbing Kelompok</h4>

                            <a href="<?php echo e(route('pembimbing.index')); ?>" class="btn btn-primary btn-sm">
                                Kembali
                            </a>
                        </div>

                        <div class="card-body">

                            <form method="POST" action="<?php echo e(route('pembimbing.update', Crypt::encrypt($kelompok_id))); ?>">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('PUT'); ?>

                                <input type="hidden" name="kelompok_id" value="<?php echo e($kelompok_id); ?>">

                                
                                <div class="form-group">

                                    <label>Kelompok</label>

                                    <input type="text" class="form-control"
                                        value="<?php echo e($kelompok->nomor_kelompok ?? '-'); ?>" readonly>

                                </div>

                                
                                <div class="form-group">
                                    <label>Pembimbing 1</label>
                                    <select name="pembimbing1" class="form-control select2">
                                        
                                        <?php if($dosenPembimbing1): ?>
                                            <option value="<?php echo e($dosenPembimbing1->user_id); ?>" selected>
                                                <?php echo e($dosenPembimbing1->nama); ?> (Pembimbing Lama)
                                            </option>
                                        <?php endif; ?>

                                        <option value="">-- Pilih Pembimbing 1 --</option>

                                        <?php $__currentLoopData = $dosen; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php if(!$dosenPembimbing1 || $item['user_id'] != $dosenPembimbing1->user_id): ?>
                                                <option value="<?php echo e($item['user_id']); ?>">
                                                    <?php echo e($item['nama']); ?>

                                                </option>
                                            <?php endif; ?>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>

                                
                                <div class="form-group">
                                    <label>Pembimbing 2</label>
                                    <select name="pembimbing2" class="form-control select2">
                                        
                                        <?php if($dosenPembimbing2): ?>
                                            <option value="<?php echo e($dosenPembimbing2->user_id); ?>" selected>
                                                <?php echo e($dosenPembimbing2->nama); ?> (Pembimbing Lama)
                                            </option>
                                        <?php endif; ?>

                                        <option value="">-- Pilih Pembimbing 2 --</option>

                                        <?php $__currentLoopData = $dosen; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php if(!$dosenPembimbing2 || $item['user_id'] != $dosenPembimbing2->user_id): ?>
                                                <option value="<?php echo e($item['user_id']); ?>">
                                                    <?php echo e($item['nama']); ?>

                                                </option>
                                            <?php endif; ?>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>

                                <button type="submit" class="btn btn-primary">

                                    <i class="fas fa-save"></i> Simpan Perubahan

                                </button>

                            </form>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.main', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\Semester 6\PA III\Project\Proyek-Kelompok-07-PA-3\ui laravel\resources\views/pages/Koordinator/pembimbing/edit.blade.php ENDPATH**/ ?>