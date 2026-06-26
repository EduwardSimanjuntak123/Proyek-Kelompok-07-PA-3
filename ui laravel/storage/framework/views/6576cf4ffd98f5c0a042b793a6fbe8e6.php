<?php $__env->startSection('title', 'List Kelompok'); ?>

<?php $__env->startSection('content'); ?>
    <section class="section custom-section">
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <h4>List Bimbingan</h4>
                        </div>
                        <div class="card-body">
                            <?php echo $__env->make('partials.alert', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                            <div class="table-responsive">
                                <table class="table table-striped" id="table-2">
                                    <thead>
                                        <tr>
                                            <th>Nomor Kelompok</th>
                                            <th>Kategori PA</th>
                                            <th>Prodi</th>
                                            <th>Keperluan</th>
                                            <th>Tanggal Mulai</th>
                                            <th>Tanggal Selesai</th>
                                            <th>Lokasi</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $bimbingan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td><?php echo e($item->kelompok->nomor_kelompok); ?></td>
                                                <td><?php echo e($item->kelompok->kategoriPA->kategori_pa); ?></td>
                                                <td><?php echo e($item->kelompok->prodi->nama_prodi); ?></td>
                                                <td><?php echo e($item->keperluan); ?></td>
                                                <td><?php echo e($item->rencana_mulai); ?></td>
                                                <td><?php echo e($item->rencana_selesai); ?></td>
                                                <td><?php echo e($item->ruangan->ruangan); ?></td>
                                                <td>
                                                    <span
                                                        class="badge 
                                                    <?php if($item->status == 'disetujui'): ?> badge-success
                                                    <?php elseif($item->status == 'ditolak'): ?> badge-danger
                                                    <?php else: ?> badge-warning <?php endif; ?>">
                                                        <?php echo e(ucfirst($item->status)); ?>

                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="d-flex" style="gap: 8px;">
                                                        <?php if($item->status == 'menunggu'): ?>
                                                            <form
                                                                action="<?php echo e(route('pembimbing.bimbingan.setujui', Crypt::encrypt($item->id))); ?>"
                                                                method="POST">
                                                                <?php echo csrf_field(); ?>
                                                                <?php echo method_field('PUT'); ?>
                                                                <button type="submit" class="btn btn-sm btn-success"
                                                                    data-toggle="tooltip" data-placement="top"
                                                                    title="Setujui">
                                                                    <i class="fas fa-check"></i>
                                                                </button>
                                                            </form>

                                                            <form
                                                                action="<?php echo e(route('pembimbing.bimbingan.tolak', Crypt::encrypt($item->id))); ?>"
                                                                method="POST">
                                                                <?php echo csrf_field(); ?>
                                                                <?php echo method_field('PUT'); ?>
                                                                <button type="submit" class="btn btn-sm btn-danger"
                                                                    data-toggle="tooltip" data-placement="top"
                                                                    title="Tolak">
                                                                    <i class="fas fa-times"></i>
                                                                </button>
                                                            </form>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
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
        $('.show_confirm').click(function(event) {
            var form = $(this).closest("form");
            var name = $(this).data("name");
            event.preventDefault();
            swal({
                    title: `Yakin ingin menghapus data ini?`,
                    text: "Data akan terhapus secara permanen!",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                .then((willDelete) => {
                    if (willDelete) {
                        form.submit();
                    }
                });
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.main', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\Semester 6\PA III\Project\Proyek-Kelompok-07-PA-3\ui laravel\resources\views/pages/Pembimbing/Bimbingan/index.blade.php ENDPATH**/ ?>