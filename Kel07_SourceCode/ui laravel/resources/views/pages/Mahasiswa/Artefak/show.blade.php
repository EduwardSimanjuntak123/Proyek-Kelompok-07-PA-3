@extends('layouts.main')
@section('title', 'View')

@section('content')
<section class="section custom-section">
    <div class="section-body">
        <div class="row">
            <div class="col-12 ">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h4>Detail Tugas</h4>
                        <a class="btn btn-primary btn-sm" href="{{ route('artefak.index') }}">Kembali</a>
                    </div>
                    <div class="card-body">
                        @include('partials.alert')

                        <div class="row">
                            <div class="col-12">
                                @foreach ($artefak as $item)
                                    <ul class="nav nav-tabs mb-4" style="border-bottom: 1px solid #ddd;">
                                        <li class="nav-item">
                                            <a class="nav-link" href="{{ route('artefak.index') }}">PENGUMPULAN BERKAS</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="{{ route('status_perizinan') }}">STATUS PERIZINAN MAJU SEMINAR</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="{{ route('jadwal.seminar') }}">JADWAL SEMINAR</a>
                                        </li>
                                         <li class="nav-item">
                                            <a class="nav-link" href="{{ route('feedback.show', Crypt::encrypt($item->id)) }}">FEEDBACK</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="{{ route('revisi.index') }}">BERKAS FINAL</a>
                                        </li>
                                    </ul>

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
                                        @if($hasSubmitted)
                                        <tr>
                                            <th>Feedback Koordinator</th>
                                            <td>{{ $existingSubmission->feedback ?? 'Belum terdapat tanggapan.' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Feedback Pembimbing</th>
                                            <td>{{ $existingSubmission->feedback_pembimbing ?? 'Belum terdapat tanggapan.' }}</td>
                                        </tr>
                                    @endif
                                    </table>

                                    {{-- Form Upload File --}}
                                    @if ($hasSubmitted)
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
                                @endforeach
                            </div>
                        </div>
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
