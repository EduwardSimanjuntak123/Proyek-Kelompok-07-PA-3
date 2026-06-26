    
    <?php $__env->startSection('title', 'Tugas'); ?>

    <?php $__env->startSection('content'); ?>
        <section class="section custom-section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4>Judul PA</h4>
                            </div>
                            
                            <div class="card">
                                <div class="card-body">

                                    <div class="card mb-4 shadow-sm border">
                                        <?php if($judul22): ?>
                                            <div class="card mb-4 shadow-sm border">
                                                <div class="card-body">

                                                    <h5 class="card-title font-weight-bold">
                                                        <?php echo e($judul->judul); ?>

                                                    </h5>

                                                    <p class="mb-2">
                                                        <?php echo e($judul->deskripsi); ?>

                                                    </p>

                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <div class="alert alert-info">
                                                Belum ada pengajuan judul proyek akhir.
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
            </div>
            </div>
        </section>
    <?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.main', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\Semester 6\PA III\Project\Proyek-Kelompok-07-PA-3\ui laravel\resources\views/pages/Mahasiswa/Judul/index.blade.php ENDPATH**/ ?>