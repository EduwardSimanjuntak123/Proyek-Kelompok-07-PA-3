@extends('layouts.main')
@section('title', 'Edit Jadwal')

@section('content')
<section class="section custom-section">
    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h4>Edit Jadwal</h4>
                        <a class="btn btn-primary btn-sm" href="{{ route('jadwal.index') }}">Kembali</a>
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
                        <form method="POST" action="{{ route('jadwal.update', Crypt::encrypt($jadwal->id)) }}"> 
                            @csrf
                            @method('PUT')

                            {{--Pilih Kelompok--}}
                            <div class="form-group">
                            <label for="kelompok_id">Kelompok</label>
                            <select id="kelompok_id" name="kelompok_id" class="select2 form-control" required>
                                <option value="">-- Pilih Kelompok --</option>
                                @foreach ($kelompok as $item)
                                    <option 
                                        value="{{ $item['id'] }}" 
                                        {{ (old('kelompok_id') ?? $jadwal->kelompok_id) == $item['id'] ? 'selected' : '' }}>
                                        {{ $item['nomor_kelompok'] }}
                                    </option>
                                @endforeach
                            </select>
                            </div>
                            {{-- Masukkan Ruangan --}}
                            <div class="form-group">
                                <label for="ruangan">Ruangan</label>
                                <select name="ruangan_id" id="ruangan_id" class="select2 form-control" required>
                                    <option value="">-- Pilih Ruangan --</option>
                                    @foreach($ruangan as $item)
                                    <option value="{{ $item->id}}" {{ (old('ruangan_id') ?? $jadwal->ruangan_id) == $item['id'] ? 'selected' : '' }}>
                                        {{ $item->ruangan}}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            {{--Masukkan lokasi
                            <div class="form-group mt-3">
                                <label for="ruangan">Lokasi</label>
                                <input type="text" name="ruangan" id="ruangan"
                                    class="form-control @error('ruangan') is-invalid @enderror"
                                    placeholder="Masukkan Ruangan"
                                    value="{{ old('ruangan', $jadwal->ruangan) }}">
                                @error('ruangan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>--}}  

                            {{--Masukkan Jam Mulai--}}
                            <div class="form-group">
                                <label for="waktu_mulai">Tanggal</label>
                                <input type="datetime-local" name="waktu_mulai" id="waktu_mulai"
                                     class="form-control @error('waktu_mulai') is-invalid @enderror"
                                    value="{{ old('waktu_mulai', \Carbon\Carbon::parse($jadwal->waktu_mulai)->format('Y-m-d\TH:i')) }}">
                                @error('waktu_mulai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror 
                            </div>

                            {{--Masukkan Jam Selsai--}}
                            <div class="form-group">
                                <label for="waktu_selesai">Tanggal</label>
                                <input type="datetime-local" name="waktu_selesai" id="waktu_selesai"
                                     class="form-control @error('waktu_selesai') is-invalid @enderror"
                                    value="{{ old('waktu_selesai', \Carbon\Carbon::parse($jadwal->waktu_selesai)->format('Y-m-d\TH:i')) }}">
                                @error('waktu_selesai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror 
                            </div>

                            <button type="submit" class="btn btn-success">Simpan Perubahan</button>
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
            const selected = select?.options[select.selectedIndex];
            const nama = selected?.getAttribute('data-nama') || '';
            if(namaInput) namaInput.value = nama;
        }

        updateNama();
        if(select) select.addEventListener('change', updateNama);
    });
</script>
@endpush
