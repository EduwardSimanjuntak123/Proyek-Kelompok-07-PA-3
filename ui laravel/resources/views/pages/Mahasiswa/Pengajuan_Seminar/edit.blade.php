@extends('layouts.main')
@section('title', 'Edit Pengajuan Seminar')

@section('content')
<section class="section custom-section">
    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h4>Edit Pengajuan Seminar</h4>
                        <a class="btn btn-primary btn-sm" href="{{ route('artefak.index') }}">Kembali</a>
                    </div>
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible show fade">
                                <div class="alert-body">
                                    <button class="close" data-dismiss="alert"><span>&times;</span></button>
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('PengajuanSeminar.update', Crypt::encrypt($pengajuanSeminar->id)) }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            
                            <div class="form-group">
                                <label for="kelompok_id">Kelompok</label>
                                <input type="text" class="form-control" value="Kelompok {{ $pengajuanSeminar->kelompok->nomor_kelompok }}" readonly>
                            </div>

                            <div class="form-group">
                                <label for="pembimbing_id">Pembimbing</label>
                                <input type="text" class="form-control" value="{{ $pembimbing->nama ?? 'Tidak ada pembimbing' }}" readonly>
                            </div>

                            <div class="form-group">
                                <label>File Saat Ini</label>
                                <div class="row">
                                    @if($pengajuanSeminar->files->count() > 0)
                                        @foreach($pengajuanSeminar->files as $file)
                                            <div class="col-md-4 mb-3">
                                                <div class="card">
                                                    <div class="card-body text-center">
                                                        @if(in_array(strtolower(pathinfo($file->file_name, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif']))
                                                            <img src="{{ Storage::url($file->file_path) }}" alt="{{ $file->file_name }}" class="img-fluid mb-2" style="max-height: 100px;">
                                                        @elseif(strtolower(pathinfo($file->file_name, PATHINFO_EXTENSION)) == 'pdf')
                                                            <i class="fas fa-file-pdf fa-3x mb-2 text-danger"></i>
                                                        @elseif(in_array(strtolower(pathinfo($file->file_name, PATHINFO_EXTENSION)), ['doc', 'docx']))
                                                            <i class="fas fa-file-word fa-3x mb-2 text-primary"></i>
                                                        @else
                                                            <i class="fas fa-file fa-3x mb-2 text-secondary"></i>
                                                        @endif
                                                        <p class="card-text small mb-1" style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $file->file_name }}">
                                                            {{ $file->file_name }}
                                                        </p>
                                                        <p class="card-text small text-muted">
                                                            {{ number_format($file->file_size / 1024, 2) }} KB
                                                        </p>
                                                        <a href="{{ Storage::url($file->file_path) }}" class="btn btn-sm btn-info" target="_blank">Lihat</a>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="col-12">
                                            <div class="alert alert-info">
                                                Tidak ada file yang tersedia.
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="files">Upload File Baru (Maksimal 5 File)</label>
                                <div id="drop-area" 
                                     class="border p-4 text-center" 
                                     style="border: 2px dashed #ccc; position: relative; transition: background-color 0.3s ease;"
                                     ondragover="handleDragOver(event)" 
                                     ondragleave="handleDragLeave(event)" 
                                     ondrop="handleFileDrop(event)">
                                     
                                    <!-- Bagian instruksi dan tombol -->
                                    <div id="upload-instructions">
                                        <i class="fas fa-upload fa-2x mb-2"></i>
                                        <p>Drag File Anda Disini</p>
                                        <p>Atau</p>
                                        <label class="btn btn-primary">
                                            Pilih File
                                            <input type="file" name="files[]" id="files" 
                                                   class="d-none @error('files') is-invalid @enderror" 
                                                   onchange="updateFileList(event)"
                                                   multiple>
                                        </label>
                                    </div>
                                    
                                    <!-- Informasi file yang dipilih -->
                                    <div id="file-info" class="mt-3">
                                        <p class="text-muted">Format: pdf, jpg, jpeg, png, docx (Max: 10MB per file)</p>
                                        <p id="file-count" class="text-muted">Belum ada file yang dipilih</p>
                                    </div>
                                    
                                    <!-- Preview file yang dipilih -->
                                    <div id="file-preview-container" class="mt-3 row justify-content-center" style="display: none;">
                                        <!-- File previews will be added here dynamically -->
                                    </div>
                                </div>
                                <small class="form-text text-muted">Semua file yang ada akan diganti dengan file baru yang diupload.</small>
                                @error('files')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                @error('files.*')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle mr-2"></i> Perhatian: Mengubah pengajuan akan mengatur ulang status menjadi "Menunggu" dan menghapus catatan sebelumnya.
                            </div>

                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('script')
<script>
    // Variabel untuk menyimpan file yang dipilih
    let selectedFiles = [];
    const maxFiles = 5;
    
    function updateFileList(event) {
        const fileInput = event.target;
        const files = Array.from(fileInput.files);
        
        // Validasi jumlah file
        if (files.length > maxFiles) {
            alert(`Maksimal ${maxFiles} file yang diperbolehkan.`);
            fileInput.value = '';
            return;
        }
        
        selectedFiles = files;
        displayFilePreview();
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

        const files = Array.from(event.dataTransfer.files);
        
        // Validasi jumlah file
        if (files.length > maxFiles) {
            alert(`Maksimal ${maxFiles} file yang diperbolehkan.`);
            return;
        }
        
        // Update file input dengan file yang di-drop
        const fileInput = document.getElementById('files');
        const dataTransfer = new DataTransfer();
        
        files.forEach(file => {
            dataTransfer.items.add(file);
        });
        
        fileInput.files = dataTransfer.files;
        selectedFiles = files;
        displayFilePreview();
    }

    function displayFilePreview() {
        const previewContainer = document.getElementById('file-preview-container');
        const fileCount = document.getElementById('file-count');
        
        // Update file count text
        fileCount.textContent = selectedFiles.length > 0 
            ? `${selectedFiles.length} file dipilih dari ${maxFiles} maksimum` 
            : 'Belum ada file yang dipilih';
        
        // Clear previous previews
        previewContainer.innerHTML = '';
        
        if (selectedFiles.length === 0) {
            previewContainer.style.display = 'none';
            return;
        }
        
        // Show preview container
        previewContainer.style.display = 'flex';
        
        // Create preview for each file
        selectedFiles.forEach((file, index) => {
            const previewItem = document.createElement('div');
            previewItem.className = 'col-md-4 col-sm-6 mb-3';
            
            const previewCard = document.createElement('div');
            previewCard.className = 'card h-100';
            previewCard.style.maxWidth = '200px';
            
            const cardBody = document.createElement('div');
            cardBody.className = 'card-body text-center';
            
            // File icon or image preview
            const isImage = file.type.startsWith('image/');
            if (isImage) {
                const img = document.createElement('img');
                img.className = 'mb-2';
                img.style.maxWidth = '100px';
                img.style.maxHeight = '100px';
                img.style.objectFit = 'contain';
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    img.src = e.target.result;
                };
                reader.readAsDataURL(file);
                
                cardBody.appendChild(img);
            } else {
                const icon = document.createElement('i');
                
                // Set icon based on file type
                if (file.type.includes('pdf')) {
                    icon.className = 'fas fa-file-pdf fa-3x mb-2 text-danger';
                } else if (file.type.includes('word') || file.name.endsWith('.docx')) {
                    icon.className = 'fas fa-file-word fa-3x mb-2 text-primary';
                } else {
                    icon.className = 'fas fa-file fa-3x mb-2 text-secondary';
                }
                
                cardBody.appendChild(icon);
            }
            
            // File name with truncation
            const fileName = document.createElement('p');
            fileName.className = 'card-text small mb-1';
            fileName.style.overflow = 'hidden';
            fileName.style.textOverflow = 'ellipsis';
            fileName.style.whiteSpace = 'nowrap';
            fileName.title = file.name;
            fileName.textContent = file.name;
            
            // File size
            const fileSize = document.createElement('p');
            fileSize.className = 'card-text small text-muted';
            fileSize.textContent = formatFileSize(file.size);
            
            // Remove button
            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'btn btn-sm btn-danger mt-2';
            removeBtn.textContent = 'Hapus';
            removeBtn.onclick = function() {
                removeFile(index);
            };
            
            cardBody.appendChild(fileName);
            cardBody.appendChild(fileSize);
            cardBody.appendChild(removeBtn);
            previewCard.appendChild(cardBody);
            previewItem.appendChild(previewCard);
            previewContainer.appendChild(previewItem);
        });
    }
    
    function removeFile(index) {
        // Remove file from array
        selectedFiles.splice(index, 1);
        
        // Update file input
        const fileInput = document.getElementById('files');
        const dataTransfer = new DataTransfer();
        
        selectedFiles.forEach(file => {
            dataTransfer.items.add(file);
        });
        
        fileInput.files = dataTransfer.files;
        
        // Update preview
        displayFilePreview();
    }
    
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
</script>
@endpush