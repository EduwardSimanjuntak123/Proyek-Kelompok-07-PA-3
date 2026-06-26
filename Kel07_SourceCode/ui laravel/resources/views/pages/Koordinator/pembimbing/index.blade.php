@extends('layouts.main')
@section('title', 'Manajemen Pembimbing')

@section('content')
    <section class="section custom-section">
        <div class="section-body">
            <div class="row">
                <div class="col-12">

                    <div class="card">

                        <div class="card-header">
                            <h4>Manajemen Pembimbing Kelompok</h4>
                        </div>

                        <div class="card-body">

                            @include('partials.alert')

                            <div class="table-responsive">

                                <table class="table table-striped">

                                    <thead>
                                        <tr>
                                            <th>Nomor Kelompok</th>
                                            <th>Pembimbing 1</th>
                                            <th>Pembimbing 2</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>

                                    <tbody>

                                        @foreach ($kelompok as $item)
                                            @php
                                                $pembimbing1 = $item->pembimbing->get(0);
                                                $pembimbing2 = $item->pembimbing->get(1);
                                            @endphp

                                            <tr>
                                                <td>
                                                    {{ $item->nomor_kelompok ?? '-' }}
                                                </td>

                                                <td>
                                                    {{ $pembimbing1->nama ?? '-' }}
                                                </td>

                                                <td>
                                                    {{ $pembimbing2->nama ?? '-' }}
                                                </td>

                                                <td>
                                                    @if ($item->pembimbing->count() == 0)
                                                        <a href="{{ route('pembimbing.create', Crypt::encrypt($item->id)) }}"
                                                            class="btn btn-success btn-sm" data-toggle="tooltip"
                                                            data-placement="top" title="Tambah Pembimbing">
                                                            <i class="fas fa-plus"></i>
                                                        </a>
                                                    @else
                                                        <div class="d-flex" style="gap: 8px;">
                                                            <a href="{{ route('pembimbing.edit', Crypt::encrypt($item->id)) }}"
                                                                class="btn btn-warning btn-sm" data-toggle="tooltip"
                                                                data-placement="top" title="Edit Pembimbing">
                                                                <i class="fas fa-edit"></i>
                                                            </a>

                                                            <form
                                                                action="{{ route('pembimbing.destroy', Crypt::encrypt($item->id)) }}"
                                                                method="POST" style="display:inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit"
                                                                    class="btn btn-danger btn-sm show_confirm"
                                                                    data-toggle="tooltip" data-placement="top"
                                                                    title="Hapus Pembimbing">
                                                                    <i class="fas fa-trash-alt"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    @endif
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
        $(function() {
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
@endpush
