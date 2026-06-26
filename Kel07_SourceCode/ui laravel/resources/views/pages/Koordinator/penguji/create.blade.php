@extends('layouts.main')
@section('title', 'Tambah Penguji')

@section('content')
    <section class="section custom-section">
        <div class="section-body">
            <div class="row">
                <div class="col-12">

                    <div class="card">

                        <div class="card-header d-flex justify-content-between">
                            <h4>Tambah Penguji Kelompok</h4>

                            <a class="btn btn-primary btn-sm" href="{{ route('penguji.index') }}">
                                Kembali
                            </a>

                        </div>

                        <div class="card-body">

                            {{-- ERROR --}}
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


                            <form method="POST" action="{{ route('penguji.store') }}">
                                @csrf

                                <input type="hidden" name="kelompok_id" value="{{ $kelompok_id }}">

                                {{-- PENGUJI 1 --}}
                                <div class="form-group">

                                    <label>Penguji 1</label>

                                    <select name="penguji1" class="form-control select2">

                                        <option value="">-- Pilih Penguji 1 --</option>

                                        @foreach ($dosen as $item)
                                            <option value="{{ $item['user_id'] }}">
                                                {{ $item['nama'] }}
                                            </option>
                                        @endforeach

                                    </select>

                                </div>

                                {{-- PENGUJI 2 --}}
                                <div class="form-group">

                                    <label>Penguji 2</label>

                                    <select name="penguji2" class="form-control select2">

                                        <option value="">-- Pilih Penguji 2 --</option>

                                        @foreach ($dosen as $item)
                                            <option value="{{ $item['user_id'] }}">
                                                {{ $item['nama'] }}
                                            </option>
                                        @endforeach

                                    </select>

                                </div>

                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Simpan
                                </button>

                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>
@endsection
