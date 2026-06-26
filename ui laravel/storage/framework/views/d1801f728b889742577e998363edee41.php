<?php $__env->startSection('title', 'Tambah Bimbingan'); ?>

<?php $__env->startSection('content'); ?>
<section class="section custom-section">
    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h4>Tambah Request Bimbingan</h4>
                        <a class="btn btn-primary btn-sm" href="<?php echo e(route('bimbingan.index')); ?>">Kembali</a>
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

                        <form method="POST" action="<?php echo e(route('bimbingan.store')); ?>" enctype="multipart/form-data">
                            <?php echo csrf_field(); ?>

                        
                       
                        <div class="form-group">
                            <input type="hidden" name="kelompok_id" value="<?php echo e($kelompokId); ?>">
                        </div>

                     
                        <div class="form-group">
                             <label for="keperluan">Keperluan</label>
                               <input type="text" name="keperluan" id="keperluan" class="form-control" required>
                         </div>


                         
                     <div class="form-group">
                        <label for="rencana_mulai">Rencana Mulai Bimbingan</label>
                        <input type="datetime-local" name="rencana_mulai" id="rencana_mulai" class="form-control <?php $__errorArgs = ['rencana_mulai'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('rencana_mulai') ? old('rencana_mulai') : ''); ?>">
                        <?php $__errorArgs = ['rencana_mulai'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    
                    <div class="form-group">
                        <label for="rencana_selesai">Rencana Selesai Bimbingan</label>
                        <input type="datetime-local" name="rencana_selesai" id="rencana_selesai" class="form-control <?php $__errorArgs = ['rencana_selesai'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('rencana_selesai') ? old('rencana_selesai') : ''); ?>">
                        <?php $__errorArgs = ['rencana_selesai'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                     <!-- 
                     <div class="form-group">
                        <label for="rencana_mulai">Rencana Mulai Bimbingan</label>
                        <input type="datetime-local" name="rencana_mulai" id="rencana_mulai" class="form-control <?php $__errorArgs = ['rencana_mulai'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('rencana_mulai') ? old('rencana_mulai') : ''); ?>">
                        <?php $__errorArgs = ['rencana_mulai'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div> -->

                   
                   <div class="form-group">
                                <label for="ruangan">Ruangan</label>
                                <select name="ruangan_id" id="ruangan_id" class="form-control" required>
                                    <option value="">-- Pilih Ruangan --</option>
                                    <?php $__currentLoopData = $ruangan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($item->id); ?>" <?php echo e(old('ruangan_id') == $item->id ? 'selected' : ''); ?>>
                                        <?php echo e($item->ruangan); ?>

                                    </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>    

                    
                    <div class="form-group">
                        <input type="hidden" name="status" value="menunggu">
                    </div>
                            <button type="submit" class="btn btn-primary">Tambah</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>
    
<?php echo $__env->make('layouts.main', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\Semester 6\PA III\Project\Proyek-Kelompok-07-PA-3\ui laravel\resources\views/pages/Mahasiswa/Bimbingan/create.blade.php ENDPATH**/ ?>