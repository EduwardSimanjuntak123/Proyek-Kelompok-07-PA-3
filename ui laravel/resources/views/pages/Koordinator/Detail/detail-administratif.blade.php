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
                                    <th>Artefak</th>
                                    <th>Seminar</th>
                                    <th>Progress</th>
                                    <th>Status</th>
                                </tr>

                            </thead>

                            <tbody>

                                @for ($i = 1; $i <= 8; $i++)
                                    <tr>

                                        <td>
                                            <div class="font-weight-bold text-primary">
                                                Kelompok {{ $i }}
                                            </div>

                                            <small class="text-muted">
                                                PA-III Tahun 2025/2026
                                            </small>
                                        </td>

                                        <td>
                                            {{ rand(3, 5) }} Mahasiswa
                                        </td>

                                        <td>

                                            <div class="progress" data-height="10">

                                                <div class="progress-bar bg-primary" style="width:{{ rand(60, 100) }}%">
                                                </div>

                                            </div>

                                            <small>
                                                {{ rand(4, 8) }}/8 sesi
                                            </small>

                                        </td>

                                        <td>

                                            @php
                                                $artefak = ['Lengkap', 'Pending', 'Belum'];
                                                $statusArtefak = $artefak[array_rand($artefak)];
                                            @endphp

                                            @if ($statusArtefak == 'Lengkap')
                                                <span class="badge badge-success">
                                                    Lengkap
                                                </span>
                                            @elseif($statusArtefak == 'Pending')
                                                <span class="badge badge-warning">
                                                    Pending
                                                </span>
                                            @else
                                                <span class="badge badge-danger">
                                                    Belum
                                                </span>
                                            @endif

                                        </td>

                                        <td>

                                            <span class="badge badge-info">
                                                Terjadwal
                                            </span>

                                        </td>

                                        <td>

                                            <div class="d-flex align-items-center">

                                                <div class="progress flex-grow-1 mr-2" data-height="8">

                                                    <div class="progress-bar bg-success" style="width:{{ rand(40, 100) }}%">
                                                    </div>

                                                </div>

                                                <small>
                                                    {{ rand(40, 100) }}%
                                                </small>

                                            </div>

                                        </td>

                                        <td>

                                            @php
                                                $status = ['Lengkap', 'Pending', 'Bermasalah'];
                                                $final = $status[array_rand($status)];
                                            @endphp

                                            @if ($final == 'Lengkap')
                                                <span class="badge badge-success">
                                                    Lengkap
                                                </span>
                                            @elseif($final == 'Pending')
                                                <span class="badge badge-warning">
                                                    Pending
                                                </span>
                                            @else
                                                <span class="badge badge-danger">
                                                    Bermasalah
                                                </span>
                                            @endif

                                        </td>
                                    </tr>
                                @endfor

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
