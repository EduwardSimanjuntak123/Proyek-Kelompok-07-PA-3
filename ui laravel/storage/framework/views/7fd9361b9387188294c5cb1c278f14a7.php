<?php $__env->startSection('title', 'Edit Dosen Role'); ?>

<?php $__env->startSection('content'); ?>
    <section class="section">
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <?php echo $__env->make('partials.alert', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <h4>Edit Dosen Role</h4>
                            <a href="<?php echo e(route('manajemen-role.index')); ?>" class="btn btn-primary">Kembali</a>
                        </div>
                        <div class="card-body">

                            <form method="POST"
                                action="<?php echo e(route('manajemen-role.update', Crypt::encrypt($dosenRole['id']))); ?>"
                                enctype="multipart/form-data">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('PUT'); ?>

                                
                                <div class="form-group">
                                    <label for="user_id">Pilih Dosen</label>
                                    <select id="user_id" name="user_id" class="select2 form-control" required>
                                        <option value="">-- Pilih Dosen --</option>
                                        <?php $__currentLoopData = $dosen; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($item['user_id'] ?? ''); ?>"
                                                data-nama="<?php echo e($item['nama'] ?? 'Tanpa Nama'); ?>"
                                                <?php echo e($item['user_id'] == $dosenRole['user_id'] ? 'selected' : ''); ?>>
                                                <?php echo e($item['nama'] ?? 'Tanpa Nama'); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label>Role</label>

                                    <input type="text" class="form-control" value="Koordinator" readonly>

                                    <input type="hidden" name="role_id" value="<?php echo e($dosenRole->role_id); ?>">
                                </div>
                                
                                <div class="form-group">
                                    <label for="prodi_id">Pilih Prodi</label>
                                    <select id="prodi_id" name="prodi_id" class="select2 form-control" required>
                                        <option value="">-- Pilih Prodi --</option>
                                        <?php $__currentLoopData = $prodi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($item['id']); ?>"
                                                data-nama="<?php echo e($item['nama_prodi'] ?? 'Tanpa Nama'); ?>"
                                                <?php echo e(old('prodi_id', $dosenRole['prodi_id'] ?? '') == $item['id'] ? 'selected' : ''); ?>>
                                                <?php echo e($item['nama_prodi']); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label for="KPA_id">Kategori Proyek Akhir</label>
                                    <select id="KPA_id" name="KPA_id" class="select2 form-control" required>
                                        <option value="">-- Pilih Kategori Proyek Akhir --</option>
                                        <?php $__currentLoopData = $kategoripa; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($item->id); ?>"
                                                <?php echo e(old('TA_id', $dosenRole->KPA_id) == $item->id ? 'selected' : ''); ?>>
                                                <?php echo e($item->kategori_pa); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label for="TM_id">Tahun Masuk</label>
                                    <select name="TM_id" id="TM_id" class="select2 form-control" required>
                                        <option value="">-- Pilih Tahun Masuk --</option>
                                        <?php $__currentLoopData = $tahun_masuk; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($item->id); ?>"
                                                <?php echo e(old('TM_id', $dosenRole->TM_id) == $item->id ? 'selected' : ''); ?>>
                                                <?php echo e($item->Tahun_Masuk); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label>Tahun Ajaran</label>
                                    <input type="text" class="form-control"
                                        value="<?php echo e($dosenRole->tahunAjaran->tahun_mulai); ?> / <?php echo e($dosenRole->tahunAjaran->tahun_selesai); ?>"
                                        readonly>
                                    <input type="hidden" name="tahun_ajaran_id" value="<?php echo e($dosenRole->tahun_ajaran_id); ?>">
                                </div>
                                
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="Aktif"
                                            <?php echo e(old('status', $dosenRole->status) == 'Aktif' ? 'selected' : ''); ?>>Aktif
                                        </option>
                                        <option value="Tidak-Aktif"
                                            <?php echo e(old('status', $dosenRole->status) == 'Tidak-Aktif' ? 'selected' : ''); ?>>
                                            Tidak-Aktif</option>
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

<?php echo $__env->make('layouts.main', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\Semester 6\PA III\Project\Proyek-Kelompok-07-PA-3\ui laravel\resources\views/pages/BAAK/Kordinator/edit.blade.php ENDPATH**/ ?>