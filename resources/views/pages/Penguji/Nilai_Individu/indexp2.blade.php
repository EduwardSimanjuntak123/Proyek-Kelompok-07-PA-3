@extends('layouts.main')
@section('title', 'Nilai Individu')

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
                            <table class="table table-bordered">
                                <thead class="thead-light">
                                    <tr>
                                        {{-- <th>No</th> --}}
                                        <th>Nomor Kelompok</th>
                                        <th>Nama Mahasiswa</th>
                                        <th>Kategori PA</th>
                                        <th>Prodi</th>
                                        <th>Komponen Penilaian</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($kelompoks as $index => $kelompok)
                                        @foreach($kelompok->KelompokMahasiswa as $mhs)
                                            @php
                                                $nilai = $nilaiindividu[$mhs->user_id] ?? null;
                                            @endphp
                                            <tr>
                                                {{-- <td>{{ $index + 1 }}</td> --}}
                                                <td>{{ $kelompok->nomor_kelompok }}</td>
                                                <td>{{ $mhs->nama ?? 'Nama tidak ditemukan' }} ({{ $mhs->nim ?? 'NIM tidak ditemukan' }})</td>
                                                <td>{{ $kelompok->kategoriPA->kategori_pa }}</td>
                                                <td>{{ $kelompok->prodi->nama_prodi}}</td><td>
                                                    <form action="{{ $nilai ? route('penguji2.NilaiIndividu.update', $nilai->id) : route('penguji2.NilaiIndividu.store') }}" method="POST">
                                                        @csrf
                                                        @if($nilai)
                                                            @method('PUT')
                                                        @endif
                                                        <input type="hidden" name="user_id" value="{{ $mhs->user_id }}">
                                                        <input type="hidden" name="penilai_id" value="{{ session('user_id') }}">

                                                        <div class="row">
                                                            @foreach([
                                                                ['B11', 'Kontak mata dengan panelis dan kelompok'],
                                                                ['B12', 'Penggunaan bahasa tubuh dan gesture'],
                                                                ['B13', 'Suara jelas terdengar dengan tempo cukup'],
                                                                ['B14', 'Semangat, senyum dan antusiasme'],
                                                                ['B15', 'Ide dan pembicaraan terstruktur dengan baik'],
                                                                ['B21', 'Slide presentasi mengikuti standar profesional'],
                                                                ['B22', 'Pembagian tugas anggota saat presentasi dan demo'],
                                                                ['B23', 'Isi presentasi dan alur demo terstruktur dengan baik'],
                                                                ['B24', 'Ketepatan waktu presentasi dan demo dengan jadwal'],
                                                                ['B25', 'Penggunaan bahasa Inggris saat presentasi dan demo'],
                                                                ['B31', 'Penguasaan materi dan konsep teknis saat sesi tanya jawab']
                                                            ] as [$name, $label])
                                                               <div class="col-12 mb-3">
                                                                <div class="d-flex align-items-center">
                                                                    <label class="form-label me-2" style="font-size: 12px; width: 60%;">
                                                                        {{ $label }}
                                                                    </label>
                                                                    <input type="number" name="{{ $name }}" 
                                                                        class="form-control form-control-sm" 
                                                                        value="{{ old($name, $nilai->$name ?? '') }}" 
                                                                        min="0" max="100" required 
                                                                        style="width: 100px;">
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                        </div>
                                                        
                                                        
                                                </td>
                                                <td class="text-center align-middle">
                                                    <button type="submit" class="btn btn-sm btn-{{ $nilai ? 'success' : 'primary' }}" style="height: 40px; padding: 10px 15px;">
                                                        {{ $nilai ? 'Update' : 'Simpan' }}
                                                    </button>
                                                    </form>

                                                    @if($nilai)
                                                        <form action="{{ route('penguji2.NilaiIndividu.destroy', $nilai->id) }}" method="POST" style="display:inline;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger show_confirm" style="height: 40px; padding: 10px 15px; margin-top:5px;">
                                                                <i class="fas fa-trash-alt"></i> Hapus
                                                            </button>
                                                        </form>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                </tbody>
                            </table>
                            {{-- Catatan: Tombol "Simpan Semua" bisa dibuat kalau semua form disatukan --}}
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
