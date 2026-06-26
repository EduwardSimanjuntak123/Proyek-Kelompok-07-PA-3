<?php $__env->startSection('title', 'Jadwal Penguji'); ?>

<?php $__env->startSection('content'); ?>
    <section class="section custom-section">
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <h4>Jadwal Seminar Penguji</h4>
                        </div>
                        <div class="card-body">
                            <?php echo $__env->make('partials.alert', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                            <div class="table-responsive">
                                <table class="table table-striped" id="table-2">
                                    <thead>
                                        <tr>
                                            <th>Program Studi</th>
                                            <th>Tahun Masuk</th>
                                            <th>Kategori PA</th>
                                            <th>Kelompok</th>
                                            <th>Tanggal</th>
                                            <th>Ruangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $jadwal; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td><?php echo e($item->prodi->nama_prodi ?? '-'); ?></td>
                                                <td><?php echo e($item->tahunMasuk->Tahun_Masuk ?? '-'); ?></td>
                                                <td><?php echo e($item->KategoriPA->kategori_pa ?? '-'); ?></td>
                                                <td><?php echo e($item->kelompok->nomor_kelompok ?? '-'); ?></td>
                                                <td><?php echo e(\Carbon\Carbon::parse($item->waktu)->format('d M Y H:i')); ?></td>
                                                <td><?php echo e($item->ruangan->ruangan); ?></td>
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
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.main', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\Semester 6\PA III\Project\Proyek-Kelompok-07-PA-3\ui laravel\resources\views/pages/Penguji/Jadwal/index.blade.php ENDPATH**/ ?>