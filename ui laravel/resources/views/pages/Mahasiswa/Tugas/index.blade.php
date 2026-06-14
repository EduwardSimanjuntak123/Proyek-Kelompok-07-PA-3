    @extends('layouts.main')
    @section('title', 'Tugas')

    @section('content')
        <section class="section custom-section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4>List Progres</h4>
                            </div>
                            {{-- Konten Utama --}}
                            <div class="card">
                                <div class="card-body">
                                    @foreach ($requestJudul as $item)
                                        <div class="card mb-4 shadow-sm border">
                                            <div class="card-body">

                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div>
                                                        <h5 class="font-weight-bold text-primary">
                                                            {{ $item->judul }}
                                                        </h5>

                                                        <p class="text-muted mb-2">
                                                            {{ Str::limit($item->deskripsi, 150) }}
                                                        </p>
                                                    </div>

                                                    <span
                                                        class="badge
                @if ($item->status == 'Disetujui') badge-success
                @elseif($item->status == 'Ditolak') badge-danger
                @elseif($item->status == 'Revisi') badge-warning
                @else badge-secondary @endif">
                                                        {{ $item->status }}
                                                    </span>
                                                </div>

                                                <hr>

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <small class="text-muted">Tanggal Pengajuan</small>
                                                        <div>
                                                            {{ \Carbon\Carbon::parse($item->created_at)->format('d M Y') }}
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <small class="text-muted">Dosen Pembimbing</small>
                                                        <div>
                                                            {{ $item->dosen->nama ?? '-' }}
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="mt-3">
                                                    <a href="{{ route('judul.show', Crypt::encrypt($item->id)) }}"
                                                        class="btn btn-primary btn-sm">
                                                        Detail Pengajuan
                                                    </a>
                                                </div>

                                            </div>
                                        </div>
                                    @endforeach

                                    @if ($tugas->isEmpty())
                                        <div class="alert alert-info">Tidak ada tugas yang tersedia.</div>
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
        </section>
    @endsection

    @push('script')
        <script type="text/javascript">
            $(document).on('click', '.show_confirm', function(event) {
                event.preventDefault();
                var form = $(this).closest("form");
                swal({
                    title: "Yakin ingin menghapus data ini?",
                    text: "Data akan terhapus secara permanen!",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                }).then((willDelete) => {
                    if (willDelete) {
                        form.submit();
                    }
                });
            });

            function updateCountdown() {
                const countdownEls = document.querySelectorAll('.countdown');

                countdownEls.forEach(el => {
                    const deadline = new Date(el.dataset.deadline).getTime();
                    const now = new Date().getTime();
                    const diff = deadline - now;

                    if (diff > 0) {
                        const days = Math.floor(diff / (1000 * 60 * 60 * 24));
                        const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                        const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));

                        if (days > 0) {
                            el.textContent = `${days} hari ${hours} jam lagi`;
                        } else {
                            el.textContent = `${hours} jam ${minutes} menit lagi`;
                        }
                    } else {
                        const absDiff = Math.abs(diff);
                        const hours = Math.floor((absDiff) / (1000 * 60 * 60));
                        const minutes = Math.floor((absDiff % (1000 * 60 * 60)) / (1000 * 60));
                        el.textContent = `Selesai ${hours} jam ${minutes} menit yang lalu`;
                        el.classList.remove('text-warning');
                        el.classList.add('text-success');
                    }
                });
            }

            updateCountdown();
            setInterval(updateCountdown, 60000); // update tiap 1 menit
        </script>
    @endpush
