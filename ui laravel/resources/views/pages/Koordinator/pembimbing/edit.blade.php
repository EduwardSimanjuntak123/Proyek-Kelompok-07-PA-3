@extends('layouts.main')
@section('title', 'Edit Pembimbing')

@section('content')
    <section class="section">
        <div class="section-body">
            <div class="row">
                <div class="col-12">

                    @include('partials.alert')

                    <div class="card">

                        <div class="card-header d-flex justify-content-between">
                            <h4>Edit Pembimbing Kelompok</h4>

                            <a href="{{ route('pembimbing.index') }}" class="btn btn-primary btn-sm">
                                Kembali
                            </a>
                        </div>

                        <div class="card-body">

                            <form method="POST" action="{{ route('pembimbing.update', Crypt::encrypt($kelompok_id)) }}">
                                @csrf
                                @method('PUT')

                                <input type="hidden" name="kelompok_id" value="{{ $kelompok_id }}">

                                {{-- KELOMPOK --}}
                                <div class="form-group">

                                    <label>Kelompok</label>

                                    <input type="text" class="form-control"
                                        value="{{ $kelompok->nomor_kelompok ?? '-' }}" readonly>

                                </div>

                                {{-- PEMBIMBING 1 --}}
                                <div class="form-group">
                                    <label>Pembimbing 1</label>
                                    <select name="pembimbing1" class="form-control select2">
                                        {{-- pembimbing lama --}}
                                        @if ($dosenPembimbing1)
                                            <option value="{{ $dosenPembimbing1->user_id }}" selected>
                                                {{ $dosenPembimbing1->nama }} (Pembimbing Lama)
                                            </option>
                                        @endif

                                        <option value="">-- Pilih Pembimbing 1 --</option>

                                        @foreach ($dosen as $item)
                                            @if (!$dosenPembimbing1 || $item['user_id'] != $dosenPembimbing1->user_id)
                                                <option value="{{ $item['user_id'] }}">
                                                    {{ $item['nama'] }}
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>

                                {{-- PEMBIMBING 2 --}}
                                <div class="form-group">
                                    <label>Pembimbing 2</label>
                                    <select name="pembimbing2" class="form-control select2">
                                        {{-- pembimbing lama --}}
                                        @if ($dosenPembimbing2)
                                            <option value="{{ $dosenPembimbing2->user_id }}" selected>
                                                {{ $dosenPembimbing2->nama }} (Pembimbing Lama)
                                            </option>
                                        @endif

                                        <option value="">-- Pilih Pembimbing 2 --</option>

                                        @foreach ($dosen as $item)
                                            @if (!$dosenPembimbing2 || $item['user_id'] != $dosenPembimbing2->user_id)
                                                <option value="{{ $item['user_id'] }}">
                                                    {{ $item['nama'] }}
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>

                                <button type="submit" class="btn btn-primary">

                                    <i class="fas fa-save"></i> Simpan Perubahan

                                </button>

                            </form>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>
@endsection
