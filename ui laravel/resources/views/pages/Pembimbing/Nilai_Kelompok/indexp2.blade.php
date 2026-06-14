@extends('layouts.main')

@section('title', 'Nilai Kelompok Pembimbing 2')

<style>
    /* ==============================
   BUTTON ACTION FIX NO SHAKE
============================== */

    .action-btn {
        width: 42px;
        height: 38px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: none;
        transition:
            box-shadow .2s ease,
            filter .2s ease;
        position: relative;
    }
    /* icon */
    .action-btn i {
        margin: 0;
    }
    /* hover stabil */
    .action-btn:hover {
        filter: brightness(1.08);
    }
    /* shadow tiap warna */
    .btn-primary.action-btn:hover {
        box-shadow: 0 5px 14px rgba(13, 110, 253, .25);
    }
    .btn-warning.action-btn:hover {
        box-shadow: 0 5px 14px rgba(255, 193, 7, .25);
    }
    .btn-success.action-btn:hover {
        box-shadow: 0 5px 14px rgba(25, 135, 84, .25);
    }
    .btn-danger.action-btn:hover {
        box-shadow: 0 5px 14px rgba(220, 53, 69, .25);
    }
    /* collapse card */
    .collapse .card {
        border-radius: 10px;
        overflow: hidden;
    }
    @media(max-width:768px) {
        .action-btn {
            width: 100%;
            margin-top: 5px;
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
                            <h4>List Nilai Kelompok Pembimbing 2 (10%)</h4>
                        </div>
                        <div class="card-body">
                            @include('partials.alert')
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>
                                                Nomor Kelompok
                                            </th>


                                            <th>
                                                Kategori PA
                                            </th>


                                            <th>
                                                Prodi
                                            </th>


                                            <th width="180">
                                                Aksi
                                            </th>


                                        </tr>

                                    </thead>



                                    <tbody>



                                        @foreach ($kelompok as $item)
                                            @php

                                                $nilai = $nilaiKelompok[$item->id] ?? null;

                                            @endphp



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



                                                <td class="text-center">


                                                    <div class="d-flex justify-content-center" style="gap:8px;">



                                                        <button
                                                            class="
btn
{{ $nilai ? 'btn-warning' : 'btn-primary' }}
action-btn
toggle-collapse
"
                                                            type="button" data-toggle="collapse"
                                                            data-target="#nilai{{ $item->id }}"
                                                            title="{{ $nilai ? 'Edit Nilai' : 'Beri Nilai' }}">


                                                            <i class="fas {{ $nilai ? 'fa-edit' : 'fa-user-check' }}"></i>


                                                        </button>





                                                        @if ($nilai)
                                                            <form
                                                                action="{{ route('pembimbing2.NilaiKelompok.destroy', $nilai->id) }}"
                                                                method="POST" class="m-0">


                                                                @csrf

                                                                @method('DELETE')



                                                                <button type="submit"
                                                                    class="btn btn-danger action-btn show_confirm"
                                                                    title="Hapus Nilai">


                                                                    <i class="fas fa-trash-alt"></i>


                                                                </button>



                                                            </form>
                                                        @endif





                                                    </div>


                                                </td>



                                            </tr>






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
                                                                    action="{{ $nilai ? route('pembimbing2.NilaiKelompok.update', $nilai->id) : route('pembimbing2.NilaiKelompok.store') }}">


                                                                    @csrf


                                                                    @if ($nilai)
                                                                        @method('PUT')
                                                                    @endif




                                                                    <input type="hidden" name="kelompok_id"
                                                                        value="{{ $item->id }}">


                                                                    <input type="hidden" name="user_id"
                                                                        value="{{ $userId }}">




                                                                    <div class="row">



                                                                        <div class="col-md-6">


                                                                            <div class="alert alert-primary">

                                                                                <b>
                                                                                    Kualitas Produk
                                                                                </b>

                                                                            </div>



                                                                            @foreach ([
            'A11' => 'Kualitas Produk: Mencakup seluruh requirements dalam laporan',
            'A12' => 'Kualitas Produk: Bebas dari error',
            'A13' => 'Kualitas Produk: Dapat digunakan dengan baik dan mudah',
        ] as $key => $label)
                                                                                <div class="form-group">


                                                                                    <label class="font-weight-bold">

                                                                                        {{ $label }}

                                                                                    </label>


                                                                                    <input type="number"
                                                                                        class="form-control"
                                                                                        name="{{ $key }}"
                                                                                        min="0" max="100"
                                                                                        required
                                                                                        value="{{ old($key, $nilai->$key ?? '') }}">


                                                                                </div>
                                                                            @endforeach



                                                                        </div>






                                                                        <div class="col-md-6">


                                                                            <div class="alert alert-success">

                                                                                <b>
                                                                                    Kualitas Laporan
                                                                                </b>

                                                                            </div>



                                                                            @foreach ([
            'A21' => 'Kualitas Laporan: Desain menggambarkan produk dengan sesuai',
            'A22' => 'Kualitas Laporan: Ditulis menurut kaidah bahasa Indonesia yang baik',
            'A23' => 'Kualitas Laporan: Sesuai kaidah penulisan dokumen dan template',
        ] as $key => $label)
                                                                                <div class="form-group">


                                                                                    <label class="font-weight-bold">

                                                                                        {{ $label }}

                                                                                    </label>



                                                                                    <input type="number"
                                                                                        class="form-control"
                                                                                        name="{{ $key }}"
                                                                                        min="0" max="100"
                                                                                        required
                                                                                        value="{{ old($key, $nilai->$key ?? '') }}">


                                                                                </div>
                                                                            @endforeach



                                                                        </div>



                                                                    </div>




                                                                    <hr>




                                                                    <div class="text-right">



                                                                        <button type="submit"
                                                                            class="
btn
{{ $nilai ? 'btn-warning' : 'btn-success' }}
action-btn
">


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


            $('.show_confirm').click(function(e) {


                e.preventDefault();


                let form = $(this).closest("form");


                swal({

                    title: "Yakin ingin menghapus data ini?",

                    text: "Data akan terhapus permanen!",

                    icon: "warning",

                    buttons: true,

                    dangerMode: true,


                }).then((ok) => {


                    if (ok) {

                        form.submit();

                    }


                });


            });



        });



        $('.collapse').on('show.bs.collapse', function() {


            let button = $('[data-target="#' + this.id + '"]');


            button.find('i')

                .removeClass('fa-user-check fa-chevron-down')

                .addClass('fa-chevron-up');


        });




        $('.collapse').on('hide.bs.collapse', function() {


            let button = $('[data-target="#' + this.id + '"]');


            button.find('i')

                .removeClass('fa-chevron-up')

                .addClass('fa-user-check');


        });
    </script>
@endpush
