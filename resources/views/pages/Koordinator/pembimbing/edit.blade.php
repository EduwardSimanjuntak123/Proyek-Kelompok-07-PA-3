@extends('layouts.main')
@section('title', 'Edit Pembimbing 1')

@section('content')
    <section class="section">
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    @include('partials.alert')
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <h4>Edit Pembimbing 1</h4>
                            <a href="{{ route('pembimbing.index') }}" class="btn btn-primary">Kembali</a>
                        </div>
                        <div class="card-body">

                            <form method="POST" action="{{ route('pembimbing.update', Crypt::encrypt($pembimbing['id'])) }}"
                                enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                {{-- Pilih Dosen --}}
                                <div class="form-group">
                                    <label>Pembimbing 1</label>

                                    <select name="pembimbing1" class="form-control select2">
                                        <option value="">-- Pilih Dosen --</option>

                                        @foreach ($dosen as $item)
                                            <option value="{{ $item['user_id'] }}">
                                                {{ $item['nama'] }}
                                            </option>
                                        @endforeach

                                    </select>
                                </div>


                                <div class="form-group">
                                    <label>Pembimbing 2</label>

                                    <select name="pembimbing2" class="form-control select2">
                                        <option value="">-- Pilih Dosen --</option>

                                        @foreach ($dosen as $item)
                                            <option value="{{ $item['user_id'] }}">
                                                {{ $item['nama'] }}
                                            </option>
                                        @endforeach

                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@push('script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const select = document.getElementById('user_id');
            const namaInput = document.getElementById('nama_dosen');

            function updateNama() {
                const selected = select.options[select.selectedIndex];
                const nama = selected.getAttribute('data-nama') || '';
                namaInput.value = nama;
            }

            updateNama(); // Set nama saat load
            select.addEventListener('change', updateNama); // Update nama saat ganti dosen
        });
    </script>
@endpush
