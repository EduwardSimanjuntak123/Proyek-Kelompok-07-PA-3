@extends('layouts.main')
@section('title', 'Tambah Bimbingan')

@section('content')
<section class="section custom-section">
    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h4>Tambah Request Bimbingan</h4>
                        <a class="btn btn-primary btn-sm" href="{{ route('bimbingan.index') }}">Kembali</a>
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

                        <form method="POST" action="{{ route('bimbingan.store')}}" enctype="multipart/form-data">
                            @csrf

                        
                       {{-- Kelompok_id--}}
                        <div class="form-group">
                            <input type="hidden" name="kelompok_id" value="{{ $kelompokId }}">
                        </div>

                     {{-- keperluan --}}
                        <div class="form-group">
                             <label for="keperluan">Keperluan</label>
                               <input type="text" name="keperluan" id="keperluan" class="form-control" required>
                         </div>


                         {{-- Rencana Bimbingan --}}
                     <div class="form-group">
                        <label for="rencana_mulai">Rencana Mulai Bimbingan</label>
                        <input type="datetime-local" name="rencana_mulai" id="rencana_mulai" class="form-control @error('rencana_mulai') is-invalid @enderror" value="{{ old('rencana_mulai') ? old('rencana_mulai') : '' }}">
                        @error('rencana_mulai')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Rencana Bimbingan --}}
                    <div class="form-group">
                        <label for="rencana_selesai">Rencana Selesai Bimbingan</label>
                        <input type="datetime-local" name="rencana_selesai" id="rencana_selesai" class="form-control @error('rencana_selesai') is-invalid @enderror" value="{{ old('rencana_selesai') ? old('rencana_selesai') : '' }}">
                        @error('rencana_selesai')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                     <!-- {{-- Rencana Bimbingan --}}
                     <div class="form-group">
                        <label for="rencana_mulai">Rencana Mulai Bimbingan</label>
                        <input type="datetime-local" name="rencana_mulai" id="rencana_mulai" class="form-control @error('rencana_mulai') is-invalid @enderror" value="{{ old('rencana_mulai') ? old('rencana_mulai') : '' }}">
                        @error('rencana_mulai')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div> -->

                   {{-- keperluan --}}
                   <div class="form-group">
                                <label for="ruangan">Ruangan</label>
                                <select name="ruangan_id" id="ruangan_id" class="form-control" required>
                                    <option value="">-- Pilih Ruangan --</option>
                                    @foreach($ruangan as $item)
                                    <option value="{{ $item->id}}" {{ old('ruangan_id') == $item->id ? 'selected' : '' }}>
                                        {{ $item->ruangan}}
                                    </option>
                                    @endforeach
                                </select>
                            </div>    

                    {{-- status --}}
                    <div class="form-group">
                        <input type="hidden" name="status" value="menunggu">
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
    