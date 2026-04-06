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
                                @foreach ($tugas as $item)
                                    @php
                                        $status = $statusByTugas->get($item->id);
                                    @endphp
                                    <div class="card mb-4 shadow-sm border">
                                        <div class="card-body">
                                            <h5 class="card-title font-weight-bold">{{ $item->Judul_Tugas }}</h5>
                                            <p class="mb-2">{{ $item->Deskripsi_Tugas }}</p>
                                            <ul class="list-unstyled">
                                                <li>
                                                    <strong>Deadline:</strong> 
                                                    <span class="text-danger">{{ $item->formatted_deadline }}</span>
                                                </li>
                                            </ul>
                                            <div class="mb-2">
                                                <span class="badge badge-{{ $status ? 'success' : 'secondary' }}">
                                                    {{ $status ? $status->status : 'Belum dikumpulkan' }}
                                                </span>
                                                <a href="{{ route('Mahasiswa.tugas.create', Crypt::encrypt($item->id)) }}" class="btn btn-sm btn-primary ml-2">Lihat Detail</a>
                                            </div>
                                            <div class="mb-2 {{ $item->status_class }}">
                                                ⏳ <span class="countdown" data-deadline="{{ $item->tanggal_pengumpulan }}"></span>
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
