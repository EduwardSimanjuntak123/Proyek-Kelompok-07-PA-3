<?php $__env->startSection('title', 'Nilai Individu Pembimbing 1'); ?>
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
                        <div class="card-header">
                            <h4 class="mb-0">List Nilai Individu Pembimbing (30%)</h4>
                        </div>
                        <div class="card-body pt-2">
                            <small class="text-muted d-block" style="font-size: 0.95rem;">
                                Bobot utama 30% untuk pembimbing. Jika terdapat 2 pembimbing, maka dibagi menjadi 20% untuk
                                Pembimbing 1 dan 10% untuk Pembimbing 2.
                            </small>
                        </div>

                        <div class="card-body">

                            <?php echo $__env->make('partials.alert', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

                            <div class="table-responsive">

                                <table class="table table-bordered table-hover">

                                    <thead class="thead-light">
                                        <tr>
                                            <th>Nomor Kelompok</th>
                                            <th>Mahasiswa</th>
                                            <th>Kategori PA</th>
                                            <th>Prodi</th>
                                            <th width="180">Aksi</th>
                                        </tr>
                                    </thead>

                                    <tbody>

                                        <?php $__currentLoopData = $kelompoks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kelompok): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php $__currentLoopData = $kelompok->KelompokMahasiswa; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mhs): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php
                                                    $nilai = $nilaiindividu[$mhs->user_id] ?? null;
                                                ?>

                                                
                                                <tr>

                                                    <td>
                                                        <?php echo e($kelompok->nomor_kelompok); ?>

                                                    </td>

                                                    <td>
                                                        <strong>
                                                            <?php echo e($mhs->nama ?? 'Nama tidak ditemukan'); ?>

                                                        </strong>
                                                        -
                                                        <small class="text-muted">
                                                            <?php echo e($mhs->nim ?? 'NIM tidak ditemukan'); ?>

                                                        </small>
                                                    </td>

                                                    <td>
                                                        <?php echo e($kelompok->kategoriPA->kategori_pa); ?>

                                                    </td>

                                                    <td>
                                                        <?php echo e($kelompok->prodi->nama_prodi); ?>

                                                    </td>

                                                    <td class="text-center align-middle">

                                                        <div class="d-flex justify-content-center align-items-center"
                                                            style="gap:8px;">

                                                            <button
                                                                class="btn <?php echo e($nilai ? 'btn-warning' : 'btn-primary'); ?> tooltip-collapse"
                                                                type="button" data-toggle="collapse"
                                                                data-target="#nilai<?php echo e($mhs->user_id); ?>"
                                                                aria-expanded="false"
                                                                data-title="<?php echo e($nilai ? 'Edit Nilai' : 'Beri Nilai'); ?>">
                                                                <i
                                                                    class="fas <?php echo e($nilai ? 'fa-edit' : 'fa-user-check'); ?>"></i>
                                                            </button>

                                                            <?php if($nilai): ?>
                                                                <form
                                                                    action="<?php echo e(route('pembimbing1.NilaiIndividu.destroy', $nilai->id)); ?>"
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

                                                    <td colspan="5" class="p-0 border-0">

                                                        <div class="collapse" id="nilai<?php echo e($mhs->user_id); ?>">

                                                            <div class="card m-3 shadow-sm">

                                                                <div class="card-header bg-primary text-white">

                                                                    <strong>
                                                                        Form Penilaian
                                                                    </strong>

                                                                </div>

                                                                <div class="card-body">

                                                                    <form
                                                                        action="<?php echo e($nilai ? route('pembimbing1.NilaiIndividu.update', $nilai->id) : route('pembimbing1.NilaiIndividu.store')); ?>"
                                                                        method="POST">

                                                                        <?php echo csrf_field(); ?>

                                                                        <?php if($nilai): ?>
                                                                            <?php echo method_field('PUT'); ?>
                                                                        <?php endif; ?>



                                                                        <input type="hidden" name="kelompok_id"
                                                                            value="<?php echo e($mhs->kelompok_id); ?>">
                                                                        <input type="hidden" name="user_id"
                                                                            value="<?php echo e($mhs->user_id); ?>">

                                                                        <input type="hidden" name="penilai_id"
                                                                            value="<?php echo e(session('user_id')); ?>">

                                                                        <input type="hidden" name="role_id" value="3">

                                                                        <?php
                                                                            $kategori = [
                                                                                'Kemampuan Komunikasi (10%)' => [
                                                                                    [
                                                                                        'B11',
                                                                                        'Kontak mata dengan panelis dan kelompok',
                                                                                    ],
                                                                                    [
                                                                                        'B12',
                                                                                        'Penggunaan bahasa tubuh dan gesture',
                                                                                    ],
                                                                                    [
                                                                                        'B13',
                                                                                        'Suara jelas terdengar dengan tempo cukup',
                                                                                    ],
                                                                                    [
                                                                                        'B14',
                                                                                        'Semangat, senyum dan antusiasme',
                                                                                    ],
                                                                                    [
                                                                                        'B15',
                                                                                        'Ide dan pembicaraan terstruktur dengan baik',
                                                                                    ],
                                                                                ],

                                                                                'Presentasi dan Demonstrasi Produk (10%)' => [
                                                                                    [
                                                                                        'B21',
                                                                                        'Slide presentasi mengikuti standar profesional',
                                                                                    ],
                                                                                    [
                                                                                        'B22',
                                                                                        'Pembagian tugas anggota saat presentasi dan demo',
                                                                                    ],
                                                                                    [
                                                                                        'B23',
                                                                                        'Isi presentasi dan alur demo terstruktur dengan baik',
                                                                                    ],
                                                                                    [
                                                                                        'B24',
                                                                                        'Ketepatan waktu presentasi dan demo dengan jadwal',
                                                                                    ],
                                                                                    [
                                                                                        'B25',
                                                                                        'Penggunaan bahasa Inggris saat presentasi dan demo',
                                                                                    ],
                                                                                ],

                                                                                'Sesi Tanya Jawab (25%)' => [
                                                                                    [
                                                                                        'B31',
                                                                                        'Penguasaan materi dan konsep secara teknis dalam saat sesi tanya jawab',
                                                                                    ],
                                                                                ],
                                                                            ];
                                                                        ?>

                                                                        <div class="row">

                                                                            <?php $__currentLoopData = $kategori; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $judul => $items): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                                <?php
                                                                                    $isTanyaJawab =
                                                                                        $judul ===
                                                                                        'Sesi Tanya Jawab (25%)';
                                                                                ?>

                                                                                <div class="col-md-6 mb-4">

                                                                                    
                                                                                    <div
                                                                                        class="alert <?php echo e($isTanyaJawab ? 'alert-success' : 'alert-primary'); ?>">
                                                                                        <strong><?php echo e($judul); ?></strong>
                                                                                    </div>

                                                                                    
                                                                                    <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$name, $label]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                                        <div class="form-group">

                                                                                            <label class="font-weight-bold">
                                                                                                <?php echo e($label); ?>

                                                                                            </label>

                                                                                            <input type="number"
                                                                                                name="<?php echo e($name); ?>"
                                                                                                class="form-control"
                                                                                                min="0"
                                                                                                max="100"
                                                                                                value="<?php echo e(old($name, $nilai->$name ?? '')); ?>"
                                                                                                required>

                                                                                        </div>
                                                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                                                                </div>
                                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

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

<?php echo $__env->make('layouts.main', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\Semester 6\PA III\Project\Proyek-Kelompok-07-PA-3\ui laravel\resources\views/pages/Pembimbing/Nilai_Individu/index.blade.php ENDPATH**/ ?>