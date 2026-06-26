<?php $__env->startSection('title', 'Tugas'); ?>

<?php $__env->startSection('content'); ?>
    <section class="section custom-section">
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <h4>List Tugas</h4>
                            <a href="<?php echo e(route('koordinator.tugas.index')); ?>" class="btn btn-primary">Kembali</a>
                        </div>
                        <div class="card-body">
                            <?php echo $__env->make('partials.alert', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                            <div class="table-responsive">
                                <table class="table table-striped" id="table-2">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nomor Kelompok</th>
                                            <th>Waktu Submit</th>
                                            <th>File</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $artefak; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php
                                                $feedback = $item->feedback ?? '-';
                                            ?>
                                            <tr>
                                                <td><?php echo e($item->id); ?></td>
                                                <td><?php echo e($item->kelompok->nomor_kelompok); ?></td>
                                                <td><?php echo e($item->waktu_submit); ?></td>
                                                <td>
                                                    <a href="<?php echo e(asset('storage/' . $item->file_path)); ?>"
                                                        target="_blank">Lihat File</a>
                                                </td>
                                                <td> <?php echo e($item->status ?? '-'); ?> <br>
                                                    
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <a href="<?php echo e(route('feedback.edit', $item->id)); ?>"
                                                            class="btn btn-primary btn-sm" data-toggle="tooltip"
                                                            data-placement="top" title="Feedback">
                                                            <i class="fas fa-comments"></i>
                                                        </a>
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

<?php echo $__env->make('layouts.main', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\Semester 6\PA III\Project\Proyek-Kelompok-07-PA-3\ui laravel\resources\views/pages/Koordinator/tugas/show_submission.blade.php ENDPATH**/ ?>