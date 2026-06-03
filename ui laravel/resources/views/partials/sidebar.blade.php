<style>
    /* Sidebar wrapper */
    .main-sidebar,
    #sidebar-wrapper {
        overflow-x: hidden;
        overflow-y: auto;
        height: 100vh;
    }

    #sidebar-wrapper::-webkit-scrollbar {
        width: 4px;
    }

    #sidebar-wrapper::-webkit-scrollbar-thumb {
        background: rgba(0, 0, 0, 0.2);
        border-radius: 2px;
    }

    /* Sidebar menu dasar */
    .sidebar-menu {
        padding: 0;
        margin: 0;
        list-style: none;
    }

    .sidebar-menu .nav-item {
        position: relative;
        width: 100%;
    }

    /* ── DROPDOWN LEVEL 1 ── */
    .sidebar-menu .dropdown-menu {
        display: none;
        position: static !important;
        float: none !important;
        width: 100% !important;
        box-shadow: none !important;
        border: none !important;
        background-color: rgba(0, 0, 0, 0.05);
        border-radius: 0;
        padding: 0;
        margin: 0;
        overflow: hidden;
    }

    /* ── MULTI-LEVEL: level-1-menu ── */
    .sidebar-menu .level-1-menu {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .sidebar-menu .level-1-menu>li {
        width: 100%;
    }

    /* ── DROPDOWN SUBMENU ── */
    .sidebar-menu .dropdown-submenu {
        position: relative;
        width: 100%;
    }

    .sidebar-menu .dropdown-submenu>.submenu-toggle {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 20px 10px 30px;
        cursor: pointer;
        font-size: 0.875rem;
        color: inherit;
        text-decoration: none;
        transition: background 0.2s;
    }

    .sidebar-menu .dropdown-submenu>.submenu-toggle:hover {
        background-color: rgba(0, 0, 0, 0.06);
    }

    /* Panah submenu level 2 */
    .sidebar-menu .dropdown-submenu>.submenu-toggle::after {
        content: '\203A';
        font-size: 1.1rem;
        transition: transform 0.25s;
        flex-shrink: 0;
    }

    .sidebar-menu .dropdown-submenu.open>.submenu-toggle::after {
        transform: rotate(90deg);
    }

    /* ── SUBMENU LEVEL 3 ── */
    .sidebar-menu .submenu-level {
        display: none;
        list-style: none;
        padding: 0;
        margin: 0;
        background-color: rgba(0, 0, 0, 0.04);
        overflow: hidden;
    }

    .sidebar-menu .submenu-level>li {
        width: 100%;
    }

    .sidebar-menu .submenu-level>li>a {
        display: block;
        padding: 9px 20px 9px 44px;
        font-size: 0.82rem;
        text-decoration: none;
        color: inherit;
        transition: background 0.2s, padding-left 0.2s;
    }

    .sidebar-menu .submenu-level>li>a:hover,
    .sidebar-menu .submenu-level>li>a.active {
        background-color: rgba(0, 0, 0, 0.08);
        padding-left: 50px;
    }

    /* ── PANAH DROPDOWN LEVEL 1 ── */
    .sidebar-menu .nav-link.has-dropdown {
        position: relative;
        display: block !important;
        cursor: pointer;
        padding-right: 35px;
    }

    .sidebar-menu .nav-link.has-dropdown::after {
        content: '\203A';

        position: absolute;
        right: 15px;
        top: 50%;

        transform: translateY(-50%);

        font-size: 1.1rem;

        transition: transform .25s ease;
    }

    .sidebar-menu .nav-item.dropdown.open>.nav-link.has-dropdown::after {
        transform: translateY(-50%) rotate(90deg);
    }
</style>

<div class="main-sidebar">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand mt-3">
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
                <img src="{{ asset('assets/img/Logovokasi.png') }}" style="width: 130px;" alt="Logo Vokasi"
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

                    {{-- ── KOORDINATOR ── --}}
                    @if (in_array(1, $dosenRoles))
                        <li class="menu-header">AI Assistant</li>
                        <li class="nav-item {{ request()->is('ai*') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('ai.kelompok') }}">
                                <img src="{{ asset('assets/img/logoagent.png') }}" alt="VokasiTera Agent"
                                    style="width: 20px; height: 20px; object-fit: contain; display: inline-block; margin-right: 8px;">
                                <span>VokasiTera Agent</span>
                            </a>
                        </li>
                        {{-- <li class="nav-item {{ request()->routeIs('agent.analytics.*') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('agent.analytics.dashboard') }}">
                                <i class="fas fa-chart-line"></i>
                                <span>Agent Analytics</span>
                            </a>
                        </li> --}}

                        <li class="menu-header">Koordinator</li>
                        <li class="nav-item {{ request()->is('dashboard/koordinator*') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('dashboard.koordinator') }}">
                                <i class="fas fa-columns"></i><span>Dashboard</span>
                            </a>
                        </li>
                        <li
                            class="nav-item {{ request()->routeIs('koordinator.tugas.*') || request()->routeIs('tugas.*') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('koordinator.tugas.index') }}">
                                <i class="fas fa-file"></i><span>Tugas</span>
                            </a>
                        </li>
                        <li class="nav-item {{ request()->is('kelompok*') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('kelompok.index') }}">
                                <i class="fas fa-users"></i><span>Kelompok</span>
                            </a>
                        </li>
                        <li class="nav-item {{ request()->routeIs('jadwal.*') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('jadwal.index') }}">
                                <i class="fas fa-calendar"></i><span>Jadwal</span>
                            </a>
                        </li>
                        <li
                            class="nav-item {{ (request()->routeIs('pembimbing.*') || request()->routeIs('pembimbing2.*')) &&
                            !request()->routeIs('pembimbing1.Nilai*') &&
                            !request()->routeIs('pembimbing2.Nilai*')
                                ? 'active'
                                : '' }}">
                            <a href="{{ route('pembimbing.index') }}" class="nav-link">
                                <i class="fas fa-user"></i><span>Pembimbing</span>
                            </a>
                        </li>
                        <li
                            class="nav-item {{ (request()->routeIs('penguji.*') || request()->routeIs('penguji2.*')) &&
                            !request()->routeIs('penguji1.Nilai*') &&
                            !request()->routeIs('penguji2.Nilai*')
                                ? 'active'
                                : '' }}">
                            <a href="{{ route('penguji.index') }}" class="nav-link">
                                <i class="fas fa-user"></i><span>Penguji</span>
                            </a>
                        </li>

                        {{-- Nilai Koordinator --}}
                        <li
                            class="nav-item dropdown {{ request()->routeIs('koordinator.NilaiAdministrasi.*') || request()->routeIs('NilaiAkhir.*') || request()->routeIs('koordinator.NilaiMatkul.*') ? 'active' : '' }}">
                            <a href="#" class="nav-link has-dropdown">
                                <i class="fas fa-clipboard-check"></i><span>Nilai</span>
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="nav-link {{ request()->is('NilaiAdministrasi*') ? 'active' : '' }}"
                                        href="{{ route('koordinator.NilaiAdministrasi.index') }}">Nilai
                                        Administrasi</a>
                                </li>
                                <li>
                                    <a class="nav-link {{ request()->is('NilaiAkhir*') ? 'active' : '' }}"
                                        href="{{ route('NilaiAkhir.index') }}">Nilai PA Mahasiswa</a>
                                </li>
                                {{-- <li>
                                    <a class="nav-link {{ request()->is('NilaiMatkul*') ? 'active' : '' }}"
                                        href="{{ route('koordinator.NilaiMatkul.index') }}">Nilai Matkul Mahasiswa</a>
                                </li> --}}
                            </ul>
                        </li>

                        <li class="nav-item {{ request()->is('pengumuman*') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('pengumuman.index') }}">
                                <i class="fas fa-bell"></i><span>Pengumuman</span>
                            </a>
                        </li>
                    @endif

                    {{-- ── PENGUJI ── --}}
                    @if (in_array(2, $dosenRoles) || in_array(4, $dosenRoles))
                        <li class="menu-header">Penguji</li>

                        <li class="nav-item {{ request()->routeIs('dashboard.penguji') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('dashboard.penguji') }}">
                                <i class="fas fa-columns"></i><span>Dashboard</span>
                            </a>
                        </li>
                        <li
                            class="nav-item {{ request()->routeIs('penguji.tugas.*') || request()->routeIs('penguji.show.submitan') || request()->routeIs('penguji.feedback.*') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('penguji.tugas.index') }}">
                                <i class="fas fa-file"></i><span>Tugas</span>
                            </a>
                        </li>
                        <li class="nav-item {{ request()->routeIs('penguji.jadwal.*') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('penguji.jadwal.index') }}">
                                <i class="fas fa-calendar"></i><span>Jadwal</span>
                            </a>
                        </li>

                        {{-- Nilai Penguji --}}
                        <li
                            class="nav-item dropdown {{ request()->routeIs('penguji1.Nilai*') || request()->routeIs('penguji2.Nilai*') ? 'active' : '' }}">
                            <a href="#" class="nav-link has-dropdown">
                                <i class="fas fa-clipboard-check"></i><span>Nilai</span>
                            </a>
                            <ul class="dropdown-menu level-1-menu">
                                @if (in_array(2, $dosenRoles))
                                    <li
                                        class="dropdown-submenu {{ request()->routeIs('penguji1.Nilai*') ? 'open' : '' }}">
                                        <a href="#" class="submenu-toggle">
                                            <span>Dosen Penguji 1</span>
                                        </a>
                                        <ul class="submenu-level">
                                            <li>
                                                <a href="{{ route('penguji1.NilaiIndividu.index') }}"
                                                    class="{{ request()->routeIs('penguji1.NilaiIndividu.index') ? 'active' : '' }}">
                                                    Nilai Individu
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ route('penguji1.NilaiKelompok.index') }}"
                                                    class="{{ request()->routeIs('penguji1.NilaiKelompok.index') ? 'active' : '' }}">
                                                    Nilai Kelompok
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                @endif
                                @if (in_array(4, $dosenRoles))
                                    <li
                                        class="dropdown-submenu {{ request()->routeIs('penguji2.Nilai*') ? 'open' : '' }}">
                                        <a href="#" class="submenu-toggle">
                                            <span>Dosen Penguji 2</span>
                                        </a>
                                        <ul class="submenu-level">
                                            <li>
                                                <a href="{{ route('penguji2.NilaiIndividu.index') }}"
                                                    class="{{ request()->routeIs('penguji2.NilaiIndividu.index') ? 'active' : '' }}">
                                                    Nilai Individu
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ route('penguji2.NilaiKelompok.index') }}"
                                                    class="{{ request()->routeIs('penguji2.NilaiKelompok.index') ? 'active' : '' }}">
                                                    Nilai Kelompok
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                @endif
                            </ul>
                        </li>
                    @endif

                    {{-- ── PEMBIMBING ── --}}
                    @if (in_array(3, $dosenRoles) || in_array(5, $dosenRoles))
                        <li class="menu-header">Pembimbing</li>

                        <li class="nav-item {{ request()->is('dashboard/pembimbing*') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('dashboard.pembimbing') }}">
                                <i class="fas fa-columns"></i><span>Dashboard</span>
                            </a>
                        </li>
                        <li
                            class="nav-item {{ request()->routeIs('pembimbing.tugas.*') || request()->routeIs('pembimbing.show.submitan') || request()->routeIs('pembimbing.feedback.*') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('pembimbing.tugas.index') }}">
                                <i class="fas fa-file"></i><span>Tugas</span>
                            </a>
                        </li>
                        <li
                            class="nav-item {{ request()->is('dosenpembimbing*') || request()->routeIs('pembimbing.bimbingan.*') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('pembimbing.bimbingan.index') }}">
                                <i class="fas fa-bullhorn"></i><span>Bimbingan</span>
                            </a>
                        </li>
                        <li class="nav-item {{ request()->routeIs('pembimbing.jadwal.*') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('pembimbing.jadwal.index') }}">
                                <i class="fas fa-calendar"></i><span>Jadwal</span>
                            </a>
                        </li>

                        {{-- Nilai Pembimbing --}}
                        <li
                            class="nav-item dropdown {{ request()->routeIs('pembimbing1.NilaiIndividu*') || request()->routeIs('pembimbing2.NilaiIndividu*') || request()->routeIs('pembimbing1.NilaiKelompok*') || request()->routeIs('pembimbing2.NilaiKelompok*') ? 'active' : '' }}">
                            <a href="#" class="nav-link has-dropdown">
                                <i class="fas fa-clipboard-check"></i><span>Nilai Seminar</span>
                            </a>
                            <ul class="dropdown-menu level-1-menu">
                                @if (in_array(3, $dosenRoles))
                                    <li
                                        class="dropdown-submenu {{ request()->routeIs('pembimbing1.NilaiIndividu*') || request()->routeIs('pembimbing1.NilaiKelompok*') ? 'open' : '' }}">
                                        <a href="#" class="submenu-toggle">
                                            <span>Dosen Pembimbing 1</span>
                                        </a>
                                        <ul class="submenu-level">
                                            <li>
                                                <a href="{{ route('pembimbing1.NilaiIndividu.index') }}"
                                                    class="{{ request()->routeIs('pembimbing1.NilaiIndividu.index') ? 'active' : '' }}">
                                                    Nilai Individu (Seminar)
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ route('pembimbing1.NilaiKelompok.index') }}"
                                                    class="{{ request()->routeIs('pembimbing1.NilaiKelompok.index') ? 'active' : '' }}">
                                                    Nilai Kelompok (Seminar)
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                @endif
                                @if (in_array(5, $dosenRoles))
                                    <li
                                        class="dropdown-submenu {{ request()->routeIs('pembimbing2.NilaiIndividu*') || request()->routeIs('pembimbing2.NilaiKelompok*') ? 'open' : '' }}">
                                        <a href="#" class="submenu-toggle">
                                            <span>Dosen Pembimbing 2</span>
                                        </a>
                                        <ul class="submenu-level">
                                            <li>
                                                <a href="{{ route('pembimbing2.NilaiIndividu.index') }}"
                                                    class="{{ request()->routeIs('pembimbing2.NilaiIndividu.index') ? 'active' : '' }}">
                                                    Nilai Individu (Seminar)
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ route('pembimbing2.NilaiKelompok.index') }}"
                                                    class="{{ request()->routeIs('pembimbing2.NilaiKelompok.index') ? 'active' : '' }}">
                                                    Nilai Kelompok (Seminar)
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                @endif
                            </ul>
                        </li>

                        {{-- Nilai Bimbingan — flat, tidak berlevel, satu menu untuk semua pembimbing --}}
                        <li
                            class="nav-item {{ request()->routeIs('pembimbing1.NilaiBimbingan*') || request()->routeIs('pembimbing2.NilaiBimbingan*') ? 'active' : '' }}">
                            <a class="nav-link"
                                href="{{ in_array(3, $dosenRoles) ? route('pembimbing1.NilaiBimbingan.index') : route('pembimbing2.NilaiBimbingan.index') }}">
                                <i class="fas fa-star-half-alt"></i><span>Nilai Bimbingan</span>
                            </a>
                        </li>

                        <li class="nav-item {{ request()->routeIs('PembimbingPengajuanSeminar.*') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('PembimbingPengajuanSeminar.index') }}">
                                <i class="fas fa-calendar-check"></i><span>Pengajuan Seminar</span>
                            </a>
                        </li>
                    @endif
                @elseif (session('role') == 'Mahasiswa')
                    <li class="menu-header">MahaSiswa</li>
                    <li class="nav-item {{ request()->is('dashboard/mahasiswa*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('dashboard.mahasiswa') }}">
                            <i class="fas fa-columns"></i><span>Dashboard</span>
                        </a>
                    </li>
                    <li
                        class="nav-item {{ request()->is('Mahasiswa/Tugas*') || request()->routeIs('Mahasiswa.tugas.*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('Mahasiswa.tugas.index') }}">
                            <i class="fas fa-file"></i><span>Tugas</span>
                        </a>
                    </li>
                    <li class="nav-item {{ request()->is('artefak*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('artefak.index') }}">
                            <i class="fas fa-file"></i><span>Artefak</span>
                        </a>
                    </li>
                    <li class="nav-item {{ request()->is('bimbingan*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('bimbingan.index') }}">
                            <i class="fas fa-list"></i><span>Bimbingan</span>
                        </a>
                    </li>
                    <li class="nav-item {{ request()->is('pengumuman*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('pengumuman.mahasiswa.index') }}">
                            <i class="fas fa-bell"></i><span>Pengumuman</span>
                        </a>
                    </li>
                    <li class="nav-item {{ request()->is('jadwal*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('mahasiswa.jadwal.index') }}">
                            <i class="fas fa-calendar"></i><span>Jadwal</span>
                        </a>
                    </li>
                    <li class="nav-item {{ request()->is('Histori*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('Histori.index') }}">
                            <i class="fas fa-history"></i><span>Histori</span>
                        </a>
                    </li>
                @elseif (session('role') == 'Staff')
                    <li class="menu-header">Staff</li>
                    <li class="nav-item {{ request()->is('dashboard/BAAK*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('dashboard.BAAK') }}">
                            <i class="fas fa-columns"></i><span>Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item {{ request()->is('manajemen role*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('manajemen-role.index') }}">
                            <i class="fas fa-user"></i><span>Manajemen-Role</span>
                        </a>
                    </li>
                    <li class="nav-item {{ request()->is('staff/jadwal*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('baak.jadwal.index') }}">
                            <i class="fas fa-calendar"></i><span>Jadwal</span>
                        </a>
                    </li>
                    <li class="nav-item {{ request()->is('pengumuman/BAAK*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('pengumuman.BAAK.index') }}">
                            <i class="fas fa-bell"></i><span>Pengumuman</span>
                        </a>
                    </li>
                    <li class="nav-item {{ request()->is('TahunMasuk*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('TahunMasuk.index') }}">
                            <i class="fas fa-graduation-cap"></i><span>Tahun Masuk</span>
                        </a>
                    </li>
                @endif

            </ul>
        @endif
    </aside>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {

        // Helper: buka menu (set height aktual dari konten)
        function openMenu(el) {
            el.style.display = "block";
            // Gunakan requestAnimationFrame agar browser sempat render display:block
            // sebelum kita baca scrollHeight
            requestAnimationFrame(() => {
                el.style.transition = "max-height 0.3s ease";
                el.style.maxHeight = el.scrollHeight + "px";

                // Setelah transisi selesai, set ke 'none' supaya child expand juga ikut
                el.addEventListener("transitionend", function handler() {
                    if (el.style.maxHeight !== "0px") {
                        el.style.maxHeight = "none";
                    }
                    el.removeEventListener("transitionend", handler);
                });
            });
        }

        // Helper: tutup menu
        function closeMenu(el) {
            // Set dulu ke height aktual agar transisi ke 0 bekerja
            el.style.maxHeight = el.scrollHeight + "px";
            requestAnimationFrame(() => {
                el.style.transition = "max-height 0.3s ease";
                el.style.maxHeight = "0px";
                setTimeout(() => {
                    el.style.display = "none";
                }, 310);
            });
        }

        // ── Toggle LEVEL 1: .has-dropdown ──
        document.querySelectorAll(".nav-link.has-dropdown").forEach(link => {

            link.addEventListener("click", function(e) {

                e.preventDefault();
                e.stopPropagation();

                const parentLi = this.closest(".nav-item.dropdown");
                if (!parentLi) return;

                const menu = parentLi.querySelector(":scope > .dropdown-menu");
                if (!menu) return;

                const isOpen = parentLi.classList.contains("open");

                // ============================
                // TUTUP SEMUA DROPDOWN LAIN
                // ============================
                document.querySelectorAll(".nav-item.dropdown.open").forEach(item => {

                    if (item !== parentLi) {

                        item.classList.remove("open");

                        const otherMenu =
                            item.querySelector(":scope > .dropdown-menu");

                        if (otherMenu) {
                            closeMenu(otherMenu);
                        }

                        // Reset semua level 2 & 3
                        item.querySelectorAll(".dropdown-submenu.open")
                            .forEach(sub => {

                                sub.classList.remove("open");

                                const subMenu =
                                    sub.querySelector(":scope > .submenu-level");

                                if (subMenu) {
                                    closeMenu(subMenu);
                                }

                            });

                    }

                });

                if (isOpen) {

                    parentLi.classList.remove("open");
                    closeMenu(menu);

                    // reset semua level 2
                    parentLi.querySelectorAll(".dropdown-submenu.open")
                        .forEach(sub => {

                            sub.classList.remove("open");

                            const subMenu =
                                sub.querySelector(":scope > .submenu-level");

                            if (subMenu) {
                                closeMenu(subMenu);
                            }

                        });

                } else {

                    parentLi.classList.add("open");
                    openMenu(menu);

                }

            });

        });

        // ── Toggle LEVEL 2: .submenu-toggle ──
        document.querySelectorAll(".submenu-toggle").forEach(toggle => {

            toggle.addEventListener("click", function(e) {

                e.preventDefault();
                e.stopPropagation();

                const parent =
                    this.closest(".dropdown-submenu");

                const submenu =
                    parent.querySelector(":scope > .submenu-level");

                if (!submenu) return;

                const isOpen =
                    parent.classList.contains("open");

                // Tutup sibling submenu
                parent.parentElement
                    .querySelectorAll(":scope > .dropdown-submenu.open")
                    .forEach(item => {

                        if (item !== parent) {

                            item.classList.remove("open");

                            const otherSub =
                                item.querySelector(":scope > .submenu-level");

                            if (otherSub) {
                                closeMenu(otherSub);
                            }

                        }

                    });

                if (isOpen) {

                    parent.classList.remove("open");
                    closeMenu(submenu);

                } else {

                    parent.classList.add("open");
                    openMenu(submenu);

                }

            });

        });

        // ── Auto-open saat page load (jika ada class active/open dari server) ──
        document.querySelectorAll(".nav-item.dropdown.active").forEach(item => {
            const menu = item.querySelector(":scope > .dropdown-menu");
            if (menu) {
                item.classList.add("open");
                menu.style.display = "block";
                menu.style.maxHeight = "none";
            }
        });

        document.querySelectorAll(".dropdown-submenu.open").forEach(sub => {
            const menu = sub.querySelector(":scope > .submenu-level");
            if (menu) {
                menu.style.display = "block";
                menu.style.maxHeight = "none";
                // Pastikan parent dropdown-nya juga terbuka
                const parentDropdown = sub.closest(".nav-item.dropdown");
                if (parentDropdown) {
                    const parentMenu = parentDropdown.querySelector(":scope > .dropdown-menu");
                    if (parentMenu) {
                        parentDropdown.classList.add("open");
                        parentMenu.style.display = "block";
                        parentMenu.style.maxHeight = "none";
                    }
                }
            }
        });

    });
</script>
