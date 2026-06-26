@extends('layouts.main')
@section('title', 'List Tahun Ajaran')

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
                                        <th>Nilai</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($mahasiswa as $index => $item)
                                        @php
                                            $nilai = $nilaiKelompok[$item->id] ?? null;
                                        @endphp
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $item->nomor_kelompok }}</td>
                                            
                                            <td>
                                                <form method="POST" action="{{ $nilai ? route('NilaiKelompok.update', $nilai->id) : route('NilaiKelompok.store') }}" class="d-flex">
                                                    @csrf
                                                    @if($nilai)
                                                        @method('PUT')
                                                    @endif
                                                    <input type="hidden" name="kelompok_id" value="{{ $item->id }}">
                                                    <input type="hidden" name="user_id" value="{{ $userId }}">
                                                    <input type="number" name="Nilai" class="form-control form-control-sm" value="{{ old('Nilai', $nilai->Nilai ?? '') }}" min="0" max="100" required>
                                            </td>
                                            
                                            <td class="d-flex">
                                                    <button class="btn btn-success btn-sm" type="submit" style="height: 30px">
                                                        {{ $nilai ? 'Update' : 'Simpan' }}
                                                    </button>
                                                </form>
                                                
                                                @if($nilai)
                                                    <form method="POST" action="{{ route('NilaiKelompok.destroy', $nilai->id) }}" class="ml-2">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button class="btn btn-danger btn-sm show_confirm" data-toggle="tooltip" title='Hapus'>
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
