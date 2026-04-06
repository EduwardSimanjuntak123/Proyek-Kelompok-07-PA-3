@extends('layouts.main')
@section('title', 'Edit Anggota Kelompok')

@section('content')
<section class="section">
    <div class="section-body">
        <div class="row">
            <div class="col-12">
                @include('partials.alert')
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h4>Edit Anggota Kelompok</h4>
                        <a href="{{route('kelompok.index') }}" class="btn btn-primary">Kembali</a>
                    </div>
                    <div class="card-body">

                        <form method="POST" action="{{ route('kelompokMahasiswa.update', $kelompokMahasiswa->id) }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                <label for="user_id">Pilih Mahasiswa</label>
                                <select id="user_id" name="user_id" class="select2 form-control" required>
                                     <option value="">-- Pilih Mahasiswa --</option>
                                     @foreach ($mahasiswabelummasuk as $item)
                                    <option 
                                    value="{{ $item['user_id'] ?? '' }}"
                                    data-nama="{{ $item['nama'] ?? 'Tanpa Nama' }}"
                                    {{ $item['user_id'] == $kelompokMahasiswa['user_id'] ? 'selected' : '' }}
                                >
                                   {{ $item['nim'] ?? 'Tanpa Nim' }} -{{ $item['nama'] ?? 'Tanpa Nama' }}
                                </option> 
                                @endforeach 
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
