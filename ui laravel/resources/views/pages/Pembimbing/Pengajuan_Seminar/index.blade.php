@extends('layouts.main')

@section('title', 'Daftar Pengajuan Seminar Mahasiswa')

@section('content')
<section class="section custom-section">
    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Daftar Pengajuan Seminar Mahasiswa</h4>
                    </div>
                    <div class="card-body">
                        @include('partials.alert')
                        <div class="table-responsive">
                            <table class="table table-striped" id="table-2">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal Pengajuan</th>
                                        <th>Kelompok</th>
                                        <th>Kategori PA</th>
                                        <th>Prodi</th>
                                        <th>File</th>
                                        <th>Status</th>
                                        <th>Catatan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pengajuanSeminars as $index => $pengajuan)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $pengajuan->created_at->format('d/m/Y') }}</td>
                                        <td>
                                            @if($pengajuan->kelompok)
                                                Kelompok {{ $pengajuan->kelompok->nomor_kelompok }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>{{ $pengajuan->kelompok->kategoriPA->kategori_pa }}</td>
                                        <td>{{ $pengajuan->kelompok->prodi->nama_prodi }}</td>
                                        <td>
                                            @if($pengajuan->files->count() > 0)
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-info dropdown-toggle" type="button" id="fileDropdown{{ $pengajuan->id }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        {{ $pengajuan->files->count() }} File
                                                    </button>
                                                    <div class="dropdown-menu" aria-labelledby="fileDropdown{{ $pengajuan->id }}">
                                                        @foreach($pengajuan->files as $file)
                                                            <a class="dropdown-item" href="{{ Storage::url($file->file_path) }}" target="_blank">
                                                                <i class="
                                                                    @if(in_array(strtolower(pathinfo($file->file_name, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif']))
                                                                        fas fa-image
                                                                    @elseif(strtolower(pathinfo($file->file_name, PATHINFO_EXTENSION)) == 'pdf')
                                                                        fas fa-file-pdf
                                                                    @elseif(in_array(strtolower(pathinfo($file->file_name, PATHINFO_EXTENSION)), ['doc', 'docx']))
                                                                        fas fa-file-word
                                                                    @else
                                                                        fas fa-file
                                                                    @endif
                                                                    mr-2"></i>
                                                                {{ $file->file_name }}
                                                            </a>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @else
                                                <span class="badge badge-danger">Tidak ada file</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge 
                                                @if($pengajuan->status == 'disetujui') badge-success
                                                @elseif($pengajuan->status == 'ditolak') badge-danger
                                                @else badge-warning
                                                @endif">
                                                {{ ucfirst($pengajuan->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($pengajuan->catatan)
                                                <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#catatanModal{{ $pengajuan->id }}">
                                                    Lihat Catatan
                                                </button>
                                                
                                                <!-- Modal untuk melihat catatan -->
                                                <div class="modal fade" id="catatanModal{{ $pengajuan->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Catatan</h5>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="alert alert-danger">
                                                                    {{ $pengajuan->catatan }}
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            @if($pengajuan->status == 'menunggu')
                                                <div class="d-flex">
                                                    <button type="button" class="btn btn-sm btn-success mr-2" data-toggle="modal" data-target="#setujuiModal{{ $pengajuan->id }}">
                                                        <i class="fas fa-check"></i> Setujui
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#tolakModal{{ $pengajuan->id }}">
                                                        <i class="fas fa-times"></i> Tolak
                                                    </button>
                                                </div>
                                                
      <!-- Modal Setujui -->
<div class="modal fade" id="setujuiModal{{ $pengajuan->id }}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Persetujuan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menyetujui pengajuan seminar ini?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <form action="{{ route('PembimbingPengajuanSeminar.setujui', Crypt::encrypt($pengajuan->id)) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <button type="submit" class="btn btn-success">Setujui</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tolak -->
<div class="modal fade" id="tolakModal{{ $pengajuan->id }}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tolak Pengajuan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('PembimbingPengajuanSeminar.tolak', Crypt::encrypt($pengajuan->id)) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label for="catatan">Catatan Penolakan <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="catatan" name="catatan" rows="4" required></textarea>
                        <small class="form-text text-muted">Berikan alasan penolakan agar mahasiswa dapat memperbaiki pengajuannya.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Tolak Pengajuan</button>
                </div>
            </form>
        </div>
    </div>
</div>
                                            @else
                                                <span class="text-muted">Tidak ada aksi</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection