@extends('layouts.main')
@section('title', 'Jadwal Mahasiswa')

@section('content')
<section class="section custom-section">
    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h4>Jadwal Mahasiswa</h4>
                    </div>                    
                    <div class="card-body">
                        @include('partials.alert')

                        @if(session('error'))
                        <div class="alert alert-warning">
                            {{ session('error') }}
                        </div>
                        @endif

                        @if(isset($jadwalUtama))
                        <div class="mb-4">
                            <h5>Jadwal Seminar Kelompok Anda</h5>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <tr>
                                        <th>Nomor Kelompok</th>
                                        <td>{{ $jadwalUtama->kelompok->nomor_kelompok ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Ruangan</th>
                                        <td>{{ $jadwalUtama->ruangan->ruangan }}</td>
                                    </tr>
                                    <tr>
                                        <th>Waktu Sidang</th>
                                        <td>{{ \Carbon\Carbon::parse($jadwalUtama->waktu)->translatedFormat('l, d F Y - H:i') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Penguji</th>
                                        <td>{!! $pengujiNama !!}</td>
                                    </tr>
                                    <tr>
                                        <th>Pembimbing</th>
                                        <td>
                                            @if(!empty($pembimbingNama))
                                                @foreach($pembimbingNama as $nama)
                                                    <div>{{ $nama }}</div>
                                                @endforeach
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        @endif

                        @if(isset($jadwalLain) && $jadwalLain->count() > 0)
                        <h5>Jadwal Seminar Kelompok Lainnya</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Nomor Kelompok</th>
                                        <th>Ruangan</th>
                                        <th>Waktu</th>
                                        <th>Penguji</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($jadwalLain as $jadwal)
                                    <tr>
                                        <td>{{ $jadwal->kelompok->nomor_kelompok ?? '-' }}</td>
                                        <td>{{ $jadwal->ruangan->ruangan }}</td>
                                        <td>{{ \Carbon\Carbon::parse($jadwal->waktu)->translatedFormat('l, d F Y - H:i') }}</td>
                                        <td>{!! $jadwal->penguji_nama !!}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @endif

                        @if(!isset($jadwalUtama))
                        <div class="alert alert-info">
                            Jadwal belum tersedia untuk kelompok Anda.
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
