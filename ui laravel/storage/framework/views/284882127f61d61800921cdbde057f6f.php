<?php $__env->startSection('title', 'Tambah Kelompok'); ?>

<?php $__env->startSection('content'); ?>
    <section class="section custom-section">
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <h4>Tambah Kelompok</h4>
                            <a class="btn btn-primary btn-sm" href="<?php echo e(route('kelompok.index')); ?>">Kembali</a>
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

                            <form method="POST" action="<?php echo e(route('kelompok.store')); ?>" enctype="multipart/form-data">
                                <?php echo csrf_field(); ?>

                                
                                <div class="form-group">
                                    <label for="nomor_kelompok">Nomor Kelompok</label>
                                    <input type="text" name="nomor_kelompok" id="nomor_kelompok" class="form-control"
                                        required>
                                </div>

                                
                                <div class="form-group">
                                    <label for="TM_id">Tahun Tahun Masuk</label>
                                    <input type="text" class="form-control"
                                        value="<?php echo e($tahun_masuk->Tahun_Masuk ?? '-'); ?>" readonly>
                                    <input type="hidden" name="TM_id" value="<?php echo e($tahun_masuk->id); ?>">
                                </div>

                                
                                <div class="form-group">
                                    <label>Tahun Ajaran</label>
                                    <input type="text" class="form-control"
                                        value="<?php echo e($tahunAjaran->tahun_mulai); ?> / <?php echo e($tahunAjaran->tahun_selesai); ?>"
                                        readonly>

                                    <input type="hidden" name="tahun_ajaran_id" value="<?php echo e($tahunAjaran->id); ?>">
                                </div>

                                
                                <div class="form-group">
                                    <label for="prodi_id">Program Studi</label>
                                    <input type="text" class="form-control" value="<?php echo e($prodi->nama_prodi ?? '-'); ?>"
                                        readonly>
                                    <input type="hidden" name="prodi_id" value="<?php echo e($prodi->id); ?>">
                                </div>

                                
                                <div class="form-group">
                                    <label for="KPA_id">Jenis PA</label>
                                    <input type="text" class="form-control"
                                        value="<?php echo e($kategoripa->kategori_pa ?? '-'); ?>" readonly>
                                    <input type="hidden" name="KPA_id" value="<?php echo e($kategoripa->id); ?>">
                                </div>

                                
                                <div class="form-group">
                                    <input type="hidden" name="status" value="Aktif">
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

<?php echo $__env->make('layouts.main', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\Semester 6\PA III\Project\Proyek-Kelompok-07-PA-3\ui laravel\resources\views/pages/Koordinator/kelompok/create.blade.php ENDPATH**/ ?>