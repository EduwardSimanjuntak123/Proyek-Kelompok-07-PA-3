@extends('layouts.main')
@section('title', 'List Kelompok')

@section('content')
    <section class="section custom-section">
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <h4>List Tugas</h4>
                            <a href="{{ route('tugas.create') }}" class="btn btn-primary">
                                <i class="nav-icon fas fa-folder-plus"></i>&nbsp; Tambah Tugas
                            </a>
                        </div>
                        <div class="card-body">
                            @include('partials.alert')
                            <div class="table-responsive">
                                <table class="table table-striped" id="table-2">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Judul Tugas</th>
                                            <th>kategori</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($tugas as $item)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $item->Judul_Tugas }}</td>
                                                <td>{{ $item->kategori_tugas }}</td>
                                                <td>{{ $item->status }}</td>
                                                <td>
                                                    <div class="d-flex" style="gap: 8px;">
                                                        <a href="{{ route('tugas.show', $item->id) }}"
                                                            class="btn btn-primary btn-sm" data-toggle="tooltip"
                                                            data-placement="top" title="Detail Tugas">
                                                            <i class="fas fa-eye"></i>
                                                        </a>

                                                        <a href="{{ route('artefak.index.koordinator', $item->id) }}"
                                                            class="btn btn-primary btn-sm" data-toggle="tooltip"
                                                            data-placement="top" title="Lihat Submitan Tugas">
                                                            <i class="fas fa-file-upload"></i>
                                                        </a>

                                                        <a href="{{ route('tugas.edit', ['id' => Crypt::encrypt($item->id)]) }}"
                                                            class="btn btn-success btn-sm" data-toggle="tooltip"
                                                            data-placement="top" title="Edit">
                                                            <i class="fas fa-edit"></i>
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

        $(function() {
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
@endpush
