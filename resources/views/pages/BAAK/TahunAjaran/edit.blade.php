@extends('layouts.main')
@section('title', 'Edit Tahun Ajaran')

@section('content')
    <section class="section">
        <div class="section-body">

            <form action="{{ route('TahunAjaran.update', $tahunAjaran->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="card">
                    <div class="card-header">
                        <h4>Edit Tahun Ajaran</h4>
                    </div>

                    <div class="card-body">

                        {{-- Tahun Mulai --}}
                        <div class="form-group">
                            <label>Tahun Mulai</label>
                            <input type="number" name="tahun_mulai" class="form-control"
                                value="{{ $tahunAjaran->tahun_mulai }}" required>
                        </div>

                        {{-- Tahun Selesai --}}
                        <div class="form-group">
                            <label>Tahun Selesai</label>
                            <input type="number" name="tahun_selesai" class="form-control"
                                value="{{ $tahunAjaran->tahun_selesai }}" required>
                        </div>

                        {{-- Status --}}
                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" class="form-control" required>
                                <option value="Aktif" {{ $tahunAjaran->status == 'Aktif' ? 'selected' : '' }}>Aktif
                                </option>
                                <option value="Nonaktif" {{ $tahunAjaran->status == 'Nonaktif' ? 'selected' : '' }}>
                                    Non
                                    Aktif</option>
                            </select>
                        </div>

                    </div>

                    <div class="card-footer text-end">
                        <button class="btn btn-primary">Update</button>
                    </div>

                </div>
            </form>

        </div>
    </section>
@endsection
