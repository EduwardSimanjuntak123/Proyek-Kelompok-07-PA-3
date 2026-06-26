@extends('layouts.main')
@section('title', 'Tambah Kelompok')

@section('content')
    <section class="section custom-section">
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <h4>Tambah Kelompok</h4>
                            <a class="btn btn-primary btn-sm" href="{{ route('kelompok.index') }}">Kembali</a>
                        </div>
                        <div class="card-body">

                            {{-- Tampilkan Error jika ada --}}
                            @if ($errors->any())
                                <div class="alert alert-danger alert-dismissible show fade">
                                    <div class="alert-body">
                                        <button class="close" data-dismiss="alert"><span>&times;</span></button>
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('kelompok.store') }}" enctype="multipart/form-data">
                                @csrf

                                {{-- Nomor Kelompok --}}
                                <div class="form-group">
                                    <label for="nomor_kelompok">Nomor Kelompok</label>
                                    <input type="text" name="nomor_kelompok" id="nomor_kelompok" class="form-control"
                                        required>
                                </div>

                                {{-- Tahun Masuk  --}}
                                <div class="form-group">
                                    <label for="TM_id">Tahun Tahun Masuk</label>
                                    <input type="text" class="form-control"
                                        value="{{ $tahun_masuk->Tahun_Masuk ?? '-' }}" readonly>
                                    <input type="hidden" name="TM_id" value="{{ $tahun_masuk->id }}">
                                </div>

                                {{-- Tahun Ajaran --}}
                                <div class="form-group">
                                    <label>Tahun Ajaran</label>
                                    <input type="text" class="form-control"
                                        value="{{ $tahunAjaran->tahun_mulai }} / {{ $tahunAjaran->tahun_selesai }}"
                                        readonly>

                                    <input type="hidden" name="tahun_ajaran_id" value="{{ $tahunAjaran->id }}">
                                </div>

                                {{-- Prodi --}}
                                <div class="form-group">
                                    <label for="prodi_id">Program Studi</label>
                                    <input type="text" class="form-control" value="{{ $prodi->nama_prodi ?? '-' }}"
                                        readonly>
                                    <input type="hidden" name="prodi_id" value="{{ $prodi->id }}">
                                </div>

                                {{-- Jenis PA --}}
                                <div class="form-group">
                                    <label for="KPA_id">Jenis PA</label>
                                    <input type="text" class="form-control"
                                        value="{{ $kategoripa->kategori_pa ?? '-' }}" readonly>
                                    <input type="hidden" name="KPA_id" value="{{ $kategoripa->id }}">
                                </div>

                                {{--  status --}}
                                <div class="form-group">
                                    <input type="hidden" name="status" value="Aktif">
                                </div>
                                <button type="submit" class="btn btn-primary">Tambah</button>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
