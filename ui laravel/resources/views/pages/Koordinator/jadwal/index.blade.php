@extends('layouts.main')
@section('title', 'Jadwal')

@section('content')
<section class="section custom-section">
    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h4>Jadwal Seminar</h4>
                        <a href="{{ route('jadwal.create') }}" class="btn btn-primary">
                            <i class="nav-icon fas fa-folder-plus"></i>&nbsp; Tambah Jadwal
                        </a>
                    </div>                    
                    <div class="card-body">
                        @include('partials.alert')
                        
                        {{-- Display warning for unapproved seminar submissions --}}
                        @if(session('warning'))
                            <div class="alert alert-danger alert-dismissible show fade">
                                <div class="alert-body">
                                    <button class="close" data-dismiss="alert"><span>&times;</span></button>
                                    {{ session('warning') }}
                                    
                                    @if(session('showUnapprovedAlert'))
                                        <br><strong>Catatan:</strong> Pengajuan seminar harus disetujui oleh dosen pembimbing sebelum jadwal dapat dibuat.
                                    @endif
                                </div>
                            </div>
                        @endif
                        
                        <div class="table-responsive">
                            <table class="table table-striped" id="table-2">
                                <thead>
                                    <tr>
                                        <th>Kelompok</th>
                                        <th>Pembimbing</th> 
                                        <th>Waktu Mulai</th>
                                        <th>Waktu Selesai</th>
                                        <th>Ruangan</th>
                                        <th>Dosen Penguji</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($jadwal as $item)
                                        <tr>
                                            <td>{{ $item->kelompok->nomor_kelompok ?? '-' }}</td>
                                            <td>{{ $item->pembimbing_nama ?? '-' }}</td>
                                            <td>{{ \Carbon\Carbon::parse($item->waktu_mulai)->format('d M Y H:i') }}</td>    
                                            <td>{{ \Carbon\Carbon::parse($item->waktu_selesai)->format('d M Y H:i') }}</td>    
                                            <td>{{ $item->ruangan->ruangan ?? '-'}}</td>
                                            <td>{!! $item->penguji_nama !!}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <a href="{{ route('jadwal.show', Crypt::encrypt($item->id)) }}" class="btn btn-info btn-sm me-2">
                                                        <i class="nav-icon fas fa-info-circle"></i>&nbsp;Detail
                                                    </a>
                                                    <a href="{{ route('jadwal.edit', Crypt::encrypt($item->id)) }}" class="btn btn-success btn-sm me-2">
                                                        <i class="nav-icon fas fa-edit"></i>&nbsp;Edit
                                                    </a>
                                                    <form method="POST" action="{{ route('jadwal.destroy', Crypt::encrypt($item->id)) }}">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button class="btn btn-danger btn-sm show_confirm" data-toggle="tooltip" title="Hapus">
                                                            <i class="nav-icon fas fa-trash-alt"></i>&nbsp;Hapus
                                                        </button>
                                                    </form>
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
        var name = $(this).data("name");
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
