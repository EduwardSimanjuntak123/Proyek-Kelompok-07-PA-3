@extends('layouts.main')
@section('title', 'Detail')

@section('content')
    <section class="section">
        <div class="section-body">

            <div class="card">
                <div class="card-header">
                    <h4>Detail Kelompok {{ $kelompok->nomor_kelompok }}</h4>
                </div>

                <div class="card-body">
                    {{-- {{ dd($kelompok->pembimbing->toArray()) }} --}}
                    <h5 class="mb-3">Informasi Dosen</h5>
                    <table class="table table-bordered">
                        <tr>
                            <th>Pembimbing</th>
                            <td>
                                @forelse ($kelompok->pembimbing as $p)
                                    {{ $p->dosen['nama'] ?? '-' }} <br>
                                @empty
                                    -
                                @endforelse
                        </tr>
                        <tr>
                            <th>Penguji</th>
                            <td>
                                @forelse ($kelompok->penguji as $u)
                                    {{ $u->dosen['nama'] ?? '-' }} <br>
                                @empty
                                    -
                                @endforelse
                            </td>
                        </tr>
                    </table>

                    <h5 class="mt-4 mb-3">Anggota Kelompok</h5>
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>NIM</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($kelompok->kelompokMahasiswa as $mhs)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $mhs->mahasiswa['nama'] ?? '-' }}</td>
                                    <td>{{ $mhs->mahasiswa['nim'] ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">
                                        Tidak ada anggota kelompok.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <a href="{{ route('Histori.index') }}" class="btn btn-secondary mt-3">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>

                </div>
            </div>

        </div>
    </section>
@endsection
