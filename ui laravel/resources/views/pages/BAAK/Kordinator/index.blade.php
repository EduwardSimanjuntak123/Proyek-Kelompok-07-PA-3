@extends('layouts.main')
@section('title', 'List Dosen Role')

@section('content')
<section class="section custom-section">
    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">

                    <div class="card-header d-flex justify-content-between flex-column flex-md-row align-items-start align-items-md-center">
                        <div>
                            <h4>List Role</h4>

                            <ul class="nav nav-underline mt-3">
                                <li class="nav-item">
                                    <a class="nav-link {{ empty($filterRole) ? 'active' : '' }}"
                                       href="{{ route('manajemen-role.index') }}">
                                        Semua
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link {{ $filterRole === 'koordinator' ? 'active' : '' }}"
                                       href="{{ route('manajemen-role.index', ['filter_role' => 'koordinator']) }}">
                                        Koordinator
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link {{ $filterRole === 'penguji' ? 'active' : '' }}"
                                       href="{{ route('manajemen-role.index', ['filter_role' => 'penguji']) }}">
                                        Penguji
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link {{ $filterRole === 'pembimbing' ? 'active' : '' }}"
                                       href="{{ route('manajemen-role.index', ['filter_role' => 'pembimbing']) }}">
                                        Pembimbing
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <div class="mt-3 mt-md-0">
                            <a href="{{ route('manajemen-role.create') }}" class="btn btn-primary">
                                <i class="nav-icon fas fa-folder-plus"></i>&nbsp; Tambah Dosen Role
                            </a>
                        </div>
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
                                        <th>Role</th>
                                        <th>Tahun Ajaran</th>
                                        <th>Tahun Masuk</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach($dosenroles as $item)
                                        <tr>
                                            <td>{{ $item->id }}</td>
                                            <td>{{ $item->nama ?? 'N/A' }}</td>
                                            <td>{{ $item->prodi->nama_prodi ?? 'N/A' }}</td>
                                            <td>{{ $item->kategoripa->kategori_pa ?? 'N/A' }}</td>
                                            <td>{{ $item->role->role_name ?? 'N/A' }}</td>
                                            <td>{{ $item->tahunAjaran->tahun_mulai }}/{{ $item->tahunAjaran->tahun_selesai }}</td>
                                            <td>{{ $item->tahunMasuk->Tahun_Masuk ?? 'N/A' }}</td>
                                            <td>{{ $item->status }}</td>

                                            <td>
                                                <div class="d-flex">
                                                    <a href="{{ route('manajemen-role.edit', Crypt::encrypt($item->id)) }}"
                                                       class="btn btn-success btn-sm">
                                                        <i class="nav-icon fas fa-edit"></i>&nbsp; Edit
                                                    </a>

                                                    <form method="POST"
                                                          action="{{ route('manajemen-role.destroy', $item->id) }}">
                                                        @csrf
                                                        @method('delete')

                                                        <button class="btn btn-danger btn-sm show_confirm"
                                                                style="margin-left: 8px">
                                                            <i class="nav-icon fas fa-trash-alt"></i>&nbsp; Hapus
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


@push('style')
<style>
    .nav-underline .nav-link {
        color: #000;
        transition: 0.2s;
        border-bottom: 2px solid transparent;
    }

    .nav-underline .nav-link.active {
        color: #0d6efd;
        font-weight: 500;
        border-bottom: 2px solid #0d6efd;
    }

    .nav-underline .nav-link:hover {
        color: #0d6efd;
    }
</style>
@endpush


@push('script')
<script type="text/javascript">
    $('.show_confirm').click(function(event) {
        var form = $(this).closest("form");
        event.preventDefault();

        swal({
            title: 'Yakin ingin menghapus data ini?',
            text: "Data akan terhapus secara permanen!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                form.submit();
            }
        });
    });
</script>
@endpush