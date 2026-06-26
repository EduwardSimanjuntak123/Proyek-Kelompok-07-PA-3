<?php $__env->startSection('title', 'Manajemen Penguji'); ?>

<?php $__env->startSection('content'); ?>
    <section class="section custom-section">
        <div class="section-body">
            <div class="row">
                <div class="col-12">

                    <div class="card">

                        <div class="card-header">
                            <h4>Manajemen Penguji Kelompok</h4>
                        </div>

                        <div class="card-body">

                            <?php echo $__env->make('partials.alert', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

                            <div class="table-responsive">

                                <table class="table table-striped">

                                    <thead>
                                        <tr>
                                            <th>Nomor Kelompok</th>
                                            <th>Penguji 1</th>
                                            <th>Penguji 2</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>

                                    <tbody>

                                        <?php $__currentLoopData = $kelompok; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php
                                                $penguji1 = $item->penguji->get(0);
                                                $penguji2 = $item->penguji->get(1);
                                            ?>

                                            <tr>
                                                <td>
                                                    <?php echo e($item->nomor_kelompok ?? '-'); ?>

                                                </td>

                                                <td>
                                                    <?php echo e($penguji1->nama ?? '-'); ?>

                                                </td>

                                                <td>
                                                    <?php echo e($penguji2->nama ?? '-'); ?>

                                                </td>

                                                <td>
                                                    <?php if($item->penguji->count() == 0): ?>
                                                        <a href="<?php echo e(route('penguji.create', Crypt::encrypt($item->id))); ?>"
                                                            class="btn btn-success btn-sm" data-toggle="tooltip"
                                                            data-placement="top" title="Tambah Penguji">
                                                            <i class="fas fa-plus"></i>
                                                        </a>
                                                    <?php else: ?>
                                                        <a href="<?php echo e(route('penguji.edit', Crypt::encrypt($item->id))); ?>"
                                                            class="btn btn-warning btn-sm" data-toggle="tooltip"
                                                            data-placement="top" title="Edit Penguji">
                                                            <i class="fas fa-edit"></i>
                                                        </a>

                                                        
                                                        <form
                                                            action="<?php echo e(route('penguji.destroy', Crypt::encrypt($item->id))); ?>"
                                                            method="POST" style="display:inline">
                                                            <?php echo csrf_field(); ?>
                                                            <?php echo method_field('DELETE'); ?>
                                                            <button type="submit"
                                                                class="btn btn-danger btn-sm show_confirm"
                                                                data-toggle="tooltip" data-placement="top"
                                                                title="Hapus Penguji">
                                                                <i class="fas fa-trash-alt"></i>
                                                            </button>
                                                        </form>
                                                    <?php endif; ?>
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

<?php echo $__env->make('layouts.main', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\Semester 6\PA III\Project\Proyek-Kelompok-07-PA-3\ui laravel\resources\views/pages/Koordinator/penguji/index.blade.php ENDPATH**/ ?>