
@extends('layouts.main')

@section('title', 'Kartu Bimbingan')

@section('content')
<section class="section custom-section">
    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Kartu Bimbingan</h4>
                        
                    </div>
                    <div class="card-body">
                        <!-- Menampilkan Kartu Bimbingan -->
                        <form method="POST" action="{{ route('bimbingan.update', Crypt::encrypt($bimbingan->id)) }}">
                        @csrf
                        @method('PUT')

                        <!-- Kelompok -->
                        <div class="form-group">
                            <label for="kelompok">Kelompok</label>
                            <input type="text" class="form-control" id="kelompok" value="{{ $bimbingan->kelompok->nomor_kelompok }}" readonly>
                        </div>

                        <!-- Pembimbing -->
                        <div class="form-group">
                            <label for="pembimbing">Pembimbing</label>
                            <input type="text" class="form-control" id="pembimbing" value="{{ $bimbingan->nama }}" readonly>
                        </div>

                        <!-- Anggota Kelompok -->
                        <div class="form-group">
                            <label for="anggota">Anggota</label>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>NIM</th>
                                        <th>Nama Mahasiswa</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($mahasiswakelompoks as $item)
                                        <tr>
                                            <td>{{ $item->nim }}</td>
                                            <td>{{ $item->nama }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Tanggal Bimbingan -->
                        <div class="form-group">
                            <label for="tanggal">Tanggal</label>
                            <input type="date" class="form-control" id="tanggal" value="{{ now()->toDateString() }}" readonly>
                        </div>

                        <!-- Topic -->
                        <div class="form-group">
                            <label for="topic">Topic</label>
                            <input type="text" class="form-control" id="topic" value="{{ $bimbingan->keperluan }}" readonly>
                        </div>

                        <!-- Hasil Bimbingan -->
                        <div class="form-group">
                            <label for="hasil_bimbingan">Hasil Bimbingan</label>
                            <textarea class="form-control" id="hasil_bimbingan" rows="6" name="hasil_bimbingan" required>{{ $bimbingan->hasil_bimbingan }}</textarea>
                        </div>
                        {{-- <div class="form-group">
                                <label for="hasil_bimbingan">Hasil Bimbingan</label>
                                <textarea class="form-control @error('hasil_bimbingan') is-invalid @enderror" id="hasil_bimbingan" rows="6" name="hasil_bimbingan" required>{{ old('hasil_bimbingan', $bimbingan->hasil_bimbingan) }}</textarea>
                                @error('hasil_bimbingan')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div> --}}

                        <!-- Tanda Tangan Pembimbing -->
                        <div class="form-group">
                            <label for="pembimbing">Tanda Tangan Pembimbing</label>
                            <input type="text" class="form-control" id="pembimbing" value="{{ $bimbingan->nama }}" readonly>
                        </div>

                        <div class="d-flex justify-content-end">
                            <!-- Tombol Simpan -->
                             {{-- @if() --}}
                            <button type="submit" class="btn btn-primary mr-2">Simpan</button>
                           
                            <!-- Tombol Download -->
                            {{-- <a href="{{ route('bimbingan.exportPdf', Crypt::encrypt($bimbingan->id)) }}" class="btn btn-success">Download File</a> --}}
                            @if(!empty($bimbingan->hasil_bimbingan))
                                    <a href="{{ route('bimbingan.exportPdf', Crypt::encrypt($bimbingan->id)) }}" class="btn btn-success">Download File</a>
                                @endif
                        </div>
                    </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
