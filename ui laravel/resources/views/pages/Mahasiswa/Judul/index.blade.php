    @extends('layouts.main')
    @section('title', 'Tugas')

    @section('content')
        <section class="section custom-section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4>Judul PA</h4>
                            </div>
                            {{-- Konten Utama --}}
                            <div class="card">
                                <div class="card-body">

                                    <div class="card mb-4 shadow-sm border">
                                        @if ($judul22)
                                            <div class="card mb-4 shadow-sm border">
                                                <div class="card-body">

                                                    <h5 class="card-title font-weight-bold">
                                                        {{ $judul->judul }}
                                                    </h5>

                                                    <p class="mb-2">
                                                        {{ $judul->deskripsi }}
                                                    </p>

                                                </div>
                                            </div>
                                        @else
                                            <div class="alert alert-info">
                                                Belum ada pengajuan judul proyek akhir.
                                            </div>
                                        @endif
                                    </div>



                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            </div>
            </div>
            </div>
        </section>
    @endsection
