<?php $__env->startSection('title', 'Edit Tahun Masuk'); ?>

<?php $__env->startSection('content'); ?>
<section class="section">
    <div class="section-body">
        <form action="<?php echo e(route('TahunMasuk.update',['id' =>Crypt::encrypt($TahunMasuk->id)])); ?>" method="POST" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            <div class="card">
                <div class="card-header"><h4>Edit Tahun Masuk</h4></div>
                <div class="card-body">
                    <label for="Tahun_Masuk">Tahun Masuk</label>
                    <select name="Tahun_Masuk" class="form-control" required>
                        <option value="">-- Pilih Tahun --</option>
                        <?php for($year = date('Y'); $year >= 2000; $year--): ?>
                            <option value="<?php echo e($year); ?>" <?php echo e($TahunMasuk->Tahun_Masuk == $year ? 'selected' : ''); ?>><?php echo e($year); ?></option>
                        <?php endfor; ?>
                    </select>
                    
                    <div class="form-group">
                        <label>Status</label>
                        <select name="Status" class="form-control" required>
                            <option value="Aktif" <?php echo e($TahunMasuk->Status == 'Aktif' ? 'selected' : ''); ?>>Aktif</option>
                            <option value="Tidak-Aktif" <?php echo e($TahunMasuk->Status == 'Tidak-Aktif' ? 'selected' : ''); ?>>Non-aktif</option>
                        </select>
                    </div>
                <div class="card-footer text-end">
                    <button class="btn btn-primary" type="submit">Update</button>
                </div>
            </div>
        </form>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.main', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\Semester 6\PA III\Project\Proyek-Kelompok-07-PA-3\ui laravel\resources\views/pages/BAAK/TahunMasuk/edit.blade.php ENDPATH**/ ?>