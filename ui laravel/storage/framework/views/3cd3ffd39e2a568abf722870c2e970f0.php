<?php $__env->startSection('title', 'Profile'); ?>

<?php $__env->startSection('content'); ?>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-6">
                <?php echo $__env->make('partials.alert', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                
                <div class="card shadow-lg border-0 rounded-lg overflow-hidden">
                    <?php $__currentLoopData = $detailUser; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if($role == 'Mahasiswa'): ?>
                            <div class="card-body bg-light p-4">
                                <h4 class="card-title text-center mb-4 text-primary">Profil Mahasiswa</h4>
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item"><strong>Nama:</strong> <?php echo e($user['nama']); ?></li>
                                    <li class="list-group-item"><strong>NIM:</strong> <?php echo e($user['nim']); ?></li>
                                    <li class="list-group-item"><strong>Username:</strong> <?php echo e($user['user_name']); ?></li>
                                    <li class="list-group-item"><strong>Email:</strong> <?php echo e($user['email']); ?></li>
                                    <li class="list-group-item"><strong>Prodi:</strong> <?php echo e($user['prodi_name']); ?></li>
                                    <li class="list-group-item"><strong>Fakultas:</strong> <?php echo e($user['fakultas']); ?></li>
                                    <li class="list-group-item"><strong>Angkatan:</strong> <?php echo e($user['angkatan']); ?></li>
                                    <li class="list-group-item"><strong>Status:</strong> <?php echo e($user['status']); ?></li>
                                </ul>
                            </div>
                        <?php else: ?>
                            <div class="card-body bg-light p-4">
                                <div class="text-center mb-4">
                                    <h4 class="mt-3 text-dark"><?php echo e($user['nama']); ?></h4>
                                    <p class="text-muted"><?php echo e($user['posisi'] ?? '-'); ?></p>
                                </div>
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item"><strong>NIP:</strong> <?php echo e($user['nip']); ?></li>
                                    <li class="list-group-item"><strong>Username:</strong> <?php echo e($user['user_name']); ?></li>
                                    <li class="list-group-item"><strong>Email:</strong> <?php echo e($user['email']); ?></li>
                                    <li class="list-group-item"><strong>Alias:</strong> <?php echo e($user['alias'] ?? '-'); ?></li>
                                    <li class="list-group-item"><strong>Status Pegawai:</strong> 
                                        <span class="badge <?php echo e($user['status_pegawai'] == 'A' ? 'bg-success' : 'bg-danger'); ?>">
                                            <?php echo e($user['status_pegawai'] == 'A' ? 'Aktif' : 'Tidak Aktif'); ?>

                                        </span>
                                    </li>
                                </ul>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.main', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\Semester 6\PA III\Project\Proyek-Kelompok-07-PA-3\ui laravel\resources\views/pages/profile.blade.php ENDPATH**/ ?>