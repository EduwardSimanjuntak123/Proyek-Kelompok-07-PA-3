@extends('layouts.main')

@section('title', 'Profile')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-6">
                @include('partials.alert')
                
                <div class="card shadow-lg border-0 rounded-lg overflow-hidden">
                    @foreach($detailUser as $user)
                        @if ($role == 'Mahasiswa')
                            <div class="card-body bg-light p-4">
                                <h4 class="card-title text-center mb-4 text-primary">Profil Mahasiswa</h4>
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item"><strong>Nama:</strong> {{ $user['nama'] }}</li>
                                    <li class="list-group-item"><strong>NIM:</strong> {{ $user['nim'] }}</li>
                                    <li class="list-group-item"><strong>Username:</strong> {{ $user['user_name'] }}</li>
                                    <li class="list-group-item"><strong>Email:</strong> {{ $user['email'] }}</li>
                                    <li class="list-group-item"><strong>Prodi:</strong> {{ $user['prodi_name'] }}</li>
                                    <li class="list-group-item"><strong>Fakultas:</strong> {{ $user['fakultas'] }}</li>
                                    <li class="list-group-item"><strong>Angkatan:</strong> {{ $user['angkatan'] }}</li>
                                    <li class="list-group-item"><strong>Status:</strong> {{ $user['status'] }}</li>
                                </ul>
                            </div>
                        @else
                            <div class="card-body bg-light p-4">
                                <div class="text-center mb-4">
                                    <h4 class="mt-3 text-dark">{{ $user['nama'] }}</h4>
                                    <p class="text-muted">{{ $user['posisi'] ?? '-' }}</p>
                                </div>
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item"><strong>NIP:</strong> {{ $user['nip'] }}</li>
                                    <li class="list-group-item"><strong>Username:</strong> {{ $user['user_name'] }}</li>
                                    <li class="list-group-item"><strong>Email:</strong> {{ $user['email'] }}</li>
                                    <li class="list-group-item"><strong>Alias:</strong> {{ $user['alias'] ?? '-' }}</li>
                                    <li class="list-group-item"><strong>Status Pegawai:</strong> 
                                        <span class="badge {{ $user['status_pegawai'] == 'A' ? 'bg-success' : 'bg-danger' }}">
                                            {{ $user['status_pegawai'] == 'A' ? 'Aktif' : 'Tidak Aktif' }}
                                        </span>
                                    </li>
                                </ul>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection
