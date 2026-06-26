<?php $__env->startSection('title', 'Edit Pengumuman'); ?>

<?php $__env->startSection('content'); ?>
<section class="section">
    <div class="section-body">
        <form action="<?php echo e(route('pengumuman.update',['id' =>Crypt::encrypt($pengumuman->id)])); ?>" method="POST" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            <div class="card">
                <div class="card-header"><h4>Edit Pengumuman</h4></div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Judul</label>
                        <input type="text" name="judul" value="<?php echo e($pengumuman->judul); ?>" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" rows="5" required><?php echo e($pengumuman->deskripsi); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control" required>
                            <option value="aktif" <?php echo e($pengumuman->status == 'aktif' ? 'selected' : ''); ?>>Aktif</option>
                            <option value="non-aktif" <?php echo e($pengumuman->status == 'non-aktif' ? 'selected' : ''); ?>>Non-aktif</option>
                        </select>
                    </div>
                </div>
                <?php if(old('file', $pengumuman->file)): ?>
                                <div class="mt-3">
                                    <label>Dokumen Saat Ini:</label>
                                    <div class="border rounded p-1 bg-light d-inline-block">
                                        <?php
                                            $filePath = asset('storage/' . $pengumuman->file); // atau sesuaikan path file sesuai lokasi penyimpananmu
                                            $fileExt = pathinfo($pengumuman->file, PATHINFO_EXTENSION);
                                        ?>

                                        <?php if(in_array(strtolower($fileExt), ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'])): ?>
                                            <img src="<?php echo e($filePath); ?>" alt="Dokumen Lama" style="max-width: 100px; max-height: 100px; border-radius: 10px;">
                                        <?php else: ?>
                                            <a href="<?php echo e($filePath); ?>" target="_blank">📄<?php echo e(basename($pengumuman->file)); ?></a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <div class="form-group">
                                <label for="file">Ganti Dokumen</label>
                                <div id="drop-area"
                                    class="border p-4 text-center"
                                    style="border: 2px dashed #ccc; transition: background-color 0.3s ease;"
                                    ondragover="handleDragOver(event)"
                                    ondragleave="handleDragLeave(event)"
                                    ondrop="handleFileDrop(event)"
                                >
                                    <!-- Instruksi Awal -->
                                    <div id="upload-instructions">
                                        <i class="fas fa-upload fa-2x mb-2"></i>
                                        <p>Drag Your File Here</p>
                                        <p>Or</p>
                                        <label class="btn btn-primary">
                                            Select File
                                            <input type="file" name="file" id="file" 
                                                class="d-none <?php $__errorArgs = ['file'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                onchange="updateFileName(event)"
                                            >
                                        </label>
                                    </div>
                            
                                    <!-- Dock Preview -->
                                    <div id="file-preview" class="mt-3" style="display: none;">
                                        <div class="border rounded p-3 bg-light d-inline-block">
                                            <div id="file-type-icon" class="mb-2" style="font-size: 24px;"></div>
                                            <img id="file-image-preview" src="" alt="File Preview" 
                                                style="max-width: 100px; max-height: 100px; display: none; border-radius: 10px;" 
                                            />
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
                <div class="card-footer text-end">
                    <button class="btn btn-primary" type="submit">Update</button>
                </div>
            </div>
        </form>
    </div>
</section>
<?php $__env->stopSection(); ?>
<?php $__env->startPush('script'); ?>
<script>
    function handleDragOver(event) {
        event.preventDefault();
        document.getElementById('drop-area').style.backgroundColor = '#f8f9fa';
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
            const fileInput = document.getElementById('file');
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            fileInput.files = dataTransfer.files;

            showFilePreview(file);
        }
    }

    function updateFileName(event) {
        const file = event.target.files[0];
        if (file) {
            showFilePreview(file);
        }
    }

    function showFilePreview(file) {
        // Sembunyikan instruksi awal
        document.getElementById('upload-instructions').style.display = 'none';

        const preview = document.getElementById('file-preview');
        const icon = document.getElementById('file-type-icon');
        const imgPreview = document.getElementById('file-image-preview');
        const fileName = document.getElementById('file-name-preview');

        // Tampilkan icon atau preview gambar
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function (e) {
                imgPreview.src = e.target.result;
                imgPreview.style.display = 'block';
            };
            reader.readAsDataURL(file);
            icon.textContent = '🖼️';
        } else {
            imgPreview.style.display = 'none';
            icon.textContent = '📄';
        }

        fileName.textContent = file.name;
        preview.style.display = 'block';
    }
</script>
<?php echo $__env->make('layouts.main', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\Semester 6\PA III\Project\Proyek-Kelompok-07-PA-3\ui laravel\resources\views/pages/Koordinator/pengumuman/edit.blade.php ENDPATH**/ ?>