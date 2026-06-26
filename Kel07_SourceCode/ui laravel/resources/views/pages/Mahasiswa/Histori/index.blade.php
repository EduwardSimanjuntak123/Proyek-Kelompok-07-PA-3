@extends('layouts.main')
@section('title', 'Histori')

@section('content')
    <section class="section custom-section">
        <div class="section-body">
            <div class="card">
                <div class="card-header">
                    <h4>Histori Dosen</h4>
                </div>

                <div class="card-body">

                    {{-- FILTER --}}
                    <form method="GET" action="{{ route('Histori.index') }}" class="mb-4">
                        <div class="row">

                            <div class="col-md-4">
                                <label>Tahun Ajaran</label>
                                <select name="tahun_ajaran" class="form-control">
                                    <option value="">-- Pilih Tahun Ajaran --</option>
                                    @foreach ($tahunAjaran as $ta)
                                        <option value="{{ $ta->id }}"
                                            {{ request('tahun_ajaran') == $ta->id ? 'selected' : '' }}>
                                            {{ $ta->tahun_mulai }} / {{ $ta->tahun_selesai }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label>Tahun Masuk</label>
                                <select name="tahun_masuk" class="form-control">
                                    <option value="">-- Pilih Tahun Masuk --</option>
                                    @foreach ($tahunMasuk as $tm)
                                        <option value="{{ $tm->id }}"
                                            {{ request('tahun_masuk') == $tm->id ? 'selected' : '' }}>
                                            {{ $tm->Tahun_Masuk }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label>Program Studi</label>
                                <select name="prodi" class="form-control">
                                    <option value="">-- Pilih Program Studi --</option>
                                    @foreach ($prodi as $p)
                                        <option value="{{ $p->id }}"
                                            {{ request('prodi') == $p->id ? 'selected' : '' }}>
                                            {{ $p->nama_prodi }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary btn-block">
                                    <i class="fas fa-search"></i> Filter
                                </button>
                            </div>

                        </div>
                    </form>

                    {{-- TABLE --}}
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th>No</th>
                                    <th>Nomor Kelompok</th>
                                    <th>Tahun Ajaran</th>
                                    <th>Tahun Masuk</th>
                                    <th>Program Studi</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($kelompok as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->nomor_kelompok }}</td>
                                        <td>
                                            {{ $item->tahunAjaran ? $item->tahunAjaran->tahun_mulai . ' / ' . $item->tahunAjaran->tahun_selesai : '-' }}
                                        </td>
                                        <td>{{ $item->TahunMasuk->Tahun_Masuk ?? '-' }}</td>
                                        <td>{{ $item->prodi->nama_prodi ?? '-' }}</td>
                                        <td>
                                            <a href="{{ route('Histori.detail', $item->id) }}" class="btn btn-info btn-sm">
                                                <i class="fas fa-eye"></i> Detail
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">
                                            Belum ada data histori.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </section>
@endsection
