@extends('layouts.main')
@section('title', 'View')

@section('content')
<section class="section custom-section">
    <div class="section-body">
        <div class="row">
            <div class="col-14 col-md-10 offset-md-1">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h4>Detail Tugas</h4>
                        <a class="btn btn-primary btn-sm" href="{{ route('Mahasiswa.tugas.index') }}">Kembali</a>
                    </div>
                    <div class="card-body">
                        @include('partials.alert')

                        <table class="table table-bordered">
                            <tr>
                                <th>Judul</th>
                                <td>{{ $tugas->Judul_Tugas }}</td>
                            </tr>
                            <tr>
                                <th>Instruksi</th>
                                <td>{{ $tugas->Deskripsi_Tugas }}</td>
                            </tr>
                            <tr>
                                <th>File Tugas</th>
                                <td>
                                    @if ($tugas->file)
                                        <a href="{{ asset('storage/' . $tugas->file) }}" target="_blank" class="btn btn-info btn-sm">
                                            Lihat File
                                        </a>
                                    @else
                                        <span class="text-muted">Tidak ada file</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Batas Waktu</th>
                                <td>
                                    {{ \Carbon\Carbon::parse($tugas->batas)->format('d-m-Y H:i') }} 
                                    <span class="{{ $tugas->status_class }}"> &mdash; {{ $tugas->time_remaining }}</span>
                                </td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>{{ $tugas->status }}</td>
                            </tr>
                            @if($existingSubmission && $existingSubmission->status === 'Submitted')
                            <tr>
                                <th>Feedback Koordinator</th>
                                <td>{{ $existingSubmission->feedback ?? 'Belum terdapat tanggapan.' }}</td>
                            </tr>
                            <tr>
                                <th>Feedback Pembimbing</th>
                                <td>{{ $existingSubmission->feedback_pembimbing ?? 'Belum terdapat tanggapan.' }}</td>
                            </tr>
                            <tr>
                                <th>Feedback Penguji</th>
                                <td>{{ $existingSubmission->feedback_penguji ?? 'Belum terdapat tanggapan.' }}</td>
                            </tr>
                        @elseif($existingSubmission && $existingSubmission->status === '')
                            <tr>
                                <td colspan="2"><span class="text-muted">Belum terdapat tanggapan.</span></td>
                            </tr>
                        @else
                            <tr>
                                <td colspan="2"><span class="text-muted">Belum terdapat tanggapan.</span></td>
                            </tr>
                        @endif
                           
                        </table>

                        {{-- Form Upload File --}}
                        @if (!$hasSubmitted)
                            <form method="POST" action="{{ route('Mahasiswa.tugas.submit', $tugas->id) }}" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="kelompok_id" value="{{ $kelompokId }}">
                                <input type="hidden" name="tugas_id" value="{{ $idTugas }}"> 

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
                        @else
                            {{-- Jika sudah submit --}}
                            @if($hasSubmitted)
                            <div class="alert alert-success">
                                <strong>Sudah dikumpulkan</strong><br>
                                Waktu Submit: {{ \Carbon\Carbon::parse($existingSubmission->waktu_submit)->format('d M Y - H:i') }}<br>
                                File: 
                                <a href="{{ asset('storage/' . $existingSubmission->file_path) }}" target="_blank" class="btn btn-sm btn-primary mt-1">
                                    Lihat File
                                </a>
                            </div>
                        @else
                            <div class="alert alert-warning">
                                Belum mengumpulkan tugas ini.
                            </div>
                        @endif
                        
                            <div class="text-right">
                                 <a href="{{ route('Mahasiswa.tugas.edit', Crypt::encrypt($existingSubmission->id)) }}" class="btn btn-warning">Edit File</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
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
            const fileInput = document.getElementById('file_path');
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
@endpush
