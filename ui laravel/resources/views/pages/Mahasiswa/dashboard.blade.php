@extends('layouts.main')
@section('title', 'Dashboard')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Dashboard Mahasiswa</h1>
    </div>

    <div class="section-body">

        {{-- Summary Cards --}}
        <div class="row">
            {{-- Mahasiswa --}}
            <div class="col-lg-4 col-md-6 col-12">
                <div class="card card-statistic-1 shadow-sm">
                    <div class="card-icon bg-primary text-white">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Jumlah Mahasiswa</h4>
                        </div>
                        <div class="card-body">
                            {{ $mahasiswa_kelompok->count() }} Orang
                        </div>
                    </div>
                </div>
            </div>

            {{-- Pembimbing --}}
            <div class="col-lg-4 col-md-6 col-12">
                <div class="card card-statistic-1 shadow-sm">
                    <div class="card-icon bg-success text-white">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Pembimbing</h4>
                        </div>
                        <div class="card-body">
                            {{ $pembimbing->count() }} Orang
                        </div>
                    </div>
                </div>
            </div>

            {{-- Penguji --}}
            <div class="col-lg-4 col-md-6 col-12">
                <div class="card card-statistic-1 shadow-sm">
                    <div class="card-icon bg-danger text-white">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Penguji</h4>
                        </div>
                        <div class="card-body">
                            {{ $penguji->count() }} Orang
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- Detail Mahasiswa Kelompok, Pembimbing, dan Penguji --}}
    <div class="row mt-5">
    {{-- Anggota Kelompok --}}
    <div class="col-lg-5">
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h4 class="mb-0">Anggota Kelompok</h4>
            </div>
            <div class="card-body p-3">
                @forelse($mahasiswa_kelompok as $item)
                    <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                        <div>
                            <strong>{{ $item->nama }}</strong> <br>
                            <small class="text-muted">NIM: {{ $item->nim }} | Angkatan: {{ $item->angkatan }}</small>
                        </div>
                        <span class="badge badge-info">Mahasiswa</span>
                    </div>
                @empty
                    <p class="text-muted">Belum ada anggota kelompok.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Pembimbing dan Penguji --}}
    <div class="col-lg-7">
        {{-- Dosen Pembimbing --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light">
                <h4 class="mb-0">Dosen Pembimbing</h4>
            </div>
            <div class="card-body p-3">
                @forelse($pembimbing as $item)
                    <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                        <div>
                            <strong>{{ $item->nama }}</strong>
                        </div>
                        <span class="badge badge-success">Pembimbing</span>
                    </div>
                @empty
                    <p class="text-muted">Belum ada pembimbing.</p>
                @endforelse
            </div>
        </div>

        {{-- Dosen Penguji --}}
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h4 class="mb-0">Dosen Penguji</h4>
            </div>
            <div class="card-body p-3">
                @forelse($penguji as $item)
                    <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                        <div>
                            <strong>{{ $item->nama }}</strong>
                        </div>
                        <span class="badge badge-danger">Penguji</span>
                    </div>
                @empty
                    <p class="text-muted">Belum ada penguji.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

        {{-- Jadwal Kelompok --}}
{{-- <div class="row mt-4">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h4 class="mb-0">Jadwal Kelompok</h4>
            </div>
            <div class="card-body">
                @forelse($jadwal as $item)
                    <div class="card mb-3 border-left-primary shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h5 class="mb-0">
                                    Kelompok {{ $item->kelompok->nomor_kelompok ?? 'N/A' }}
                                </h5>
                                <span class="badge badge-primary">
                                    {{ $item->ruangan->ruangan ?? 'N/A' }}
                                </span>
                            </div>
                            <p class="mb-1">
                                <i class="far fa-clock text-warning"></i>
                                {{ \Carbon\Carbon::parse($item->waktu_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($item->waktu_selesai)->format('H:i') }}
                            </p>
                        </div>
                    </div>
                @empty
                    <p class="text-muted text-center">Tidak ada jadwal ditemukan.</p>
                @endforelse
            </div>
        </div>
    </div>
</div> --}}


    </div>
</section>
@endsection
