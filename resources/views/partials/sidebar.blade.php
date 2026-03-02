<div class="main-sidebar">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand mt-3">
            {{-- <img src="{{ file_exists(public_path('assets/img/logovokasi.png')) ? asset('assets/img/logovokasi.png') : 'https://via.placeholder.com/300' }}" style="width: 130px"> --}}
            <img src="{{ asset('assets/img/Logovokasi.png') }}" style="width: 130px" alt="Logo Vokasi">
            @if (session('isLoggin'))
                <p>User Logged In as: {{ session('role') }}</p>
            @else
                <p>Tidak ada pengguna yang login.</p>
            @endif

        </div>
        @if (session('isLoggin'))
            <ul class="sidebar-menu">
                @if (session('role') == 'Dosen')
                    @php $dosenRoles = session('dosen_roles'); @endphp

                    {{--  untuk Koordinator --}}
                    @if (in_array(1, $dosenRoles))
                        <li><a class="nav-link" href="{{ route('dashboard.koordinator') }}"><i class="fas fa-columns"></i>
                                <span>Dashboard</span></a></li>
                        <li class="menu-header">Kordinator</li>
                        <li><a class="nav-link" href="{{ route('koordinator.tugas.index') }}"><i
                                    class="fas fa-file"></i><span>Tugas</span></a></li>
                        <li><a class="nav-link" href="{{ route('kelompok.index') }}"><i class="fas fa-users"></i>
                                <span>Kelompok</span></a></li>
                        <li><a class="nav-link" href="{{ route('jadwal.index') }}"><i class="fas fa-calendar"></i>
                                <span>Jadwal</span></a></li>
                        <li class="nav-item dropdown {{ request()->is('pembimbing*') ? 'active' : '' }}">
                            <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                                <i class="fas fa-user"></i> <span>Pembimbing</span>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="nav-link {{--  --}}"
                                        href="{{ route('pembimbing.index') }}">Pembimbing 1</a></li>
                                <li><a class="nav-link {{--  --}}"
                                        href="{{ route('pembimbing2.index') }}">Pembimbing 2</a></li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown {{ request()->is('penguji*') ? 'active' : '' }}">
                            <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                                <i class="fas fa-user"></i> <span>Penguji</span>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="nav-link {{--  --}}"
                                        href="{{ route('penguji.index') }}">Penguji 1</a></li>
                                <li><a class="nav-link {{--  --}}"
                                        href="{{ route('penguji2.index') }}">Penguji 2</a></li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown {{ request()->is('nilai*') ? 'active' : '' }}">
                            <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                                <i class="fas fa-clipboard-check"></i> <span>Nilai</span>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="nav-link {{--  --}}"
                                        href="{{ route('koordinator.NilaiAdministrasi.index') }}">Nilai Administrasi
                                    </a></li>
                                <li>
                                    <a class="nav-link {{--  --}}"
                                        href="{{ route('NilaiAkhir.index') }}">Nilai Mahasiswa</a>
                                </li>

                            </ul>
                        </li>
                        <li><a class="nav-link" href="{{ route('pengumuman.index') }}"><i class="fas fa-bell"></i>
                                <span>Pengumuman</span></a></li>
                    @endif
                    {{--  untuk Penguji --}}
                    @if (in_array(2, $dosenRoles) || in_array(4, $dosenRoles))
                        <li class="menu-header">Penguji</li>
                        <li><a class="nav-link" href="{{ route('dashboard.penguji') }}"><i class="fas fa-columns"></i>
                                <span>Dashboard</span></a></li>
                        <li><a class="nav-link" href="{{ route('penguji.tugas.index') }}"><i class="fas fa-file"></i>
                                <span>Tugas</span></a></li>

                        <li><a class="nav-link" href="{{ route('penguji.jadwal.index') }}"><i
                                    class="fas fa-calendar"></i> <span>Jadwal</span></a></li>
                        <li class="nav-item dropdown">
                            <a href="#"
                                class="nav-link has-dropdown {{ request()->is('nilai*') ? 'active' : '' }}"
                                data-toggle="dropdown">
                                <i class="fas fa-clipboard-check"></i> <span>Nilai</span>
                            </a>
                            <ul class="dropdown-menu">
                                @if (in_array(2, $dosenRoles))
                                    <li><a class="nav-link {{ request()->routeIs('penguji1.NilaiKelompok.index') ? 'active' : '' }}"
                                            href="{{ route('penguji1.NilaiKelompok.index') }}">Nilai Kelompok</a></li>
                                    <li><a class="nav-link {{ request()->routeIs('penguji1.NilaiIndividu.index') ? 'active' : '' }}"
                                            href="{{ route('penguji1.NilaiIndividu.index') }}">Nilai Individu</a></li>
                                @elseif (in_array(4, $dosenRoles))
                                    <li><a class="nav-link {{ request()->routeIs('penguji2.NilaiKelompok.index') ? 'active' : '' }}"
                                            href="{{ route('penguji2.NilaiKelompok.index') }}">Nilai Kelompok</a></li>
                                    <li><a class="nav-link {{ request()->routeIs('penguji2.NilaiIndividu.index') ? 'active' : '' }}"
                                            href="{{ route('penguji2.NilaiIndividu.index') }}">Nilai Individu</a></li>
                                @endif
                            </ul>
                        </li>
                        {{-- <li ><a class="nav-link" href="{{route('penguji.pengumuman.index')}}"><i class="fas fa-bell"></i> <span>Pengumuman</span></a></li> --}}

                    @endif
                    {{-- Untuk  Pembimbing --}}
                    @if (in_array(3, $dosenRoles) || in_array(5, $dosenRoles))
                        <li class="menu-header">Pembimbing</li>
                        <li><a class="nav-link" href="{{ route('dashboard.pembimbing') }}"><i
                                    class="fas fa-columns"></i> <span>Dashboard</span></a></li>
                        <li><a class="nav-link" href="{{ route('pembimbing.tugas.index') }}"><i
                                    class="fas fa-file"></i> <span>Tugas</span></a></li>
                        <li><a class="nav-link" href="{{ route('pembimbing.bimbingan.index') }}"><i
                                    class="fas fa-bullhorn"></i> <span>Bimbingan</span></a></li>
                        <li><a class="nav-link" href="{{ route('pembimbing.jadwal.index') }}"><i
                                    class="fas fa-calendar"></i> <span>Jadwal</span></a></li>
                        <li><a class="nav-link" href="{{ route('PembimbingPengajuanSeminar.index') }}"><i
                                    class="fas fa-calendar-check"></i> <span>Pengajuan Seminar</span></a></li>
                        <li class="nav-item dropdown {{ request()->is('nilai*') ? 'active' : '' }}">
                        <li class="nav-item dropdown {{ request()->is('nilai*') ? 'active' : '' }}">
                            <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                                <i class="fas fa-clipboard-check"></i> <span>Nilai</span>
                            </a>
                            <ul class="dropdown-menu">
                                @if (in_array(3, $dosenRoles))
                                    <li><a class="nav-link {{ request()->routeIs('penguji2.NilaiKelompok.index') ? 'active' : '' }}"
                                            href="{{ route('pembimbing1.NilaiKelompok.index') }}">Nilai Kelompok
                                            (Seminar) </a></li>
                                    <li><a class="nav-link {{ request()->routeIs('pembimbing1.NilaiIndividu.index') ? 'active' : '' }}"
                                            href="{{ route('pembimbing1.NilaiIndividu.index') }}">Nilai Individu
                                            (Seminar)</a></li>
                                    <li><a class="nav-link {{ request()->routeIs('pembimbing1.NilaiBimbingan.index') ? 'active' : '' }}"
                                            href="{{ route('pembimbing1.NilaiBimbingan.index') }}">Nilai Bimbingan</a>
                                    </li>
                                @elseif (in_array(5, $dosenRoles))
                                    <li><a class="nav-link {{ request()->routeIs('pembimbing2.NilaiKelompok.index') ? 'active' : '' }}"
                                            href="{{ route('pembimbing2.NilaiKelompok.index') }}">Nilai Kelompok
                                            (Seminar)</a></li>
                                    <li><a class="nav-link {{ request()->routeIs('pembimbing2.NilaiIndividu.index') ? 'active' : '' }}"
                                            href="{{ route('pembimbing2.NilaiIndividu.index') }}">Nilai Individu
                                            (Seminar)</a></li>
                                    {{-- <li><a class="nav-link {{ request()->routeIs('pembimbing2.NilaiBimbingan.index') ? 'active' : '' }}" href="{{ route('pembimbing2.NilaiBimbingan.index') }}">Nilai Bimbingan</a></li> --}}
                                @endif
                            </ul>
                        </li>
                        {{-- <li ><a class="nav-link" href="{{route('pembimbing.pengumuman.index')}}"><i class="fas fa-bell"></i> <span>Pengumuman</span></a></li> --}}

                    @endif
                    {{-- Untuk  Mahasiswa --}}
                @elseif (session('role') == 'Mahasiswa')
                    <li class="menu-header">MahaSiswa</li>
                    <li><a class="nav-link" href="{{ route('dashboard.mahasiswa') }}"><i class="fas fa-columns"></i>
                            <span>Dashboard</span></a></li>
                    <li><a class="nav-link" href="{{ route('Mahasiswa.tugas.index') }}"><i class="fas fa-file"></i>
                            <span>Tugas</span></a></li>
                    <li><a class="nav-link" href="{{ route('artefak.index') }}"><i class="fas fa-file"></i>
                            <span>Artefak</span></a></li>
                    <li><a class="nav-link" href="{{ route('bimbingan.index') }}"><i class="fas fa-list"></i>
                            <span>Bimbingan</span></a></li>
                    <li><a class="nav-link" href="{{ route('pengumuman.mahasiswa.index') }}"><i
                                class="fas fa-bell"></i> <span>Pengumuman</span></a></li>
                    <li><a class="nav-link" href="{{ route('mahasiswa.jadwal.index') }}"><i
                                class="fas fa-calendar"></i> <span>Jadwal</span></a></li>
                    <li><a class="nav-link" href=""><i class="fas fa-history"></i> <span>Histori</span></a>
                    </li>

                    {{-- Untuk Staff BAAK --}}
                @elseif (session('role') == 'Staff')
                    <li class="menu-header">Staff</li>
                    <li><a class="nav-link" href="{{ route('dashboard.BAAK') }}"><i class="fas fa-columns"></i>
                            <span>Dashboard</span></a></li>
                    <li><a class="nav-link" href="{{ route('manajemen-role.index') }}"><i class="fas fa-user"></i>
                            <span>Manajemen-Role</span></a></li>
                    <li><a class="nav-link" href="{{ route('baak.jadwal.index') }}"><i class="fas fa-calendar"></i>
                            <span>Jadwal</span></a></li>
                    <li><a class="nav-link" href="{{ route('pengumuman.BAAK.index') }}"><i class="fas fa-bell"></i>
                            <span>Pengumuman</span></a></li>
                    <li><a class="nav-link" href="{{ route('TahunMasuk.index') }}"><i
                                class="fas fa-graduation-cap"></i> <span>Tahun Masuk</span></a></li>
                    <li><a class="nav-link" href="{{ route('listDosen.index') }}"><i
                                class="fas fa-graduation-cap"></i> <span>Daftar dosen</span></a></li>
                    <li><a class="nav-link" href="{{ route('listMahasiswa.index') }}"><i
                                class="fas fa-graduation-cap"></i> <span>Daftar Mahasiswa</span></a></li>
                @endif
            </ul>
        @endif
    </aside>
</div>
