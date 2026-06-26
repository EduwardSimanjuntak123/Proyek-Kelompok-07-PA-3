<?php $__env->startSection('title', 'View'); ?>

<?php $__env->startSection('content'); ?>
<section class="section custom-section">
    <div class="section-body">
        <div class="row">
            <div class="col-12 ">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h4>Detail Tugas</h4>
                        <a class="btn btn-primary btn-sm" href="<?php echo e(route('artefak.index')); ?>">Kembali</a>
                    </div>
                    <div class="card-body">
                        <?php echo $__env->make('partials.alert', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

                        <div class="row">
                            <div class="col-12">
                                <?php $__currentLoopData = $artefak; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <ul class="nav nav-tabs mb-4" style="border-bottom: 1px solid #ddd;">
                                        <li class="nav-item">
                                            <a class="nav-link" href="<?php echo e(route('artefak.index')); ?>">PENGUMPULAN BERKAS</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="<?php echo e(route('status_perizinan')); ?>">STATUS PERIZINAN MAJU SEMINAR</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="<?php echo e(route('jadwal.seminar')); ?>">JADWAL SEMINAR</a>
                                        </li>
                                         <li class="nav-item">
                                            <a class="nav-link" href="<?php echo e(route('feedback.show', Crypt::encrypt($item->id))); ?>">FEEDBACK</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="<?php echo e(route('revisi.index')); ?>">BERKAS FINAL</a>
                                        </li>
                                    </ul>

                                    <table class="table table-bordered">
                                        <tr>
                                            <th>Judul</th>
                                            <td><?php echo e($tugas->Judul_Tugas); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Instruksi</th>
                                            <td><?php echo e($tugas->Deskripsi_Tugas); ?></td>
                                        </tr>
                                        <tr>
                                            <th>File Tugas</th>
                                            <td>
                                                <?php if($tugas->file): ?>
                                                    <a href="<?php echo e(asset('storage/' . $tugas->file)); ?>" target="_blank" class="btn btn-info btn-sm">
                                                        Lihat File
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-muted">Tidak ada file</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Batas Waktu</th>
                                            <td>
                                                <?php echo e(\Carbon\Carbon::parse($tugas->batas)->format('d-m-Y H:i')); ?>

                                                <span class="<?php echo e($tugas->status_class); ?>"> &mdash; <?php echo e($tugas->time_remaining); ?></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Status</th>
                                            <td><?php echo e($tugas->status); ?></td>
                                        </tr>
                                        <?php if($hasSubmitted): ?>
                                        <tr>
                                            <th>Feedback Koordinator</th>
                                            <td><?php echo e($existingSubmission->feedback ?? 'Belum terdapat tanggapan.'); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Feedback Pembimbing</th>
                                            <td><?php echo e($existingSubmission->feedback_pembimbing ?? 'Belum terdapat tanggapan.'); ?></td>
                                        </tr>
                                    <?php endif; ?>
                                    </table>

                                    
                                    <?php if($hasSubmitted): ?>
                                        <div class="alert alert-success">
                                            <strong>Sudah dikumpulkan</strong><br>
                                            Waktu Submit: <?php echo e(\Carbon\Carbon::parse($existingSubmission->waktu_submit)->format('d M Y - H:i')); ?><br>
                                            File:
                                            <a href="<?php echo e(asset('storage/' . $existingSubmission->file_path)); ?>" target="_blank" class="btn btn-sm btn-primary mt-1">
                                                Lihat File
                                            </a>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-warning">
                                            Belum mengumpulkan tugas ini.
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
<script>
    function updateFileName(event) {
        const file = event.target.files[0];
        if (file) {
            showFileDetails(file);
        }
    }

    function handleDragOver(event) {
        event.preventDefault();
        document.getElementById('drop-area').style.backgroundColor = '#f0f8ff';
    }

    function handleDragLeave(event) {
        event.preventDefault();
        document.getElementById('drop-area').style.backgroundColor = '';
    }

    function handleFileDrop(event) {
        event.preventDefault();
        document.getElementById('drop-area').style.backgroundColor = '';

        const file = event.dataTransfer.files[0];
        if (file) {
            const fileInput = document.getElementById('file_path');
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            fileInput.files = dataTransfer.files;

            showFileDetails(file);
        }
    }

    function showFileDetails(file) {
        const uploadInstructions = document.getElementById('upload-instructions');
        const fileName = document.getElementById('file-name');
        const previewContainer = document.getElementById('file-preview');
        const imgPreview = document.getElementById('file-image-preview');
        const fileTypeIcon = document.getElementById('file-type-icon');
        const fileNamePreview = document.getElementById('file-name-preview');

        uploadInstructions.style.display = 'none';
        fileName.style.display = 'none';

        const isImage = file.type.startsWith('image/');
        if (isImage) {
            const reader = new FileReader();
            reader.onload = function(e) {
                imgPreview.src = e.target.result;
                imgPreview.style.display = 'block';
            };
            reader.readAsDataURL(file);
            fileTypeIcon.textContent = '🖼️';
        } else {
            imgPreview.style.display = 'none';
            fileTypeIcon.textContent = '📄';
        }

        fileNamePreview.textContent = file.name;
        previewContainer.style.display = 'block';
    }
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.main', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\Semester 6\PA III\Project\Proyek-Kelompok-07-PA-3\ui laravel\resources\views/pages/Mahasiswa/Artefak/show.blade.php ENDPATH**/ ?>