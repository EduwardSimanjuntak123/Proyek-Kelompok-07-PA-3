<?php $__env->startSection('title', 'Nilai Kelompok Pembimbing 2'); ?>

<style>
    .toggle-collapse,
    .show_confirm {
        min-width: 115px;
        height: 38px;
        border-radius: 8px;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all .2s ease;
    }

    /* Beri Nilai */
    .btn-primary.toggle-collapse:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(13, 110, 253, .25);
    }

    /* Edit Nilai */
    .btn-success.toggle-collapse:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(25, 135, 84, .25);
    }

    /* Hapus */
    .btn-danger.show_confirm:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(220, 53, 69, .25);
    }

    .toggle-collapse i,
    .show_confirm i {
        margin-right: 5px;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .d-flex.justify-content-center {
            flex-direction: column;
        }

        .toggle-collapse,
        .show_confirm {
            width: 100%;
        }
    }
</style>

<?php $__env->startSection('content'); ?>
    <section class="section custom-section">
        <div class="section-body">
            <div class="row">
                <div class="col-12">

                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <h4>List Nilai Kelompok Pembimbing 2 (10%)</h4>
                        </div>

                        <div class="card-body">

                            <?php echo $__env->make('partials.alert', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

                            <div class="table-responsive">

                                <table class="table table-bordered table-hover">

                                    <thead class="thead-light">
                                        <tr>
                                            <th>Nomor Kelompok</th>
                                            <th>Kategori PA</th>
                                            <th>Prodi</th>
                                            <th width="180">Aksi</th>
                                        </tr>
                                    </thead>

                                    <tbody>

                                        <?php $__currentLoopData = $kelompok; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php
                                                $nilai = $nilaiKelompok[$item->id] ?? null;

                                                $komponen = ['A11', 'A12', 'A13', 'A21', 'A22', 'A23'];

                                                $terisi = 0;

                                                if ($nilai) {
                                                    foreach ($komponen as $k) {
                                                        if (!is_null($nilai->$k)) {
                                                            $terisi++;
                                                        }
                                                    }
                                                }
                                            ?>

                                            
                                            <tr>

                                                <td>
                                                    <?php echo e($item->nomor_kelompok); ?>

                                                </td>

                                                <td>
                                                    <?php echo e($item->kategoriPA->kategori_pa); ?>

                                                </td>

                                                <td>
                                                    <?php echo e($item->prodi->nama_prodi); ?>

                                                </td>

                                                <td class="text-center align-middle">

                                                    <div class="d-flex justify-content-center align-items-center"
                                                        style="gap:8px;">

                                                        <button
                                                            class="btn <?php echo e($nilai ? 'btn-warning' : 'btn-primary'); ?> tooltip-collapse"
                                                            type="button" data-toggle="collapse"
                                                            data-target="#nilai<?php echo e($item->id); ?>" aria-expanded="false"
                                                            data-title="<?php echo e($nilai ? 'Edit Nilai' : 'Beri Nilai'); ?>">
                                                            <i class="fas <?php echo e($nilai ? 'fa-edit' : 'fa-user-check'); ?>"></i>
                                                        </button>

                                                        <?php if($nilai): ?>
                                                            <form
                                                                action="<?php echo e(route('pembimbing2.NilaiKelompok.destroy', $nilai->id)); ?>"
                                                                method="POST" class="m-0">

                                                                <?php echo csrf_field(); ?>
                                                                <?php echo method_field('DELETE'); ?>
                                                                <button type="submit"
                                                                    class="btn btn-sm btn-danger show_confirm"
                                                                    data-toggle="tooltip" data-placement="top"
                                                                    title="Hapus Nilai">
                                                                    <i class="fas fa-trash-alt"></i>
                                                                </button>

                                                            </form>
                                                        <?php endif; ?>

                                                    </div>

                                                </td>

                                            </tr>

                                            
                                            <tr>

                                                <td colspan="4" class="p-0 border-0">

                                                    <div class="collapse" id="nilai<?php echo e($item->id); ?>">

                                                        <div class="card m-3 shadow-sm">

                                                            <div class="card-header bg-primary text-white">

                                                                <strong>
                                                                    Form Penilaian Kelompok
                                                                </strong>

                                                            </div>

                                                            <div class="card-body">

                                                                <form method="POST"
                                                                    action="<?php echo e($nilai ? route('pembimbing2.NilaiKelompok.update', $nilai->id) : route('pembimbing2.NilaiKelompok.store')); ?>">

                                                                    <?php echo csrf_field(); ?>

                                                                    <?php if($nilai): ?>
                                                                        <?php echo method_field('PUT'); ?>
                                                                    <?php endif; ?>

                                                                    <input type="hidden" name="kelompok_id"
                                                                        value="<?php echo e($item->id); ?>">

                                                                    <input type="hidden" name="user_id"
                                                                        value="<?php echo e($userId); ?>">

                                                                    <?php

                                                                        $kolomKiri = [
                                                                            [
                                                                                'A11',
                                                                                'Kualitas Produk: Mencakup seluruh requirements dalam laporan',
                                                                            ],
                                                                            [
                                                                                'A12',
                                                                                'Kualitas Produk: Bebas dari error',
                                                                            ],
                                                                            [
                                                                                'A13',
                                                                                'Kualitas Produk: Dapat digunakan dengan baik dan mudah',
                                                                            ],
                                                                        ];

                                                                        $kolomKanan = [
                                                                            [
                                                                                'A21',
                                                                                'Kualitas Laporan: Desain menggambarkan produk dengan sesuai',
                                                                            ],
                                                                            [
                                                                                'A22',
                                                                                'Kualitas Laporan: Ditulis menurut kaidah bahasa Indonesia yang baik',
                                                                            ],
                                                                            [
                                                                                'A23',
                                                                                'Kualitas Laporan: Sesuai kaidah penulisan dokumen dan template',
                                                                            ],
                                                                        ];

                                                                    ?>

                                                                    <div class="row">

                                                                        
                                                                        <div class="col-md-6">

                                                                            <div class="alert alert-primary">
                                                                                <strong>
                                                                                    Kualitas Produk
                                                                                </strong>
                                                                            </div>

                                                                            <?php $__currentLoopData = $kolomKiri; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$name, $label]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                                <div class="form-group">

                                                                                    <label class="font-weight-bold">
                                                                                        <?php echo e($label); ?>

                                                                                    </label>

                                                                                    <input type="number"
                                                                                        name="<?php echo e($name); ?>"
                                                                                        class="form-control" min="0"
                                                                                        max="100"
                                                                                        value="<?php echo e(old($name, $nilai->$name ?? '')); ?>"
                                                                                        required>

                                                                                </div>
                                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                                                        </div>

                                                                        
                                                                        <div class="col-md-6">

                                                                            <div class="alert alert-success">
                                                                                <strong>
                                                                                    Kualitas Laporan
                                                                                </strong>
                                                                            </div>

                                                                            <?php $__currentLoopData = $kolomKanan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$name, $label]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                                <div class="form-group">

                                                                                    <label class="font-weight-bold">
                                                                                        <?php echo e($label); ?>

                                                                                    </label>

                                                                                    <input type="number"
                                                                                        name="<?php echo e($name); ?>"
                                                                                        class="form-control" min="0"
                                                                                        max="100"
                                                                                        value="<?php echo e(old($name, $nilai->$name ?? '')); ?>"
                                                                                        required>

                                                                                </div>
                                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                                                        </div>

                                                                    </div>

                                                                    <hr>

                                                                    <div class="text-right">
                                                                        <button type="submit"
                                                                            class="btn <?php echo e($nilai ? 'btn-warning' : 'btn-success'); ?>"
                                                                            data-toggle="tooltip" data-placement="top"
                                                                            title="<?php echo e($nilai ? 'Update Nilai' : 'Simpan Nilai'); ?>">
                                                                            <i
                                                                                class="fas <?php echo e($nilai ? 'fa-edit' : 'fa-save'); ?>"></i>
                                                                        </button>
                                                                    </div>

                                                                </form>

                                                            </div>

                                                        </div>

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
    <script>
        $(document).ready(function() {
            $('.tooltip-collapse').each(function() {
                $(this).tooltip({
                    title: $(this).attr('data-title'),
                    placement: 'top',
                    trigger: 'hover'
                });
            });
        });


        $('.show_confirm').click(function(event) {

            var form = $(this).closest("form");

            event.preventDefault();

            swal({
                    title: "Yakin ingin menghapus data ini?",
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

        $('.collapse').on('show.bs.collapse', function() {

            let button = $('[data-target="#' + $(this).attr('id') + '"]');

            button.find('i')
                .removeClass('fa-chevron-down')
                .addClass('fa-chevron-up');

        });

        $('.collapse').on('hide.bs.collapse', function() {

            let button = $('[data-target="#' + $(this).attr('id') + '"]');

            button.find('i')
                .removeClass('fa-chevron-up')
                .addClass('fa-chevron-down');

        });

        $('.collapse').on('show.bs.collapse', function() {
            let button = $('[data-target="#' + $(this).attr('id') + '"]');

            button.find('i')
                .removeClass('fa-chevron-down')
                .addClass('fa-chevron-up');
        });

        $('.collapse').on('hide.bs.collapse', function() {
            let button = $('[data-target="#' + $(this).attr('id') + '"]');

            button.find('i')
                .removeClass('fa-chevron-up')
                .addClass('fa-chevron-down');
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.main', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\Semester 6\PA III\Project\Proyek-Kelompok-07-PA-3\ui laravel\resources\views/pages/Pembimbing/Nilai_Kelompok/indexp2.blade.php ENDPATH**/ ?>