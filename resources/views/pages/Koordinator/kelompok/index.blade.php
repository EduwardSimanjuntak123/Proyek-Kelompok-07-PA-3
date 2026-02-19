@extends('layouts.main')
@section('title', 'List Kelompok')

@section('content')
<section class="section custom-section">
    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h4>List Kelompok</h4>
                        <a href="{{ route('kelompok.create') }}" class="btn btn-primary">
                            <i class="nav-icon fas fa-folder-plus"></i>&nbsp; Tambah Kelompok
                        </a>
                    </div>                    
                    <div class="card-body">
                        @include('partials.alert')
                        <div class="table-responsive">
                            <table class="table table-striped" id="table-2">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nomor Kelompok</th>
                                        <th>Kategori Proyek</th>
                                        <th>Angkatan</th>
                                        <th>Program Studi</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                     @foreach($kelompok as $item)
                                        <tr>
                                            <td>{{ $item->id }}</td>
                                            <td>{{ $item->nomor_kelompok }}</td>
                                            <td>{{ $item->kategoripa->kategori_pa ?? 'N/A' }}</td>
                                            <td>{{ $item->tahunMasuk->Tahun_Masuk ?? 'N/A' }}</td>    
                                            <td>{{ $item->prodi->nama_prodi ?? 'N/A'}}</td>  
                                            <td>{{ $item->status }}</td>
                                            <td>
                                                <div class="d-flex">
                                                    <a href="{{ route('kelompokMahasiswa.index',$item->id) }}" class="btn btn-primary btn-sm"><i class="nav-icon fas fa-edit"></i> &nbsp; Kelola</a>&nbsp;&nbsp;
                                                    <a href="{{route('kelompok.edit', Crypt::encrypt($item->id))}}" class="btn btn-success btn-sm"><i class="nav-icon fas fa-edit"></i> &nbsp; Edit</a>
                                                    <form method="POST" action="{{route('kelompok.destroy', $item->id)}}">
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
