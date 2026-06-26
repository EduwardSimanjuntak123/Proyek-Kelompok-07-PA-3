<?php $__env->startSection('title', 'Tambah Pengumuman'); ?>

<?php $__env->startSection('content'); ?>
    <section class="section">
        <div class="section-body">
            <form action="<?php echo e(route('pengumuman.BAAK.store')); ?>" method="POST" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h4>Tambah Pengumuman</h4>
                        <a class="btn btn-primary btn-sm" href="<?php echo e(route('pengumuman.BAAK.index')); ?>">Kembali</a>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            
                            <input type="hidden" name="user_id" value="<?php echo e($user_id); ?>">
                            
                            <div class="form-group">
                                <label for="TA_id">Pilih Kategori PA</label>
                                <select name="KPA_id" id="KPA_id" class="select2 form-control" required>
                                    <option value="">-- Pilih Kategori PA --</option>
                                    <?php $__currentLoopData = $KPA; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($item->id); ?>"
                                            <?php echo e(old('KPA_id') == $item->id ? 'selected' : ''); ?>> <?php echo e($item->kategori_pa); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="TM_id">Pilih Tahun Masuk Mahasiswa</label>
                                <select name="TM_id" id="TM_id" class="select2 form-control" required>
                                    <option value="">-- Pilih Tahun Masuk Mahasiswa --</option>
                                    <?php $__currentLoopData = $TM; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($item->id); ?>"
                                            <?php echo e(old('TM_id') == $item->id ? 'selected' : ''); ?>> <?php echo e($item->Tahun_Masuk); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="prodi_id">Pilih Prodi</label>
                                <select id="prodi_id" name="prodi_id" class="select2 form-control" required>
                                    <option value="">-- Pilih Prodi --</option>
                                    <?php $__currentLoopData = $prodi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($item->id); ?>"
                                            <?php echo e(old('prodi_id') == $item->id ? 'selected' : ''); ?>>
                                            <?php echo e($item->nama_prodi); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            
                            <label>Judul</label>
                            <input type="text" name="judul" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="deskripsi">Deskripsi</label>
                            <textarea id="deskripsi" name="deskripsi" class="form-control" rows="10" style="min-height: 200px;" required><?php echo e(old('deskripsi')); ?></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="file">Dokumen (Opsional)</label>
                            <div id="drop-area" class="border p-4 text-center"
                                style="border: 2px dashed #ccc; position: relative; transition: background-color 0.3s ease;"
                                ondragover="handleDragOver(event)" ondragleave="handleDragLeave(event)"
                                ondrop="handleFileDrop(event)">

                                <!-- Bagian instruksi dan tombol -->
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
                                <!-- Nama file sebelum preview -->
                                <div id="file-name" class="mt-2 text-muted">
                                    <span>No file selected</span>
                                </div>
                                <!-- File Preview (dock style) -->
                                <div id="file-preview" class="mt-3" style="display: none;">
                                    <div class="dock-preview border rounded p-3 d-inline-block text-center"
                                        style="background-color: #f8f9fa;">
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
                        
                        <div class="form-group">
                            <input type="hidden" name = "status" value="aktif">
                        </div>
                        <button class="btn btn-primary" type="submit">Simpan</button>
                    </div>
                </div>
            </form>
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
                const fileInput = document.getElementById('file');
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

            // Sembunyikan instruksi awal
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

<?php echo $__env->make('layouts.main', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\Semester 6\PA III\Project\Proyek-Kelompok-07-PA-3\ui laravel\resources\views/pages/BAAK/pengumuman/create.blade.php ENDPATH**/ ?>