@extends('layouts.main')
@section('title', 'Edit Penguji')

@section('content')
    <section class="section">
        <div class="section-body">
            <div class="row">
                <div class="col-12">

                    @include('partials.alert')

                    <div class="card">

                        <div class="card-header d-flex justify-content-between">
                            <h4>Edit Penguji Kelompok</h4>

                            <a href="{{ route('penguji.index') }}" class="btn btn-primary btn-sm">
                                Kembali
                            </a>
                        </div>

                        <div class="card-body">

                            <form method="POST" action="{{ route('penguji.update', Crypt::encrypt($kelompok_id)) }}">
                                @csrf
                                @method('PUT')

                                <input type="hidden" name="kelompok_id" value="{{ $kelompok_id }}">

                                {{-- KELOMPOK --}}
                                <div class="form-group">

                                    <label>Kelompok</label>

                                    <input type="text" class="form-control"
                                        value="{{ $kelompok->nomor_kelompok ?? '-' }}" readonly>

                                </div>

                                {{-- PENGUJI 1 --}}
                                <div class="form-group">
                                    <label>Penguji 1</label>
                                    <select name="penguji1" class="form-control select2">
                                        {{-- penguji lama --}}
                                        @if ($dosenPenguji1)
                                            <option value="{{ $dosenPenguji1->user_id }}" selected>
                                                {{ $dosenPenguji1->nama }} (Penguji Lama)
                                            </option>
                                        @endif

                                        <option value="">-- Pilih Penguji 1 --</option>

                                        @foreach ($dosen as $item)
                                            @if (!$dosenPenguji1 || $item['user_id'] != $dosenPenguji1->user_id)
                                                <option value="{{ $item['user_id'] }}">
                                                    {{ $item['nama'] }}
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>

                                {{-- PENGUJI 2 --}}
                                <div class="form-group">
                                    <label>Penguji 2</label>
                                    <select name="penguji2" class="form-control select2">
                                        {{-- penguji lama --}}
                                        @if ($dosenPenguji2)
                                            <option value="{{ $dosenPenguji2->user_id }}" selected>
                                                {{ $dosenPenguji2->nama }} (Penguji Lama)
                                            </option>
                                        @endif

                                        <option value="">-- Pilih Penguji 2 --</option>

                                        @foreach ($dosen as $item)
                                            @if (!$dosenPenguji2 || $item['user_id'] != $dosenPenguji2->user_id)
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
