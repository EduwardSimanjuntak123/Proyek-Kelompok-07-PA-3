    
    <?php $__env->startSection('title', 'Tugas'); ?>

    <?php $__env->startSection('content'); ?>
        <section class="section custom-section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4>List Progres</h4>
                            </div>
                            
                            <div class="card-body">
                                <?php $__currentLoopData = $tugas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $status = $statusByTugas->get($item->id);
                                    ?>
                                    <div class="card mb-4 shadow-sm border">
                                        <div class="card-body">
                                            <h5 class="card-title font-weight-bold"><?php echo e($item->Judul_Tugas); ?></h5>
                                            <p class="mb-2"><?php echo e($item->Deskripsi_Tugas); ?></p>
                                            <ul class="list-unstyled">
                                                <li>
                                                    <strong>Deadline:</strong>
                                                    <span class="text-danger"><?php echo e($item->formatted_deadline); ?></span>
                                                </li>
                                            </ul>
                                            <div class="mb-2">
                                                <span class="badge badge-<?php echo e($status ? 'success' : 'secondary'); ?>">
                                                    <?php echo e($status ? $status->status : 'Belum dikumpulkan'); ?>

                                                </span>
                                                <a href="<?php echo e(route('Mahasiswa.tugas.create', Crypt::encrypt($item->id))); ?>"
                                                    class="btn btn-sm btn-primary ml-2">Lihat Detail</a>
                                            </div>
                                            <div class="mb-2 <?php echo e($item->status_class); ?>">
                                                ⏳ <span class="countdown"
                                                    data-deadline="<?php echo e($item->tanggal_pengumpulan); ?>"></span>

                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php if($tugas->isEmpty()): ?>
                                    <div class="alert alert-info">Tidak ada tugas yang tersedia.</div>
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

            function updateCountdown() {
                const countdownEls = document.querySelectorAll('.countdown');

                countdownEls.forEach(el => {
                    const deadline = new Date(el.dataset.deadline).getTime();
                    const now = new Date().getTime();
                    const diff = deadline - now;

                    if (diff > 0) {
                        const days = Math.floor(diff / (1000 * 60 * 60 * 24));
                        const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                        const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));

                        if (days > 0) {
                            el.textContent = `${days} hari ${hours} jam lagi`;
                        } else {
                            el.textContent = `${hours} jam ${minutes} menit lagi`;
                        }
                    } else {
                        const absDiff = Math.abs(diff);
                        const hours = Math.floor((absDiff) / (1000 * 60 * 60));
                        const minutes = Math.floor((absDiff % (1000 * 60 * 60)) / (1000 * 60));
                        el.textContent = `Selesai ${hours} jam ${minutes} menit yang lalu`;
                        el.classList.remove('text-warning');
                        el.classList.add('text-success');
                    }
                });
            }

            updateCountdown();
            setInterval(updateCountdown, 60000); // update tiap 1 menit
        </script>
    <?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.main', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\Semester 6\PA III\Project\Proyek-Kelompok-07-PA-3\ui laravel\resources\views/pages/Mahasiswa/Tugas/index.blade.php ENDPATH**/ ?>