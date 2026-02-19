    @extends('layouts.main')
@section('title', 'Feedback Pembimbing')

@section('content')
<section class="section">
    <div class="section-body">
        <div class="card">
            <div class="card-header">
                <h4>Feedback untuk Kelompok {{ $artefak->kelompok->nomor_kelompok }}</h4>
            </div>
            <div class="card-body">
                @include('partials.alert')
                <form action="{{ route('pembimbing.feedback.submit', $artefak->id) }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="feedback_pembimbing">Feedback</label>
                        <textarea name="feedback_pembimbing" id="feedback_pembimbing" class="form-control" rows="5" required>{{ old('feedback_pembimbing', $artefak->feedback_pembimbing) }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-primary mt-2">Kirim Feedback</button>
                    <a href="{{ route('pembimbing.show.submitan', $artefak->tugas_id) }}" class="btn btn-secondary mt-2">Kembali</a>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection
