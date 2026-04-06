@extends('layouts.main')
@section('title', 'Edit File Tugas')

@section('content')
<section class="section">
    <div class="section-body">
        <div class="row">
            <div class="col-12">
                @include('partials.alert')
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h4>Edit File</h4>
                        <a href="{{route('Mahasiswa.tugas.index')}}" class="btn btn-primary">Kembali</a>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('Mahasiswa.tugas.update', ['id' => Crypt::encrypt($artefak->id)]) }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
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
                                                class="d-none @error('file') is-invalid @enderror" 
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
                                @error('file')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="text-right mt-3">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        
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