@extends('layouts.main')
@section('title', 'Edit Pengumuman')

@section('content')
<section class="section">
    <div class="section-body">
        <form action="{{route('pengumuman.BAAK.update',['id' =>Crypt::encrypt($pengumuman->id)])}}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h4>Edit Pengumuman</h4>
                    <a class="btn btn-primary btn-sm" href="{{ route('pengumuman.BAAK.index') }}">Kembali</a>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Judul</label>
                        <input type="text" name="judul" value="{{ $pengumuman->judul }}" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Deskripsi</label>
                        <textarea id="deskripsi" name="deskripsi" class="form-control" rows="10" style="min-height: 200px;" required>{{ $pengumuman->deskripsi }}</textarea>
                    </div>
                                      
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control" required>
                            <option value="aktif" {{ $pengumuman->status == 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="non-aktif" {{ $pengumuman->status == 'non-aktif' ? 'selected' : '' }}>Non-aktif</option>
                        </select>
                    </div>
                </div>
                @if(old('file', $pengumuman->file))
                                <div class="mt-3">
                                    <label>Dokumen Saat Ini:</label>
                                    <div class="border rounded p-1 bg-light d-inline-block">
                                        @php
                                            $filePath = asset('storage/' . $pengumuman->file); // atau sesuaikan path file sesuai lokasi penyimpananmu
                                            $fileExt = pathinfo($pengumuman->file, PATHINFO_EXTENSION);
                                        @endphp

                                        @if(in_array(strtolower($fileExt), ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp']))
                                            <img src="{{ $filePath }}" alt="Dokumen Lama" style="max-width: 100px; max-height: 100px; border-radius: 10px;">
                                        @else
                                            <a href="{{ $filePath }}" target="_blank">📄{{ basename($pengumuman->file) }}</a>
                                        @endif
                                    </div>
                                </div>
                            @endif

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
                                                class="d-none @error('file') is-invalid @enderror"
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
                            
                                @error('file')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                <div class="card-footer text-end">
                    <button class="btn btn-primary" type="submit">Update</button>
                </div>
            </div>
        </form>
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
