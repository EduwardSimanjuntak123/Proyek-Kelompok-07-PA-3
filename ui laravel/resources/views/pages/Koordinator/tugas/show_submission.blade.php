@extends('layouts.main')
@section('title','Tugas')

@section('content')
<section class="section custom-section">
    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h4>List Tugas</h4>
                        <a href="{{ route('koordinator.tugas.index') }}" class="btn btn-primary">Kembali</a>
                    </div> 
                    <div class="card-body">
                        @include('partials.alert')
                        <div class="table-responsive">
                            <table class="table table-striped" id="table-2">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nomor Kelompok</th>
                                        <th>Waktu Submit</th>
                                        <th>File</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                     @foreach($artefak as $item)
                                     @php
                                        $feedback = $item->feedback ?? '-';
                                    @endphp
                                        <tr>
                                            <td>{{ $item->id }}</td>
                                            <td>{{ $item->kelompok->nomor_kelompok }}</td>
                                            <td>{{ $item->waktu_submit }}</td>
                                            <td>
                                                <a href="{{ asset('storage/' . $item->file_path) }}" target="_blank">Lihat File</a>
                                            </td>
                                            <td> {{ $item->status ?? '-' }} <br>
                                                {{-- <strong>Feedback:</strong>
                                                {{ \Illuminate\Support\Str::length($feedback) > 20 
                                                    ? \Illuminate\Support\Str::limit($feedback, 20, '...') 
                                                    : $feedback }} --}}
                                            </td> 
                                            <td>
                                                <div class="d-flex">
                                                <a href="{{ route('feedback.edit', $item->id) }}" class="btn btn-primary btn-sm">
                                                    <i class="nav-icon fas fa-comments"></i> &nbsp; FeedBack
                                                </a>                                                   
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
