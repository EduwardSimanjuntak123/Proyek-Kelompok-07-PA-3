@extends('layouts.main')
@section('title', 'Edit Kelompok')

@section('content')
<section class="section">
    <div class="section-body">
        <div class="row">
            <div class="col-12">
                @include('partials.alert')
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h4>Edit Kelompok</h4>
                        <a href="{{ route('kelompok.index') }}" class="btn btn-primary">Kembali</a>
                    </div>
                    <div class="card-body">

                        <form method="POST" action="{{ route('kelompok.update', Crypt::encrypt($kelompok['id'])) }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            {{-- Nomor Kelompok --}}    
                            <div class="form-group">
                                <label for="nomor_kelompok">Nomor Kelompok</label>
                                <input type="text" class="form-control" id="nomor_kelompok" name="nomor_kelompok" value="{{ $kelompok->nomor_kelompok }}" required>
                            </div>
                             {{-- Jenis PA --}}
                             <div class="form-group">
                                <label for="KPA_id">Jenis PA (Readonly)</label>
                                <input type="text" class="form-control" value="{{ $kelompok->kategoripa->kategori_pa ?? '-' }}" readonly>
                                <input type="hidden" name="KPA_id" value="{{ $kelompok->KPA_id }}">
                            </div>
                            {{-- Prodi --}}
                            <div class="form-group">
                                <label for="prodi_id">Program Studi (Readonly)</label>
                                <input type="text" class="form-control" value="{{ $kelompok->prodi->nama_prodi ?? '-' }}" readonly>
                                <input type="hidden" name="prodi_id" value="{{ $kelompok->prodi_id }}">
                            </div>

                            {{-- Tahun Angkatan --}}
                            <div class="form-group">
                                <label for="TA_id">Tahun Angkatan (Readonly)</label>
                                <input type="text" class="form-control" value="{{ $kelompok->tahunMasuk->Tahun_Masuk ?? '-' }}" readonly>
                                <input type="hidden" name="TM_id" value="{{ $kelompok->TM_id }}">
                            </div>
                           {{-- Status --}}
                           <div class="form-group">
                            <label for="status">Status</label>
                            <select name="status" id="status" class="form-control">
                                <option value="Aktif" {{ old('status', $kelompok->status) == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="Tidak-Aktif" {{ old('status', $kelompok->status) == 'Tidak-Aktif' ? 'selected' : '' }}>Tidak-Aktif</option>
                            </select>
                        </div>

                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
