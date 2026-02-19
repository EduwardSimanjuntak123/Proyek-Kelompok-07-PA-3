@extends('layouts.main')
@section('title', 'Edit Tahun Masuk')

@section('content')
<section class="section">
    <div class="section-body">
        <form action="{{route('TahunMasuk.update',['id' =>Crypt::encrypt($TahunMasuk->id)])}}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="card">
                <div class="card-header"><h4>Edit Tahun Masuk</h4></div>
                <div class="card-body">
                    <label for="Tahun_Masuk">Tahun Masuk</label>
                    <select name="Tahun_Masuk" class="form-control" required>
                        <option value="">-- Pilih Tahun --</option>
                        @for($year = date('Y'); $year >= 2000; $year--)
                            <option value="{{ $year }}" {{ $TahunMasuk->Tahun_Masuk == $year ? 'selected' : '' }}>{{ $year }}</option>
                        @endfor
                    </select>
                    
                    <div class="form-group">
                        <label>Status</label>
                        <select name="Status" class="form-control" required>
                            <option value="Aktif" {{ $TahunMasuk->Status == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="Tidak-Aktif" {{ $TahunMasuk->Status == 'Tidak-Aktif' ? 'selected' : '' }}>Non-aktif</option>
                        </select>
                    </div>
                <div class="card-footer text-end">
                    <button class="btn btn-primary" type="submit">Update</button>
                </div>
            </div>
        </form>
    </div>
</section>
@endsection
