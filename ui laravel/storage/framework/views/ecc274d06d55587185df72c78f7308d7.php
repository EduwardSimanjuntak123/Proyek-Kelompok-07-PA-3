<?php $__env->startSection('title', 'Tambah Tahun Masuk'); ?>

<?php $__env->startSection('content'); ?>
<section class="section">
    <div class="section-body">
        <form action="<?php echo e(route('TahunMasuk.store')); ?>" method="POST"  enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <div class="card">
                <div class="card-header"><h4>Tambah Tahun Masuk</h4></div>
                <div class="card-body">
                    <label for="Tahun_Masuk">Tahun Masuk</label>
                    <select name="Tahun_Masuk" class="form-control" required>
                        <option value="">-- Pilih Tahun --</option>
                        <?php for($year = date('Y'); $year >= 2000; $year--): ?>
                            <option value="<?php echo e($year); ?>"><?php echo e($year); ?></option>
                        <?php endfor; ?>
                    </select>
                    <?php $__errorArgs = ['Tahun_Masuk'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <small class="text-danger"><?php echo e($message); ?></small>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>                    
                      
                      <div class="form-group">
                       <input type="hidden" name = "Status" value="Aktif">
                    </div>
                    <button class="btn btn-primary" type="submit">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</section>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.main', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\Semester 6\PA III\Project\Proyek-Kelompok-07-PA-3\ui laravel\resources\views/pages/BAAK/TahunMasuk/create.blade.php ENDPATH**/ ?>