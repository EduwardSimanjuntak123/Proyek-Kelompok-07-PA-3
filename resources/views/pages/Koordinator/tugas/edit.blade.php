@extends('layouts.main')
@section('title', 'Edit Tugas')

@section('content')
<section class="section">
    <div class="section-body">
        <div class="row">
            <div class="col-12">
                @include('partials.alert')
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h4>Edit Tugas</h4>
                        <a href="{{ route('koordinator.tugas.index') }}" class="btn btn-primary">Kembali</a>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('tugas.update', ['id' => Crypt::encrypt($tugas->id)]) }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            {{--  input user_id hide --}}
                            <input type="hidden" name="user_id" value="{{ $user_id }}">
                            {{--  input TA_id hide --}}
                            <input type="hidden" name="TM_id" value="{{ $tahun_masuk->id }}">
                            {{--  input prodi_id hide --}}
                            <input type="hidden" name="prodi_id" value="{{ $prodi->id }}">
                             {{--  KPA_id hide --}}
                            <input type="hidden" name="KPA_id" value="{{ $kategoripa->id }}">

                            <div class="form-group">
                                <label for="Judul_Tugas">Judul Tugas</label>
                                <input type="text" name="Judul_Tugas" id="Judul_Tugas" class="form-control @error('Judul_Tugas') is-invalid @enderror" placeholder="Masukkan Judul Tugas" value="{{ old('Judul_Tugas', $tugas->Judul_Tugas) }}">
                                @error('Judul_Tugas')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="Deskripsi_Tugas" class="form-label">Deskripsi Tugas</label>
                                <textarea name="Deskripsi_Tugas" 
                                          id="Deskripsi_Tugas" 
                                          rows="5" 
                                          class="form-control fs-5 @error('Deskripsi_Tugas') is-invalid @enderror" 
                                          style="height: 200px;" 
                                          placeholder="Masukkan Deskripsi">{{ old('Deskripsi_Tugas', $tugas->Deskripsi_Tugas) }}</textarea>
                                @error('Deskripsi_Tugas')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            

                            <div class="form-group">
                                <label for="tanggal_pengumpulan">Batas Pengumpulan</label>
                                <input type="datetime-local" name="tanggal_pengumpulan" id="tanggal_pengumpulan" class="form-control @error('tanggal_pengumpulan') is-invalid @enderror" value="{{ old('Judul_Tugas', $tugas->tanggal_pengumpulan) }}">
                                @error('tanggal_pengumpulan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            @if(old('file', $tugas->file))
                                <div class="mt-3">
                                    <label>Dokumen Saat Ini:</label>
                                    <div class="border rounded p-1 bg-light d-inline-block">
                                        @php
                                            $filePath = asset('storage/' . $tugas->file); // atau sesuaikan path file sesuai lokasi penyimpananmu
                                            $fileExt = pathinfo($tugas->file, PATHINFO_EXTENSION);
                                        @endphp

                                        @if(in_array(strtolower($fileExt), ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp']))
                                            <img src="{{ $filePath }}" alt="Dokumen Lama" style="max-width: 100px; max-height: 100px; border-radius: 10px;">
                                        @else
                                            <a href="{{ $filePath }}" target="_blank">📄{{ basename($tugas->file) }}</a>
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

                            <!-- Status -->
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="berlangsung" {{ old('status', $tugas->status) == 'berlangsung' ? 'selected' : '' }}>Berlangsung</option>
                                    <option value="selesai" {{ old('status', $tugas->status) == 'selesai' ? 'selected' : '' }}>Selesai</option>
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