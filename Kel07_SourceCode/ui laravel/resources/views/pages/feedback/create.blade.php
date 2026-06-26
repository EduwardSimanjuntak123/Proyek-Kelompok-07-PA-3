@extends('layouts.main')
@section('title', 'Berikan Feedback')

@section('content')
<section class="section">
    <div class="section-body">
        <div class="card">
            <div class="card-header">
                <h4>Berikan Feedback - Kelompok {{ $pengumpulan->kelompok->nomor_kelompok }}</h4>
            </div>
            <div class="card-body">
                @include('partials.alert')
                
                <!-- <p><strong>Judul Tugas:</strong> {{ $pengumpulan->tugas->Judul_Tugas }}</p>
                <p><strong>File Tugas:</strong> 
                    <a href="{{ asset('storage/' . $pengumpulan->file_path) }}" target="_blank">Lihat File</a>
                </p> -->
                
                <form method="POST" action="{{ route('feedback.update', $pengumpulan->id) }}">
                    @csrf
                    <div class="form-group">
                        <div class="form-group">
                        <label for="feedback_penguji">Feedback</label>
                        <textarea name="feedback" id="feedback" class="form-control" rows="5" required>{{ old('feedback', $pengumpulan->feedback) }}</textarea>
                    <!-- </div>
                        <textarea name="feedback" id="feedback" class="form-control" rows="5" required>{{ old('feedback', $pengumpulan->feedback) }}</textarea> -->
                    </div>
                    <button type="submit" class="btn btn-primary mt-2">Simpan Feedback</button>
                    <a href="{{ url()->previous() }}" class="btn btn-secondary mt-2">Batal</a>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection