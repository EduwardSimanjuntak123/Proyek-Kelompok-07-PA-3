@extends('layouts.main')
@section('title', 'Nilai Kelompok Pembimbing 1')

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
                        <div class="card-header">
                            <h4 class="mb-0">List Nilai Kelompok Pembimbing (30%)</h4>
                        </div>
                        <div class="card-body pt-2">
                            <small class="text-muted d-block" style="font-size: 0.95rem;">
                                Bobot utama 30% untuk pembimbing. Jika terdapat 2 pembimbing, maka dibagi menjadi 20% untuk
                                Pembimbing 1 dan 10% untuk Pembimbing 2.
                            </small>
                        </div>

                        <div class="card-body">

                            @include('partials.alert')

                            <div class="table-responsive">

                                <table class="table table-bordered table-hover">

                                    <thead class="thead-light">
                                        <tr>
                                            <th>Nomor Kelompok</th>
                                            <th>Kategori PA</th>
                                            <th>Prodi</th>
                                            <th width="180">Aksi</th>
                                        </tr>
                                    </thead>

                                    <tbody>

                                        @foreach ($kelompok as $item)
                                            @php
                                                $nilai = $nilaiKelompok[$item->id] ?? null;

                                                $komponen = ['A11', 'A12', 'A13', 'A21', 'A22', 'A23'];

                                                $terisi = 0;

                                                if ($nilai) {
                                                    foreach ($komponen as $k) {
                                                        if (!is_null($nilai->$k)) {
                                                            $terisi++;
                                                        }
                                                    }
                                                }
                                            @endphp

                                            {{-- ROW DATA --}}
                                            <tr>

                                                <td>
                                                    {{ $item->nomor_kelompok }}
                                                </td>

                                                <td>
                                                    {{ $item->kategoriPA->kategori_pa }}
                                                </td>

                                                <td>
                                                    {{ $item->prodi->nama_prodi }}
                                                </td>

                                                <td class="text-center align-middle">

                                                    <div class="d-flex justify-content-center align-items-center"
                                                        style="gap:8px;">

                                                        <button
                                                            class="btn {{ $nilai ? 'btn-warning' : 'btn-primary' }} tooltip-collapse"
                                                            type="button" data-toggle="collapse"
                                                            data-target="#nilai{{ $item->id }}" aria-expanded="false"
                                                            data-title="{{ $nilai ? 'Edit Nilai' : 'Beri Nilai' }}">
                                                            <i class="fas {{ $nilai ? 'fa-edit' : 'fa-user-check' }}"></i>
                                                        </button>

                                                        @if ($nilai)
                                                            <form
                                                                action="{{ route('pembimbing1.NilaiKelompok.destroy', $nilai->id) }}"
                                                                method="POST" class="m-0">

                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit"
                                                                    class="btn btn-sm btn-danger show_confirm"
                                                                    data-toggle="tooltip" data-placement="top"
                                                                    title="Hapus Nilai">
                                                                    <i class="fas fa-trash-alt"></i>
                                                                </button>

                                                            </form>
                                                        @endif

                                                    </div>

                                                </td>

                                            </tr>

                                            {{-- COLLAPSE FORM --}}
                                            <tr>

                                                <td colspan="4" class="p-0 border-0">

                                                    <div class="collapse" id="nilai{{ $item->id }}">

                                                        <div class="card m-3 shadow-sm">

                                                            <div class="card-header bg-primary text-white">

                                                                <strong>
                                                                    Form Penilaian Kelompok
                                                                </strong>

                                                            </div>

                                                            <div class="card-body">

                                                                <form method="POST"
                                                                    action="{{ $nilai ? route('pembimbing1.NilaiKelompok.update', $nilai->id) : route('pembimbing1.NilaiKelompok.store') }}">

                                                                    @csrf

                                                                    @if ($nilai)
                                                                        @method('PUT')
                                                                    @endif

                                                                    <input type="hidden" name="kelompok_id"
                                                                        value="{{ $item->id }}">

                                                                    <input type="hidden" name="user_id"
                                                                        value="{{ $userId }}">

                                                                    @php

                                                                        $kolomKiri = [
                                                                            [
                                                                                'A11',
                                                                                'Kualitas Produk: Mencakup seluruh requirements dalam laporan',
                                                                            ],
                                                                            [
                                                                                'A12',
                                                                                'Kualitas Produk: Bebas dari error',
                                                                            ],
                                                                            [
                                                                                'A13',
                                                                                'Kualitas Produk: Dapat digunakan dengan baik dan mudah',
                                                                            ],
                                                                        ];

                                                                        $kolomKanan = [
                                                                            [
                                                                                'A21',
                                                                                'Kualitas Laporan: Desain menggambarkan produk dengan sesuai',
                                                                            ],
                                                                            [
                                                                                'A22',
                                                                                'Kualitas Laporan: Ditulis menurut kaidah bahasa Indonesia yang baik',
                                                                            ],
                                                                            [
                                                                                'A23',
                                                                                'Kualitas Laporan: Sesuai kaidah penulisan dokumen dan template',
                                                                            ],
                                                                        ];

                                                                    @endphp

                                                                    <div class="row">

                                                                        {{-- KOLOM KIRI --}}
                                                                        <div class="col-md-6">

                                                                            <div class="alert alert-primary">
                                                                                <strong>
                                                                                    Kualitas Produk
                                                                                </strong>
                                                                            </div>

                                                                            @foreach ($kolomKiri as [$name, $label])
                                                                                <div class="form-group">

                                                                                    <label class="font-weight-bold">
                                                                                        {{ $label }}
                                                                                    </label>

                                                                                    <input type="number"
                                                                                        name="{{ $name }}"
                                                                                        class="form-control" min="0"
                                                                                        max="100"
                                                                                        value="{{ old($name, $nilai->$name ?? '') }}"
                                                                                        required>

                                                                                </div>
                                                                            @endforeach

                                                                        </div>

                                                                        {{-- KOLOM KANAN --}}
                                                                        <div class="col-md-6">

                                                                            <div class="alert alert-success">
                                                                                <strong>
                                                                                    Kualitas Laporan
                                                                                </strong>
                                                                            </div>

                                                                            @foreach ($kolomKanan as [$name, $label])
                                                                                <div class="form-group">

                                                                                    <label class="font-weight-bold">
                                                                                        {{ $label }}
                                                                                    </label>

                                                                                    <input type="number"
                                                                                        name="{{ $name }}"
                                                                                        class="form-control" min="0"
                                                                                        max="100"
                                                                                        value="{{ old($name, $nilai->$name ?? '') }}"
                                                                                        required>

                                                                                </div>
                                                                            @endforeach

                                                                        </div>

                                                                    </div>

                                                                    <hr>

                                                                    <div class="text-right">
                                                                        <button type="submit"
                                                                            class="btn {{ $nilai ? 'btn-warning' : 'btn-success' }}"
                                                                            data-toggle="tooltip" data-placement="top"
                                                                            title="{{ $nilai ? 'Update Nilai' : 'Simpan Nilai' }}">
                                                                            <i
                                                                                class="fas {{ $nilai ? 'fa-edit' : 'fa-save' }}"></i>
                                                                        </button>
                                                                    </div>

                                                                </form>

                                                            </div>

                                                        </div>

                                                    </div>

                                                </td>

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
    </section>
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            $('.tooltip-collapse').each(function() {
                $(this).tooltip({
                    title: $(this).attr('data-title'),
                    placement: 'top',
                    trigger: 'hover'
                });
            });
        });


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
