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
                        <a class="btn btn-primary btn-sm" href="{{ route('kelompokMahasiswa.index', ['id' => $kelompok->id]) }}">Kembali</a>
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
                        <form method="POST" action="{{ route('kelompokMahasiswa.store')}}" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="kelompok_id" value="{{ $kelompok->id }}">
                            @php
                            $jumlahMin = 4;
                            $jumlahMax = 6;
                        
                            if (session('KPA_id') == 1) {
                                $jumlahMin = 3;
                                $jumlahMax = 5;
                            }
                        @endphp
                       <div class="alert alert-info">
                        <strong>Petunjuk:</strong> Pilih minimal <strong>{{ $jumlahMin }}</strong> mahasiswa dan maksimal <strong>{{ $jumlahMax }}</strong> mahasiswa untuk dimasukkan ke dalam kelompok.
                    </div>

                    <form method="POST" action="{{ route('kelompokMahasiswa.store') }}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="kelompok_id" value="{{ $kelompok->id }}">

                        <div class="row">
                            @forelse ($mahasiswa as $item)
                                <div class="col-md-6 col-lg-5 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="user_id[]" value="{{ $item['user_id'] }}" id="mhs{{ $item['user_id'] }}">
                                        <label class="form-check-label" for="mhs{{ $item['user_id'] }}">
                                            {{ $item['nim'] }} — {{ $item['nama'] }}
                                        </label>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12">
                                    <div class="alert alert-warning">
                                        Semua mahasiswa sudah memiliki kelompok.
                                    </div>
                                </div>
                            @endforelse
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-plus-circle mr-1"></i> Tambah
                            </button>
                        </div>
                    </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
    