
@extends('layouts.main')

@section('title', 'Tambah Pengumuman')

@section('content')
<section class="section">
    <div class="section-body">
        <form action="{{ route('pengumuman.store') }}" method="POST"  enctype="multipart/form-data">
            @csrf
            <div class="card">
                <div class="card-header"><h4>Tambah Pengumuman</h4></div>
                <div class="card-body">
                    <div class="form-group">
                         {{--  input user_id hide --}}
                         <input type="hidden" name="user_id" value="{{ $user_id }}">
                         {{--  input TA_id --}}
                         <input type="hidden" name="TM_id" value="{{ $TM_id }}">
                          {{--  input KPA_id --}}
                          <input type="hidden" name="KPA_id" value="{{ $KPA_id }}">
                           {{--  input Prodi_id --}}
                         <input type="hidden" name="prodi_id" value="{{ $prodi_id }}">
                        {{-- judul --}}
                        <label>Judul</label>
                        <input type="text" name="judul" class="form-control" required>
                    </div>
                    {{-- deskripsi --}}
                    <div class="form-group">
                        <label>deskripsi</label>
                        <input type="text" name="deskripsi" class="form-control" required>
                    </div>
                    {{-- file --}}
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
                                           class="d-none @error('file') is-invalid @enderror" 
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
                        @error('file')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                      {{-- Status --}}
                      <div class="form-group">
                       <input type="hidden" name = "status" value="aktif">
                    </div>
                    <button class="btn btn-primary" type="submit">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</section>
@endsection
@push('script')
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

