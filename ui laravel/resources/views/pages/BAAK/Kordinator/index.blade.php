@extends('layouts.main')
@section('title', 'List Dosen Role')

@section('content')
    <section class="section custom-section">
        <div class="section-body">
            <div class="row">
                <div class="col-12">

                    <div class="card shadow-sm border-0">

                        <!-- HEADER -->
                        <div
                            class="card-header d-flex justify-content-between flex-column flex-lg-row align-items-start align-items-lg-center">

                            <div class="w-100">

                                <div
                                    class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">

                                    <div>
                                        <h4 class="mb-1 font-weight-bold">
                                            List Role
                                        </h4>

                                        <p class="text-muted mb-0">
                                            Manajemen data role dosen pembimbing, penguji, dan koordinator
                                        </p>
                                    </div>

                                    <div class="mt-3 mt-md-0">
                                        <a href="{{ route('manajemen-role.create') }}"
                                            class="btn btn-primary btn-lg shadow-sm">
                                            <i class="fas fa-folder-plus mr-1"></i>
                                            Tambah Dosen Role
                                        </a>
                                    </div>
                                </div>

                                <!-- FILTER -->
                                <div class="role-filter-wrapper">
                                    <ul class="nav role-filter-nav">

                                        <li class="nav-item">
                                            <a class="nav-link {{ empty($filterRole) ? 'active' : '' }}"
                                                href="{{ route('manajemen-role.index') }}">

                                                Semua

                                                <span class="count-badge">
                                                    {{ $countAll ?? 0 }}
                                                </span>
                                            </a>
                                        </li>

                                        <li class="nav-item">
                                            <a class="nav-link {{ $filterRole === 'koordinator' ? 'active' : '' }}"
                                                href="{{ route('manajemen-role.index', ['filter_role' => 'koordinator']) }}">

                                                <i class="fas fa-user-tie"></i>
                                                Koordinator

                                                <span class="count-badge">
                                                    {{ $countKoordinator ?? 0 }}
                                                </span>
                                            </a>
                                        </li>

                                        <li class="nav-item">
                                            <a class="nav-link {{ $filterRole === 'pembimbing' ? 'active' : '' }}"
                                                href="{{ route('manajemen-role.index', ['filter_role' => 'pembimbing']) }}">

                                                <i class="fas fa-user-graduate"></i>
                                                Pembimbing

                                                <span class="count-badge">
                                                    {{ $countPembimbing ?? 0 }}
                                                </span>
                                            </a>
                                        </li>

                                        <li class="nav-item">
                                            <a class="nav-link {{ $filterRole === 'penguji' ? 'active' : '' }}"
                                                href="{{ route('manajemen-role.index', ['filter_role' => 'penguji']) }}">

                                                <i class="fas fa-user-check"></i>
                                                Penguji

                                                <span class="count-badge">
                                                    {{ $countPenguji ?? 0 }}
                                                </span>
                                            </a>
                                        </li>

                                    </ul>
                                </div>

                            </div>

                        </div>

                        <!-- BODY -->
                        <div class="card-body">

                            @include('partials.alert')

                            <div class="table-responsive">

                                <table class="table table-hover align-middle custom-table" id="table-2">

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
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>

                                    <tbody>

                                        @forelse ($dosenroles as $item)
                                            <tr>

                                                <td>
                                                    {{ $loop->iteration }}
                                                </td>

                                                <td class="font-weight-semibold">
                                                    {{ $item->nama ?? 'N/A' }}
                                                </td>

                                                <td>
                                                    {{ $item->prodi->nama_prodi ?? 'N/A' }}
                                                </td>

                                                <td>
                                                    {{ $item->kategoripa->kategori_pa ?? 'N/A' }}
                                                </td>

                                                <td>
                                                    <span class="badge badge-info px-3 py-2">
                                                        {{ $item->role->role_name ?? 'N/A' }}
                                                    </span>
                                                </td>

                                                <td>
                                                    {{ $item->tahunAjaran->tahun_mulai }}/{{ $item->tahunAjaran->tahun_selesai }}
                                                </td>

                                                <td>
                                                    {{ $item->tahunMasuk->Tahun_Masuk ?? 'N/A' }}
                                                </td>

                                                <td>
                                                    @if ($item->status == 'Aktif')
                                                        <span class="badge badge-success px-3 py-2">
                                                            Aktif
                                                        </span>
                                                    @else
                                                        <span class="badge badge-danger px-3 py-2">
                                                            Tidak Aktif
                                                        </span>
                                                    @endif
                                                </td>

                                                <td>

                                                    <div class="d-flex justify-content-center">

                                                        <a href="{{ route('manajemen-role.edit', Crypt::encrypt($item->id)) }}"
                                                            class="btn btn-success btn-sm mr-2">

                                                            <i class="fas fa-edit"></i>
                                                        </a>

                                                        <form method="POST"
                                                            action="{{ route('manajemen-role.destroy', $item->id) }}">

                                                            @csrf
                                                            @method('delete')

                                                            <button class="btn btn-danger btn-sm show_confirm">

                                                                <i class="fas fa-trash-alt"></i>
                                                            </button>

                                                        </form>

                                                    </div>

                                                </td>

                                            </tr>
                                        @empty

                                            <tr>
                                                <td colspan="9" class="text-center text-muted py-5">

                                                    <i class="fas fa-folder-open fa-2x mb-3"></i>

                                                    <br>

                                                    Data role dosen belum tersedia
                                                </td>
                                            </tr>
                                        @endforelse

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
        /* CARD */
        .card {
            border-radius: 18px;
            overflow: hidden;
        }

        .card-header {
            background: #fff;
            padding: 30px;
            border-bottom: 1px solid #f1f1f1;
        }

        /* FILTER */
        .role-filter-wrapper {
            width: 100%;
        }

        .role-filter-nav {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            padding: 0;
            margin: 0;
            list-style: none;
        }

        .role-filter-nav .nav-link {
            padding: 12px 20px;
            border-radius: 14px;
            background: #f4f7fb;
            color: #6c757d;
            font-weight: 600;
            transition: all 0.25s ease;
            border: 1px solid transparent;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .role-filter-nav .nav-link:hover {
            background: #e8f4fa;
            color: #4C9BC8;
            transform: translateY(-2px);
        }

        .role-filter-nav .nav-link.active {
            background: #4C9BC8;
            color: white;
            box-shadow: 0 5px 15px rgba(95, 168, 198, 0.3);
        }

        .count-badge {
            background: rgba(255, 255, 255, 0.2);
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 12px;
        }

        .role-filter-nav .nav-link.active .count-badge {
            background: rgba(255, 255, 255, 0.25);
        }

        /* TABLE */
        .custom-table {
            border-collapse: separate;
            border-spacing: 0 10px;
        }

        .custom-table thead th {
            border: none;
            background: #f8fafc;
            color: #6c757d;
            font-size: 13px;
            text-transform: uppercase;
            padding: 18px;
            font-weight: 700;
        }

        .custom-table tbody tr {
            background: #fff;
            transition: 0.2s;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
        }

        .custom-table tbody tr:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.06);
        }

        .custom-table tbody td {
            padding: 18px;
            vertical-align: middle;
            border-top: none;
        }

        /* BADGE */
        .badge {
            font-size: 12px;
            border-radius: 30px;
            font-weight: 600;
        }

        /* BUTTON */
        .btn-sm {
            border-radius: 10px;
            width: 38px;
            height: 38px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* RESPONSIVE */
        @media (max-width: 768px) {

            .card-header {
                padding: 20px;
            }

            .role-filter-nav {
                gap: 10px;
            }

            .role-filter-nav .nav-link {
                width: 100%;
                justify-content: center;
            }

            .btn-lg {
                width: 100%;
            }

            .custom-table thead {
                display: none;
            }

            .custom-table tbody tr {
                display: block;
                margin-bottom: 15px;
                border-radius: 16px;
                overflow: hidden;
            }

            .custom-table tbody td {
                display: flex;
                justify-content: space-between;
                padding: 14px 16px;
                border-bottom: 1px solid #f1f1f1;
            }

            .custom-table tbody td:last-child {
                border-bottom: none;
            }
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
