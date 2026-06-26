@extends('layouts.main')
@section('title', 'Nilai Kelompok')

@section('content')
    <section class="section custom-section">
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <h4>Daftar Nilai Akhir Mahasiswa</h4>
                        </div>
                        <div class="card-body">
                            @include('partials.alert')
                            <div class="table-responsive">
                                <div class="container">
                                    <h3>Daftar Nilai Akhir Mahasiswa</h3>
                                    <a href="{{ route('nilai.akhir.export', ['prodi_id' => $prodi_id, 'KPA_id' => $KPA_id, 'TM_id' => $TM_id]) }}"
                                        class="btn btn-success mb-3">Export ke Excel</a>

                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Kelompok</th>
                                                <th>Nama</th>
                                                <th>NIM</th>
                                                <th>Nilai Administrasi 10%</th>
                                                <th>Nilai Pameran 5%</th>
                                                <th>Nilai Seminar 45%</th>
                                                <th>Nilai Bimbingan 40%</th>
                                                <th>Nilai Akhir</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($nilai_akhir as $index => $nilai)
                                                @php
                                                    $mhs = $mahasiswa[$nilai->user_id] ?? null;
                                                @endphp
                                                <tr>
                                                    <td>{{ $nilai->nomor_kelompok ?? '-' }}</td>
                                                    <td>{{ $mhs['nama'] ?? '-' }}</td>
                                                    <td>{{ $mhs['nim'] ?? '-' }}</td>
                                                    <td>{{ $nilai->Administrasi ?? 0 }}</td>
                                                    <td>{{ $nilai->Pameran ?? 0 }}</td>
                                                    <td>{{ $nilai->nilai_seminar ?? 0 }}</td>
                                                    <td>{{ number_format($nilai->rata_bimbingan ?? 0, 2) }}</td>
                                                    <td>{{ number_format($nilai->nilai_akhir, 2) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
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
        window.location.reload = "{{ route('pembimbing.Nilaiseminar.index') }}";
        $('.show_confirm').click(function(event) {
            var form = $(this).closest("form");
            event.preventDefault();
            swal({
                    title: "Yakin ingin menghapus data ini?",
                    text: "Data akan terhapus secara permanen!",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                .then((willDelete) => {
                    if (willDelete) {
                        form.submit();
                    }
                });
        });
    </script>
@endpush
