@extends('layouts.main')
@section('title', 'Jadwal Staff')

@section('content')
<section class="section custom-section">
    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h4>Jadwal Seminar</h4>
                        <a href="{{ route('baak.jadwal.create') }}" class="btn btn-primary">
                            <i class="nav-icon fas fa-folder-plus"></i>&nbsp; Tambah Jadwal
                        </a>
                    </div>                    
                    <div class="card-body">
                        @include('partials.alert')
                        <div class="table-responsive">
                            <table class="table table-striped" id="table-2">
                                <thead>
                                    <tr>
                                        <th>Kategori PA</th>
                                        <th>Program Studi</th>
                                        <th>Tahun Ajaran</th>
                                        <th>Kelompok</th>
                                        <th>Tanggal</th>
                                        <!-- <th>Dosen Penguji</th> -->
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                     @foreach($jadwal as $item)
                                        <tr>
                                            <!-- <td>{{ $item->id }}</td> -->
                                             <td>{{ $item->kategoriPA->kategori_pa}}</td>
                                             <td>{{ $item->prodi->nama_prodi}}</td>
                                             <td>{{ $item->tahunMasuk->Tahun_Masuk}}</td>
                                            <td>{{ $item->kelompok->nomor_kelompok ?? '-' }}</td>
                                            <td>{{ \Carbon\Carbon::parse($item->waktu)->format('d M Y H:i') }}</td>    
                                            <!-- <td>{{ $item->penguji1_nama }}<br>{{ $item->penguji2_nama }}</td> -->
                                            <td>
                                            <div class="d-flex align-items-center">
                                                    <a href="{{ route('baak.jadwal.show', Crypt::encrypt($item->id)) }}" class="btn btn-info btn-sm me-2">
                                                        <i class="nav-icon fas fa-info-circle"></i>&nbsp;Detail
                                                    </a>
                                                    <a href="{{ route('baak.jadwal.edit', Crypt::encrypt($item->id)) }}" class="btn btn-success btn-sm me-2">
                                                        <i class="nav-icon fas fa-edit"></i>&nbsp;Edit
                                                    </a>
                                                    <form method="POST" action="{{ route('baak.jadwal.destroy', Crypt::encrypt($item->id)) }}">
                                                        @csrf
                                                        @method('delete')
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
