<?php $__env->startSection('title', 'Jadwal Mahasiswa'); ?>

<?php $__env->startSection('content'); ?>
<section class="section custom-section">
    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h4>Jadwal Mahasiswa</h4>
                    </div>                    
                    <div class="card-body">
                        <?php echo $__env->make('partials.alert', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

                        <?php if(session('error')): ?>
                        <div class="alert alert-warning">
                            <?php echo e(session('error')); ?>

                        </div>
                        <?php endif; ?>

                        <?php if(isset($jadwalUtama)): ?>
                        <div class="mb-4">
                            <h5>Jadwal Seminar Kelompok Anda</h5>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <tr>
                                        <th>Nomor Kelompok</th>
                                        <td><?php echo e($jadwalUtama->kelompok->nomor_kelompok ?? '-'); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Ruangan</th>
                                        <td><?php echo e($jadwalUtama->ruangan->ruangan); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Waktu Sidang</th>
                                        <td><?php echo e(\Carbon\Carbon::parse($jadwalUtama->waktu)->translatedFormat('l, d F Y - H:i')); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Penguji</th>
                                        <td><?php echo $pengujiNama; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Pembimbing</th>
                                        <td>
                                            <?php if(!empty($pembimbingNama)): ?>
                                                <?php $__currentLoopData = $pembimbingNama; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $nama): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <div><?php echo e($nama); ?></div>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if(isset($jadwalLain) && $jadwalLain->count() > 0): ?>
                        <h5>Jadwal Seminar Kelompok Lainnya</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Nomor Kelompok</th>
                                        <th>Ruangan</th>
                                        <th>Waktu</th>
                                        <th>Penguji</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $jadwalLain; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $jadwal): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($jadwal->kelompok->nomor_kelompok ?? '-'); ?></td>
                                        <td><?php echo e($jadwal->ruangan->ruangan); ?></td>
                                        <td><?php echo e(\Carbon\Carbon::parse($jadwal->waktu)->translatedFormat('l, d F Y - H:i')); ?></td>
                                        <td><?php echo $jadwal->penguji_nama; ?></td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                        <?php endif; ?>

                        <?php if(!isset($jadwalUtama)): ?>
                        <div class="alert alert-info">
                            Jadwal belum tersedia untuk kelompok Anda.
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.main', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\Semester 6\PA III\Project\Proyek-Kelompok-07-PA-3\ui laravel\resources\views/pages/Mahasiswa/Jadwal/index.blade.php ENDPATH**/ ?>