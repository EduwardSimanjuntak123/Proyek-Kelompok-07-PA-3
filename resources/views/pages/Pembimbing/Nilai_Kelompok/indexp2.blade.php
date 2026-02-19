@extends('layouts.main')
@section('title', 'Nilai Kelompok')

@section('content')
<section class="section custom-section">
    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h4>List Nilai Kelompok</h4>
                    </div> 
                    <div class="card-body">
                        @include('partials.alert')
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nomor Kelompok</th>
                                        <th>Kategori PA</th>
                                        <th>Prodi</th>
                                        <th>Komponen Penilaian </th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($kelompok as $index => $item)
                                        @php
                                            $nilai = $nilaiKelompok[$item->id] ?? null;
                                        @endphp
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $item->nomor_kelompok }}</td>
                                            <td>{{ $item->kategoriPA->kategori_pa }}</td>
                                            <td>{{ $item->prodi->nama_prodi}}</td>
                                            {{-- <td>{{ $item-> }}</td> --}}
                                            <td>
                                                <form method="POST" action="{{ $nilai ? route('pembimbing2.NilaiKelompok.update', $nilai->id) : route('pembimbing2.NilaiKelompok.store') }}">
                                                    @csrf
                                                    @if($nilai)
                                                        @method('PUT')
                                                    @endif
                                                    <input type="hidden" name="kelompok_id" value="{{ $item->id }}">
                                                    <input type="hidden" name="user_id" value="{{ $userId }}">
                                            
                                                    <div class="row" style="width: 100%;">
                                                            <div class="col-12 mb-3 row align-items-center">
                                                                <label class="col-md-8 col-form-label" style="font-size: 12px;">
                                                                    Kualitas Produk: Mencakup seluruh requirements dalam laporan
                                                                </label>
                                                                <div class="col-md-4">
                                                                    <input type="number" name="A11" class="form-control form-control-sm"
                                                                        value="{{ old('A11', $nilai->A11 ?? '') }}" min="0" max="100" required>
                                                                </div>
                                                            </div>
                                                            <div class="col-12 mb-3 row align-items-center">
                                                                <label class="col-md-8 col-form-label" style="font-size: 12px;">
                                                                    Kualitas Produk: Bebas dari error
                                                                </label>
                                                                <div class="col-md-4">
                                                                    <input type="number" name="A12" class="form-control form-control-sm"
                                                                        value="{{ old('A12', $nilai->A12 ?? '') }}" min="0" max="100" required>
                                                                </div>
                                                            </div>
                                                            <div class="col-12 mb-3 row align-items-center">
                                                                <label class="col-md-8 col-form-label" style="font-size: 12px;">
                                                                    Kualitas Produk: Dapat digunakan dengan baik dan mudah
                                                                </label>
                                                                <div class="col-md-4">
                                                                    <input type="number" name="A13" class="form-control form-control-sm"
                                                                        value="{{ old('A13', $nilai->A13 ?? '') }}" min="0" max="100" required>
                                                                </div>
                                                            </div>
                                                            <div class="col-12 mb-3 row align-items-center">
                                                                <label class="col-md-8 col-form-label" style="font-size: 12px;">
                                                                    Kualitas Laporan: Desain menggambarkan produk dengan sesuai
                                                                </label>
                                                                <div class="col-md-4">
                                                                    <input type="number" name="A21" class="form-control form-control-sm"
                                                                        value="{{ old('A21', $nilai->A21 ?? '') }}" min="0" max="100" required>
                                                                </div>
                                                            </div>
                                                            <div class="col-12 mb-3 row align-items-center">
                                                                <label class="col-md-8 col-form-label" style="font-size: 12px;">
                                                                    Kualitas Laporan: Ditulis menurut kaidah bahasa Indonesia yang baik
                                                                </label>
                                                                <div class="col-md-4">
                                                                    <input type="number" name="A22" class="form-control form-control-sm"
                                                                        value="{{ old('A22', $nilai->A22 ?? '') }}" min="0" max="100" required>
                                                                </div>
                                                            </div>
                                                            <div class="col-12 mb-3 row align-items-center">
                                                                <label class="col-md-8 col-form-label" style="font-size: 12px;">
                                                                    Kualitas Laporan: Sesuai kaidah penulisan dokumen dan template
                                                                </label>
                                                                <div class="col-md-4">
                                                                    <input type="number" name="A23" class="form-control form-control-sm"
                                                                        value="{{ old('A23', $nilai->A23 ?? '') }}" min="0" max="100" required>
                                                                </div>
                                                            </div>
                                                        </div>

                                            </td>
                                            <td>
                                                <button class="btn btn-success btn-sm" type="submit" style="height: 40px; padding: 10px 15px;">
                                                    {{ $nilai ? 'Update' : 'Simpan' }}
                                                </button>
                                            </form>
                                                @if($nilai)
                                                    <form method="POST" action="{{ route('pembimbing2.NilaiKelompok.destroy', $nilai->id) }}" class="ml-2" style="display: inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button class="btn btn-danger btn-sm show_confirm" data-toggle="tooltip" title="Hapus" style="height: 40px; padding: 10px 15px;">
                                                            <i class="fas fa-trash-alt"></i> Hapus
                                                        </button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            {{-- Tombol simpan semua bisa ditambahkan kalau semua input dimasukkan ke satu form --}}
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
