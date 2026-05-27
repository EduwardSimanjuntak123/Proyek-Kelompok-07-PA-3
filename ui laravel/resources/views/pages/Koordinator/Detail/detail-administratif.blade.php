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

                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="card card-statistic-1 shadow-sm">
                        <div class="card-icon bg-primary">
                            <i class="fas fa-users"></i>
                        </div>

                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Total Kelompok</h4>
                            </div>
                            <div class="card-body">
                                {{ $total_kelompok ?? 15 }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="card card-statistic-1 shadow-sm">
                        <div class="card-icon bg-success">
                            <i class="fas fa-check-circle"></i>
                        </div>

                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Lengkap</h4>
                            </div>
                            <div class="card-body">
                                {{ $lengkap ?? 8 }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="card card-statistic-1 shadow-sm">
                        <div class="card-icon bg-warning">
                            <i class="fas fa-clock"></i>
                        </div>

                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Pending</h4>
                            </div>
                            <div class="card-body">
                                {{ $pending ?? 4 }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="card card-statistic-1 shadow-sm">
                        <div class="card-icon bg-danger">
                            <i class="fas fa-exclamation-circle"></i>
                        </div>

                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Bermasalah</h4>
                            </div>
                            <div class="card-body">
                                {{ $bermasalah ?? 3 }}
                            </div>
                        </div>
                    </div>
                </div>

            </div>


            {{-- FILTER --}}
            <div class="card shadow-sm mb-4">
                <div class="card-body">

                    <div class="row">

                        <div class="col-md-4">
                            <select class="form-control">
                                <option>Semua Status</option>
                                <option>Lengkap</option>
                                <option>Pending</option>
                                <option>Bermasalah</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <select class="form-control">
                                <option>Urutkan Kelompok</option>
                                <option>Progress Tertinggi</option>
                                <option>Progress Terendah</option>
                                <option>Terbaru</option>
                            </select>
                        </div>

                        <div class="col-md-4 text-right">

                            <button class="btn btn-primary">
                                <i class="fas fa-download"></i>
                                Export Excel
                            </button>

                        </div>

                    </div>

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


                {{-- pagination --}}
                <div class="card-footer text-right">

                    <nav>

                        <ul class="pagination justify-content-end mb-0">

                            <li class="page-item disabled">
                                <a class="page-link">
                                    <i class="fas fa-angle-left"></i>
                                </a>
                            </li>

                            <li class="page-item active">
                                <a class="page-link">
                                    1
                                </a>
                            </li>

                            <li class="page-item">
                                <a class="page-link">
                                    2
                                </a>
                            </li>

                            <li class="page-item">
                                <a class="page-link">
                                    3
                                </a>
                            </li>

                            <li class="page-item">
                                <a class="page-link">
                                    <i class="fas fa-angle-right"></i>
                                </a>
                            </li>

                        </ul>

                    </nav>

                </div>

            </div>

        </div>

    </section>
@endsection
