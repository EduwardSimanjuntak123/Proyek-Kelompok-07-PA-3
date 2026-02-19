@extends('layouts.main')
@section('title', 'Create penguji 1')

@section('content')
<section class="section custom-section">
    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h4>Tambah Penguji 1</h4>
                        <a class="btn btn-primary btn-sm" href="{{route('penguji.index')}}">Kembali</a>
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

                        <form method="POST" action="{{route('penguji.store')}}" enctype="multipart/form-data">
                            @csrf
                       
                            {{-- Dosen --}}
                        <div class="form-group">
                            <label for="user_id">Pilih Dosen Penguji</label>
                            <select id="user_id" name="user_id" class="select2 form-control" required>
                                <option value="">-- Dosen Pembimbing --</option>
                                @foreach ($dosen as $item)
                                    <option 
                                        value="{{ $item ['user_id'] }}" 
                                    >
                                        {{ $item['nama'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <label for="nomor">Nomor Kelompok</label>
                        <div class="row">
                            @forelse ($kelompok as $item)
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="card shadow-sm">
                                        <div class="card-body">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="kelompok_id[]" value="{{ $item['id'] }}" id="klmpk{{ $item['id'] }}">
                                                <label class="form-check-label" for="klmpk{{ $item['id'] }}">Kelompok
                                                    {{ $item['nomor_kelompok'] }}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12">
                                    <div class="alert alert-warning">
                                        Semua kelompok sudah memiliki Pembimbing.
                                    </div>
                                </div>
                            @endforelse
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
    document.addEventListener('DOMContentLoaded', function () {
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
