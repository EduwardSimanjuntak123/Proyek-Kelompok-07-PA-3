@extends('layouts.main')
@section('title', 'Edit Bimbingan')

@section('content')
<section class="section custom-section">
    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Edit Request Bimbingan</h4>
                    </div>
                    <div class="card-body">
                        @include('partials.alert')
                        <form action="{{ route('bimbingan.update', Crypt::encrypt($bimbingan->id)) }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="form-group">
                                <label for="keperluan">Keperluan</label>
                                <textarea class="form-control" id="keperluan" name="keperluan" rows="3" required>{{ $bimbingan->keperluan }}</textarea>
                            </div>
                            
                            <div class="form-group">
                                <label for="ruangan_id">Ruangan</label>
                                <select class="form-control" id="ruangan_id" name="ruangan_id" required>
                                    <option value="">Pilih Ruangan</option>
                                    @foreach($ruangan as $r)
                                        <option value="{{ $r->id }}" {{ $bimbingan->ruangan_id == $r->id ? 'selected' : '' }}>
                                            {{ $r->ruangan }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="rencana_mulai">Tanggal Mulai</label>
                                <input type="datetime-local" class="form-control" id="rencana_mulai" name="rencana_mulai" value="{{ date('Y-m-d\TH:i', strtotime($bimbingan->rencana_mulai)) }}" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="rencana_selesai">Tanggal Selesai</label>
                                <input type="datetime-local" class="form-control" id="rencana_selesai" name="rencana_selesai" value="{{ date('Y-m-d\TH:i', strtotime($bimbingan->rencana_selesai)) }}" required>
                            </div>
                            
                            <div class="form-group text-right">
                                <a href="{{ route('bimbingan.index') }}" class="btn btn-secondary">Batal</a>
                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
