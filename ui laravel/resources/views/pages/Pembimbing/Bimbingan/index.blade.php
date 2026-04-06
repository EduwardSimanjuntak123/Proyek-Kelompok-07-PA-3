@extends('layouts.main')
@section('title', 'List Kelompok')

@section('content')
<section class="section custom-section">
    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h4>List Bimbingan</h4>
                    </div>                    
                    <div class="card-body">
                        @include('partials.alert')
                        <div class="table-responsive">
                            <table class="table table-striped" id="table-2">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nomor Kelompok</th>
                                        <th>Kategori PA</th>
                                        <th>Prodi</th>
                                        <th>Keperluan</th>
                                        <th>Tanggal Mulai</th>
                                        <th>Tanggal Selesai</th>
                                        <th>Lokasi</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                     @foreach($bimbingan as $item)
                                        <tr>
                                            <td>{{ $loop->iteration}}</td>
                                            <td>{{ $item->kelompok->nomor_kelompok }}</td>
                                            <td>{{ $item->kelompok->kategoriPA->kategori_pa}}</td>
                                            <td>{{ $item->kelompok->prodi->nama_prodi}}</td>
                                            <td>{{ $item->keperluan }}</td>
                                            <td>{{ $item->rencana_mulai }}</td>    
                                            <td>{{ $item->rencana_selesai}}</td> 
                                            <td>{{ $item->ruangan->ruangan }}</td> 
                                            <td>
                                                <span class="badge 
                                                    @if($item->status == 'disetujui') badge-success
                                                    @elseif($item->status == 'ditolak') badge-danger
                                                    @else badge-warning
                                                    @endif">
                                                    {{ ucfirst($item->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex">
                                                @if($item->status == 'menunggu')
                                                <form action="{{ route('pembimbing.bimbingan.setujui', Crypt::encrypt($item->id)) }}" method="POST" style="display: inline;">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="btn btn-sm btn-success mr-2">
                                                         <i class="fas fa-check"></i> Setujui
                                                    </button>
                                                </form>
                                                <form action="{{ route('pembimbing.bimbingan.tolak', Crypt::encrypt($item->id)) }}" method="POST" style="display: inline;">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="fas fa-times"></i> Tolak
                                                    </button>
                                                </form>
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