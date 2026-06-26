<?php $__env->startSection('title', 'List Kelompok'); ?>

<?php $__env->startSection('content'); ?>
    <section class="section custom-section">
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <h4>List pengumuman</h4>
                            <a href="<?php echo e(route('pengumuman.create')); ?>" class="btn btn-primary">
                                <i class="nav-icon fas fa-folder-plus"></i>&nbsp; Tambah Pengumuman
                            </a>
                        </div>
                        <div class="card-body">
                            <?php echo $__env->make('partials.alert', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                            <div class="table-responsive">
                                <table class="table table-striped" id="table-2">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Judul</th>
                                            
                                            <th>File</th>
                                            <th>Tanggal</th>
                                            <th>Status</th>
                                            <th>Aksi</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $pengumuman; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td><?php echo e($item->id); ?></td>
                                                <td><a
                                                        href="<?php echo e(route('pengumuman.koordinator.show', $item->id)); ?>"><?php echo e($item->judul); ?></a>
                                                </td>
                                                
                                                <td>
                                                    <?php if($item->file): ?>
                                                        <a href="<?php echo e(asset('storage/' . $item->file)); ?>"target="_blank"
                                                            class="btn btn-info btn-sm">Lihat File</a>
                                                    <?php else: ?>
                                                        <span class="text-muted">Tidak ada file</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo e($item->tanggal_penulisan); ?></td>
                                                <td><?php echo e($item->status); ?></td>
                                                <td>
                                                    <div class="d-flex" style="gap: 8px;">
                                                        <a href="<?php echo e(route('pengumuman.edit', Crypt::encrypt($item->id))); ?>"
                                                            class="btn btn-warning btn-sm" data-toggle="tooltip"
                                                            data-placement="top" title="Edit Penguji">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <form method="POST"
                                                            action="<?php echo e(route('pengumuman.destroy', $item->id)); ?>">
                                                            <?php echo csrf_field(); ?>
                                                            <?php echo method_field('DELETE'); ?>
                                                            <button type="submit"
                                                                class="btn btn-danger btn-sm show_confirm"
                                                                data-toggle="tooltip" data-placement="top"
                                                                title="Hapus Penguji">
                                                                <i class="fas fa-trash-alt"></i>
                                                            </button>
                                                        </form>
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
        $(function() {
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.main', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\Semester 6\PA III\Project\Proyek-Kelompok-07-PA-3\ui laravel\resources\views/pages/Koordinator/pengumuman/index.blade.php ENDPATH**/ ?>