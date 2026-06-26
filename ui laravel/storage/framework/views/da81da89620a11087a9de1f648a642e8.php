<?php $__env->startSection('title', 'Daftar Pengajuan Seminar Mahasiswa'); ?>

<?php $__env->startSection('content'); ?>
    <section class="section custom-section">
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Daftar Pengajuan Seminar Mahasiswa</h4>
                        </div>
                        <div class="card-body">
                            <?php echo $__env->make('partials.alert', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                            <div class="table-responsive">
                                <table class="table table-striped" id="table-2">
                                    <thead>
                                        <tr>
                                            <th>Tanggal Pengajuan</th>
                                            <th>Kelompok</th>
                                            <th>Kategori PA</th>
                                            <th>Prodi</th>
                                            <th>File</th>
                                            <th>Status</th>
                                            <th>Catatan</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $pengajuanSeminars; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $pengajuan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td><?php echo e($pengajuan->created_at->format('d/m/Y')); ?></td>
                                                <td>
                                                    <?php if($pengajuan->kelompok): ?>
                                                        Kelompok <?php echo e($pengajuan->kelompok->nomor_kelompok); ?>

                                                    <?php else: ?>
                                                        -
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo e($pengajuan->kelompok->kategoriPA->kategori_pa); ?></td>
                                                <td><?php echo e($pengajuan->kelompok->prodi->nama_prodi); ?></td>
                                                <td>
                                                    <?php if($pengajuan->files->count() > 0): ?>
                                                        <div class="dropdown">
                                                            <button class="btn btn-sm btn-info dropdown-toggle"
                                                                type="button" id="fileDropdown<?php echo e($pengajuan->id); ?>"
                                                                data-toggle="dropdown" aria-haspopup="true"
                                                                aria-expanded="false">
                                                                <?php echo e($pengajuan->files->count()); ?> File
                                                            </button>
                                                            <div class="dropdown-menu"
                                                                aria-labelledby="fileDropdown<?php echo e($pengajuan->id); ?>">
                                                                <?php $__currentLoopData = $pengajuan->files; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $file): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                    <a class="dropdown-item"
                                                                        href="<?php echo e(Storage::url($file->file_path)); ?>"
                                                                        target="_blank">
                                                                        <i
                                                                            class="
                                                                    <?php if(in_array(strtolower(pathinfo($file->file_name, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif'])): ?> fas fa-image
                                                                    <?php elseif(strtolower(pathinfo($file->file_name, PATHINFO_EXTENSION)) == 'pdf'): ?>
                                                                        fas fa-file-pdf
                                                                    <?php elseif(in_array(strtolower(pathinfo($file->file_name, PATHINFO_EXTENSION)), ['doc', 'docx'])): ?>
                                                                        fas fa-file-word
                                                                    <?php else: ?>
                                                                        fas fa-file <?php endif; ?>
                                                                    mr-2"></i>
                                                                        <?php echo e($file->file_name); ?>

                                                                    </a>
                                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                            </div>
                                                        </div>
                                                    <?php else: ?>
                                                        <span class="badge badge-danger">Tidak ada file</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge 
                                                <?php if($pengajuan->status == 'disetujui'): ?> badge-success
                                                <?php elseif($pengajuan->status == 'ditolak'): ?> badge-danger
                                                <?php else: ?> badge-warning <?php endif; ?>">
                                                        <?php echo e(ucfirst($pengajuan->status)); ?>

                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if($pengajuan->catatan): ?>
                                                        <button type="button" class="btn btn-sm btn-info"
                                                            data-toggle="modal"
                                                            data-target="#catatanModal<?php echo e($pengajuan->id); ?>">
                                                            Lihat Catatan
                                                        </button>

                                                        <!-- Modal untuk melihat catatan -->
                                                        <div class="modal fade" id="catatanModal<?php echo e($pengajuan->id); ?>"
                                                            tabindex="-1" role="dialog" aria-hidden="true">
                                                            <div class="modal-dialog" role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title">Catatan</h5>
                                                                        <button type="button" class="close"
                                                                            data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <div class="alert alert-danger">
                                                                            <?php echo e($pengajuan->catatan); ?>

                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary"
                                                                            data-dismiss="modal">Tutup</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php else: ?>
                                                        -
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if($pengajuan->status == 'menunggu'): ?>
                                                        <div class="d-flex" style="gap: 8px;">
                                                            <button type="button" class="btn btn-sm btn-success"
                                                                data-toggle="modal"
                                                                data-target="#setujuiModal<?php echo e($pengajuan->id); ?>"
                                                                title="Setujui" data-toggle="tooltip" data-placement="top">
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-danger"
                                                                data-toggle="modal"
                                                                data-target="#tolakModal<?php echo e($pengajuan->id); ?>"
                                                                title="Tolak" data-toggle="tooltip" data-placement="top">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        </div>

                                                        <!-- Modal Setujui -->
                                                        <div class="modal fade" id="setujuiModal<?php echo e($pengajuan->id); ?>"
                                                            tabindex="-1" role="dialog" aria-hidden="true">
                                                            <div class="modal-dialog" role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title">Konfirmasi Persetujuan</h5>
                                                                        <button type="button" class="close"
                                                                            data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <p>Apakah Anda yakin ingin menyetujui pengajuan
                                                                            seminar ini?</p>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary"
                                                                            data-dismiss="modal">Batal</button>
                                                                        <form
                                                                            action="<?php echo e(route('PembimbingPengajuanSeminar.setujui', Crypt::encrypt($pengajuan->id))); ?>"
                                                                            method="POST">
                                                                            <?php echo csrf_field(); ?>
                                                                            <?php echo method_field('PUT'); ?>
                                                                            <button type="submit"
                                                                                class="btn btn-success">Setujui</button>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- Modal Tolak -->
                                                        <div class="modal fade" id="tolakModal<?php echo e($pengajuan->id); ?>"
                                                            tabindex="-1" role="dialog" aria-hidden="true">
                                                            <div class="modal-dialog" role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title">Tolak Pengajuan</h5>
                                                                        <button type="button" class="close"
                                                                            data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <form
                                                                        action="<?php echo e(route('PembimbingPengajuanSeminar.tolak', Crypt::encrypt($pengajuan->id))); ?>"
                                                                        method="POST">
                                                                        <?php echo csrf_field(); ?>
                                                                        <?php echo method_field('PUT'); ?>
                                                                        <div class="modal-body">
                                                                            <div class="form-group">
                                                                                <label for="catatan">Catatan Penolakan
                                                                                    <span
                                                                                        class="text-danger">*</span></label>
                                                                                <textarea class="form-control" id="catatan" name="catatan" rows="4" required></textarea>
                                                                                <small class="form-text text-muted">Berikan
                                                                                    alasan penolakan agar mahasiswa dapat
                                                                                    memperbaiki pengajuannya.</small>
                                                                            </div>
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <button type="button"
                                                                                class="btn btn-secondary"
                                                                                data-dismiss="modal">Batal</button>
                                                                            <button type="submit"
                                                                                class="btn btn-danger">Tolak
                                                                                Pengajuan</button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php else: ?>
                                                        <span class="text-muted">Tidak ada aksi</span>
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
    <script>
        $(document).ready(function() {
            $('.tooltip-modal').each(function() {
                $(this).tooltip({
                    title: $(this).attr('data-title'),
                    placement: 'top',
                    trigger: 'hover'
                });
            });
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.main', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\Semester 6\PA III\Project\Proyek-Kelompok-07-PA-3\ui laravel\resources\views/pages/Pembimbing/Pengajuan_Seminar/index.blade.php ENDPATH**/ ?>