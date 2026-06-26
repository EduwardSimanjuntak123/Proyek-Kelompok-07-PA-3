
@extends('layouts.main')

@section('title', 'Tambah Tahun Masuk')

@section('content')
<section class="section">
    <div class="section-body">
        <form action="{{ route('TahunMasuk.store') }}" method="POST"  enctype="multipart/form-data">
            @csrf
            <div class="card">
                <div class="card-header"><h4>Tambah Tahun Masuk</h4></div>
                <div class="card-body">
                    <label for="Tahun_Masuk">Tahun Masuk</label>
                    <select name="Tahun_Masuk" class="form-control" required>
                        <option value="">-- Pilih Tahun --</option>
                        @for($year = date('Y'); $year >= 2000; $year--)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endfor
                    </select>
                    @error('Tahun_Masuk')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror                    
                      {{-- Status --}}
                      <div class="form-group">
                       <input type="hidden" name = "Status" value="Aktif">
                    </div>
                    <button class="btn btn-primary" type="submit">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</section>
@endsection


