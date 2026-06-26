<?php $__env->startSection('title', 'Nilai Administrasi'); ?>

<?php $__env->startSection('content'); ?>
    <section class="section custom-section">
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>List Nilai Administrasi Kelompok</h4>
                        </div>
                        <div class="card-body p-0">
                            <?php echo $__env->make('partials.alert', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                            <div class="table-responsive">
                                <table class="table table-bordered align-middle mb-0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th width="18%">Nomor Kelompok</th>
                                            <th>Nilai Administrasi Kelompok</th>
                                            <th width="15%" class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $kelompok; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php
                                                $nk = $nilaiKelompok[$item->id] ?? null;
                                                $anggota = $item->KelompokMahasiswa;
                                                $isEdit = $nk !== null;
                                            ?>

                                            
                                            <tr>
                                                
                                                <td class="align-middle">
                                                    <div class="font-weight-600">
                                                        Kelompok <?php echo e($item->nomor_kelompok); ?>

                                                    </div>
                                                    <?php if($nk): ?>
                                                        <span class="badge badge-success mt-1" style="font-size:11px;">
                                                            Akumulasi Nilai: <?php echo e(number_format($nk->C_total, 2)); ?>

                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge badge-secondary mt-1" style="font-size:11px;">
                                                            Belum dinilai
                                                        </span>
                                                    <?php endif; ?>
                                                </td>

                                                
                                                <td class="align-middle">
                                                    <div class="d-flex align-items-center justify-content-between"
                                                        style="cursor:pointer;" data-toggle="collapse"
                                                        data-target="#formKelompok<?php echo e($item->id); ?>"
                                                        aria-expanded="false">

                                                        <?php if($nk): ?>
                                                            <span class="text-muted" style="font-size:13px;">
                                                                <i class="fas fa-check-circle text-success mr-1"></i>
                                                                Sudah dinilai — klik untuk edit
                                                            </span>
                                                        <?php else: ?>
                                                            <span class="text-muted" style="font-size:13px;">
                                                                <i class="fas fa-plus-circle text-primary mr-1"></i>
                                                                Klik untuk mengisi nilai administrasi kelompok
                                                            </span>
                                                        <?php endif; ?>

                                                        <i class="fas fa-chevron-down text-primary ml-3 chevron-icon"
                                                            style="font-size:13px; flex-shrink:0; transition:transform .2s;"></i>
                                                    </div>

                                                    
                                                    <div class="collapse mt-3" id="formKelompok<?php echo e($item->id); ?>">
                                                        <div class="card border-primary shadow-sm mb-0">
                                                            <div
                                                                class="card-header bg-primary text-white py-2 d-flex justify-content-between align-items-center">
                                                                <strong>
                                                                    <i class="fas fa-clipboard-list mr-1"></i>
                                                                    <?php echo e($isEdit ? 'Edit' : 'Form'); ?> Nilai Administrasi (10%)
                                                                    — Kelompok <?php echo e($item->nomor_kelompok); ?>

                                                                </strong>
                                                            </div>

                                                            <div class="card-body">
                                                                <form method="POST"
                                                                    action="<?php echo e($isEdit
                                                                        ? route('koordinator.NilaiAdministrasi.update', $nk->id)
                                                                        : route('koordinator.NilaiAdministrasi.store')); ?>">
                                                                    <?php echo csrf_field(); ?>
                                                                    <?php if($isEdit): ?>
                                                                        <?php echo method_field('PUT'); ?>
                                                                    <?php endif; ?>
                                                                    <input type="hidden" name="kelompok_id"
                                                                        value="<?php echo e($item->id); ?>">

                                                                    <div class="row">
                                                                        <?php
                                                                            $komponen = [
                                                                                'C1' => 'DPP',
                                                                                'C2' => 'TOR',
                                                                                'C3' => 'Bukti Kartu Bimbingan',
                                                                                'C4' => 'Turnitin',
                                                                                'C5' => 'Kode',
                                                                            ];
                                                                        ?>
                                                                        <?php $__currentLoopData = $komponen; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                            <div class="col-md-4 col-sm-6 mb-3">
                                                                                <label class="font-weight-bold"
                                                                                    style="font-size:13px;">
                                                                                    <?php echo e($label); ?>

                                                                                </label>
                                                                                <input type="number"
                                                                                    name="<?php echo e($field); ?>"
                                                                                    class="form-control form-control-sm"
                                                                                    min="0" max="100"
                                                                                    value="<?php echo e(old($field, $nk->$field ?? '')); ?>"
                                                                                    required>
                                                                            </div>
                                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                    </div>
                                                            </div>

                                                            
                                                            <div class="card border-primary mb-3">
                                                                <div class="card-header bg-primary text-white py-2">
                                                                    <strong>
                                                                        <i class="fas fa-paint-brush mr-1"></i>
                                                                        Pameran (5%)
                                                                    </strong>
                                                                </div>
                                                                <div class="card-body py-3">
                                                                    <div class="col-md-4 col-sm-6 px-0">
                                                                        <label class="font-weight-bold"
                                                                            style="font-size:13px;">
                                                                            Nilai Pameran
                                                                        </label>
                                                                        <input type="number" name="Pameran"
                                                                            class="form-control form-control-sm"
                                                                            min="0" max="100"
                                                                            value="<?php echo e(old('Pameran', $nk->Pameran ?? '')); ?>"
                                                                            placeholder="0 - 100" required>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <?php if($nk): ?>
                                                                <div class="alert alert-info py-2 mb-3">
                                                                    <strong>Akumulasi Nilai saat ini:</strong>
                                                                    <?php echo e(number_format($nk->Administrasi ?? 0, 2)); ?>

                                                                </div>
                                                            <?php endif; ?>

                                                            <div class="text-right d-flex justify-content-end"
                                                                style="gap: 8px;">
                                                                <?php if($nk): ?>
                                                                    <form method="POST"
                                                                        action="<?php echo e(route('koordinator.NilaiAdministrasi.destroy', $nk->id)); ?>"
                                                                        class="d-inline">
                                                                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                                                        <button type="button"
                                                                            class="btn btn-danger btn-sm show_confirm_kelompok"
                                                                            data-toggle="tooltip" data-placement="top"
                                                                            title="Hapus Nilai Kelompok">
                                                                            <i class="fas fa-trash-alt"></i>
                                                                        </button>
                                                                    </form>
                                                                <?php endif; ?>
                                                                <button type="submit"
                                                                    class="btn btn-sm <?php echo e($isEdit ? 'btn-warning' : 'btn-success'); ?>"
                                                                    data-toggle="tooltip" data-placement="top"
                                                                    title="<?php echo e($isEdit ? 'Update Nilai Kelompok' : 'Simpan Nilai Kelompok'); ?>">
                                                                    <i
                                                                        class="fas <?php echo e($isEdit ? 'fa-edit' : 'fa-save'); ?>"></i>
                                                                </button>
                                                            </div>

                                                            </form>
                                                        </div>
                                                    </div>
                            </div>
                            </td>

                            
                            <td class="text-center align-middle">
                                <?php if($nk): ?>
                                    <button class="btn btn-primary btn-sm tooltip-collapse" type="button"
                                        data-toggle="collapse" data-target="#formAnggota<?php echo e($item->id); ?>"
                                        data-title="Beri Nilai">
                                        <i class="fas fa-user-check"></i>
                                    </button>
                                <?php else: ?>
                                    <span class="text-muted" style="font-size:12px;">
                                        Isi nilai<br>kelompok terlebih dahulu
                                    </span>
                                <?php endif; ?>
                            </td>
                            </tr>

                            
                            <?php if($nk): ?>
                                <tr>
                                    <td colspan="3" class="p-0 border-left-0 border-right-0">
                                        <div class="collapse" id="formAnggota<?php echo e($item->id); ?>">
                                            <div class="card m-3 border-primary shadow-sm mb-2">
                                                <div
                                                    class="card-header bg-primary text-white py-2 d-flex justify-content-between align-items-center">
                                                    <strong>
                                                        <i class="fas fa-users mr-1"></i>
                                                        Nilai Logbook Anggota — Kelompok
                                                        <?php echo e($item->nomor_kelompok); ?>

                                                    </strong>
                                                    <small>
                                                        Akumulasi Nilai Kelompok:
                                                        <?php echo e(number_format($nk->C_total, 2)); ?>

                                                    </small>
                                                </div>

                                                
                                                <div class="px-3 pt-3 pb-0">
                                                    <div class="alert alert-info py-2 mb-3" style="font-size:12px;">
                                                        <i class="fas fa-info-circle mr-1"></i>
                                                        <strong>Rumus:</strong>
                                                        ((Nilai Kelompok + Nilai LogBook) / 2) × 10% =
                                                        Akumulasi Nilai Administrasi per Mahasiswa
                                                    </div>
                                                </div>

                                                <div class="card-body p-0">
                                                    <table class="table table-bordered table-sm mb-0">
                                                        <thead class="thead-light">
                                                            <tr>
                                                                <th width="30%">Nama Anggota</th>
                                                                <th width="15%" class="text-center">
                                                                    Status</th>
                                                                <th width="30%">Nilai LogBook</th>
                                                                <th width="25%" class="text-center">
                                                                    Aksi</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php $__empty_1 = true; $__currentLoopData = $anggota; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mhs): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                                <?php
                                                                    $ni =
                                                                        $nilaiIndividu[
                                                                            $item->id . '_' . $mhs->mahasiswa->user_id
                                                                        ] ?? null;
                                                                ?>
                                                                <tr>
                                                                    
                                                                    <td class="align-middle">
                                                                        <i class="fas fa-user text-primary mr-1"></i>
                                                                        <span
                                                                            style="font-size:13px;"><?php echo e($mhs->mahasiswa->nama ?? 'Mahasiswa'); ?></span>
                                                                    </td>

                                                                    
                                                                    <td class="align-middle text-center">
                                                                        <?php if($ni && $ni->D1 !== null): ?>
                                                                            <span class="badge badge-success"
                                                                                style="font-size:11px;">Sudah
                                                                                dinilai</span>
                                                                        <?php else: ?>
                                                                            <span class="badge badge-warning"
                                                                                style="font-size:11px;">Belum
                                                                                dinilai</span>
                                                                        <?php endif; ?>
                                                                    </td>

                                                                    
                                                                    <td class="align-middle">
                                                                        <form method="POST"
                                                                            id="formLogbook<?php echo e($item->id); ?>_<?php echo e($mhs->mahasiswa->user_id); ?>"
                                                                            action="<?php echo e($ni
                                                                                ? route('koordinator.NilaiAdministrasi.updateIndividu', $ni->id)
                                                                                : route('koordinator.NilaiAdministrasi.storeIndividu')); ?>">
                                                                            <?php echo csrf_field(); ?>
                                                                            <?php if($ni): ?>
                                                                                <?php echo method_field('PUT'); ?>
                                                                            <?php endif; ?>
                                                                            <input type="hidden" name="kelompok_id"
                                                                                value="<?php echo e($item->id); ?>">
                                                                            <input type="hidden" name="user_id"
                                                                                value="<?php echo e($mhs->mahasiswa->user_id); ?>">

                                                                            <input type="number" name="D1"
                                                                                class="form-control form-control-sm"
                                                                                min="0" max="100"
                                                                                value="<?php echo e(old('D1', $ni->D1 ?? '')); ?>"
                                                                                placeholder="0 - 100" required>
                                                                        </form>
                                                                    </td>

                                                                    
                                                                    <td class="align-middle text-center">
                                                                        <div class="d-flex justify-content-center"
                                                                            style="gap: 8px;">
                                                                            <button type="submit"
                                                                                form="formLogbook<?php echo e($item->id); ?>_<?php echo e($mhs->mahasiswa->user_id); ?>"
                                                                                class="btn btn-sm <?php echo e($ni ? 'btn-warning' : 'btn-success'); ?>"
                                                                                data-toggle="tooltip" data-placement="top"
                                                                                title="<?php echo e($ni ? 'Edit Nilai' : 'Simpan Nilai'); ?>">
                                                                                <i
                                                                                    class="fas <?php echo e($ni ? 'fa-edit' : 'fa-save'); ?>"></i>
                                                                            </button>

                                                                            <?php if($ni): ?>
                                                                                <form method="POST"
                                                                                    action="<?php echo e(route('koordinator.NilaiAdministrasi.destroyIndividu', $ni->id)); ?>">
                                                                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                                                                    <button type="button"
                                                                                        class="btn btn-danger btn-sm show_confirm_individu"
                                                                                        data-toggle="tooltip"
                                                                                        data-placement="top"
                                                                                        title="Hapus Nilai LogBook">
                                                                                        <i class="fas fa-trash-alt"></i>
                                                                                    </button>
                                                                                </form>
                                                                            <?php endif; ?>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                                <tr>
                                                                    <td colspan="4" class="text-center text-muted p-3">
                                                                        Tidak ada anggota kelompok.
                                                                    </td>
                                                                </tr>
                                                            <?php endif; ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
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
        // Buka collapse jika ada fragment di URL
        $(function() {
            const hash = window.location.hash;
            if (hash) {
                const target = $(hash);
                if (target.length) {
                    target.collapse('show');
                    // Scroll ke elemen
                    $('html, body').animate({
                        scrollTop: target.offset().top - 100
                    }, 400);
                }
            }
        });

        // Tooltip untuk tombol yang juga punya data-toggle="collapse"
        $(function() {
            $('.tooltip-collapse').each(function() {
                $(this).tooltip({
                    title: $(this).data('title'),
                    placement: 'top',
                    trigger: 'hover'
                });
            });
        });

        // Konfirmasi hapus nilai kelompok
        $(document).on('click', '.show_confirm_kelompok', function(e) {
            e.preventDefault();
            var form = $(this).closest('form');
            swal({
                title: 'Hapus nilai kelompok ini?',
                text: 'Data akan terhapus secara permanen!',
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) form.submit();
            });
        });

        // Konfirmasi hapus nilai individu
        $(document).on('click', '.show_confirm_individu', function(e) {
            e.preventDefault();
            var form = $(this).closest('form');
            swal({
                title: 'Hapus nilai logbook ini?',
                text: 'Data logbook mahasiswa ini akan terhapus permanen!',
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) form.submit();
            });
        });

        // Putar ikon chevron saat collapse dibuka/tutup
        $(document).on('show.bs.collapse', '.collapse', function() {
            var trigger = $('[data-target="#' + this.id + '"]');
            trigger.find('.chevron-icon').css('transform', 'rotate(180deg)');
        });
        $(document).on('hide.bs.collapse', '.collapse', function() {
            var trigger = $('[data-target="#' + this.id + '"]');
            trigger.find('.chevron-icon').css('transform', 'rotate(0deg)');
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.main', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\Semester 6\PA III\Project\Proyek-Kelompok-07-PA-3\ui laravel\resources\views/pages/Koordinator/Nilai_Administrasi/index.blade.php ENDPATH**/ ?>