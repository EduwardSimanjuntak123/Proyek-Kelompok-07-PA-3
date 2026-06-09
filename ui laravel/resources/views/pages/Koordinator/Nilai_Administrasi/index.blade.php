@extends('layouts.main')
@section('title', 'Nilai Administrasi')

@section('content')
    <section class="section custom-section">
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>List Nilai Administrasi Kelompok</h4>
                        </div>
                        <div class="card-body p-0">
                            @include('partials.alert')
                            <div class="table-responsive">
                                <table class="table table-bordered align-middle mb-0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th width="18%">Nomor Kelompok</th>
                                            <th>Nilai Administrasi Kelompok</th>
                                            <th width="15%" class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($kelompok as $item)
                                            @php
                                                $nk = $nilaiKelompok[$item->id] ?? null;
                                                $anggota = $item->KelompokMahasiswa;
                                                $isEdit = $nk !== null;
                                            @endphp

                                            {{-- ══ ROW UTAMA ══ --}}
                                            <tr>
                                                {{-- Nomor Kelompok --}}
                                                <td class="align-middle">
                                                    <div class="font-weight-600">
                                                        Kelompok {{ $item->nomor_kelompok }}
                                                    </div>
                                                    @if ($nk)
                                                        <span class="badge badge-success mt-1" style="font-size:11px;">
                                                            Akumulasi Nilai: {{ number_format($nk->C_total, 2) }}
                                                        </span>
                                                    @else
                                                        <span class="badge badge-secondary mt-1" style="font-size:11px;">
                                                            Belum dinilai
                                                        </span>
                                                    @endif
                                                </td>

                                                {{-- Kolom Nilai Administrasi — klik untuk expand form --}}
                                                <td class="align-middle">
                                                    <div class="d-flex align-items-center justify-content-between"
                                                        style="cursor:pointer;" data-toggle="collapse"
                                                        data-target="#formKelompok{{ $item->id }}"
                                                        aria-expanded="false">

                                                        @if ($nk)
                                                            <span class="text-muted" style="font-size:13px;">
                                                                <i class="fas fa-check-circle text-success mr-1"></i>
                                                                Sudah dinilai — klik untuk edit
                                                            </span>
                                                        @else
                                                            <span class="text-muted" style="font-size:13px;">
                                                                <i class="fas fa-plus-circle text-primary mr-1"></i>
                                                                Klik untuk mengisi nilai administrasi kelompok
                                                            </span>
                                                        @endif

                                                        <i class="fas fa-chevron-down text-primary ml-3 chevron-icon"
                                                            style="font-size:13px; flex-shrink:0; transition:transform .2s;"></i>
                                                    </div>

                                                    {{-- COLLAPSE: FORM NILAI KELOMPOK --}}
                                                    <div class="collapse mt-3" id="formKelompok{{ $item->id }}">
                                                        <div class="card border-primary shadow-sm mb-0">
                                                            <div
                                                                class="card-header bg-primary text-white py-2 d-flex justify-content-between align-items-center">
                                                                <strong>
                                                                    <i class="fas fa-clipboard-list mr-1"></i>
                                                                    {{ $isEdit ? 'Edit' : 'Form' }} Nilai Administrasi (10%)
                                                                    — Kelompok {{ $item->nomor_kelompok }}
                                                                </strong>
                                                            </div>

                                                            <div class="card-body">
                                                                <form method="POST"
                                                                    action="{{ $isEdit
                                                                        ? route('koordinator.NilaiAdministrasi.update', $nk->id)
                                                                        : route('koordinator.NilaiAdministrasi.store') }}">
                                                                    @csrf
                                                                    @if ($isEdit)
                                                                        @method('PUT')
                                                                    @endif
                                                                    <input type="hidden" name="kelompok_id"
                                                                        value="{{ $item->id }}">

                                                                    <div class="row">
                                                                        @php
                                                                            $komponen = [
                                                                                'C1' => 'DPP',
                                                                                'C2' => 'TOR',
                                                                                'C3' => 'Bukti Kartu Bimbingan',
                                                                                'C4' => 'Turnitin',
                                                                                'C5' => 'Kode',
                                                                            ];
                                                                        @endphp
                                                                        @foreach ($komponen as $field => $label)
                                                                            <div class="col-md-4 col-sm-6 mb-3">
                                                                                <label class="font-weight-bold"
                                                                                    style="font-size:13px;">
                                                                                    {{ $label }}
                                                                                </label>
                                                                                <input type="number"
                                                                                    name="{{ $field }}"
                                                                                    class="form-control form-control-sm"
                                                                                    min="0" max="100"
                                                                                    value="{{ old($field, $nk->$field ?? '') }}"
                                                                                    required>
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                            </div>

                                                            {{-- Section Pameran --}}
                                                            <div class="card border-primary mb-3">
                                                                <div class="card-header bg-primary text-white py-2">
                                                                    <strong>
                                                                        <i class="fas fa-paint-brush mr-1"></i>
                                                                        Pameran (5%)
                                                                    </strong>
                                                                </div>
                                                                <div class="card-body py-3">
                                                                    <div class="col-md-4 col-sm-6 px-0">
                                                                        <label class="font-weight-bold"
                                                                            style="font-size:13px;">
                                                                            Nilai Pameran
                                                                        </label>
                                                                        <input type="number" name="Pameran"
                                                                            class="form-control form-control-sm"
                                                                            min="0" max="100"
                                                                            value="{{ old('Pameran', $nk->Pameran ?? '') }}"
                                                                            placeholder="0 - 100" required>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            @if ($nk)
                                                                <div class="alert alert-info py-2 mb-3">
                                                                    <strong>Akumulasi Nilai saat ini:</strong>
                                                                    {{ number_format($nk->Administrasi ?? 0, 2) }}
                                                                </div>
                                                            @endif

                                                            <div class="text-right d-flex justify-content-end"
                                                                style="gap: 8px;">
                                                                @if ($nk)
                                                                    <form method="POST"
                                                                        action="{{ route('koordinator.NilaiAdministrasi.destroy', $nk->id) }}"
                                                                        class="d-inline">
                                                                        @csrf @method('DELETE')
                                                                        <button type="button"
                                                                            class="btn btn-danger btn-sm show_confirm_kelompok"
                                                                            data-toggle="tooltip" data-placement="top"
                                                                            title="Hapus Nilai Kelompok">
                                                                            <i class="fas fa-trash-alt"></i>
                                                                        </button>
                                                                    </form>
                                                                @endif
                                                                <button type="submit"
                                                                    class="btn btn-sm {{ $isEdit ? 'btn-warning' : 'btn-success' }}"
                                                                    data-toggle="tooltip" data-placement="top"
                                                                    title="{{ $isEdit ? 'Update Nilai Kelompok' : 'Simpan Nilai Kelompok' }}">
                                                                    <i
                                                                        class="fas {{ $isEdit ? 'fa-edit' : 'fa-save' }}"></i>
                                                                </button>
                                                            </div>

                                                            </form>
                                                        </div>
                                                    </div>
                            </div>
                            </td>

                            {{-- KOLOM AKSI — tombol Beri Nilai --}}
                            <td class="text-center align-middle">
                                @if ($nk)
                                    <button class="btn btn-primary btn-sm tooltip-collapse" type="button"
                                        data-toggle="collapse" data-target="#formAnggota{{ $item->id }}"
                                        data-title="Beri Nilai">
                                        <i class="fas fa-user-check"></i>
                                    </button>
                                @else
                                    <span class="text-muted" style="font-size:12px;">
                                        Isi nilai<br>kelompok terlebih dahulu
                                    </span>
                                @endif
                            </td>
                            </tr>

                            {{-- ══ COLLAPSE: SEMUA ANGGOTA + FORM LOGBOOK ══ --}}
                            @if ($nk)
                                <tr>
                                    <td colspan="3" class="p-0 border-left-0 border-right-0">
                                        <div class="collapse" id="formAnggota{{ $item->id }}">
                                            <div class="card m-3 border-primary shadow-sm mb-2">
                                                <div
                                                    class="card-header bg-primary text-white py-2 d-flex justify-content-between align-items-center">
                                                    <strong>
                                                        <i class="fas fa-users mr-1"></i>
                                                        Nilai Logbook Anggota — Kelompok
                                                        {{ $item->nomor_kelompok }}
                                                    </strong>
                                                    <small>
                                                        Akumulasi Nilai Kelompok:
                                                        {{ number_format($nk->C_total, 2) }}
                                                    </small>
                                                </div>

                                                {{-- Info rumus global --}}
                                                <div class="px-3 pt-3 pb-0">
                                                    <div class="alert alert-info py-2 mb-3" style="font-size:12px;">
                                                        <i class="fas fa-info-circle mr-1"></i>
                                                        <strong>Rumus:</strong>
                                                        ((Nilai Kelompok + Nilai LogBook) / 2) × 10% =
                                                        Akumulasi Nilai Administrasi per Mahasiswa
                                                    </div>
                                                </div>

                                                <div class="card-body p-0">
                                                    <table class="table table-bordered table-sm mb-0">
                                                        <thead class="thead-light">
                                                            <tr>
                                                                <th width="30%">Nama Anggota</th>
                                                                <th width="15%" class="text-center">
                                                                    Status</th>
                                                                <th width="30%">Nilai LogBook</th>
                                                                <th width="25%" class="text-center">
                                                                    Aksi</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @forelse ($anggota as $mhs)
                                                                @php
                                                                    $ni =
                                                                        $nilaiIndividu[
                                                                            $item->id . '_' . $mhs->mahasiswa->user_id
                                                                        ] ?? null;
                                                                @endphp
                                                                <tr>
                                                                    {{-- Nama --}}
                                                                    <td class="align-middle">
                                                                        <i class="fas fa-user text-primary mr-1"></i>
                                                                        <span
                                                                            style="font-size:13px;">{{ $mhs->mahasiswa->nama ?? 'Mahasiswa' }}</span>
                                                                    </td>

                                                                    {{-- Status --}}
                                                                    <td class="align-middle text-center">
                                                                        @if ($ni && $ni->D1 !== null)
                                                                            <span class="badge badge-success"
                                                                                style="font-size:11px;">Sudah
                                                                                dinilai</span>
                                                                        @else
                                                                            <span class="badge badge-warning"
                                                                                style="font-size:11px;">Belum
                                                                                dinilai</span>
                                                                        @endif
                                                                    </td>

                                                                    {{-- Input Nilai --}}
                                                                    <td class="align-middle">
                                                                        <form method="POST"
                                                                            id="formLogbook{{ $item->id }}_{{ $mhs->mahasiswa->user_id }}"
                                                                            action="{{ $ni
                                                                                ? route('koordinator.NilaiAdministrasi.updateIndividu', $ni->id)
                                                                                : route('koordinator.NilaiAdministrasi.storeIndividu') }}">
                                                                            @csrf
                                                                            @if ($ni)
                                                                                @method('PUT')
                                                                            @endif
                                                                            <input type="hidden" name="kelompok_id"
                                                                                value="{{ $item->id }}">
                                                                            <input type="hidden" name="user_id"
                                                                                value="{{ $mhs->mahasiswa->user_id }}">

                                                                            <input type="number" name="D1"
                                                                                class="form-control form-control-sm"
                                                                                min="0" max="100"
                                                                                value="{{ old('D1', $ni->D1 ?? '') }}"
                                                                                placeholder="0 - 100" required>
                                                                        </form>
                                                                    </td>

                                                                    {{-- Aksi --}}
                                                                    <td class="align-middle text-center">
                                                                        <div class="d-flex justify-content-center"
                                                                            style="gap: 8px;">
                                                                            <button type="submit"
                                                                                form="formLogbook{{ $item->id }}_{{ $mhs->mahasiswa->user_id }}"
                                                                                class="btn btn-sm {{ $ni ? 'btn-warning' : 'btn-success' }}"
                                                                                data-toggle="tooltip" data-placement="top"
                                                                                title="{{ $ni ? 'Edit Nilai' : 'Simpan Nilai' }}">
                                                                                <i
                                                                                    class="fas {{ $ni ? 'fa-edit' : 'fa-save' }}"></i>
                                                                            </button>

                                                                            @if ($ni)
                                                                                <form method="POST"
                                                                                    action="{{ route('koordinator.NilaiAdministrasi.destroyIndividu', $ni->id) }}">
                                                                                    @csrf @method('DELETE')
                                                                                    <button type="button"
                                                                                        class="btn btn-danger btn-sm show_confirm_individu"
                                                                                        data-toggle="tooltip"
                                                                                        data-placement="top"
                                                                                        title="Hapus Nilai LogBook">
                                                                                        <i class="fas fa-trash-alt"></i>
                                                                                    </button>
                                                                                </form>
                                                                            @endif
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            @empty
                                                                <tr>
                                                                    <td colspan="4" class="text-center text-muted p-3">
                                                                        Tidak ada anggota kelompok.
                                                                    </td>
                                                                </tr>
                                                            @endforelse
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endif
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
        // Buka collapse jika ada fragment di URL
        $(function() {
            const hash = window.location.hash;
            if (hash) {
                const target = $(hash);
                if (target.length) {
                    target.collapse('show');
                    // Scroll ke elemen
                    $('html, body').animate({
                        scrollTop: target.offset().top - 100
                    }, 400);
                }
            }
        });

        // Tooltip untuk tombol yang juga punya data-toggle="collapse"
        $(function() {
            $('.tooltip-collapse').each(function() {
                $(this).tooltip({
                    title: $(this).data('title'),
                    placement: 'top',
                    trigger: 'hover'
                });
            });
        });

        // Konfirmasi hapus nilai kelompok
        $(document).on('click', '.show_confirm_kelompok', function(e) {
            e.preventDefault();
            var form = $(this).closest('form');
            swal({
                title: 'Hapus nilai kelompok ini?',
                text: 'Data akan terhapus secara permanen!',
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) form.submit();
            });
        });

        // Konfirmasi hapus nilai individu
        $(document).on('click', '.show_confirm_individu', function(e) {
            e.preventDefault();
            var form = $(this).closest('form');
            swal({
                title: 'Hapus nilai logbook ini?',
                text: 'Data logbook mahasiswa ini akan terhapus permanen!',
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) form.submit();
            });
        });

        // Putar ikon chevron saat collapse dibuka/tutup
        $(document).on('show.bs.collapse', '.collapse', function() {
            var trigger = $('[data-target="#' + this.id + '"]');
            trigger.find('.chevron-icon').css('transform', 'rotate(180deg)');
        });
        $(document).on('hide.bs.collapse', '.collapse', function() {
            var trigger = $('[data-target="#' + this.id + '"]');
            trigger.find('.chevron-icon').css('transform', 'rotate(0deg)');
        });
    </script>
@endpush
