@extends('layouts.main')
@section('title', 'List Tahun Ajaran')

@section('content')
<section class="section custom-section">
    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h4>Show Pengumuman</h4>
                        <a class="btn btn-primary btn-sm" href="{{ route('pengumuman.index') }}">Kembali</a>
                    </div>
                    <div class="card-body">
                        @include('partials.alert')
                        <div class="mb-4">
                            <h3 class="text-muted">{{ $pengumuman->judul }}</h3>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <p><strong><i class="fas fa-calendar-alt mr-1"></i> Tanggal:</strong> {{ $pengumuman->created_at->format('d-m-Y') }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong><i class="fas fa-info-circle mr-1"></i> Status:</strong> 
                                    <span class="badge badge-{{ $pengumuman->status === 'aktif' ? 'success' : 'secondary' }}">
                                        {{ ucfirst($pengumuman->status) }}
                                    </span>
                                </p>
                            </div>
                        </div>

                        <hr>

                        <div class="mb-4">
                            <h6 class="mb-2"><strong><i class="fas fa-align-left mr-1"></i> Deskripsi:</strong></h6>
                            <div class="bg-light border rounded p-3" style="white-space: pre-line;">
                                {{ $pengumuman->deskripsi }}
                            </div>
                        </div>

                        @if ($pengumuman->file)
                        <div class="text-center mt-5">
                             <a href="{{ asset('storage/' . $pengumuman->file) }}" class="btn btn-primary btn-sm" target="_blank">
                                <i class="fas fa-file"></i> Lihat File
                            </a>
                        </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
