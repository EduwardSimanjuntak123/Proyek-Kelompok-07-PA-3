<?php $__env->startSection('title', 'List Tahun Ajaran'); ?>

<?php $__env->startSection('content'); ?>
<section class="section custom-section">
    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h4>Show Pengumuman</h4>
                        <a class="btn btn-primary btn-sm" href="<?php echo e(route('dashboard.pembimbing')); ?>">Kembali</a>
                    </div>
                    <div class="card-body">
                        <?php echo $__env->make('partials.alert', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        <div class="mb-4">
                            <h3 class="text-muted"><?php echo e($pengumuman->judul); ?></h3>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <p><strong><i class="fas fa-calendar-alt mr-1"></i> Tanggal:</strong> <?php echo e($pengumuman->created_at->format('d-m-Y')); ?></p>
                                <p><strong>Pengirim:</strong><?php echo e($pengumuman->nama); ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong><i class="fas fa-info-circle mr-1"></i> Status:</strong> 
                                    <span class="badge badge-<?php echo e($pengumuman->status === 'aktif' ? 'success' : 'secondary'); ?>">
                                        <?php echo e(ucfirst($pengumuman->status)); ?>

                                    </span>
                                </p>
                            </div>
                        </div>

                        <hr>

                        <div class="mb-4">
                            <h6 class="mb-2"><strong><i class="fas fa-align-left mr-1"></i> Deskripsi:</strong></h6>
                            <div class="bg-light border rounded p-3" style="white-space: pre-line;">
                                <?php echo e($pengumuman->deskripsi); ?>

                            </div>
                        </div>

                        
                        <?php if($pengumuman->file): ?>
                        <div class="text-center mt-5">
                             <a href="<?php echo e(asset('storage/' . $pengumuman->file)); ?>" class="btn btn-primary btn-sm" target="_blank">
                                <i class="fas fa-file"></i> Lihat File
                            </a>
                        </div>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.main', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\Semester 6\PA III\Project\Proyek-Kelompok-07-PA-3\ui laravel\resources\views/pages/Pembimbing/Pengumuman/show.blade.php ENDPATH**/ ?>