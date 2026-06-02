@extends('layouts.main')
@section('title', 'Nilai Individu Pembimbing 2')
<style>
    .toggle-collapse,
    .show_confirm {
        min-width: 115px;
        height: 38px;
        border-radius: 8px;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all .2s ease;
    }

    /* Beri Nilai */
    .btn-primary.toggle-collapse:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(13, 110, 253, .25);
    }

    /* Edit Nilai */
    .btn-success.toggle-collapse:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(25, 135, 84, .25);
    }

    /* Hapus */
    .btn-danger.show_confirm:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(220, 53, 69, .25);
    }

    .toggle-collapse i,
    .show_confirm i {
        margin-right: 5px;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .d-flex.justify-content-center {
            flex-direction: column;
        }

        .toggle-collapse,
        .show_confirm {
            width: 100%;
        }
    }
</style>

@section('content')
    <section class="section custom-section">
        <div class="section-body">
            <div class="row">
                <div class="col-12">

                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <h4>List Nilai Individu Pembimbing 2</h4>
                        </div>

                        <div class="card-body">

                            @include('partials.alert')

                            <div class="table-responsive">

                                <table class="table table-bordered table-hover">

                                    <thead class="thead-light">
                                        <tr>
                                            <th>Nomor Kelompok</th>
                                            <th>Mahasiswa</th>
                                            <th>Kategori PA</th>
                                            <th>Prodi</th>
                                            <th width="180">Aksi</th>
                                        </tr>
                                    </thead>

                                    <tbody>

                                        @foreach ($kelompoks as $kelompok)
                                            @foreach ($kelompok->KelompokMahasiswa as $mhs)
                                                @php
                                                    $nilai = $nilaiindividu[$mhs->user_id] ?? null;
                                                @endphp

                                                {{-- ROW DATA --}}
                                                <tr>

                                                    <td>
                                                        {{ $kelompok->nomor_kelompok }}
                                                    </td>

                                                    <td>
                                                        <strong>
                                                            {{ $mhs->nama ?? 'Nama tidak ditemukan' }}
                                                        </strong>
                                                        -
                                                        <small class="text-muted">
                                                            {{ $mhs->nim ?? 'NIM tidak ditemukan' }}
                                                        </small>
                                                    </td>

                                                    <td>
                                                        {{ $kelompok->kategoriPA->kategori_pa }}
                                                    </td>

                                                    <td>
                                                        {{ $kelompok->prodi->nama_prodi }}
                                                    </td>

                                                    <td class="text-center align-middle">

                                                        <div class="d-flex justify-content-center align-items-center"
                                                            style="gap:8px;">

                                                            <button
                                                                class="btn btn-sm {{ $nilai ? 'btn-success' : 'btn-primary' }} toggle-collapse d-flex align-items-center"
                                                                type="button" data-toggle="collapse"
                                                                data-target="#nilai{{ $mhs->user_id }}"
                                                                aria-expanded="false">

                                                                <i class="fas fa-chevron-down mr-1"></i>

                                                                {{ $nilai ? 'Edit Nilai' : 'Beri Nilai' }}

                                                            </button>

                                                            @if ($nilai)
                                                                <form
                                                                    action="{{ route('pembimbing2.NilaiIndividu.destroy', $nilai->id) }}"
                                                                    method="POST" class="m-0">

                                                                    @csrf
                                                                    @method('DELETE')

                                                                    <button type="submit"
                                                                        class="btn btn-sm btn-danger show_confirm">

                                                                        <i class="fas fa-trash-alt mr-1"></i>
                                                                        Hapus

                                                                    </button>

                                                                </form>
                                                            @endif

                                                        </div>

                                                    </td>

                                                </tr>

                                                {{-- COLLAPSE FORM --}}
                                                <tr>

                                                    <td colspan="5" class="p-0 border-0">

                                                        <div class="collapse" id="nilai{{ $mhs->user_id }}">

                                                            <div class="card m-3 shadow-sm">

                                                                <div class="card-header bg-primary text-white">

                                                                    <strong>
                                                                        Form Penilaian
                                                                    </strong>

                                                                </div>

                                                                <div class="card-body">

                                                                    <form
                                                                        action="{{ $nilai ? route('pembimbing1.NilaiIndividu.update', $nilai->id) : route('pembimbing1.NilaiIndividu.store') }}"
                                                                        method="POST">

                                                                        @csrf

                                                                        @if ($nilai)
                                                                            @method('PUT')
                                                                        @endif

                                                                        <input type="hidden" name="user_id"
                                                                            value="{{ $mhs->user_id }}">

                                                                        <input type="hidden" name="penilai_id"
                                                                            value="{{ session('user_id') }}">

                                                                        <input type="hidden" name="role_id" value="3">

                                                                        @php
                                                                            $kolomKiri = [
                                                                                [
                                                                                    'B11',
                                                                                    'Kontak mata dengan panelis dan kelompok',
                                                                                ],
                                                                                [
                                                                                    'B12',
                                                                                    'Penggunaan bahasa tubuh dan gesture',
                                                                                ],
                                                                                [
                                                                                    'B13',
                                                                                    'Suara jelas terdengar dengan tempo cukup',
                                                                                ],
                                                                                [
                                                                                    'B14',
                                                                                    'Semangat, senyum dan antusiasme',
                                                                                ],
                                                                                [
                                                                                    'B15',
                                                                                    'Ide dan pembicaraan terstruktur dengan baik',
                                                                                ],
                                                                            ];

                                                                            $kolomKanan = [
                                                                                [
                                                                                    'B21',
                                                                                    'Slide presentasi mengikuti standar profesional',
                                                                                ],
                                                                                [
                                                                                    'B22',
                                                                                    'Pembagian tugas anggota saat presentasi dan demo',
                                                                                ],
                                                                                [
                                                                                    'B23',
                                                                                    'Isi presentasi dan alur demo terstruktur dengan baik',
                                                                                ],
                                                                                [
                                                                                    'B24',
                                                                                    'Ketepatan waktu presentasi dan demo dengan jadwal',
                                                                                ],
                                                                                [
                                                                                    'B25',
                                                                                    'Penggunaan bahasa Inggris saat presentasi dan demo',
                                                                                ],
                                                                                [
                                                                                    'B31',
                                                                                    'Penguasaan materi dan konsep teknis saat sesi tanya jawab',
                                                                                ],
                                                                            ];
                                                                        @endphp

                                                                        <div class="row">

                                                                            {{-- KOLOM KIRI --}}
                                                                            <div class="col-md-6">

                                                                                <div class="alert alert-primary">
                                                                                    <strong>
                                                                                        Komunikasi dan Presentasi
                                                                                    </strong>
                                                                                </div>

                                                                                @foreach ($kolomKiri as [$name, $label])
                                                                                    <div class="form-group">

                                                                                        <label class="font-weight-bold">
                                                                                            {{ $label }}
                                                                                        </label>

                                                                                        <input type="number"
                                                                                            name="{{ $name }}"
                                                                                            class="form-control"
                                                                                            min="0" max="100"
                                                                                            value="{{ old($name, $nilai->$name ?? '') }}"
                                                                                            required>

                                                                                    </div>
                                                                                @endforeach

                                                                            </div>

                                                                            {{-- KOLOM KANAN --}}
                                                                            <div class="col-md-6">

                                                                                <div class="alert alert-success">
                                                                                    <strong>
                                                                                        Presentasi, Demonstrasi dan Tanya
                                                                                        Jawab
                                                                                    </strong>
                                                                                </div>

                                                                                @foreach ($kolomKanan as [$name, $label])
                                                                                    <div class="form-group">

                                                                                        <label class="font-weight-bold">
                                                                                            {{ $label }}
                                                                                        </label>

                                                                                        <input type="number"
                                                                                            name="{{ $name }}"
                                                                                            class="form-control"
                                                                                            min="0" max="100"
                                                                                            value="{{ old($name, $nilai->$name ?? '') }}"
                                                                                            required>

                                                                                    </div>
                                                                                @endforeach

                                                                            </div>

                                                                        </div>

                                                                        <hr>

                                                                        <div class="text-right">

                                                                            <button type="submit" class="btn btn-success">

                                                                                <i class="fas fa-save"></i>

                                                                                {{ $nilai ? 'Update Nilai' : 'Simpan Nilai' }}

                                                                            </button>

                                                                        </div>

                                                                    </form>

                                                                </div>

                                                            </div>

                                                        </div>

                                                    </td>

                                                </tr>
                                            @endforeach
                                        @endforeach

                                    </tbody>

                                </table>

                            </div>

                        </div>

                    </div>

                </div>
            </div>
        </div>
    </section>
@endsection

@push('script')
    <script>
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

        $('.collapse').on('show.bs.collapse', function() {

            let button = $('[data-target="#' + $(this).attr('id') + '"]');

            button.find('i')
                .removeClass('fa-chevron-down')
                .addClass('fa-chevron-up');

        });

        $('.collapse').on('hide.bs.collapse', function() {

            let button = $('[data-target="#' + $(this).attr('id') + '"]');

            button.find('i')
                .removeClass('fa-chevron-up')
                .addClass('fa-chevron-down');

        });

        $('.collapse').on('show.bs.collapse', function() {
            let button = $('[data-target="#' + $(this).attr('id') + '"]');

            button.find('i')
                .removeClass('fa-chevron-down')
                .addClass('fa-chevron-up');
        });

        $('.collapse').on('hide.bs.collapse', function() {
            let button = $('[data-target="#' + $(this).attr('id') + '"]');

            button.find('i')
                .removeClass('fa-chevron-up')
                .addClass('fa-chevron-down');
        });
    </script>
@endpush
