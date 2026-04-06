@extends('layouts.main')
@section('title', 'List Dosen Role')

@section('content')
<section class="section custom-section">
    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h4>List Role</h4>
                        <a href="{{ route('manajemen-role.create') }}" class="btn btn-primary">
                            <i class="nav-icon fas fa-folder-plus"></i>&nbsp; Tambah Dosen Role
                        </a>
                    </div>                    
                    <div class="card-body">
                        @include('partials.alert')
                        <div class="table-responsive">
                            <table class="table table-striped" id="table-2">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Dosen</th>
                                        <th>Prodi</th>
                                        <th>Kategori PA</th>
                                        <th>Role </th>
                                        <th>Tahun Ajaran</th>
                                        <th>Tahun Masuk</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- @dd($dosenroles) --}}
                                    @foreach($dosenroles as $item)
                                        <tr>
                                            <td>{{ $item->id }}</td>
                                            <td>{{ $item->nama }}</td>
                                            <td>{{ $item->prodi->nama_prodi ?? 'N/A'}}</td>                                           
                                            <td>{{ $item->kategoripa->kategori_pa ?? 'N/A' }}</td>
                                            <td>{{ $item->role->role_name ?? 'N/A' }}</td>
                                            <td>{{ $item->tahunAjaran->tahun_mulai }}/{{ $item->tahunAjaran->tahun_selesai }}</td>
                                            <td>{{ $item->tahunMasuk->Tahun_Masuk ?? 'N/A' }}</td>    
                                            <td>{{ $item->status }}</td>
                                            <td>
                                                <div class="d-flex">
                                                    <a href="{{route('manajemen-role.edit', Crypt::encrypt($item->id))}}" class="btn btn-success btn-sm"><i class="nav-icon fas fa-edit"></i> &nbsp; Edit</a>
                                                    <form method="POST" action="{{ route('manajemen-role.destroy', $item->id)}}">
                                                        @csrf
                                                        @method('delete')
                                                        <button class="btn btn-danger btn-sm show_confirm" data-toggle="tooltip" title='Delete' style="margin-left: 8px"><i class="nav-icon fas fa-trash-alt"></i> &nbsp; Hapus</button>
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
                title: `Yakin ingin menghapus data ini?`
                , text: "Data akan terhapus secara permanen!"
                , icon: "warning"
                , buttons: true
                , dangerMode: true
            , })
            .then((willDelete) => {
                if (willDelete) {
                    form.submit();
                }
            });
    });

</script>
@endpush
