@extends('layouts.main')
@section('title', 'AI Agent Kelompok')

@section('content')

    <section class="section">
        <div class="section-body">

            <!-- INFO USER -->
            <div class="alert alert-info">

                <b>Login sebagai:</b> {{ $user->nama ?? 'User' }} <br>
                <b>User ID:</b> {{ session('user_id') }}

                <br><br>

                <b>Role:</b>
                @foreach ($roles as $r)
                    <span class="badge badge-primary">
                        {{ $r['role'] }} - {{ $r['kategori_pa'] }} - Angkatan {{ $r['angkatan'] }} {{ $r['prodi'] }}
                    </span>
                @endforeach

            </div>


            <!-- CARD AI -->
            <div class="card">

                <div class="card-header">
                    <h4>AI Agent Pembentukan Kelompok {{ $r['kategori_pa'] }} - Angkatan {{ $r['angkatan'] }}
                        {{ $r['prodi'] }}</h4>
                </div>

                <div class="card-body text-center">

                    <p>
                        Sistem akan membuat <b>kelompok mahasiswa {{ $r['kategori_pa'] }} - Angkatan {{ $r['angkatan'] }}
                            {{ $r['prodi'] }} otomatis</b>
                        berdasarkan keseimbangan nilai akademik.
                    </p>

                    <form id="formGenerate" action="{{ route('ai.generate') }}" method="POST">
                        @csrf

                        <button type="button" id="btnGenerate" class="btn btn-lg btn-primary">
                            <i class="fas fa-cogs"></i> Generate Kelompok
                        </button>

                    </form>

                </div>

            </div>


            <!-- STATUS -->
            @if (session('success'))
                <div class="alert alert-success mt-3">
                    {{ session('success') }}
                </div>
            @endif


            <!-- HASIL -->
            @if (session('kelompok'))

                <div class="card mt-4">

                    <div class="card-header">
                        <h4>Hasil Pembentukan Kelompok</h4>
                    </div>

                    <div class="card-body">

                        @foreach (session('kelompok') as $k)
                            <div class="mb-4">

                                <h5 class="text-primary">
                                    Kelompok {{ $k['kelompok'] }}
                                </h5>

                                <div class="alert alert-info">
                                    <strong>Alasan AI:</strong>
                                    {{ $k['alasan'] ?? 'AI menyeimbangkan mahasiswa berdasarkan rata-rata nilai akademik.' }}
                                </div>

                                <table class="table table-bordered table-striped">

                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>NIM</th>
                                            <th>Nama</th>
                                            <th>Sem 1</th>
                                            <th>Sem 2</th>
                                            <th>Sem 3</th>
                                            <th>Sem 4</th>
                                            <th>Sem 5</th>
                                            {{-- <th>Rata Nilai</th> --}}
                                        </tr>
                                    </thead>

                                    <tbody>

                                        @foreach ($k['members'] as $i => $m)
                                            <tr>
                                                <td>{{ $i + 1 }}</td>

                                                <td>{{ $m['nim'] }}</td>

                                                <td>{{ $m['nama'] }}</td>



                                                <td>{{ $m['nilai_per_semester'][1] ?? '-' }}</td>
                                                <td>{{ $m['nilai_per_semester'][2] ?? '-' }}</td>
                                                <td>{{ $m['nilai_per_semester'][3] ?? '-' }}</td>
                                                <td>{{ $m['nilai_per_semester'][4] ?? '-' }}</td>
                                                <td>{{ $m['nilai_per_semester'][5] ?? '-' }}</td>

                                                {{-- <td>{{ $m['rata_nilai_semester'] }}</td> --}}

                                            </tr>
                                        @endforeach

                                    </tbody>

                                </table>

                            </div>
                        @endforeach

                    </div>

                </div>

            @endif

        </div>
    </section>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {

            document.getElementById("btnGenerate").addEventListener("click", function() {

                fetch("{{ route('ai.cekKelompok') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        }
                    })

                    .then(res => res.json())

                    .then(data => {

                        if (data.exists) {

                            Swal.fire({
                                title: "Kelompok sudah ada",
                                text: "Apakah ingin generate ulang kelompok?",
                                icon: "warning",
                                showCancelButton: true,
                                confirmButtonText: "Ya, Generate Ulang",
                                cancelButtonText: "Batal"
                            }).then((result) => {

                                if (result.isConfirmed) {
                                    showLoading()
                                    document.getElementById("formGenerate").submit()
                                }

                            })

                        } else {

                            showLoading()
                            document.getElementById("formGenerate").submit()

                        }

                    })

            })

        })

        function showLoading() {

            Swal.fire({
                title: "AI Agent sedang bekerja...",
                text: "Menganalisis mahasiswa dan membentuk kelompok",
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading()
                }
            })

        }
    </script>


@endsection
