<!-- @extends('layouts.main')

@section('title', 'Detail Pengajuan Seminar')

@section('content')
<section class="section custom-section">
    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Detail Pengajuan Seminar</h4>
                    </div>
                    <div class="card-body">
                        @include('partials.alert')

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Kelompok:</strong><br>
                                Kelompok {{ $pengajuan->kelompok->nomor_kelompok }}
                            </div>
                            <div class="col-md-6">
                                <strong>Tanggal Pengajuan:</strong><br>
                                {{ $pengajuan->created_at->format('d/m/Y H:i') }}
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Status:</strong><br>
                                <span class="badge 
                                    @if($pengajuan->status == 'disetujui') badge-success
                                    @elseif($pengajuan->status == 'ditolak') badge-danger
                                    @else badge-warning
                                    @endif">
                                    {{ ucfirst($pengajuan->status) }}
                                </span>
                            </div>
                            <div class="col-md-6">
                                <strong>File Pengajuan:</strong><br>
                                @if($pengajuan->file)
                                    <a href="{{ Storage::url($pengajuan->file) }}" target="_blank" class="btn btn-sm btn-info">
                                        <i class="fas fa-file-download"></i> Unduh File
                                    </a>
                                @else
                                    Tidak ada file
                                @endif
                            </div>
                        </div>

                        @if($pengajuan->catatan)
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <strong>Catatan Penolakan:</strong>
                                <div class="alert alert-danger mt-2">
                                    {{ $pengajuan->catatan }}
                                </div>
                            </div>
                        </div>
                        @endif

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('PembimbingPengajuanSeminar.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>

                            @if($pengajuan->status == 'menunggu')
                            <div class="d-flex">
                                <form method="POST" action="{{ route('PembimbingPengajuanSeminar.setujui', Crypt::encrypt($pengajuan->id)) }}" class="mr-2">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-check"></i> Setujui
                                    </button>
                                </form>

                                <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#tolakModal">
                                    <i class="fas fa-times"></i> Tolak
                                </button>
                            </div>

                            <!-- Modal Penolakan -->
                            <div class="modal fade" id="tolakModal" tabindex="-1" role="dialog" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <form method="POST" action="{{ route('PembimbingPengajuanSeminar.tolak', Crypt::encrypt($pengajuan->id)) }}">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-header">
                                                <h5 class="modal-title">Alasan Penolakan</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label for="catatan">Berikan alasan penolakan:</label>
                                                    <textarea class="form-control" id="catatan" name="catatan" rows="3" required></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-danger">Submit Penolakan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection -->