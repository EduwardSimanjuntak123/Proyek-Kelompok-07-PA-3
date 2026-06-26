<?php $__env->startSection('title', 'List Dosen Role'); ?>

<?php $__env->startSection('content'); ?>
    <section class="section custom-section">
        <div class="section-body">
            <div class="row">
                <div class="col-12">

                    <div class="card shadow-sm border-0">

                        <!-- HEADER -->
                        <div
                            class="card-header d-flex justify-content-between flex-column flex-lg-row align-items-start align-items-lg-center">

                            <div class="w-100">

                                <div
                                    class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">

                                    <div>
                                        <h4 class="mb-1 font-weight-bold">
                                            List Role
                                        </h4>

                                        <p class="text-muted mb-0">
                                            Manajemen data role dosen pembimbing, penguji, dan koordinator
                                        </p>
                                    </div>

                                    <div class="mt-3 mt-md-0">
                                        <a href="<?php echo e(route('manajemen-role.create')); ?>"
                                            class="btn btn-primary btn-lg shadow-sm">
                                            <i class="fas fa-folder-plus mr-1"></i>
                                            Tambah Dosen Role
                                        </a>
                                    </div>
                                </div>

                                <!-- FILTER -->
                                <div class="role-filter-wrapper">
                                    <ul class="nav role-filter-nav">

                                        <li class="nav-item">
                                            <a class="nav-link <?php echo e(empty($filterRole) ? 'active' : ''); ?>"
                                                href="<?php echo e(route('manajemen-role.index')); ?>">

                                                Semua

                                                <span class="count-badge">
                                                    <?php echo e($countAll ?? 0); ?>

                                                </span>
                                            </a>
                                        </li>

                                        <li class="nav-item">
                                            <a class="nav-link <?php echo e($filterRole === 'koordinator' ? 'active' : ''); ?>"
                                                href="<?php echo e(route('manajemen-role.index', ['filter_role' => 'koordinator'])); ?>">

                                                <i class="fas fa-user-tie"></i>
                                                Koordinator

                                                <span class="count-badge">
                                                    <?php echo e($countKoordinator ?? 0); ?>

                                                </span>
                                            </a>
                                        </li>

                                        <li class="nav-item">
                                            <a class="nav-link <?php echo e($filterRole === 'pembimbing' ? 'active' : ''); ?>"
                                                href="<?php echo e(route('manajemen-role.index', ['filter_role' => 'pembimbing'])); ?>">

                                                <i class="fas fa-user-graduate"></i>
                                                Pembimbing

                                                <span class="count-badge">
                                                    <?php echo e($countPembimbing ?? 0); ?>

                                                </span>
                                            </a>
                                        </li>

                                        <li class="nav-item">
                                            <a class="nav-link <?php echo e($filterRole === 'penguji' ? 'active' : ''); ?>"
                                                href="<?php echo e(route('manajemen-role.index', ['filter_role' => 'penguji'])); ?>">

                                                <i class="fas fa-user-check"></i>
                                                Penguji

                                                <span class="count-badge">
                                                    <?php echo e($countPenguji ?? 0); ?>

                                                </span>
                                            </a>
                                        </li>

                                    </ul>
                                </div>

                            </div>

                        </div>

                        <!-- BODY -->
                        <div class="card-body">

                            <?php echo $__env->make('partials.alert', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

                            <div class="table-responsive">

                                <table class="table table-hover align-middle custom-table" id="table-2">

                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Dosen</th>
                                            <th>Prodi</th>
                                            <th>Kategori PA</th>
                                            <th>Role</th>
                                            <th>Tahun Ajaran</th>
                                            <th>Tahun Masuk</th>
                                            <th>Status</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>

                                    <tbody>

                                        <?php $__empty_1 = true; $__currentLoopData = $dosenroles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                            <tr>

                                                <td>
                                                    <?php echo e($loop->iteration); ?>

                                                </td>

                                                <td class="font-weight-semibold">
                                                    <?php echo e($item->nama ?? 'N/A'); ?>

                                                </td>

                                                <td>
                                                    <?php echo e($item->prodi->nama_prodi ?? 'N/A'); ?>

                                                </td>

                                                <td>
                                                    <?php echo e($item->kategoripa->kategori_pa ?? 'N/A'); ?>

                                                </td>

                                                <td>
                                                    <span class="badge badge-info px-3 py-2">
                                                        <?php echo e($item->role->role_name ?? 'N/A'); ?>

                                                    </span>
                                                </td>

                                                <td>
                                                    <?php echo e($item->tahunAjaran->tahun_mulai); ?>/<?php echo e($item->tahunAjaran->tahun_selesai); ?>

                                                </td>

                                                <td>
                                                    <?php echo e($item->tahunMasuk->Tahun_Masuk ?? 'N/A'); ?>

                                                </td>

                                                <td>
                                                    <?php if($item->status == 'Aktif'): ?>
                                                        <span class="badge badge-success px-3 py-2">
                                                            Aktif
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge badge-danger px-3 py-2">
                                                            Tidak Aktif
                                                        </span>
                                                    <?php endif; ?>
                                                </td>

                                                <td>

                                                    <div class="d-flex justify-content-center">

                                                        <a href="<?php echo e(route('manajemen-role.edit', Crypt::encrypt($item->id))); ?>"
                                                            class="btn btn-success btn-sm mr-2">

                                                            <i class="fas fa-edit"></i>
                                                        </a>

                                                        <form method="POST"
                                                            action="<?php echo e(route('manajemen-role.destroy', $item->id)); ?>">

                                                            <?php echo csrf_field(); ?>
                                                            <?php echo method_field('delete'); ?>

                                                            <button class="btn btn-danger btn-sm show_confirm">

                                                                <i class="fas fa-trash-alt"></i>
                                                            </button>

                                                        </form>

                                                    </div>

                                                </td>

                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>

                                            <tr>
                                                <td colspan="9" class="text-center text-muted py-5">

                                                    <i class="fas fa-folder-open fa-2x mb-3"></i>

                                                    <br>

                                                    Data role dosen belum tersedia
                                                </td>
                                            </tr>
                                        <?php endif; ?>

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


<?php $__env->startPush('style'); ?>
    <style>
        /* CARD */
        .card {
            border-radius: 18px;
            overflow: hidden;
        }

        .card-header {
            background: #fff;
            padding: 30px;
            border-bottom: 1px solid #f1f1f1;
        }

        /* FILTER */
        .role-filter-wrapper {
            width: 100%;
        }

        .role-filter-nav {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            padding: 0;
            margin: 0;
            list-style: none;
        }

        .role-filter-nav .nav-link {
            padding: 12px 20px;
            border-radius: 14px;
            background: #f4f7fb;
            color: #6c757d;
            font-weight: 600;
            transition: all 0.25s ease;
            border: 1px solid transparent;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .role-filter-nav .nav-link:hover {
            background: #e8f4fa;
            color: #4C9BC8;
            transform: translateY(-2px);
        }

        .role-filter-nav .nav-link.active {
            background: #4C9BC8;
            color: white;
            box-shadow: 0 5px 15px rgba(95, 168, 198, 0.3);
        }

        .count-badge {
            background: rgba(255, 255, 255, 0.2);
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 12px;
        }

        .role-filter-nav .nav-link.active .count-badge {
            background: rgba(255, 255, 255, 0.25);
        }

        /* TABLE */
        .custom-table {
            border-collapse: separate;
            border-spacing: 0 10px;
        }

        .custom-table thead th {
            border: none;
            background: #f8fafc;
            color: #6c757d;
            font-size: 13px;
            text-transform: uppercase;
            padding: 18px;
            font-weight: 700;
        }

        .custom-table tbody tr {
            background: #fff;
            transition: 0.2s;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
        }

        .custom-table tbody tr:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.06);
        }

        .custom-table tbody td {
            padding: 18px;
            vertical-align: middle;
            border-top: none;
        }

        /* BADGE */
        .badge {
            font-size: 12px;
            border-radius: 30px;
            font-weight: 600;
        }

        /* BUTTON */
        .btn-sm {
            border-radius: 10px;
            width: 38px;
            height: 38px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* RESPONSIVE */
        @media (max-width: 768px) {

            .card-header {
                padding: 20px;
            }

            .role-filter-nav {
                gap: 10px;
            }

            .role-filter-nav .nav-link {
                width: 100%;
                justify-content: center;
            }

            .btn-lg {
                width: 100%;
            }

            .custom-table thead {
                display: none;
            }

            .custom-table tbody tr {
                display: block;
                margin-bottom: 15px;
                border-radius: 16px;
                overflow: hidden;
            }

            .custom-table tbody td {
                display: flex;
                justify-content: space-between;
                padding: 14px 16px;
                border-bottom: 1px solid #f1f1f1;
            }

            .custom-table tbody td:last-child {
                border-bottom: none;
            }
        }
    </style>
<?php $__env->stopPush(); ?>


<?php $__env->startPush('script'); ?>
    <script type="text/javascript">
        $('.show_confirm').click(function(event) {

            var form = $(this).closest("form");

            event.preventDefault();

            swal({
                title: 'Yakin ingin menghapus data ini?',
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

<?php echo $__env->make('layouts.main', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\Semester 6\PA III\Project\Proyek-Kelompok-07-PA-3\ui laravel\resources\views/pages/BAAK/Kordinator/index.blade.php ENDPATH**/ ?>