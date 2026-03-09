@extends('layouts.main')
@section('title', 'Nilai Mata Kuliah Mahasiswa')

@section('content')

    <section class="section">

        <div class="card">

            <div class="card-header">
                <h4>Nilai Mata Kuliah Mahasiswa</h4>
            </div>

            <div class="card-body">

                {{-- FILTER --}}
                <form method="GET" class="mb-4">

                    <div class="row">

                        <div class="col-md-3">
                            <input type="text" name="nim" class="form-control" placeholder="Cari NIM"
                                value="{{ request('nim') }}">
                        </div>

                        <div class="col-md-3">
                            <input type="text" name="nama" class="form-control" placeholder="Cari Nama"
                                value="{{ request('nama') }}">
                        </div>

                        <div class="col-md-3">
                            <select name="angkatan" class="form-control">

                                <option value="">Semua Angkatan</option>

                                @foreach ($listAngkatan as $angkatan)
                                    <option value="{{ $angkatan }}"
                                        {{ request('angkatan') == $angkatan ? 'selected' : '' }}>
                                        {{ $angkatan }}
                                    </option>
                                @endforeach

                            </select>
                        </div>

                        <div class="col-md-2">
                            <button class="btn btn-primary">Filter</button>
                        </div>

                    </div>

                </form>


                {{-- DATA MAHASISWA --}}
                @if ($mahasiswa->count())

                    @php
                        $mhs = $mahasiswa->first();
                    @endphp

                    <div class="mb-4">

                        <h5>{{ $mhs->nama }}</h5>

                        <p>
                            NIM : {{ $mhs->nim }} <br>
                            Angkatan : {{ $mhs->angkatan }}
                        </p>

                    </div>


                    {{-- LOOP SEMESTER --}}
                    @foreach ($nilaiSemester as $semester => $items)
                        <div class="card mb-3">

                            <div class="card-header">
                                Semester {{ $semester }}
                            </div>

                            <div class="card-body">

                                @foreach ($items as $nilai)
                                    <div class="row mb-2">
                                        <div class="col-md-6">
                                            {{ $nilai->nama_matkul }}
                                        </div>

                                        <div class="col-md-3">
                                            {{ $nilai->nilai_angka }}
                                        </div>

                                        <div class="col-md-3">
                                            {{ $nilai->nilai_huruf }}
                                        </div>
                                    </div>
                                @endforeach

                            </div>

                        </div>
                    @endforeach
                @endif


                {{-- PAGINATION --}}
                <div class="mt-3">

                    {{ $mahasiswa->links() }}

                </div>

            </div>
        </div>

    </section>

@endsection
