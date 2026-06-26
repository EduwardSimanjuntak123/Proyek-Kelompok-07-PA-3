    
    <?php $__env->startSection('title', 'Tugas'); ?>

    <?php $__env->startSection('content'); ?>
    <section class="section custom-section">
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4>Proyek Akhir</h4>
                        </div>                    
                        <div class="card-body">
                            <?php echo $__env->make('partials.alert', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                            <div class="row">
                                <div class="col-12">
                                    <ul class="nav nav-tabs mb-4" style="border-bottom: 1px solid #ddd;">
                                        <li class="nav-item">
                                            <a class="nav-link " href="<?php echo e(route('artefak.index')); ?>">
                                                PENGUMPULAN BERKAS
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link " href="<?php echo e(route('status_perizinan')); ?>">
                                                STATUS PERIZINAN MAJU SEMINAR
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link " href="<?php echo e(route('jadwal.seminar')); ?>">
                                                JADWAL SEMINAR
                                            </a>
                                        </li>
                                         <li class="nav-item">
                                            <?php $__currentLoopData = $artefak; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <a class="nav-link " href="<?php echo e(route('feedback.show', Crypt::encrypt($item->id))); ?>">
                                                FEEDBACK
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link " href="<?php echo e(route('revisi.index')); ?>">
                                                BERKAS FINAL
                                            </a>
                                        </li>
                                    </ul>
                                
                            <div class="table-responsive">
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
                                        <th>Waktu Mulai</th>
                                        <td><?php echo e(\Carbon\Carbon::parse($jadwalUtama->waktu_mulai)->translatedFormat('l, d F Y - H:i')); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Waktu Selesai</th>
                                        <td><?php echo e(\Carbon\Carbon::parse($jadwalUtama->waktu_selesai)->translatedFormat('l, d F Y - H:i')); ?></td>
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
            </div>
        </div>
    </div>
    </section>
    <?php $__env->stopSection(); ?>

    <?php $__env->startPush('script'); ?>
    <script type="text/javascript">
        $(document).on('click', '.show_confirm', function(event) {
            event.preventDefault();
            var form = $(this).closest("form");
            swal({
                title: "Yakin ingin menghapus data ini?",
                text: "Data akan terhapus secara permanen!",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    form.submit();
                }
            });
        });
    </script>

    <?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.main', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\Semester 6\PA III\Project\Proyek-Kelompok-07-PA-3\ui laravel\resources\views/pages/Mahasiswa/Artefak/jadwal.blade.php ENDPATH**/ ?>