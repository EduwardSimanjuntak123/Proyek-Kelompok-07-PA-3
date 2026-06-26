<?php $__env->startSection('title', 'Create Dosen Role'); ?>

<?php $__env->startSection('content'); ?>
    <section class="section custom-section">
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <h4>Tambah Dosen Role</h4>
                            <a class="btn btn-primary btn-sm" href="<?php echo e(route('manajemen-role.index')); ?>">Kembali</a>
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

                            <form method="POST" action="<?php echo e(route('manajemen-role.store')); ?>" enctype="multipart/form-data">
                                <?php echo csrf_field(); ?>

                                
                                <div class="form-group">
                                    <label for="user_id">Pilih Dosen</label>
                                    <select id="user_id" name="user_id" class="select2 form-control" required>
                                        <option value="">-- Pilih Dosen --</option>
                                        <?php $__currentLoopData = $dosen; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($item['user_id']); ?>"
                                                <?php echo e(old('user_id') == $item['user_id'] ? 'selected' : ''); ?>>
                                                <?php echo e($item['nama']); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>

                                
                                <div class="form-group">
                                    <label>Role</label>

                                    <input type="text" class="form-control" value="Koordinator" readonly>

                                    <input type="hidden" name="role_id" value="<?php echo e($role->id); ?>">
                                </div>
                                
                                <div class="form-group">
                                    <label for="prodi_id">Pilih Prodi</label>
                                    <select id="prodi_id" name="prodi_id" class="select2 form-control" required>
                                        <option value="">-- Pilih Prodi --</option>
                                        <?php $__currentLoopData = $prodi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($item->id); ?>"
                                                <?php echo e(old('prodi_id') == $item->id ? 'selected' : ''); ?>>
                                                <?php echo e($item->nama_prodi); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label for="jenis_pa">Kategori Proyek Akhir</label>
                                    <select id="KPA_id" name="KPA_id" class="select2 form-control" required>
                                        <option value="">-- Pilih Kategori Proyek Akhir --</option>
                                        <?php $__currentLoopData = $kategoripa; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option
                                                value="<?php echo e($item->id); ?>"<?php echo e(old('KPA_id') == $item->id ? 'selected' : ''); ?>>
                                                <?php echo e($item->kategori_pa); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                                
                                
                                <div class="form-group">
                                    <label for="tahun_Masuk_id">Tahun Masuk</label>
                                    <select name="TM_id" id="TM_id" class="select2 form-control" required>

                                        <option value="">-- Pilih Tahun Masuk --</option>
                                        <?php $__currentLoopData = $tahun_masuk; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($item->id); ?>"
                                                <?php echo e(old('TM_id') == $item->id ? 'selected' : ''); ?>>
                                                <?php echo e($item->Tahun_Masuk); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label>Tahun Ajaran</label>

                                    <input type="text" class="form-control"
                                        value="<?php echo e($tahunAjaranAktif->tahun_mulai); ?> / <?php echo e($tahunAjaranAktif->tahun_selesai); ?>"
                                        readonly>

                                    <input type="hidden" name="tahun_ajaran_id" value="<?php echo e($tahunAjaranAktif->id); ?>">
                                </div>
                                
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <input type="text" class="form-control" value="Aktif" readonly>
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

<?php echo $__env->make('layouts.main', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\Semester 6\PA III\Project\Proyek-Kelompok-07-PA-3\ui laravel\resources\views/pages/BAAK/Kordinator/create.blade.php ENDPATH**/ ?>