<?php $__env->startSection('title', 'Edit Anggota Kelompok'); ?>

<?php $__env->startSection('content'); ?>
<section class="section">
    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <?php echo $__env->make('partials.alert', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h4>Edit Anggota Kelompok</h4>
                        <a href="<?php echo e(route('kelompok.index')); ?>" class="btn btn-primary">Kembali</a>
                    </div>
                    <div class="card-body">

                        <form method="POST" action="<?php echo e(route('kelompokMahasiswa.update', $kelompokMahasiswa->id)); ?>" enctype="multipart/form-data">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PUT'); ?>
                            <div class="form-group">
                                <label for="user_id">Pilih Mahasiswa</label>
                                <select id="user_id" name="user_id" class="select2 form-control" required>
                                     <option value="">-- Pilih Mahasiswa --</option>
                                     <?php $__currentLoopData = $mahasiswabelummasuk; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option 
                                    value="<?php echo e($item['user_id'] ?? ''); ?>"
                                    data-nama="<?php echo e($item['nama'] ?? 'Tanpa Nama'); ?>"
                                    <?php echo e($item['user_id'] == $kelompokMahasiswa['user_id'] ? 'selected' : ''); ?>

                                >
                                   <?php echo e($item['nim'] ?? 'Tanpa Nim'); ?> -<?php echo e($item['nama'] ?? 'Tanpa Nama'); ?>

                                </option> 
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?> 
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

<?php echo $__env->make('layouts.main', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\Semester 6\PA III\Project\Proyek-Kelompok-07-PA-3\ui laravel\resources\views/pages/Koordinator/kelompok-mahasiswa/edit.blade.php ENDPATH**/ ?>