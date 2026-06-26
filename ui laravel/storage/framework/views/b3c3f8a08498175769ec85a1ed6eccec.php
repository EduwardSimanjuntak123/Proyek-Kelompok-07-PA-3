<?php $__env->startSection('title', 'View'); ?>

<?php $__env->startSection('content'); ?>
<section class="section custom-section">
    <div class="section-body">
        <div class="row">
            <div class="col-14 col-md-10 offset-md-1">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h4>Detail Tugas</h4>
                        <a class="btn btn-primary btn-sm" href="<?php echo e(route('Mahasiswa.tugas.index')); ?>">Kembali</a>
                    </div>
                    <div class="card-body">
                        <?php echo $__env->make('partials.alert', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

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
                            <?php if($existingSubmission && $existingSubmission->status === 'Submitted'): ?>
                            <tr>
                                <th>Feedback Koordinator</th>
                                <td><?php echo e($existingSubmission->feedback ?? 'Belum terdapat tanggapan.'); ?></td>
                            </tr>
                            <tr>
                                <th>Feedback Pembimbing</th>
                                <td><?php echo e($existingSubmission->feedback_pembimbing ?? 'Belum terdapat tanggapan.'); ?></td>
                            </tr>
                            <tr>
                                <th>Feedback Penguji</th>
                                <td><?php echo e($existingSubmission->feedback_penguji ?? 'Belum terdapat tanggapan.'); ?></td>
                            </tr>
                        <?php elseif($existingSubmission && $existingSubmission->status === ''): ?>
                            <tr>
                                <td colspan="2"><span class="text-muted">Belum terdapat tanggapan.</span></td>
                            </tr>
                        <?php else: ?>
                            <tr>
                                <td colspan="2"><span class="text-muted">Belum terdapat tanggapan.</span></td>
                            </tr>
                        <?php endif; ?>
                           
                        </table>

                        
                        <?php if(!$hasSubmitted): ?>
                            <form method="POST" action="<?php echo e(route('Mahasiswa.tugas.submit', $tugas->id)); ?>" enctype="multipart/form-data">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="kelompok_id" value="<?php echo e($kelompokId); ?>">
                                <input type="hidden" name="tugas_id" value="<?php echo e($idTugas); ?>"> 

                                <div class="form-group mt-4">
                                    <label for="file_path"><strong>Upload Dokumen</strong></label>
                                    <div id="drop-area" 
                                        class="border p-4 text-center" 
                                        style="border: 2px dashed #ccc; position: relative; transition: background-color 0.3s ease;"
                                        ondragover="handleDragOver(event)" 
                                        ondragleave="handleDragLeave(event)" 
                                        ondrop="handleFileDrop(event)">
                                        
                                        <!-- Instruksi dan tombol -->
                                        <div id="upload-instructions">
                                            <i class="fas fa-upload fa-2x mb-2"></i>
                                            <p>Drag Your File Here</p>
                                            <p>Or</p>
                                            <label class="btn btn-primary">
                                                Select File
                                                <input type="file" name="file_path" id="file_path" 
                                                    class="d-none <?php $__errorArgs = ['file'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                                    onchange="updateFileName(event)">
                                            </label>
                                        </div>

                                        <!-- Nama file -->
                                        <div id="file-name" class="mt-2 text-muted">
                                            <span>No file selected</span>
                                        </div>

                                        <!-- Preview -->
                                        <div id="file-preview" class="mt-3" style="display: none;">
                                            <div class="dock-preview border rounded p-3 d-inline-block text-center" style="background-color: #f8f9fa;">
                                                <div id="file-type-icon" class="mb-2" style="font-size: 24px;"></div>
                                                <img id="file-image-preview" src="" alt="File Preview" style="max-width: 100px; max-height: 100px; display: none; border-radius: 10px;" />
                                                <div id="file-name-preview" class="mt-2 font-weight-bold"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php $__errorArgs = ['file'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>

                                <div class="text-right mt-3">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </form>
                        <?php else: ?>
                            
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
                        
                            <div class="text-right">
                                 <a href="<?php echo e(route('Mahasiswa.tugas.edit', Crypt::encrypt($existingSubmission->id))); ?>" class="btn btn-warning">Edit File</a>
                            </div>
                        <?php endif; ?>
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

<?php echo $__env->make('layouts.main', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\Semester 6\PA III\Project\Proyek-Kelompok-07-PA-3\ui laravel\resources\views/pages/Mahasiswa/Tugas/show.blade.php ENDPATH**/ ?>