@extends('layouts.main')
@section('title', 'Create Dosen Role')

@section('content')
    <section class="section custom-section">
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <h4>Tambah Dosen Role</h4>
                            <a class="btn btn-primary btn-sm" href="{{ route('manajemen-role.index') }}">Kembali</a>
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

                            <form method="POST" action="{{ route('manajemen-role.store') }}" enctype="multipart/form-data">
                                @csrf

                                {{-- Pilih Dosen --}}
                                <div class="form-group">
                                    <label for="user_id">Pilih Dosen</label>
                                    <select id="user_id" name="user_id" class="select2 form-control" required>
                                        <option value="">-- Pilih Dosen --</option>
                                        @foreach ($dosen as $item)
                                            <option value="{{ $item['user_id'] }}"
                                                {{ old('user_id') == $item['user_id'] ? 'selected' : '' }}>
                                                {{ $item['nama'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Role --}}
                                <div class="form-group">
                                    <label for="role_id">Pilih Role</label>
                                    <select id="role_id" name="role_id" class="select2 form-control" required>
                                        <option value="">-- Pilih Role --</option>
                                        @foreach ($role as $item)
                                            <option value="{{ $item->id }}"
                                                {{ old('role_id') == $item->id ? 'selected' : '' }}>
                                                {{ $item->role_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                {{-- Prodi --}}
                                <div class="form-group">
                                    <label for="prodi_id">Pilih Prodi</label>
                                    <select id="prodi_id" name="prodi_id" class="select2 form-control" required>
                                        <option value="">-- Pilih Prodi --</option>
                                        @foreach ($prodi as $item)
                                            <option value="{{ $item->id }}"
                                                {{ old('prodi_id') == $item->id ? 'selected' : '' }}>
                                                {{ $item->nama_prodi }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                {{-- Kategori Proyek Akhir --}}
                                <div class="form-group">
                                    <label for="jenis_pa">Kategori Proyek Akhir</label>
                                    <select id="KPA_id" name="KPA_id" class="select2 form-control" required>
                                        <option value="">-- Pilih Kategori Proyek Akhir --</option>
                                        @foreach ($kategoripa as $item)
                                            <option
                                                value="{{ $item->id }}"{{ old('KPA_id') == $item->id ? 'selected' : '' }}>
                                                {{ $item->kategori_pa }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                {{-- Tahun Masuk --}}
                                {{-- @dd($tahun_ajaran) --}}
                                <div class="form-group">
                                    <label for="tahun_Masuk_id">Tahun Masuk</label>
                                    <select name="TM_id" id="TM_id" class="select2 form-control" required>

                                        <option value="">-- Pilih Tahun Masuk --</option>
                                        @foreach ($tahun_masuk as $item)
                                            <option value="{{ $item->id }}"
                                                {{ old('TM_id') == $item->id ? 'selected' : '' }}>
                                                {{ $item->Tahun_Masuk }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                {{-- Tahun Ajaran --}}
                                <div class="form-group">
                                    <label>Tahun Ajaran</label>

                                    <input type="text" class="form-control"
                                        value="{{ $tahunAjaranAktif->tahun_mulai }} / {{ $tahunAjaranAktif->tahun_selesai }}"
                                        readonly>

                                    <input type="hidden" name="tahun_ajaran_id" value="{{ $tahunAjaranAktif->id }}">
                                </div>
                                {{-- Status --}}
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <input type="text" class="form-control" value="Aktif" readonly>
                                    <input type="hidden" name="status" value="Aktif">
                                    {{-- <select name="status" id="status" class="form-control">
                                    <option value="Aktif" {{ old('status', 'Aktif') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                </select> --}}
                                </div>
                                <button type="submit" class="btn btn-primary">Tambah</button>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const select = document.getElementById('user_id');
            const namaInput = document.getElementById('nama_dosen');

            function updateNama() {
                const selected = select.options[select.selectedIndex];
                const nama = selected.getAttribute('data-nama') || '';
                namaInput.value = nama;
            }

            updateNama();
            select.addEventListener('change', updateNama);
        });
    </script>
@endpush
