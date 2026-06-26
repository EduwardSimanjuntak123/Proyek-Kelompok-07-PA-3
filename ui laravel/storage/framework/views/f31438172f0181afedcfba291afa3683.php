<?php $__env->startSection('title', 'Jadwal'); ?>

<?php $__env->startSection('content'); ?>
    <section class="section custom-section">
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <h4>Jadwal Seminar</h4>
                            <a href="<?php echo e(route('jadwal.create')); ?>" class="btn btn-primary">
                                <i class="nav-icon fas fa-folder-plus"></i>&nbsp; Tambah Jadwal
                            </a>
                        </div>
                        <div class="card-body">
                            <?php echo $__env->make('partials.alert', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

                            
                            <?php if(session('warning')): ?>
                                <div class="alert alert-danger alert-dismissible show fade">
                                    <div class="alert-body">
                                        <button class="close" data-dismiss="alert"><span>&times;</span></button>
                                        <?php echo e(session('warning')); ?>


                                        <?php if(session('showUnapprovedAlert')): ?>
                                            <br><strong>Catatan:</strong> Pengajuan seminar harus disetujui oleh dosen
                                            pembimbing sebelum jadwal dapat dibuat.
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <div class="table-responsive">
                                <table class="table table-striped" id="table-2">
                                    <thead>
                                        <tr>
                                            <th>Kelompok</th>
                                            <th>Waktu Mulai</th>
                                            <th>Waktu Selesai</th>
                                            <th>Ruangan</th>
                                            <th>Dosen Pembimbing</th>
                                            <th>Dosen Penguji</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php $__currentLoopData = $jadwal; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>

                                                
                                                <td><?php echo e($item->kelompok->nomor_kelompok ?? '-'); ?></td>

                                                
                                                <td>
                                                    <div>
                                                        <strong style="color:#2563eb;">
                                                            <?php echo e(\Carbon\Carbon::parse($item->waktu_mulai)->format('d M Y')); ?>

                                                        </strong>
                                                        <br>
                                                        <small class="text-muted">
                                                            <i class="fas fa-clock"></i>
                                                            <?php echo e(\Carbon\Carbon::parse($item->waktu_mulai)->format('H:i')); ?>

                                                            WIB
                                                        </small>
                                                    </div>
                                                </td>

                                                
                                                <td>
                                                    <div>
                                                        <strong style="color:#16a34a;">
                                                            <?php echo e(\Carbon\Carbon::parse($item->waktu_selesai)->format('d M Y')); ?>

                                                        </strong>
                                                        <br>
                                                        <small class="text-muted">
                                                            <i class="fas fa-clock"></i>
                                                            <?php echo e(\Carbon\Carbon::parse($item->waktu_selesai)->format('H:i')); ?>

                                                            WIB
                                                        </small>
                                                    </div>
                                                </td>

                                                
                                                <td><?php echo e($item->ruangan->ruangan ?? '-'); ?></td>

                                                
                                                <td><?php echo e($item->pembimbing_nama ?? '-'); ?></td>

                                                
                                                <td><?php echo $item->penguji_nama; ?></td>

                                                
                                                <td>
                                                    <div class="d-flex align-items-center" style="gap: 8px;">
                                                        <a href="<?php echo e(route('jadwal.show', Crypt::encrypt($item->id))); ?>"
                                                            class="btn btn-info btn-sm" data-toggle="tooltip"
                                                            data-placement="top" title="Detail">
                                                            <i class="fas fa-info-circle"></i>
                                                        </a>

                                                        <a href="<?php echo e(route('jadwal.edit', Crypt::encrypt($item->id))); ?>"
                                                            class="btn btn-success btn-sm" data-toggle="tooltip"
                                                            data-placement="top" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>

                                                        <form method="POST"
                                                            action="<?php echo e(route('jadwal.destroy', Crypt::encrypt($item->id))); ?>">
                                                            <?php echo csrf_field(); ?>
                                                            <?php echo method_field('DELETE'); ?>
                                                            <button class="btn btn-danger btn-sm show_confirm"
                                                                data-toggle="tooltip" data-placement="top" title="Hapus">
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
<?php $__env->startPush('style'); ?>
    <style>
        .table td {
            vertical-align: middle !important;
        }

        .badge {
            font-size: 13px;
        }

        .btn-group .btn {
            margin-right: 5px;
        }
    </style>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.main', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\Semester 6\PA III\Project\Proyek-Kelompok-07-PA-3\ui laravel\resources\views/pages/Koordinator/jadwal/index.blade.php ENDPATH**/ ?>