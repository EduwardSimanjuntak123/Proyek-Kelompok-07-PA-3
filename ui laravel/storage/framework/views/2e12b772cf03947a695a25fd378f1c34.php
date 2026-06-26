<?php $__env->startSection('title', 'Nilai Kelompok'); ?>

<?php $__env->startSection('content'); ?>
    <section class="section custom-section">
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <h4>Daftar Nilai Akhir Mahasiswa</h4>
                        </div>
                        <div class="card-body">
                            <?php echo $__env->make('partials.alert', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                            <div class="table-responsive">
                                <div class="container">
                                    <h3>Daftar Nilai Akhir Mahasiswa</h3>
                                    <a href="<?php echo e(route('nilai.akhir.export', ['prodi_id' => $prodi_id, 'KPA_id' => $KPA_id, 'TM_id' => $TM_id])); ?>"
                                        class="btn btn-success mb-3">Export ke Excel</a>

                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Kelompok</th>
                                                <th>Nama</th>
                                                <th>NIM</th>
                                                <th>Nilai Administrasi 10%</th>
                                                <th>Nilai Pameran 5%</th>
                                                <th>Nilai Seminar 45%</th>
                                                <th>Nilai Bimbingan 40%</th>
                                                <th>Nilai Akhir</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $__currentLoopData = $nilai_akhir; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $nilai): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php
                                                    $mhs = $mahasiswa[$nilai->user_id] ?? null;
                                                ?>
                                                <tr>
                                                    <td><?php echo e($nilai->nomor_kelompok ?? '-'); ?></td>
                                                    <td><?php echo e($mhs['nama'] ?? '-'); ?></td>
                                                    <td><?php echo e($mhs['nim'] ?? '-'); ?></td>
                                                    <td><?php echo e($nilai->Administrasi ?? 0); ?></td>
                                                    <td><?php echo e($nilai->Pameran ?? 0); ?></td>
                                                    <td><?php echo e($nilai->nilai_seminar ?? 0); ?></td>
                                                    <td><?php echo e(number_format($nilai->rata_bimbingan ?? 0, 2)); ?></td>
                                                    <td><?php echo e(number_format($nilai->nilai_akhir, 2)); ?></td>
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
        window.location.reload = "<?php echo e(route('pembimbing.Nilaiseminar.index')); ?>";
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
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.main', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\Semester 6\PA III\Project\Proyek-Kelompok-07-PA-3\ui laravel\resources\views/pages/Koordinator/Nilai_Administrasi/NilaiAkhir.blade.php ENDPATH**/ ?>