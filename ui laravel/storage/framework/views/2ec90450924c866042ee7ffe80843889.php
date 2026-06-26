<?php $__env->startSection('title', 'Nilai Kelompok Pembimbing 1'); ?>

<style>
    .btn-action {
        min-width: 38px;
        height: 38px;
        border-radius: 8px;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: box-shadow .2s ease;
    }
    .btn-action:hover { box-shadow: 0 4px 12px rgba(0,0,0,.2); }
    @media (max-width: 768px) {
        .d-flex.justify-content-center { flex-direction: column; }
        .btn-action { width: 100%; }
    }
</style>

<?php $__env->startSection('content'); ?>
<section class="section custom-section">
    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">List Nilai Kelompok Pembimbing (30%)</h4>
                    </div>
                    <div class="card-body pt-2">
                        <small class="text-muted d-block" style="font-size:0.95rem;">
                            Bobot utama 30% untuk pembimbing. Jika terdapat 2 pembimbing, maka dibagi menjadi
                            20% untuk Pembimbing 1 dan 10% untuk Pembimbing 2.
                        </small>
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
                                        <?php $nilai = $nilaiKelompok[$item->id] ?? null; ?>

                                        <tr>
                                            <td><?php echo e($item->nomor_kelompok); ?></td>
                                            <td><?php echo e($item->kategoriPA->kategori_pa); ?></td>
                                            <td><?php echo e($item->prodi->nama_prodi); ?></td>
                                            <td class="text-center align-middle">
                                                <div class="d-flex justify-content-center align-items-center" style="gap:8px;">
                                                    <button class="btn <?php echo e($nilai ? 'btn-warning' : 'btn-primary'); ?> btn-action"
                                                        type="button" data-toggle="collapse"
                                                        data-target="#nilai<?php echo e($item->id); ?>" aria-expanded="false"
                                                        title="<?php echo e($nilai ? 'Edit Nilai' : 'Beri Nilai'); ?>"
                                                        data-toggle-tooltip="true">
                                                        <i class="fas <?php echo e($nilai ? 'fa-edit' : 'fa-user-check'); ?>"></i>
                                                    </button>
                                                    <?php if($nilai): ?>
                                                        <form action="<?php echo e(route('pembimbing1.NilaiKelompok.destroy', $nilai->id)); ?>" method="POST" class="m-0">
                                                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                                            <button type="submit" class="btn btn-danger btn-action show_confirm" title="Hapus Nilai">
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
                                                            <strong>Form Penilaian Kelompok</strong>
                                                        </div>
                                                        <div class="card-body">
                                                            <form method="POST" action="<?php echo e($nilai ? route('pembimbing1.NilaiKelompok.update', $nilai->id) : route('pembimbing1.NilaiKelompok.store')); ?>">
                                                                <?php echo csrf_field(); ?>
                                                                <?php if($nilai): ?> <?php echo method_field('PUT'); ?> <?php endif; ?>
                                                                <input type="hidden" name="kelompok_id" value="<?php echo e($item->id); ?>">
                                                                <input type="hidden" name="user_id" value="<?php echo e($userId); ?>">
                                                                <?php
                                                                    $kolomKiri = [
                                                                        ['A11', 'Kualitas Produk: Mencakup seluruh requirements dalam laporan'],
                                                                        ['A12', 'Kualitas Produk: Bebas dari error'],
                                                                        ['A13', 'Kualitas Produk: Dapat digunakan dengan baik dan mudah'],
                                                                    ];
                                                                    $kolomKanan = [
                                                                        ['A21', 'Kualitas Laporan: Desain menggambarkan produk dengan sesuai'],
                                                                        ['A22', 'Kualitas Laporan: Ditulis menurut kaidah bahasa Indonesia yang baik'],
                                                                        ['A23', 'Kualitas Laporan: Sesuai kaidah penulisan dokumen dan template'],
                                                                    ];
                                                                ?>
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <div class="alert alert-primary"><strong>Kualitas Produk</strong></div>
                                                                        <?php $__currentLoopData = $kolomKiri; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$name, $label]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                            <div class="form-group">
                                                                                <label class="font-weight-bold"><?php echo e($label); ?></label>
                                                                                <input type="number" name="<?php echo e($name); ?>" class="form-control"
                                                                                    min="0" max="100" value="<?php echo e(old($name, $nilai->$name ?? '')); ?>" required>
                                                                            </div>
                                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="alert alert-success"><strong>Kualitas Laporan</strong></div>
                                                                        <?php $__currentLoopData = $kolomKanan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$name, $label]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                            <div class="form-group">
                                                                                <label class="font-weight-bold"><?php echo e($label); ?></label>
                                                                                <input type="number" name="<?php echo e($name); ?>" class="form-control"
                                                                                    min="0" max="100" value="<?php echo e(old($name, $nilai->$name ?? '')); ?>" required>
                                                                            </div>
                                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                    </div>
                                                                </div>
                                                                <hr>
                                                                <div class="text-right">
                                                                    <button type="submit" class="btn <?php echo e($nilai ? 'btn-warning' : 'btn-success'); ?>"
                                                                        title="<?php echo e($nilai ? 'Update Nilai' : 'Simpan Nilai'); ?>">
                                                                        <i class="fas <?php echo e($nilai ? 'fa-edit' : 'fa-save'); ?>"></i>
                                                                        <?php echo e($nilai ? 'Update Nilai' : 'Simpan Nilai'); ?>

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
$(document).ready(function () {
    $('[data-toggle-tooltip="true"]').tooltip({ placement: 'top', trigger: 'hover' });

    $('.show_confirm').on('click', function (e) {
        var form = $(this).closest('form');
        e.preventDefault();
        swal({
            title: 'Yakin ingin menghapus data ini?',
            text: 'Data akan terhapus secara permanen!',
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then(function (willDelete) {
            if (willDelete) form.submit();
        });
    });

    $('.collapse').on('show.bs.collapse', function () {
        $('[data-target="#' + $(this).attr('id') + '"]').find('i')
            .removeClass('fa-user-check fa-edit').addClass('fa-chevron-up');
    });

    $('.collapse').on('hide.bs.collapse', function () {
        var btn = $('[data-target="#' + $(this).attr('id') + '"]');
        btn.find('i').removeClass('fa-chevron-up')
            .addClass(btn.hasClass('btn-warning') ? 'fa-edit' : 'fa-user-check');
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.main', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\Semester 6\PA III\Project\Proyek-Kelompok-07-PA-3\ui laravel\resources\views/pages/Pembimbing/Nilai_Kelompok/index.blade.php ENDPATH**/ ?>