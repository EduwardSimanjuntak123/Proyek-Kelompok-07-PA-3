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
                            <div class="table-responsive">
                                @if(isset($jadwalUtama))
                        <div class="mb-4">
                            <h5>Jadwal Seminar Kelompok Anda</h5>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <tr>
                                        <th>Nomor Kelompok</th>
                                        <td>{{ $jadwalUtama->kelompok->nomor_kelompok ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Ruangan</th>
                                        <td>{{ $jadwalUtama->ruangan->ruangan }}</td>
                                    </tr>
                                    <tr>
                                        <th>Waktu Mulai</th>
                                        <td>{{ \Carbon\Carbon::parse($jadwalUtama->waktu_mulai)->translatedFormat('l, d F Y - H:i') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Waktu Selesai</th>
                                        <td>{{ \Carbon\Carbon::parse($jadwalUtama->waktu_selesai)->translatedFormat('l, d F Y - H:i') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Penguji</th>
                                        <td>{!! $pengujiNama !!}</td>
                                    </tr>
                                    <tr>
                                        <th>Pembimbing</th>
                                        <td>
                                            @if(!empty($pembimbingNama))
                                                @foreach($pembimbingNama as $nama)
                                                    <div>{{ $nama }}</div>
                                                @endforeach
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        @endif
                        @if(!isset($jadwalUtama))
                        <div class="alert alert-info">
                            Jadwal belum tersedia untuk kelompok Anda.
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
    </script>

    @endpush
