<div class="main-sidebar">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand mt-3">
            {{-- <img src="{{ file_exists(public_path('assets/img/logovokasi.png')) ? asset('assets/img/logovokasi.png') : 'https://via.placeholder.com/300' }}" style="width: 130px"> --}}
            @php
                $dashboardRoute = '#';
                if (session('isLoggin')) {
                    $role = session('role');
                    if ($role == 'Dosen') {
                        $dosenRoles = session('dosen_roles');
                        if (in_array(1, $dosenRoles)) {
                            $dashboardRoute = route('dashboard.koordinator');
                        } elseif (in_array(2, $dosenRoles) || in_array(4, $dosenRoles)) {
                            $dashboardRoute = route('dashboard.penguji');
                        } elseif (in_array(3, $dosenRoles) || in_array(5, $dosenRoles)) {
                            $dashboardRoute = route('dashboard.pembimbing');
                        }
                    } elseif ($role == 'Mahasiswa') {
                        $dashboardRoute = route('dashboard.mahasiswa');
                    } elseif ($role == 'Staff') {
                        $dashboardRoute = route('dashboard.BAAK');
                    }
                }
            @endphp

            <a href="{{ $dashboardRoute }}" style="text-decoration: none; cursor: pointer;">
                <img src="{{ asset('assets/img/Logovokasi.png') }}"
                    style="width: 130px; transition: opacity 0.3s; hover:opacity 0.8;" alt="Logo Vokasi"
                    title="Kembali ke Dashboard">
            </a>

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
                        <li class="menu-header">AI Assistant</li>
                        <li class="nav-item {{ request()->is('ai*') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('ai.kelompok') }}">
                                <img src="{{ asset('assets/img/logoagent1.jpeg') }}" alt="VokasiTera Agent"
                                    style="width: 20px; height: 20px; object-fit: contain; display: inline-block; margin-right: 8px;">
                                <span>VokasiTera Agent </span>
                            </a>
                        </li>
                        <li class="menu-header">Kordinator</li>
                        <li class="nav-item {{ request()->is('dashboard/koordinator*') ? 'active' : '' }}"><a
                                class="nav-link" href="{{ route('dashboard.koordinator') }}"><i
                                    class="fas fa-columns"></i>
                                <span>Dashboard</span></a></li>
                        <li class="nav-item {{ request()->is('tugas*') ? 'active' : '' }}"><a class="nav-link"
                                href="{{ route('koordinator.tugas.index') }}"><i
                                    class="fas fa-file"></i><span>Tugas</span></a></li>
                        <li class="nav-item {{ request()->is('kelompok*') ? 'active' : '' }}"><a class="nav-link"
                                href="{{ route('kelompok.index') }}"><i class="fas fa-users"></i>
                                <span>Kelompok</span></a></li>
                        <li class="nav-item {{ request()->is('jadwal*') ? 'active' : '' }}"><a class="nav-link"
                                href="{{ route('jadwal.index') }}"><i class="fas fa-calendar"></i>
                                <span>Jadwal</span></a></li>
                        <li class="nav-item {{ request()->is('pembimbing*') ? 'active' : '' }}">
                            <a href="{{ route('pembimbing.index') }}" class="nav-link">
                                <i class="fas fa-user"></i> <span>Pembimbing</span>
                            </a>
                        </li>
                        <li class="nav-item {{ request()->is('penguji*') ? 'active' : '' }}">
                            <a href="{{ route('penguji.index') }}" class="nav-link">
                                <i class="fas fa-user"></i> <span>Penguji</span>
                            </a>
                        </li>
                        <li class="nav-item dropdown {{ request()->is('nilai*') ? 'active' : '' }}">
                            <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                                <i class="fas fa-clipboard-check"></i> <span>Nilai</span>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="nav-link {{ request()->is('NilaiAdministrasi*') ? 'active' : '' }}"
                                        href="{{ route('koordinator.NilaiAdministrasi.index') }}">Nilai Administrasi
                                    </a></li>
                                <li>
                                    <a class="nav-link {{ request()->is('NilaiAkhir*') ? 'active' : '' }}"
                                        href="{{ route('NilaiAkhir.index') }}">Nilai PA Mahasiswa</a>
                                </li>
                                <li>
                                    <a class="nav-link {{ request()->is('NilaiMatkul*') ? 'active' : '' }}"
                                        href="{{ route('koordinator.NilaiMatkul.index') }}">Nilai Matkul Mahasiswa</a>
                                </li>

                            </ul>
                        </li>
                        <li class="nav-item {{ request()->is('pengumuman*') ? 'active' : '' }}"><a class="nav-link"
                                href="{{ route('pengumuman.index') }}"><i class="fas fa-bell"></i>
                                <span>Pengumuman</span></a></li>
                    @endif
                    {{--  untuk Penguji --}}
                    @if (in_array(2, $dosenRoles) || in_array(4, $dosenRoles))
                        <li class="menu-header">Penguji</li>
                        <li class="nav-item {{ request()->is('dashboard/penguji*') ? 'active' : '' }}"><a
                                class="nav-link" href="{{ route('dashboard.penguji') }}"><i class="fas fa-columns"></i>
                                <span>Dashboard</span></a></li>
                        <li class="nav-item {{ request()->is('penguji/tugas*') ? 'active' : '' }}"><a class="nav-link"
                                href="{{ route('penguji.tugas.index') }}"><i class="fas fa-file"></i>
                                <span>Tugas</span></a></li>

                        <li class="nav-item {{ request()->is('penguji/jadwal*') ? 'active' : '' }}"><a class="nav-link"
                                href="{{ route('penguji.jadwal.index') }}"><i class="fas fa-calendar"></i>
                                <span>Jadwal</span></a></li>
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
                                @endif
                                @if (in_array(4, $dosenRoles))
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
                        <li class="nav-item {{ request()->is('dashboard/pembimbing*') ? 'active' : '' }}"><a
                                class="nav-link" href="{{ route('dashboard.pembimbing') }}"><i
                                    class="fas fa-columns"></i> <span>Dashboard</span></a></li>
                        <li class="nav-item {{ request()->is('pembimbing/tugas*') ? 'active' : '' }}"><a
                                class="nav-link" href="{{ route('pembimbing.tugas.index') }}"><i
                                    class="fas fa-file"></i> <span>Tugas</span></a></li>
                        <li class="nav-item {{ request()->is('pembimbing/bimbingan*') ? 'active' : '' }}"><a
                                class="nav-link" href="{{ route('pembimbing.bimbingan.index') }}"><i
                                    class="fas fa-bullhorn"></i> <span>Bimbingan</span></a></li>
                        <li class="nav-item {{ request()->is('pembimbing/jadwal*') ? 'active' : '' }}"><a
                                class="nav-link" href="{{ route('pembimbing.jadwal.index') }}"><i
                                    class="fas fa-calendar"></i> <span>Jadwal</span></a></li>
                        <li class="nav-item {{ request()->is('pengajuan*') ? 'active' : '' }}"><a class="nav-link"
                                href="{{ route('PembimbingPengajuanSeminar.index') }}"><i
                                    class="fas fa-calendar-check"></i> <span>Pengajuan Seminar</span></a></li>
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
                                @endif
                                @if (in_array(5, $dosenRoles))
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
                    <li class="nav-item {{ request()->is('dashboard/mahasiswa*') ? 'active' : '' }}"><a
                            class="nav-link" href="{{ route('dashboard.mahasiswa') }}"><i
                                class="fas fa-columns"></i>
                            <span>Dashboard</span></a></li>
                    <li class="nav-item {{ request()->is('tugas*') ? 'active' : '' }}"><a class="nav-link"
                            href="{{ route('Mahasiswa.tugas.index') }}"><i class="fas fa-file"></i>
                            <span>Tugas</span></a></li>
                    <li class="nav-item {{ request()->is('artefak*') ? 'active' : '' }}"><a class="nav-link"
                            href="{{ route('artefak.index') }}"><i class="fas fa-file"></i>
                            <span>Artefak</span></a></li>
                    <li class="nav-item {{ request()->is('bimbingan*') ? 'active' : '' }}"><a class="nav-link"
                            href="{{ route('bimbingan.index') }}"><i class="fas fa-list"></i>
                            <span>Bimbingan</span></a></li>
                    <li class="nav-item {{ request()->is('pengumuman*') ? 'active' : '' }}"><a class="nav-link"
                            href="{{ route('pengumuman.mahasiswa.index') }}"><i class="fas fa-bell"></i>
                            <span>Pengumuman</span></a></li>
                    <li class="nav-item {{ request()->is('jadwal*') ? 'active' : '' }}"><a class="nav-link"
                            href="{{ route('mahasiswa.jadwal.index') }}"><i class="fas fa-calendar"></i>
                            <span>Jadwal</span></a></li>
                    <li class="nav-item {{ request()->is('Histori*') ? 'active' : '' }}"><a class="nav-link"
                            href="{{ route('Histori.index') }}"><i class="fas fa-history"></i>
                            <span>Histori</span></a>
                    </li>

                    {{-- Untuk Staff BAAK --}}
                @elseif (session('role') == 'Staff')
                    <li class="menu-header">Staff</li>
                    <li class="nav-item {{ request()->is('dashboard/BAAK*') ? 'active' : '' }}"><a class="nav-link"
                            href="{{ route('dashboard.BAAK') }}"><i class="fas fa-columns"></i>
                            <span>Dashboard</span></a></li>
                    <li class="nav-item {{ request()->is('manajemen role*') ? 'active' : '' }}"><a class="nav-link"
                            href="{{ route('manajemen-role.index') }}"><i class="fas fa-user"></i>
                            <span>Manajemen-Role</span></a></li>
                    <li class="nav-item {{ request()->is('staff/jadwal*') ? 'active' : '' }}"><a class="nav-link"
                            href="{{ route('baak.jadwal.index') }}"><i class="fas fa-calendar"></i>
                            <span>Jadwal</span></a></li>
                    <li class="nav-item {{ request()->is('pengumuman/BAAK*') ? 'active' : '' }}"><a class="nav-link"
                            href="{{ route('pengumuman.BAAK.index') }}"><i class="fas fa-bell"></i>
                            <span>Pengumuman</span></a></li>
                    <li class="nav-item {{ request()->is('TahunMasuk*') ? 'active' : '' }}"><a class="nav-link"
                            href="{{ route('TahunMasuk.index') }}"><i class="fas fa-graduation-cap"></i> <span>Tahun
                                Masuk</span></a></li>
                @endif
            </ul>
        @endif
    </aside>
</div>
