@extends('layouts.main')

@section('title', 'Daftar Pengajuan Seminar')

@section('content')
<section class="section custom-section">
    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h4>Daftar Pengajuan Seminar</h4>
                        <a href="{{ route('PengajuanSeminar.create')}}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Tambah Pengajuan
                        </a>
                    </div>
                    <div class="card-body">
                        @include('partials.alert')
                        <div class="table-responsive">
                            <table class="table table-striped" id="table-2">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal Pengajuan</th>
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
                                                                <h5 class="modal-title">Catatan Pembimbing</h5>
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
                                            <div class="d-flex">
                                                @if($pengajuan->status == 'menunggu')
                                                <a href="{{ route('PengajuanSeminar.edit', Crypt::encrypt($pengajuan->id)) }}" 
                                                   class="btn btn-sm btn-primary mr-2">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form method="POST" action="{{ route('PengajuanSeminar.destroy', Crypt::encrypt($pengajuan->id)) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger show_confirm">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                                @elseif($pengajuan->status == 'ditolak')
                                                <a href="{{ route('PengajuanSeminar.edit', Crypt::encrypt($pengajuan->id)) }}" 
                                                   class="btn btn-sm btn-warning mr-2">
                                                    <i class="fas fa-redo"></i> Ajukan Ulang
                                                </a>
                                                @endif
                                            </div>
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

@push('script')
<script type="text/javascript">
    $('.show_confirm').click(function(event) {
        var form = $(this).closest("form");
        event.preventDefault();
        swal({
            title: `Yakin ingin menghapus data ini?`,
            text: "Data akan terhapus secara permanen!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        })
        .then((willDelete) => {
            if (willDelete) {
                form.submit();
            }
        });
    });
</script>
@endpush