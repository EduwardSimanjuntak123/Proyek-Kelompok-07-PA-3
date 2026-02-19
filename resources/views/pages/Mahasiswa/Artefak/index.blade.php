@extends('layouts.main')
@section('title', 'Tugas')

@section('content')
<section class="section custom-section">
    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4>Proyek Akhir</h4>
                    </div>                    
                    <div class="card-body">
                        @include('partials.alert')
                        <div class="row">
                            <div class="col-12">
                                @foreach ($artefak as $item)
                                <ul class="nav nav-tabs mb-4" style="border-bottom: 1px solid #ddd;">
                                    <li class="nav-item">
                                        <a class="nav-link {{--  --}}" href="{{route('artefak.index')}}">
                                            PENGUMPULAN BERKAS
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{--  --}}" href="{{route('status_perizinan')}}">
                                            STATUS PERIZINAN MAJU SEMINAR
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{--  --}}" href="{{ route('jadwal.seminar')}}">
                                            JADWAL SEMINAR
                                        </a>
                                    </li>
                                      <li class="nav-item">
                                        <a class="nav-link {{--  --}}" href="{{ route('feedback.show', Crypt::encrypt($item->id)) }}">
                                            FEEDBACK
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{--  --}}" href="{{route('revisi.index')}}">
                                            BERKAS FINAL
                                        </a>
                                    </li>
                                </ul>
                          
                                {{-- Konten Utama --}}
                                <div class="card">
                                    <div class="card-body">
                                        
                                        @php
                                            $status = $statusByTugas->get($item->id);
                                        @endphp
                                        <div class="card mb-4 shadow-sm border">
                                            <div class="card-body">
                                                <h5 class="card-title font-weight-bold">{{ $item->Judul_Tugas }}</h5>
                                                <p class="mb-2">{{ $item->Deskripsi_Tugas }}</p>
                                                <ul class="list-unstyled">
                                                    <li>
                                                        <strong>Deadline:</strong> 
                                                        <span class="text-danger">{{ $item->formatted_deadline }}</span>
                                                    </li>
                                                </ul>
                                                <div class="mb-2">
                                                    <span class="badge badge-{{ $status ? 'success' : 'secondary' }}">
                                                        {{ $status ? $status->status : 'Belum dikumpulkan' }}
                                                    </span>
                                                    <a href="{{ route('artefak.create', Crypt::encrypt($item->id)) }}" class="btn btn-sm btn-primary ml-2">Lihat Detail</a>
                                                </div>
                                                <div class="mb-2 {{ $item->status_class }}">
                                                    ⏳ <span class="countdown" data-deadline="{{ $item->tanggal_pengumpulan }}"></span>
                                                </div>
                                            </div>
                                        </div>
                                      
                                        @if ($artefak->isEmpty())
                                            <div class="alert alert-info">Tidak ada tugas yang tersedia.</div>
                                        @endif

                                        {{-- Fixed Conditional Logic for Seminar Application Button --}}
                                        @if($status && $status->status === 'Submitted')
                                            @if($pengajuanSeminars->isEmpty())
                                                <div class="text">
                                                    <a href="{{ route('PengajuanSeminar.create') }}">
                                                        <button type="submit" class="btn btn-warning">
                                                            <i class="fas fa-paper-plane mr-1"></i> Ajukan Maju Seminar
                                                        </button>
                                                    </a>
                                                </div>
                                            @else
                                                @foreach($pengajuanSeminars as $item)
                                                    @if($item->status == 'menunggu')
                                                        <a href="{{ route('PengajuanSeminar.edit', Crypt::encrypt($item->id)) }}" 
                                                            class="btn btn-sm btn-primary mr-2">Edit Pengajuan
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <form method="POST" action="{{ route('PengajuanSeminar.destroy', Crypt::encrypt($item->id)) }}" style="display: inline;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger show_confirm"> Batalkan Pengajuan
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    @elseif($item->status == 'ditolak')
                                                        <a href="{{ route('PengajuanSeminar.edit', Crypt::encrypt($item->id)) }}" 
                                                            class="btn btn-sm btn-warning mr-2">
                                                            <i class="fas fa-redo"></i> Ajukan Ulang
                                                        </a>
                                                    @endif
                                                @endforeach
                                            @endif
                                        @endif
                                    </div>
                                </div>
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
<script type="text/javascript">
    $(document).on('click', '.show_confirm', function(event) {
        event.preventDefault();
        var form = $(this).closest("form");
        swal({
            title: "Yakin ingin menghapus data ini?",
            text: "Data akan terhapus secara permanen!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                form.submit();
            }
        });
    });
    
    function updateCountdown() {
        const countdownEls = document.querySelectorAll('.countdown');

        countdownEls.forEach(el => {
            const deadline = new Date(el.dataset.deadline).getTime();
            const now = new Date().getTime();
            const diff = deadline - now;

            if (diff > 0) {
                const days = Math.floor(diff / (1000 * 60 * 60 * 24));
                const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));

                if (days > 0) {
                    el.textContent = `${days} hari ${hours} jam lagi`;
                } else {
                    el.textContent = `${hours} jam ${minutes} menit lagi`;
                }
            } else {
                const absDiff = Math.abs(diff);
                const hours = Math.floor((absDiff) / (1000 * 60 * 60));
                const minutes = Math.floor((absDiff % (1000 * 60 * 60)) / (1000 * 60));
                el.textContent = `Selesai ${hours} jam ${minutes} menit yang lalu`;
                el.classList.remove('text-warning');
                el.classList.add('text-success');
            }
        });
    }

    updateCountdown();
    setInterval(updateCountdown, 60000); // update tiap 1 menit

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
