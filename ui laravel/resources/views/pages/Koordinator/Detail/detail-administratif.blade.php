@extends('layouts.main')
@section('title', 'Detail Administratif')

@section('content')
    <section class="section">

        {{-- Header --}}
        <div class="section-header">
            <div>
                <h1>Detail Status Administratif</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item">
                        <a href="{{ route('dashboard.koordinator') }}">
                            Dashboard
                        </a>
                    </div>
                    <div class="breadcrumb-item">
                        Detail Administratif
                    </div>
                </div>
            </div>
        </div>

        <div class="section-body">

            {{-- KPI --}}
            <div class="row mb-4">

                <div class="col-lg-4 col-md-6 col-sm-6">
                    <div class="card card-statistic-1 shadow-sm">
                        <div class="card-icon bg-primary">
                            <i class="fas fa-users"></i>
                        </div>

                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Total Kelompok</h4>
                            </div>

                            <div class="card-body">
                                {{ $jumlah_kelompok }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 col-sm-6">
                    <div class="card card-statistic-1 shadow-sm">
                        <div class="card-icon bg-success">
                            <i class="fas fa-check-circle"></i>
                        </div>

                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Selesai</h4>
                            </div>
                            <div class="card-body">
                                {{ $selesai }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 col-sm-6">
                    <div class="card card-statistic-1 shadow-sm">
                        <div class="card-icon bg-warning">
                            <i class="fas fa-clock"></i>
                        </div>

                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Berlangsung</h4>
                            </div>
                            <div class="card-body">
                                {{ $berlangsung }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            {{-- FILTER --}}
            {{-- FILTER --}}
            <div class="card shadow-sm border-0 mb-4">

                <div class="card-body py-3">

                    <form method="GET" action="{{ url()->current() }}">

                        <div class="row align-items-end">

                            {{-- Filter Status --}}
                            <div class="col-lg-4 col-md-6 mb-3">

                                <label class="font-weight-bold text-dark mb-2">
                                    Status Kelompok
                                </label>

                                <select name="status" class="form-control shadow-sm">

                                    <option value="">
                                        Semua Status
                                    </option>

                                    <option value="Selesai" {{ request('status') == 'Selesai' ? 'selected' : '' }}>
                                        Selesai
                                    </option>

                                    <option value="Berlangsung" {{ request('status') == 'Berlangsung' ? 'selected' : '' }}>
                                        Berlangsung
                                    </option>

                                </select>

                            </div>

                            {{-- Sorting --}}
                            <div class="col-lg-4 col-md-6 mb-3">

                                <label class="font-weight-bold text-dark mb-2">
                                    Urutkan Data
                                </label>

                                <select name="sort" class="form-control shadow-sm">

                                    <option value="">
                                        Default
                                    </option>

                                    <option value="tinggi" {{ request('sort') == 'tinggi' ? 'selected' : '' }}>
                                        Progress Tertinggi
                                    </option>

                                    <option value="rendah" {{ request('sort') == 'rendah' ? 'selected' : '' }}>
                                        Progress Terendah
                                    </option>

                                    <option value="terbaru" {{ request('sort') == 'terbaru' ? 'selected' : '' }}>
                                        Terbaru
                                    </option>

                                </select>

                            </div>

                            {{-- Button --}}
                            <div class="col-lg-4 col-md-12 mb-3">

                                <div class="d-flex flex-wrap align-items-center" style="gap:10px;">

                                    {{-- Cari --}}
                                    <button type="submit" class="btn btn-primary shadow-sm px-4">

                                        <i class="fas fa-search mr-1"></i>
                                        Cari

                                    </button>

                                    {{-- Reset --}}
                                    <a href="{{ url()->current() }}" class="btn btn-light border shadow-sm px-4">

                                        <i class="fas fa-redo-alt mr-1"></i>
                                        Reset

                                    </a>

                                </div>

                            </div>

                        </div>

                    </form>

                </div>

            </div>


            {{-- TABLE --}}
            <div class="card shadow-sm">

                <div class="card-header">
                    <h4>Monitoring Administrasi Kelompok</h4>
                </div>

                <div class="card-body p-0">

                    <div class="table-responsive">

                        <table class="table table-hover">

                            <thead class="thead-light">

                                <tr>
                                    <th>Kelompok</th>
                                    <th>Jumlah Anggota</th>
                                    <th>Bimbingan</th>
                                    <th>Submit Artefak</th>
                                    <th>Maju Seminar</th>
                                    <th>Progress</th>
                                    <th>Status</th>
                                </tr>

                            </thead>

                            <tbody>
                                @forelse ($daftar_kelompok as $kelompok)
                                    <tr>

                                        <td>
                                            <div class="font-weight-bold text-primary">
                                                {{ $kelompok->nomor_kelompok }}
                                            </div>

                                            <small class="text-muted">
                                                PA-{{ $KPA_id }} Tahun 2025/2026
                                            </small>
                                        </td>

                                        <td>
                                            {{ $kelompok->jumlah_anggota ?? 0 }} Mahasiswa
                                        </td>

                                        <td>

                                            @php
                                                $jumlahSesi = $kelompok->jumlah_bimbingan_selesai ?? 0;
                                                $persentase = min(($jumlahSesi / 8) * 100, 100);
                                            @endphp

                                            <div class="progress" data-height="10">

                                                <div class="progress-bar bg-primary" style="width: {{ $persentase }}%">
                                                </div>

                                            </div>



                                            <small>
                                                {{ $kelompok->jumlah_bimbingan_selesai ?? 0 }}/8 sesi
                                            </small>

                                        </td>

                                        <td>

                                            @if ($kelompok->jumlah_artefak_submit > 0)
                                                <span class="badge badge-success">
                                                    Lengkap
                                                </span>
                                            @else
                                                <span class="badge badge-danger">
                                                    Belum
                                                </span>
                                            @endif

                                        </td>

                                        <td>

                                            @if ($kelompok->sudah_memiliki_jadwal)
                                                <span class="badge badge-info">
                                                    Terjadwal
                                                </span>
                                            @else
                                                <span class="badge badge-warning">
                                                    Belum Terjadwal
                                                </span>
                                            @endif

                                        </td>

                                        <td>

                                            @php
                                                $progressCount = 0;

                                                // 1. Bimbingan minimal 8x
                                                if (($kelompok->jumlah_bimbingan_selesai ?? 0) >= 8) {
                                                    $progressCount++;
                                                }

                                                // 2. Artefak sudah submit
                                                if (($kelompok->jumlah_artefak_submit ?? 0) > 0) {
                                                    $progressCount++;
                                                }

                                                // 3. Seminar sudah terjadwal
                                                if ($kelompok->sudah_memiliki_jadwal) {
                                                    $progressCount++;
                                                }

                                                $persentaseProgress = ($progressCount / 3) * 100;
                                            @endphp

                                            <div class="d-flex align-items-center">

                                                <div class="progress flex-grow-1 mr-2" data-height="8">

                                                    <div class="progress-bar 
            @if ($progressCount == 3) bg-success
            @elseif($progressCount == 2)
                bg-warning
            @else
                bg-danger @endif
        "
                                                        style="width: {{ $persentaseProgress }}%">
                                                    </div>

                                                </div>

                                                <small>
                                                    {{ $progressCount }}/3
                                                </small>

                                            </div>

                                        </td>

                                        <td>

                                            @if ($progressCount >= 3)
                                                <span class="badge badge-success">
                                                    Selesai
                                                </span>
                                            @else
                                                <span class="badge badge-warning">
                                                    Berlangsung
                                                </span>
                                            @endif

                                        </td>

                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">
                                            Belum ada data kelompok
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                    </div>

                </div>


                {{-- Pagination --}}
                <div class="card-footer bg-white border-0 py-3">

                    <div class="d-flex justify-content-between align-items-center flex-wrap">

                        <small class="text-muted mb-2 mb-md-0">
                            Menampilkan
                            {{ $daftar_kelompok->firstItem() ?? 0 }}
                            -
                            {{ $daftar_kelompok->lastItem() ?? 0 }}

                            dari

                            {{ $daftar_kelompok->total() }}
                            kelompok
                        </small>

                        <div>
                            {{ $daftar_kelompok->withQueryString()->links() }}
                        </div>

                    </div>

                </div>

            </div>

    </section>
@endsection
