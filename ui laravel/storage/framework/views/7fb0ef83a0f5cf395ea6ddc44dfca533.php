<?php $__env->startSection('title', 'View'); ?>

<?php $__env->startSection('content'); ?>
    <section class="section custom-section">
        <div class="section-body">
            <div class="row">
                <div class="col-14 col-md-10 offset-md-1">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <h4>Detail Tugas</h4>
                            <a class="btn btn-primary btn-sm" href="<?php echo e(route('koordinator.tugas.index')); ?>">Kembali</a>
                        </div>
                        <div class="card-body">
                            <?php echo $__env->make('partials.alert', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                            <table class="table table-bordered">
                                <tr>
                                    <th>Judul</th>
                                    <td><?php echo e($tugas->Judul_Tugas); ?></td>
                                </tr>
                                <tr>
                                    <th>Instruksi</th>
                                    <td><?php echo e($tugas->Deskripsi_Tugas); ?></td>
                                </tr>
                                <tr>
                                    <th>File</th>
                                    <td>
                                        <?php if($tugas->file): ?>
                                            <a href="<?php echo e(asset('storage/' . $tugas->file)); ?>" target="_blank"
                                                class="btn btn-info btn-sm">
                                                Lihat File
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted">Tidak ada file</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Batas Waktu</th>
                                    <td><?php echo e(\Carbon\Carbon::parse($tugas->batas)->format('d-m-Y H:i')); ?></td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td><?php echo e($tugas->status); ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.main', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\Semester 6\PA III\Project\Proyek-Kelompok-07-PA-3\ui laravel\resources\views/pages/Koordinator/tugas/show.blade.php ENDPATH**/ ?>