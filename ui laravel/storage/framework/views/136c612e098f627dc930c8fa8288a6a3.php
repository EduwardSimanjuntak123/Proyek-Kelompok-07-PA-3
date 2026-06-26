<?php $__env->startSection('title', 'Edit Tugas'); ?>

<?php $__env->startSection('content'); ?>
    <section class="section">
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <?php echo $__env->make('partials.alert', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <h4>Edit Tugas</h4>
                            <a href="<?php echo e(route('koordinator.tugas.index')); ?>" class="btn btn-primary">Kembali</a>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="<?php echo e(route('tugas.update', ['id' => Crypt::encrypt($tugas->id)])); ?>"
                                enctype="multipart/form-data">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('PUT'); ?>
                                
                                <input type="hidden" name="user_id" value="<?php echo e($user_id); ?>">
                                
                                <input type="hidden" name="TM_id" value="<?php echo e($tahun_masuk->id); ?>">
                                
                                <input type="hidden" name="prodi_id" value="<?php echo e($prodi->id); ?>">
                                
                                <input type="hidden" name="KPA_id" value="<?php echo e($kategoripa->id); ?>">

                                <div class="form-group">
                                    <label for="Judul_Tugas">Judul Tugas</label>
                                    <input type="text" name="Judul_Tugas" id="Judul_Tugas"
                                        class="form-control <?php $__errorArgs = ['Judul_Tugas'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        placeholder="Masukkan Judul Tugas"
                                        value="<?php echo e(old('Judul_Tugas', $tugas->Judul_Tugas)); ?>">
                                    <?php $__errorArgs = ['Judul_Tugas'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>

                                <div class="mb-3">
                                    <label for="Deskripsi_Tugas" class="form-label">Deskripsi Tugas</label>
                                    <textarea name="Deskripsi_Tugas" id="Deskripsi_Tugas" rows="5"
                                        class="form-control fs-5 <?php $__errorArgs = ['Deskripsi_Tugas'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" style="height: 200px;"
                                        placeholder="Masukkan Deskripsi"><?php echo e(old('Deskripsi_Tugas', $tugas->Deskripsi_Tugas)); ?></textarea>
                                    <?php $__errorArgs = ['Deskripsi_Tugas'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>


                                <div class="form-group">
                                    <label for="tanggal_pengumpulan">Batas Pengumpulan</label>
                                    <input type="datetime-local" name="tanggal_pengumpulan" id="tanggal_pengumpulan"
                                        class="form-control <?php $__errorArgs = ['tanggal_pengumpulan'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        value="<?php echo e(old('Judul_Tugas', $tugas->tanggal_pengumpulan)); ?>">
                                    <?php $__errorArgs = ['tanggal_pengumpulan'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>

                                <?php if(old('file', $tugas->file)): ?>
                                    <div class="mt-3">
                                        <label>Dokumen Saat Ini:</label>
                                        <div class="border rounded p-1 bg-light d-inline-block">
                                            <?php
                                                $filePath = asset('storage/' . $tugas->file); // atau sesuaikan path file sesuai lokasi penyimpananmu
                                                $fileExt = pathinfo($tugas->file, PATHINFO_EXTENSION);
                                            ?>

                                            <?php if(in_array(strtolower($fileExt), ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'])): ?>
                                                <img src="<?php echo e($filePath); ?>" alt="Dokumen Lama"
                                                    style="max-width: 100px; max-height: 100px; border-radius: 10px;">
                                            <?php else: ?>
                                                <a href="<?php echo e($filePath); ?>"
                                                    target="_blank">📄<?php echo e(basename($tugas->file)); ?></a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <div class="form-group">
                                    <label for="file">Ganti Dokumen</label>
                                    <div id="drop-area" class="border p-4 text-center"
                                        style="border: 2px dashed #ccc; transition: background-color 0.3s ease;"
                                        ondragover="handleDragOver(event)" ondragleave="handleDragLeave(event)"
                                        ondrop="handleFileDrop(event)">
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
                                                    onchange="updateFileName(event)">
                                            </label>
                                        </div>

                                        <!-- Dock Preview -->
                                        <div id="file-preview" class="mt-3" style="display: none;">
                                            <div class="border rounded p-3 bg-light d-inline-block">
                                                <div id="file-type-icon" class="mb-2" style="font-size: 24px;"></div>
                                                <img id="file-image-preview" src="" alt="File Preview"
                                                    style="max-width: 100px; max-height: 100px; display: none; border-radius: 10px;" />
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

                                <!-- Status -->
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="Berlangsung"
                                            <?php echo e(old('status', $tugas->status) == 'Berlangsung' ? 'selected' : ''); ?>>
                                            Berlangsung</option>
                                        <option value="Selesai"
                                            <?php echo e(old('status', $tugas->status) == 'Selesai' ? 'selected' : ''); ?>>Selesai
                                        </option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="nav-icon fas fa-save"></i> &nbsp; Simpan Perubahan
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
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
                reader.onload = function(e) {
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

<?php echo $__env->make('layouts.main', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\Semester 6\PA III\Project\Proyek-Kelompok-07-PA-3\ui laravel\resources\views/pages/Koordinator/tugas/edit.blade.php ENDPATH**/ ?>