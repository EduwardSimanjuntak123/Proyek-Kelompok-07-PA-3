@extends('layouts.main')
@section('title', 'List Kelompok')

@section('content')
<section class="section custom-section">
    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h4>List pengumuman</h4>
                        <a href="{{route('pengumuman.BAAK.create')}}" class="btn btn-primary">
                            <i class="nav-icon fas fa-folder-plus"></i>&nbsp; Tambah Pengumuman
                        </a>
                    </div> 
                    <div class="card-body">
                        @include('partials.alert')
                        <div class="table-responsive">
                            <table class="table table-striped" id="table-2">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Judul</th>
                                        <th>Status</th>
                                        <th>Aksi</th>

                                    </tr>
                                </thead>
                                <tbody>
                                     @foreach($pengumuman as $item)
                                        <tr>
                                            <td>{{ $item->id }}</td>
                                            <td><a href="{{ route('pengumuman.BAAK.show',$item->id) }}">{{ $item->judul }}</a></td>
                                            {{-- <td>{{ $item->deskripsi }}</td>
                                            <td>
                                                @if($item->file)
                                                <a href="{{ asset('storage/' .$item->file) }}"target="_blank" class="btn btn-info btn-sm">Lihat File</a>
                                                @else
                                                    <span class="text-muted">Tidak ada file</span>
                                                @endif
                                            </td>  
                                            <td>{{ $item->tanggal_penulisan }}</td>
                                            <td>{{ $item->prodi->nama_prodi }}</td>
                                            <td>{{ $item->kategoriPA->kategori_pa }}</td>
                                            <td>{{ $item->tahunMasuk->Tahun_Masuk }}</td> --}}
                                            <td>{{ $item->status }}</td>
                                            <td>
                                            <div class="d-flex">
                                                <a href="{{route('pengumuman.BAAK.edit', Crypt::encrypt($item->id))}}" class="btn btn-success btn-sm"><i class="nav-icon fas fa-edit"></i> &nbsp; Edit</a>
                                                <form method="POST" action="{{route('pengumuman.BAAK.destroy', $item->id)}}">
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
