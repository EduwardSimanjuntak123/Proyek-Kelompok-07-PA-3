@extends('layouts.main')
@section('title', 'Jadwal Pembimbing')

@section('content')
<section class="section custom-section">
    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h4>Jadwal Seminar Pembimbing</h4>
                    </div>                    
                    <div class="card-body">
                        @include('partials.alert')
                        <div class="table-responsive">
                            <table class="table table-striped" id="table-2">
                                <thead>
                                    <tr>
                                        <th>Program Studi</th>
                                        <th>Tahun Masuk</th>
                                        <th>Kategori PA</th>
                                        <th>Kelompok</th>
                                        <th>Penguji</th>
                                        <th>Tanggal</th>
                                        <th>Ruangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($jadwal as $item)
                                        <tr>
                                            <td>{{ $item->prodi->nama_prodi ?? '-' }}</td>
                                            <td>{{ $item->tahunMasuk->Tahun_Masuk ?? '-' }}</td>
                                            <td>{{ $item->KategoriPA->kategori_pa ?? '-' }}</td>
                                            <td>{{ $item->kelompok->nomor_kelompok ?? '-' }}</td>
                                            <td>{!! $item->penguji_nama ?? '-' !!}</td> 
                                            <td>{{ \Carbon\Carbon::parse($item->waktu)->format('d M Y H:i') }}</td>
                                            <td>{{ $item->ruangan->ruangan }}</td>
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
