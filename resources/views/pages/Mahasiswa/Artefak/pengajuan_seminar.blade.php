    @extends('layouts.main')
    @section('title', 'Tugas')

    @section('content')
    <section class="section custom-section">
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4>Proyek Akhir</h4>
                        </div>                    
                        <div class="card-body">
                            @include('partials.alert')
                            <div class="row">
                                <div class="col-12">
                                    <ul class="nav nav-tabs mb-4" style="border-bottom: 1px solid #ddd;">
                                        <li class="nav-item">
                                            <a class="nav-link {{--  --}}" href="{{route('artefak.index')}}">
                                                PENGUMPULAN BERKAS
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link {{--  --}}" href="{{route('status_perizinan')}}">
                                                STATUS PERIZINAN MAJU SEMINAR
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link {{--  --}}" href="{{ route('jadwal.seminar')}}">
                                                JADWAL SEMINAR
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            @foreach ($artefak as $item)
                                            <a class="nav-link {{--  --}}" href="{{ route('feedback.show', Crypt::encrypt($item->id)) }}">
                                                FEEDBACK
                                                @endforeach
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link {{--  --}}" href="{{route('revisi.index')}}">
                                                BERKAS FINAL
                                            </a>
                                        </li>
                                    </ul>
                                {{-- Konten Utama --}}
                                @foreach ($pengajuanSeminar as $item)   
                                @if($item->status == 'disetujui')
                                    <div class="card">
                                        <div class="card-body">
                                            <p><strong>Kelompok:</strong> {{ $item->kelompok->nomor_kelompok }}</p>
                                            <div class="border p-3 rounded">
                                                <p><strong>Dosen Pembimbing:</strong></p>
                                                <ul>
                                                    <li>
                                                        @php
                                                            $userId = $item->pembimbing->user_id ?? null;
                                                            $namaDosen = $userId && isset($dosen_map[$userId]) 
                                                                ? $dosen_map[$userId]['nama'] 
                                                                : 'Nama tidak ditemukan';
                                                        @endphp
                                                        {{ $namaDosen }}, <strong>{{ $item->status }}</strong>
                                                        <span class="text-success"><i class="fas fa-check-circle"></i> telah menyetujui untuk maju sidang</span>
                                                    </li>
                                                </ul>
                                                <large class="text-muted">
                                                    Silakan mengumpulkan berkas hardcopy jika pembimbing telah menyetujui Anda untuk maju sidang, baik diizinkan langsung oleh pembimbing atau melalui kaprodi.
                                                </large>
                                            </div>
                                        </div>
                                    </div>
                                @elseif($item->status == 'menunggu')
                                    <div class="alert alert-warning">
                                        Status pengajuan kelompok {{ $item->kelompok->nomor_kelompok }} belum disetujui oleh pembimbing.
                                    </div>
                                  @elseif($item->status == 'ditolak')
                                    <div class="alert alert-warning">
                                        Status pengajuan kelompok {{ $item->kelompok->nomor_kelompok }} ditolak dengan ALasan: {{ $item->catatan }}.
                                    </div>
                                @else
                                <div class="alert alert-info">
                                    Status pengajuan kelompok {{ $item->kelompok->nomor_kelompok }} belum Request.
                                </div>
                                @endif
                            @endforeach         
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
