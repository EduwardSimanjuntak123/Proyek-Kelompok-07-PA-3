@extends('layouts.main')
@section('title', 'Edit Dosen Role')

@section('content')
<section class="section">
    <div class="section-body">
        <div class="row">
            <div class="col-12">
                @include('partials.alert')
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h4>Edit Dosen Role</h4>
                        <a href="{{ route('manajemen-role.index') }}" class="btn btn-primary">Kembali</a>
                    </div>
                    <div class="card-body">

                        <form method="POST" action="{{ route('manajemen-role.update', Crypt::encrypt($dosenRole['id'])) }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            {{-- Pilih Dosen --}}
                            <div class="form-group">
                                <label for="user_id">Pilih Dosen</label>
                                <select id="user_id" name="user_id" class="select2 form-control" required>
                                    <option value="">-- Pilih Dosen --</option>
                                    @foreach ($dosen as $item)
                                    <option 
                                    value="{{ $item['user_id'] ?? '' }}"
                                    data-nama="{{ $item['nama'] ?? 'Tanpa Nama' }}"
                                    {{ $item['user_id'] == $dosenRole['user_id'] ? 'selected' : '' }}
                                >
                                    {{ $item['nama'] ?? 'Tanpa Nama' }}
                                </option>
                                @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="role_id">Pilih Role</label>
                                <select id="role_id" name="role_id" class="select2 form-control" required>
                                    <option value="">-- Pilih Role --</option>
                                    @foreach ($role as $item)
                                        <option 
                                            value="{{ $item['id'] }}" 
                                            data-nama="{{ $item['role_name'] ?? 'Tanpa Nama' }}"
                                            {{ old('role_id', $dosenRole['role_id']) == $item['id'] ? 'selected' : '' }}
                                        >
                                            {{ $item['role_name'] }}
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
                                        <option 
                                            value="{{ $item['id'] }}" 
                                            data-nama="{{ $item['nama_prodi'] ?? 'Tanpa Nama' }}"
                                            {{ old('prodi_id', $dosenRole['prodi_id'] ?? '') == $item['id'] ? 'selected' : '' }}
                                        >
                                            {{ $item['nama_prodi'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                               {{-- Kategori Proyek Akhir --}}
                               <div class="form-group">
                                <label for="KPA_id">Kategori Proyek Akhir</label>
                                <select id="KPA_id" name="KPA_id" class="select2 form-control" required>
                                    <option value="">-- Pilih Kategori Proyek Akhir --</option>
                                    @foreach ($kategoripa as $item)
                                    <option 
                                            value="{{ $item->id }}" 
                                            {{ old('TA_id', $dosenRole->KPA_id) == $item->id ? 'selected' : '' }}
                                        >
                                            {{ $item->kategori_pa }}
                                        </option>
                                @endforeach
                                </select>
                            </div>
                            {{--Tahun Masuk  --}}
                            <div class="form-group">
                                <label for="TM_id">Tahun Masuk</label>
                                <select name="TM_id" id="TM_id" class="select2 form-control" required>
                                    <option value="">-- Pilih Tahun Masuk --</option>
                                    @foreach ($tahun_masuk as $item)
                                        <option 
                                            value="{{ $item->id }}" 
                                            {{ old('TM_id', $dosenRole->TM_id) == $item->id ? 'selected' : '' }}
                                        >
                                            {{ $item->Tahun_Masuk }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            {{-- Tahun Ajaran --}}
                        <div class="form-group">
                            <label for="Tahun_Ajaran">Tahun Ajaran</label>
                            <input type="text"name="Tahun_Ajaran" id="Tahun_Ajaran" class="form-control" required  pattern="\d{4}/\d{4}" title="Format: 2024/2025" 
                                value="{{ old('Tahun_Ajaran', $dosenRole->Tahun_Ajaran) }}">
                        </div>

                            {{-- Status --}}
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="Aktif" {{ old('status', $dosenRole->status) == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                    <option value="Tidak-Aktif" {{ old('status', $dosenRole->status) == 'Tidak-Aktif' ? 'selected' : '' }}>Tidak-Aktif</option>
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
@push('script')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const select = document.getElementById('user_id');
        const namaInput = document.getElementById('nama_dosen');

        function updateNama() {
            const selected = select.options[select.selectedIndex];
            const nama = selected.getAttribute('data-nama') || '';
            namaInput.value = nama;
        }

        updateNama(); // Set nama saat load
        select.addEventListener('change', updateNama); // Update nama saat ganti dosen
    });
</script>
@endpush
