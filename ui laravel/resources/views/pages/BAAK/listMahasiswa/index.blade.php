@extends('layouts.main')
@section('title', 'List Mahasiswa')

@section('content')
    <section class="section custom-section">
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">

                        <div class="card-header d-flex justify-content-between">
                            <h4>List Mahasiswa</h4>
                        </div>

                        <div class="card-body">
                            @include('partials.alert')

                            <div class="table-responsive">
                                <table class="table table-striped" id="table-2">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Username</th>
                                            <th>NIM</th>
                                            <th>Nama</th>
                                            <th>Email</th>
                                            <th>Prodi</th>
                                            <th>Fakultas</th>
                                            <th>Angkatan</th>
                                            <th>Status</th>
                                            <th>Asrama</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($mahasiswa as $index => $mhs)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $mhs['user_name'] ?? '-' }}</td>
                                                <td>{{ $mhs['nim'] ?? '-' }}</td>
                                                <td>{{ $mhs['nama'] ?? '-' }}</td>
                                                <td>{{ $mhs['email'] ?? '-' }}</td>
                                                <td>{{ $mhs['prodi_name'] ?? '-' }}</td>
                                                <td>{{ $mhs['fakultas'] ?? '-' }}</td>
                                                <td>{{ $mhs['angkatan'] ?? '-' }}</td>
                                                <td>
                                                    @if (($mhs['status'] ?? '') === 'Aktif')
                                                        <span class="badge badge-success">Aktif</span>
                                                    @else
                                                        <span class="badge badge-secondary">
                                                            {{ $mhs['status'] ?? '-' }}
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>{{ $mhs['asrama'] ?: '-' }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="10" class="text-center">
                                                    Data mahasiswa tidak ditemukan
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
            #table-2 {
                font-size: 13px;
            }

            #table-2 th,
            #table-2 td {
                padding: 6px 10px !important;
                white-space: nowrap;
            }

            #table-2 th {
                font-weight: 600;
            }

            .table-responsive {
                overflow-x: auto;
            }
        </style>
    @endpush
@endsection
