<?php $__env->startSection('title', 'Tambah Tugas'); ?>

<?php $__env->startSection('content'); ?>
<section class="section custom-section">
    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h4>Tambah Tugas</h4>
                        <a class="btn btn-primary btn-sm" href="<?php echo e(route('koordinator.tugas.index')); ?>">Kembali</a>
                    </div>
                    <div class="card-body">
                        
                        <?php if($errors->any()): ?>
                            <div class="alert alert-danger alert-dismissible show fade">
                                <div class="alert-body">
                                    <button class="close" data-dismiss="alert"><span>&times;</span></button>
                                    <ul>
                                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <li><?php echo e($error); ?></li>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </ul>
                                </div>
                            </div>
                        <?php endif; ?>
                    <form method="POST" action="<?php echo e(route('tugas.store')); ?>" enctype="multipart/form-data">
                            <?php echo csrf_field(); ?>
                           
                            
                            <input type="hidden" name="user_id" value="<?php echo e($user_id); ?>">
                            
                            <input type="hidden" name="TM_id" value="<?php echo e($tahun_masuk->id); ?>">
                            
                            <input type="hidden" name="prodi_id" value="<?php echo e($prodi->id); ?>">
                             
                            <input type="hidden" name="KPA_id" value="<?php echo e($kategoripa->id); ?>">

                            <div class="form-group">
                                <label for="Judul_Tugas">Judul Tugas</label>
                                <input type="text" name="Judul_Tugas" id="Judul_Tugas" class="form-control <?php $__errorArgs = ['Judul_Tugas'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" placeholder="Masukkan Judul Tugas" value="<?php echo e(old('Judul_Tugas')); ?>">
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

                            <div class="form-group">
                                <label for="Deskripsi_Tugas">Deskripsi Tugas</label>
                                <textarea name="Deskripsi_Tugas" id="Deskripsi_Tugas"
                                    class="form-control <?php $__errorArgs = ['Deskripsi_Tugas'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    placeholder="Masukkan Deskripsi Tugas"
                                    rows="5"><?php echo e(old('Deskripsi_Tugas')); ?></textarea>
                                </div>

                            <div class="form-group">
                                <label for="kategori_tugas">Kategori </label>
                                <select name="kategori_tugas" id="kategori_tugas" class="form-control">
                                    <option value="">--Pilih Kategori Tugas --</option>
                                    <option value="Tugas" >Tugas(submitan Progres)</option>
                                    <option value="Artefak">Artefak(Submitan Final)</option>
                                    <option value="Revisi">Revisi(Submitan Perbaikan)</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="tanggal_pengumpulan">Batas Pengumpulan</label>
                                <input type="datetime-local" name="tanggal_pengumpulan" id="tanggal_pengumpulan" class="form-control <?php $__errorArgs = ['tanggal_pengumpulan'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('tanggal_pengumpulan') ? old('tanggal_pengumpulan') : ''); ?>">
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

                            <div class="form-group">
                                <label for="file">Dokumen (Opsional)</label>
                                <div id="drop-area" 
                                     class="border p-4 text-center" 
                                     style="border: 2px dashed #ccc; position: relative; transition: background-color 0.3s ease;"
                                     ondragover="handleDragOver(event)" 
                                     ondragleave="handleDragLeave(event)" 
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
                            
                        
                            
                            <div class="form-group">
                                <select name="status" id="status" class="form-control" hidden>
                                    <option value="berlangsung" <?php echo e(old('status', 'berlangsung') == 'berlangsung' ? 'selected' : ''); ?>>Berlangsung</option>
                                </select>
                            </div>
                            
                            
                            <button type="submit" class="btn btn-primary">Tambah</button>
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
<?php echo $__env->make('layouts.main', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\Semester 6\PA III\Project\Proyek-Kelompok-07-PA-3\ui laravel\resources\views/pages/Koordinator/tugas/create.blade.php ENDPATH**/ ?>