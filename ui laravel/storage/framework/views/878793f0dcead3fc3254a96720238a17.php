<?php $__env->startSection('title', 'Edit Kelompok'); ?>

<?php $__env->startSection('content'); ?>
<section class="section">
    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <?php echo $__env->make('partials.alert', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h4>Edit Kelompok</h4>
                        <a href="<?php echo e(route('kelompok.index')); ?>" class="btn btn-primary">Kembali</a>
                    </div>
                    <div class="card-body">

                        <form method="POST" action="<?php echo e(route('kelompok.update', Crypt::encrypt($kelompok['id']))); ?>" enctype="multipart/form-data">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PUT'); ?>

                                
                            <div class="form-group">
                                <label for="nomor_kelompok">Nomor Kelompok</label>
                                <input type="text" class="form-control" id="nomor_kelompok" name="nomor_kelompok" value="<?php echo e($kelompok->nomor_kelompok); ?>" required>
                            </div>
                             
                             <div class="form-group">
                                <label for="KPA_id">Jenis PA (Readonly)</label>
                                <input type="text" class="form-control" value="<?php echo e($kelompok->kategoripa->kategori_pa ?? '-'); ?>" readonly>
                                <input type="hidden" name="KPA_id" value="<?php echo e($kelompok->KPA_id); ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="prodi_id">Program Studi (Readonly)</label>
                                <input type="text" class="form-control" value="<?php echo e($kelompok->prodi->nama_prodi ?? '-'); ?>" readonly>
                                <input type="hidden" name="prodi_id" value="<?php echo e($kelompok->prodi_id); ?>">
                            </div>

                            
                            <div class="form-group">
                                <label for="TA_id">Tahun Angkatan (Readonly)</label>
                                <input type="text" class="form-control" value="<?php echo e($kelompok->tahunMasuk->Tahun_Masuk ?? '-'); ?>" readonly>
                                <input type="hidden" name="TM_id" value="<?php echo e($kelompok->TM_id); ?>">
                            </div>
                           
                           <div class="form-group">
                            <label for="status">Status</label>
                            <select name="status" id="status" class="form-control">
                                <option value="Aktif" <?php echo e(old('status', $kelompok->status) == 'Aktif' ? 'selected' : ''); ?>>Aktif</option>
                                <option value="Tidak-Aktif" <?php echo e(old('status', $kelompok->status) == 'Tidak-Aktif' ? 'selected' : ''); ?>>Tidak-Aktif</option>
                            </select>
                        </div>

                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.main', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\Semester 6\PA III\Project\Proyek-Kelompok-07-PA-3\ui laravel\resources\views/pages/Koordinator/kelompok/edit.blade.php ENDPATH**/ ?>