<?php $__env->startSection('title', 'Histori'); ?>

<?php $__env->startSection('content'); ?>
    <section class="section custom-section">
        <div class="section-body">
            <div class="card">
                <div class="card-header">
                    <h4>Histori Dosen</h4>
                </div>

                <div class="card-body">

                    
                    <form method="GET" action="<?php echo e(route('Histori.index')); ?>" class="mb-4">
                        <div class="row">

                            <div class="col-md-4">
                                <label>Tahun Ajaran</label>
                                <select name="tahun_ajaran" class="form-control">
                                    <option value="">-- Pilih Tahun Ajaran --</option>
                                    <?php $__currentLoopData = $tahunAjaran; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($ta->id); ?>"
                                            <?php echo e(request('tahun_ajaran') == $ta->id ? 'selected' : ''); ?>>
                                            <?php echo e($ta->tahun_mulai); ?> / <?php echo e($ta->tahun_selesai); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label>Tahun Masuk</label>
                                <select name="tahun_masuk" class="form-control">
                                    <option value="">-- Pilih Tahun Masuk --</option>
                                    <?php $__currentLoopData = $tahunMasuk; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($tm->id); ?>"
                                            <?php echo e(request('tahun_masuk') == $tm->id ? 'selected' : ''); ?>>
                                            <?php echo e($tm->Tahun_Masuk); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label>Program Studi</label>
                                <select name="prodi" class="form-control">
                                    <option value="">-- Pilih Program Studi --</option>
                                    <?php $__currentLoopData = $prodi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($p->id); ?>"
                                            <?php echo e(request('prodi') == $p->id ? 'selected' : ''); ?>>
                                            <?php echo e($p->nama_prodi); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>

                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary btn-block">
                                    <i class="fas fa-search"></i> Filter
                                </button>
                            </div>

                        </div>
                    </form>

                    
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th>No</th>
                                    <th>Nomor Kelompok</th>
                                    <th>Tahun Ajaran</th>
                                    <th>Tahun Masuk</th>
                                    <th>Program Studi</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $kelompok; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><?php echo e($loop->iteration); ?></td>
                                        <td><?php echo e($item->nomor_kelompok); ?></td>
                                        <td>
                                            <?php echo e($item->tahunAjaran ? $item->tahunAjaran->tahun_mulai . ' / ' . $item->tahunAjaran->tahun_selesai : '-'); ?>

                                        </td>
                                        <td><?php echo e($item->TahunMasuk->Tahun_Masuk ?? '-'); ?></td>
                                        <td><?php echo e($item->prodi->nama_prodi ?? '-'); ?></td>
                                        <td>
                                            <a href="<?php echo e(route('Histori.detail', $item->id)); ?>" class="btn btn-info btn-sm">
                                                <i class="fas fa-eye"></i> Detail
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">
                                            Belum ada data histori.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.main', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\Semester 6\PA III\Project\Proyek-Kelompok-07-PA-3\ui laravel\resources\views/pages/mahasiswa/Histori/index.blade.php ENDPATH**/ ?>