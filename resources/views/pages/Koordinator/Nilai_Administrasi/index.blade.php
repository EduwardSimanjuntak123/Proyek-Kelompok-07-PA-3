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
                                        <th>Komponen Penilaian </th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($kelompok as $index => $item)
                                        @php
                                            $nilai = $nilaiAdministrasi[$item->id] ?? null;
                                        @endphp
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $item->nomor_kelompok }}</td>
                                            <td>
                                                <form method="POST" action="{{ $nilai ? route('koordinator.NilaiAdministrasi.update', $nilai->id) : route('koordinator.NilaiAdministrasi.store') }}">
                                                    @csrf
                                                    @if($nilai)
                                                        @method('PUT')
                                                    @endif
                                                    <input type="hidden" name="kelompok_id" value="{{ $item->id }}">
                                                    <input type="hidden" name="user_id" value="{{ $userId }}">
                                            
                                                    <div class="row" style="width: 100%;">
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label" style="font-size: 12px;">Nilai Administrasi</label>
                                                            <input type="number" name="Administrasi" class="form-control form-control-sm" value="{{ old('Administrasi', $nilai->Administrasi ?? '') }}" min="0" max="100" required>
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label" style="font-size: 12px;">Nilai Pameran</label>
                                                            <input type="number" name="Pameran" class="form-control form-control-sm" value="{{ old('Pameran', $nilai->Pameran ?? '') }}" min="0" max="100" required>
                                                        </div>
                                                    </div>
                                                    
                                            </td>
                                            <td>
                                                <button class="btn btn-success btn-sm" type="submit" style="height: 40px; padding: 10px 15px;">
                                                    {{ $nilai ? 'Update' : 'Simpan' }}
                                                </button>
                                            </form>
                                                @if($nilai)
                                                    <form method="POST" action="{{ route('koordinator.NilaiAdministrasi.destroy', $nilai->id) }}" class="ml-2" style="display: inline;">
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
