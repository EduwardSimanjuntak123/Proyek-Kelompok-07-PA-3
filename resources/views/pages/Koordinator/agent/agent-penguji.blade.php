@extends('layouts.main')
@section('title', 'AI Agent Kelompok')

@section('content')
    <section class="section">
        <div class="section-body">

            <!-- INFO USER -->
            <div class="alert alert-warning">
                <b>Login sebagai:</b> {{ $user->nama ?? 'User' }} <br>
                <b>User ID:</b> {{ session('user_id') }}<br><br>
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
                    <h4>AI Agent Pembentukan Penguji</h4>
                </div>
                <div class="card-body text-center">
                    <p>
                        Sistem akan membuat <b>Penguji kelompok mahasiswa</b> otomatis
                        berdasarkan keseimbangan Jabatan.
                    </p>

                    <button type="button" id="btnGenerate" class="btn btn-lg btn-primary">
                        <i class="fas fa-cogs"></i> Generate Penguji
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Ambil data role dari Blade
        const roleData = @json($roles);
        console.log("roleData:", roleData); // 🔹 log untuk cek apakah roleData ada

        function showLoading() {
            console.log("showLoading dipanggil"); // 🔹 log cek fungsi showLoading
            Swal.fire({
                title: "AI Agent sedang bekerja...",
                text: "Menganalisis mahasiswa dan membentuk kelompok",
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading()
                }
            });
        }

        document.getElementById('btnGenerate').addEventListener('click', function() {
            console.log("Tombol Generate diklik"); // 🔹 log cek tombol
            if (roleData.length === 0) {
                console.error("Data role kosong");
                Swal.fire('Gagal', 'Data role tidak tersedia', 'error');
                return;
            }

            showLoading();

            const role = roleData[0];
            console.log("role yang dikirim:", role); // 🔹 log data role sebelum fetch

            fetch("{{ route('ai.penguji.generate') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}",
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        KPA_id: role.KPA_id,
                        prodi_id: role.prodi_id,
                        TM_id: role.TM_id,
                        tahun_ajaran_id: role.tahun_ajaran_id
                    })
                })
                .then(async response => {
                    console.log("Status response:", response.status); // 🔹 log status HTTP
                    let data;
                    try {
                        data = await response.json();
                    } catch (e) {
                        console.error("JSON parsing error:", e);
                        Swal.close();
                        Swal.fire('Error', 'Response bukan JSON', 'error');
                        throw e;
                    }
                    console.log("Response data:", data); // 🔹 log response JSON

                    Swal.close();
                    if (data.status === 'error') {
                        Swal.fire('Gagal', data.message, 'error');
                    } else {
                        Swal.fire('Sukses', data.message, 'success');
                    }
                })
                .catch(err => {
                    console.error("Fetch error:", err); // 🔹 log error fetch
                    Swal.close();
                    Swal.fire('Error', 'Terjadi kesalahan server', 'error');
                });
        });
    </script>
@endsection
