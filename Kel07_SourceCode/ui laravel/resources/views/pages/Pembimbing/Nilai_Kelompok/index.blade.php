@extends('layouts.main')
@section('title', 'Nilai Kelompok Pembimbing 1')

<style>
    .btn-action {
        min-width: 38px;
        height: 38px;
        border-radius: 8px;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: box-shadow .2s ease;
    }
    .btn-action:hover { box-shadow: 0 4px 12px rgba(0,0,0,.2); }
    @media (max-width: 768px) {
        .d-flex.justify-content-center { flex-direction: column; }
        .btn-action { width: 100%; }
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
                        <small class="text-muted d-block" style="font-size:0.95rem;">
                            Bobot utama 30% untuk pembimbing. Jika terdapat 2 pembimbing, maka dibagi menjadi
                            20% untuk Pembimbing 1 dan 10% untuk Pembimbing 2.
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
                                        @php $nilai = $nilaiKelompok[$item->id] ?? null; @endphp

                                        <tr>
                                            <td>{{ $item->nomor_kelompok }}</td>
                                            <td>{{ $item->kategoriPA->kategori_pa }}</td>
                                            <td>{{ $item->prodi->nama_prodi }}</td>
                                            <td class="text-center align-middle">
                                                <div class="d-flex justify-content-center align-items-center" style="gap:8px;">
                                                    <button class="btn {{ $nilai ? 'btn-warning' : 'btn-primary' }} btn-action"
                                                        type="button" data-toggle="collapse"
                                                        data-target="#nilai{{ $item->id }}" aria-expanded="false"
                                                        title="{{ $nilai ? 'Edit Nilai' : 'Beri Nilai' }}"
                                                        data-toggle-tooltip="true">
                                                        <i class="fas {{ $nilai ? 'fa-edit' : 'fa-user-check' }}"></i>
                                                    </button>
                                                    @if ($nilai)
                                                        <form action="{{ route('pembimbing1.NilaiKelompok.destroy', $nilai->id) }}" method="POST" class="m-0">
                                                            @csrf @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-action show_confirm" title="Hapus Nilai">
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
                                                            <strong>Form Penilaian Kelompok</strong>
                                                        </div>
                                                        <div class="card-body">
                                                            <form method="POST" action="{{ $nilai ? route('pembimbing1.NilaiKelompok.update', $nilai->id) : route('pembimbing1.NilaiKelompok.store') }}">
                                                                @csrf
                                                                @if ($nilai) @method('PUT') @endif
                                                                <input type="hidden" name="kelompok_id" value="{{ $item->id }}">
                                                                <input type="hidden" name="user_id" value="{{ $userId }}">
                                                                @php
                                                                    $kolomKiri = [
                                                                        ['A11', 'Kualitas Produk: Mencakup seluruh requirements dalam laporan'],
                                                                        ['A12', 'Kualitas Produk: Bebas dari error'],
                                                                        ['A13', 'Kualitas Produk: Dapat digunakan dengan baik dan mudah'],
                                                                    ];
                                                                    $kolomKanan = [
                                                                        ['A21', 'Kualitas Laporan: Desain menggambarkan produk dengan sesuai'],
                                                                        ['A22', 'Kualitas Laporan: Ditulis menurut kaidah bahasa Indonesia yang baik'],
                                                                        ['A23', 'Kualitas Laporan: Sesuai kaidah penulisan dokumen dan template'],
                                                                    ];
                                                                @endphp
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <div class="alert alert-primary"><strong>Kualitas Produk</strong></div>
                                                                        @foreach ($kolomKiri as [$name, $label])
                                                                            <div class="form-group">
                                                                                <label class="font-weight-bold">{{ $label }}</label>
                                                                                <input type="number" name="{{ $name }}" class="form-control"
                                                                                    min="0" max="100" value="{{ old($name, $nilai->$name ?? '') }}" required>
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="alert alert-success"><strong>Kualitas Laporan</strong></div>
                                                                        @foreach ($kolomKanan as [$name, $label])
                                                                            <div class="form-group">
                                                                                <label class="font-weight-bold">{{ $label }}</label>
                                                                                <input type="number" name="{{ $name }}" class="form-control"
                                                                                    min="0" max="100" value="{{ old($name, $nilai->$name ?? '') }}" required>
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                </div>
                                                                <hr>
                                                                <div class="text-right">
                                                                    <button type="submit" class="btn {{ $nilai ? 'btn-warning' : 'btn-success' }}"
                                                                        title="{{ $nilai ? 'Update Nilai' : 'Simpan Nilai' }}">
                                                                        <i class="fas {{ $nilai ? 'fa-edit' : 'fa-save' }}"></i>
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
$(document).ready(function () {
    $('[data-toggle-tooltip="true"]').tooltip({ placement: 'top', trigger: 'hover' });

    $('.show_confirm').on('click', function (e) {
        var form = $(this).closest('form');
        e.preventDefault();
        swal({
            title: 'Yakin ingin menghapus data ini?',
            text: 'Data akan terhapus secara permanen!',
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then(function (willDelete) {
            if (willDelete) form.submit();
        });
    });

    $('.collapse').on('show.bs.collapse', function () {
        $('[data-target="#' + $(this).attr('id') + '"]').find('i')
            .removeClass('fa-user-check fa-edit').addClass('fa-chevron-up');
    });

    $('.collapse').on('hide.bs.collapse', function () {
        var btn = $('[data-target="#' + $(this).attr('id') + '"]');
        btn.find('i').removeClass('fa-chevron-up')
            .addClass(btn.hasClass('btn-warning') ? 'fa-edit' : 'fa-user-check');
    });
});
</script>
@endpush
