    @extends('layouts.main')

    @section('title', 'Tambah Pengajuan Seminar')

    @section('content')
    <section class="section custom-section">
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <h4>Tambah Pengajuan Seminar</h4>
                            <a class="btn btn-primary btn-sm" href="{{route('artefak.index')}}">Kembali</a>
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

                            
                            <form method="POST" action="{{ route('PengajuanSeminar.store') }}" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group">
                                    <label for="kelompok_id">Kelompok</label>
                                    <input type="hidden" name="kelompok_id" value="{{ $kelompok->id }}">
                                    <input type="text" class="form-control" value="Kelompok {{ $kelompok->nomor_kelompok }}" readonly>
                                </div>

                                <div class="form-group">
                                    <label for="pembimbing_id">Pembimbing</label>
                                    <input type="hidden" name="pembimbing_id" value="{{ $pembimbing->id }}">
                                    <input type="text" class="form-control" value="{{ $pembimbing->nama }}" readonly>
                                </div>

                                <div class="form-group">
                                <label for="files" class="font-weight-bold mb-2">
                                    Silakan unggah dokumen pengajuan yang meliputi:
                                </label>
                                <ul class="mb-3 pl-4 text-muted">
                                    <li>Dokumen Pengembangan Produk</li>
                                    <li>Slide Presentasi</li>
                                    <li>Kartu Bimbingan</li>
                                    <li>Surat Pernyataan Siap Maju Proyek AKhir </li>
                                    <li><strong>Maksimal 5 file, maksimal 10MB per file</strong></li>
                                    <li>Format yang diperbolehkan: pdf, jpg, jpeg, png, docx</li>
                                </ul>
                                <div id="drop-area"
                                    class="border rounded p-4 text-center"
                                    style="border: 2px dashed #aaa; background-color: #fafafa; cursor: pointer;"
                                    ondragover="handleDragOver(event)"
                                    ondragleave="handleDragLeave(event)"
                                    ondrop="handleFileDrop(event)"
                                >
                                    <div id="upload-instructions">
                                        <i class="fas fa-cloud-upload-alt fa-3x mb-2 text-primary"></i>
                                        <p class="mb-1">Seret file ke sini</p>
                                        <p class="mb-2">atau</p>
                                        <label class="btn btn-outline-primary">
                                            Pilih File
                                            <input type="file" name="files[]" id="files"
                                                class="d-none @error('files') is-invalid @enderror"
                                                onchange="updateFileList(event)"
                                                multiple
                                            >
                                        </label>
                                    </div>
                                    <div id="file-info" class="mt-3">
                                        <p id="file-count" class="text-muted">Belum ada file yang dipilih</p>
                                    </div>
                                    <div id="file-preview-container" class="mt-3 row justify-content-start" style="display: none;">
                                        <!-- Preview akan ditambahkan via JS -->
                                    </div>
                                </div>
                                @error('files')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                @error('files.*')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>


                                <input type="hidden" name="status" value="menunggu">

                                <button type="submit" class="btn btn-primary">Simpan Pengajuan</button>
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