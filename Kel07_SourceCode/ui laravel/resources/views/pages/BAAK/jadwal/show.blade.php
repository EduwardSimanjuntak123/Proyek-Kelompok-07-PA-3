@extends('layouts.main')
@section('title', 'Detail Jadwal Seminar')

@section('content')
<section class="section custom-section">
    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h4>Detail Jadwal Seminar</h4>
                    </div>
                    <div class="card-body">
                        @include('partials.alert')
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <tr>
                                    <th>Program Studi</th>
                                    <td>{{ $jadwal->prodi->nama_prodi ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Tahun Ajaran</th>
                                    <td>{{ $jadwal->tahunMasuk->Tahun_Masuk ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Kategori PA</th>
                                    <td>{{ $jadwal->kategoriPA->kategori_pa ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Nomor Kelompok</th>
                                    <td>{{ $jadwal->kelompok->nomor_kelompok ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Tanggal & Waktu</th>
                                    <td>{{ \Carbon\Carbon::parse($jadwal->waktu)->translatedFormat('l, d F Y - H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Ruangan</th>
                                    <td>{{ $jadwal->ruangan->ruangan }}</td>
                                </tr>
                                <tr>
                                    <th>Dosen Penguji</th>
                                    <td>
                                        @if(!empty($pengujiNama))
                                            <ul class="pl-3">
                                                @foreach ($pengujiNama as $nama)
                                                    <li>{{ $nama }}</li>
                                                @endforeach
                                            </ul>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Pembimbing</th>
                                    <td>
                                        @if(!empty($pembimbingNames))
                                            <ul class="pl-3">
                                                @foreach ($pembimbingNames as $namaPembimbing)
                                                    <li>{{ $namaPembimbing }}</li>
                                                @endforeach
                                            </ul>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <a href="{{ route('baak.jadwal.index') }}" class="btn btn-secondary mt-3">Kembali</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
