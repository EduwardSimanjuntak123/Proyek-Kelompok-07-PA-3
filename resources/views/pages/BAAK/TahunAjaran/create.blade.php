@extends('layouts.main')

@section('title', 'Tambah Tahun Masuk')

@section('content')
    <section class="section">
        <div class="section-body">
            <form action="{{ route('TahunAjaran.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card">
                    <div class="card-header">
                        <h4>Tambah Tahun Ajaran</h4>
                    </div>
                    <div class="card-body">

                        {{-- Tahun Mulai --}}
                        <div class="form-group">
                            <label>Tahun Mulai</label>
                            <input type="number" name="tahun_mulai" class="form-control" placeholder="Contoh: 2024"
                                min="2000" max="2100" required>
                        </div>

                        {{-- Tahun Selesai --}}
                        <div class="form-group">
                            <label>Tahun Selesai</label>
                            <input type="number" name="tahun_selesai" class="form-control" placeholder="Contoh: 2025"
                                min="2000" max="2100" required>
                        </div>

                        {{-- Status --}}
                        <input type="hidden" name="status" value="Aktif">

                        <button class="btn btn-primary" type="submit">Simpan</button>

                    </div>
                </div>
            </form>
        </div>
    </section>
@endsection
