@extends('layouts.main')
@section('title', 'List Bimbingan')

@section('content')
<section class="section custom-section">
    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h4>List Bimbingan</h4>
                        <a href="{{ route('bimbingan.create') }}" class="btn btn-primary">
                            <i class="nav-icon fas fa-folder-plus"></i>&nbsp; Request Bimbingan
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
                                        <th>Keperluan</th>
                                        <th>Tanggal Mulai</th>
                                        <th>Tanggal Selesai</th>
                                        <th>Ruangan</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                     @foreach($bimbingan as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item->kelompok->nomor_kelompok }}</td>
                                            <td>{{ $item->keperluan }}</td>
                                            <td>{{ $item->rencana_mulai }}</td>    
                                            <td>{{ $item->rencana_selesai}}</td> 
                                            <td>{{ $item->ruangan->ruangan }}</td> 
                                            <td>
                                                <span class="badge 
                                                    @if($item->status == 'disetujui') badge-success
                                                    @elseif($item->status == 'ditolak') badge-danger
                                                    @elseif($item->status == 'selesai') badge-info
                                                    @else badge-warning
                                                    @endif">
                                                    {{ ucfirst($item->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex">
                                                    @if($item->status == 'menunggu') 
                                                    <a href="{{ route('bimbingan.edit', Crypt::encrypt($item->id)) }}" 
                                                        class="btn btn-sm btn-primary mr-2">
                                                        <i class="fas fa-edit"></i> Edit
                                                        </a>

                                                        <form method="POST" action="{{ route('bimbingan.destroy', $item->id) }}" style="display: inline;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger show_confirm">
                                                                <i class="fas fa-trash"></i> Hapus
                                                            </button>
                                                        </form>
                                        
                                                    @elseif($item->status == 'selesai') 
                                                    <a href="{{ route('bimbingan.kartu', Crypt::encrypt($item->id)) }}" class="btn btn-success btn-sm mr-2">
                                                        <i class="nav-icon fas fa-eye"></i> &nbsp;Kartu bimbingan
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
