@extends('layouts.main')
@section('title', 'Pengumuman Mahasiswa')

@section('content')
<section class="section custom-section">
    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Daftar Pengumuman</h4>
                    </div>
                    <div class="card-body">
                        @include('partials.alert') <!-- Menampilkan alert -->
                        <div class="table-responsive">
                            <table class="table table-striped" id="table-2">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Judul</th>
                                        <th>Kategori PA</th>
                                        <th>Prodi</th>
                                        <th>Pengirim</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($pengumuman as $index => $pengumuman)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td><a href="{{route('pengumuman.pembimbing.show',$pengumuman->id)}}">{{ $pengumuman->judul }}</a></td>
                                        <td>{{ $pengumuman->kategoriPA->kategori_pa }}</td>
                                        <td>{{ $pengumuman->prodi->nama_prodi }}</td>
                                        <td>{{ $pengumuman->nama}}</td>
                                        <td>
                                            <span class="badge {{ $pengumuman->status == 'aktif' ? 'bg-success' : 'bg-secondary' }}">
                                                {{ ucfirst($pengumuman->status) }}
                                            </span>
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
