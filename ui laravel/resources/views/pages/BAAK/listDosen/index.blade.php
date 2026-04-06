@extends('layouts.main')
@section('title', 'List Dosen')

@section('content')
    <section class="section custom-section">
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">

                        <div class="card-header d-flex justify-content-between">
                            <h4>List Dosen</h4>
                            <a href="#" class="btn btn-primary">
                                <i class="nav-icon fas fa-user-plus"></i>&nbsp; Tambah Dosen
                            </a>
                        </div>
                        {{-- <form method="GET" action="">
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <select name="prodi_id" class="form-control" onchange="this.form.submit()">
                                        <option value="">-- Semua Prodi --</option>
                                        @foreach ($prodi as $p)
                                            <option value="{{ $p->id }}"
                                                {{ request('prodi_id') == $p->id ? 'selected' : '' }}>
                                                {{ $p->nama_prodi }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </form> --}}

                        <div class="card-body">
                            @include('partials.alert')
                            {{-- @dd($dosen) --}}
                            <div class="table-responsive">
                                <table class="table table-striped" id="table-2">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>NIP</th>
                                            <th>Nama</th>
                                            <th>Email</th>
                                            <th>Prodi</th>
                                            <th>Jabatan Akademik</th>
                                            <th>Jenjang Pendidikan</th>
                                            <th>NIDN</th>
                                            {{-- <th>Aksi</th> --}}
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($dosen as $index => $item)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $item['nip'] ?? '-' }}</td>
                                                <td>{{ $item['nama'] ?? '-' }}</td>
                                                <td>{{ $item['email'] ?? '-' }}</td>
                                                <td>{{ $item['prodi'] ?? '-' }}</td>
                                                <td>{{ $item['jabatan_akademik_desc'] ?? '-' }}</td>
                                                <td>{{ $item['jenjang_pendidikan'] ?? '-' }}</td>
                                                <td>{{ $item['nidn'] ?? '-' }}</td>
                                                {{-- <td>
                                                <div class="d-flex">
                                                    <a href="{{ route('dosen.edit', $item['user_id']) }}" 
                                                       class="btn btn-success btn-sm">
                                                        <i class="nav-icon fas fa-edit"></i> Edit
                                                    </a>

                                                    <form method="POST" 
                                                          action="{{ route('dosen.destroy', $item['user_id']) }}">
                                                        @csrf
                                                        @method('delete')
                                                        <button type="submit"
                                                                class="btn btn-danger btn-sm show_confirm"
                                                                style="margin-left:8px">
                                                            <i class="nav-icon fas fa-trash-alt"></i> Hapus
                                                        </button>
                                                    </form>
                                                </div>
                                            </td> --}}
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="9" class="text-center">
                                                    Data tidak ditemukan
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

    @push('style')
        <style>
            /* Kecilkan font table */
            #table-2 {
                font-size: 13px;
            }

            /* Kecilkan padding cell */
            #table-2 th,
            #table-2 td {
                padding: 6px 10px !important;
                /* vertical-align: middle; */
                white-space: nowrap;
                /* Supaya 1 baris */
            }

            /* Header sedikit lebih bold */
            #table-2 th {
                font-weight: 600;
            }

            /* Supaya table tidak pecah */
            .table-responsive {
                overflow-x: auto;
            }
        </style>
    @endpush
@endsection
