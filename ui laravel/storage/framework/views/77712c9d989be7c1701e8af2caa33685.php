<style>
    /* ══════════════════════════════════════
       SIDEBAR WRAPPER
    ══════════════════════════════════════ */
    .main-sidebar {
        width: 250px;
        height: 100vh;
        position: fixed;
        top: 0;
        left: 0;
        z-index: 1040;
        transition: width 0.3s ease, transform 0.3s ease;
        overflow: visible !important;
    }

    #sidebar-wrapper {
        width: 250px;
        height: 100vh;
        overflow-x: hidden;
        overflow-y: auto;
        transition: width 0.3s ease;
    }

    #sidebar-wrapper::-webkit-scrollbar {
        width: 4px;
    }

    #sidebar-wrapper::-webkit-scrollbar-thumb {
        background: rgba(0, 0, 0, 0.2);
        border-radius: 2px;
    }

    /* ══════════════════════════════════════
       TOMBOL CLOSE (mobile)
    ══════════════════════════════════════ */
    .sidebar-close-btn {
        display: none;
        position: absolute;
        top: 10px;
        right: 10px;
        background: rgba(255, 255, 255, 0.15);
        border: none;
        color: #fff;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        font-size: 18px;
        line-height: 1;
        cursor: pointer;
        z-index: 10;
        align-items: center;
        justify-content: center;
        transition: background 0.2s;
    }

    .sidebar-close-btn:hover {
        background: rgba(255, 255, 255, 0.3);
    }

    /* ══════════════════════════════════════
       OVERLAY BACKDROP (mobile)
    ══════════════════════════════════════ */
    .sidebar-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.45);
        z-index: 1039;
        backdrop-filter: blur(2px);
    }

    .sidebar-overlay.active {
        display: block;
    }

    /* ══════════════════════════════════════
       SIDEBAR MENU DASAR
    ══════════════════════════════════════ */
    .sidebar-menu {
        padding: 0;
        margin: 0;
        list-style: none;
    }

    .sidebar-menu .nav-item {
        position: relative;
        width: 100%;
    }

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

    .sidebar-menu .level-1-menu {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .sidebar-menu .level-1-menu>li {
        width: 100%;
    }

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

    .sidebar-menu .dropdown-submenu>.submenu-toggle::after {
        content: '\203A';
        font-size: 1.1rem;
        transition: transform 0.25s;
        flex-shrink: 0;
    }

    .sidebar-menu .dropdown-submenu.open>.submenu-toggle::after {
        transform: rotate(90deg);
    }

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

    .sidebar-menu .nav-link.has-dropdown {
        position: relative;
        display: flex !important;
        align-items: center;
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
        transition: transform 0.25s ease;
    }

    .sidebar-menu .nav-item.dropdown.open>.nav-link.has-dropdown::after {
        transform: translateY(-50%) rotate(90deg);
    }

    /* ══════════════════════════════════════
       MODE MINI / COLLAPSED (desktop)
    ══════════════════════════════════════ */
    body.sidebar-mini .main-sidebar,
    body.sidebar-gone .main-sidebar {
        width: 60px;
        overflow: visible !important;
    }

    body.sidebar-mini #sidebar-wrapper,
    body.sidebar-gone #sidebar-wrapper {
        width: 60px;
        overflow: hidden;
    }

    body.sidebar-mini .menu-header,
    body.sidebar-gone .menu-header {
        opacity: 0;
        height: 0 !important;
        padding: 0 !important;
        margin: 0 !important;
        overflow: hidden;
        pointer-events: none;
    }

    body.sidebar-mini .sidebar-brand p,
    body.sidebar-mini .sidebar-brand .brand-label,
    body.sidebar-gone .sidebar-brand p,
    body.sidebar-gone .sidebar-brand .brand-label {
        display: none;
    }

    body.sidebar-mini .sidebar-brand,
    body.sidebar-gone .sidebar-brand {
        justify-content: center;
        padding: 8px 0;
    }

    body.sidebar-mini .sidebar-brand img,
    body.sidebar-gone .sidebar-brand img {
        width: 36px !important;
    }

    body.sidebar-mini .sidebar-menu .nav-link,
    body.sidebar-gone .sidebar-menu .nav-link {
        justify-content: center !important;
        padding: 14px 0 !important;
        padding-right: 0 !important;
    }

    body.sidebar-mini .sidebar-menu .nav-link>span,
    body.sidebar-gone .sidebar-menu .nav-link>span {
        display: none !important;
    }

    body.sidebar-mini .sidebar-menu .nav-link i,
    body.sidebar-mini .sidebar-menu .nav-link img,
    body.sidebar-gone .sidebar-menu .nav-link i,
    body.sidebar-gone .sidebar-menu .nav-link img {
        margin-right: 0 !important;
        margin: 0 !important;
    }

    body.sidebar-mini .sidebar-menu .nav-link.has-dropdown::after,
    body.sidebar-gone .sidebar-menu .nav-link.has-dropdown::after {
        display: none !important;
    }

    body.sidebar-mini .sidebar-menu .dropdown-menu,
    body.sidebar-gone .sidebar-menu .dropdown-menu {
        display: none !important;
        max-height: 0 !important;
        overflow: hidden !important;
    }

    /* ══════════════════════════════════════
       RESPONSIVE BREAKPOINTS
    ══════════════════════════════════════ */

    /* ── TABLET (768px – 1023px): auto mini ── */
    @media (max-width: 1023px) and (min-width: 768px) {
        .main-sidebar {
            width: 60px !important;
            overflow: visible !important;
        }

        #sidebar-wrapper {
            width: 60px !important;
            overflow: hidden !important;
        }

        .menu-header {
            opacity: 0 !important;
            height: 0 !important;
            padding: 0 !important;
            margin: 0 !important;
            overflow: hidden !important;
            pointer-events: none !important;
        }

        .sidebar-brand p,
        .sidebar-brand .brand-label {
            display: none !important;
        }

        .sidebar-brand {
            justify-content: center !important;
            padding: 8px 0 !important;
        }

        .sidebar-brand img {
            width: 36px !important;
        }

        .sidebar-menu .nav-link {
            justify-content: center !important;
            padding: 14px 0 !important;
            padding-right: 0 !important;
        }

        .sidebar-menu .nav-link>span {
            display: none !important;
        }

        .sidebar-menu .nav-link i,
        .sidebar-menu .nav-link img {
            margin-right: 0 !important;
            margin: 0 !important;
        }

        .sidebar-menu .nav-link.has-dropdown::after {
            display: none !important;
        }

        .sidebar-menu .dropdown-menu {
            display: none !important;
            max-height: 0 !important;
            overflow: hidden !important;
        }

        .sidebar-close-btn {
            display: none !important;
        }

        /* Konten utama mengikuti */
        #main-wrapper,
        .main-content,
        .page-wrapper {
            margin-left: 60px !important;
        }
    }

    /* ── MOBILE (< 768px): sidebar sebagai drawer ── */
    @media (max-width: 767px) {
        .main-sidebar {
            width: 260px !important;
            transform: translateX(-100%);
            z-index: 1040;
            overflow: visible !important;
        }

        #sidebar-wrapper {
            width: 260px !important;
            overflow-y: auto !important;
            overflow-x: hidden !important;
        }

        /* Sidebar terbuka di mobile */
        .main-sidebar.mobile-open {
            transform: translateX(0);
        }

        /* Reset mini mode di mobile */
        body.sidebar-mini .main-sidebar,
        body.sidebar-gone .main-sidebar {
            width: 260px !important;
            transform: translateX(-100%);
        }

        body.sidebar-mini .main-sidebar.mobile-open,
        body.sidebar-gone .main-sidebar.mobile-open {
            transform: translateX(0);
        }

        body.sidebar-mini #sidebar-wrapper,
        body.sidebar-gone #sidebar-wrapper {
            width: 260px !important;
        }

        /* Tampilkan teks menu di mobile walaupun sidebar-mini */
        body.sidebar-mini .sidebar-menu .nav-link,
        body.sidebar-gone .sidebar-menu .nav-link {
            justify-content: flex-start !important;
            padding: 12px 15px !important;
        }

        body.sidebar-mini .sidebar-menu .nav-link>span,
        body.sidebar-gone .sidebar-menu .nav-link>span {
            display: inline !important;
        }

        body.sidebar-mini .sidebar-menu .nav-link i,
        body.sidebar-mini .sidebar-menu .nav-link img,
        body.sidebar-gone .sidebar-menu .nav-link i,
        body.sidebar-gone .sidebar-menu .nav-link img {
            margin-right: 8px !important;
        }

        body.sidebar-mini .sidebar-menu .nav-link.has-dropdown::after,
        body.sidebar-gone .sidebar-menu .nav-link.has-dropdown::after {
            display: block !important;
        }

        body.sidebar-mini .sidebar-menu .dropdown-menu,
        body.sidebar-gone .sidebar-menu .dropdown-menu {
            display: none !important;
            max-height: unset !important;
            overflow: hidden !important;
        }

        body.sidebar-mini .menu-header,
        body.sidebar-gone .menu-header {
            opacity: 1 !important;
            height: auto !important;
            padding: initial !important;
            pointer-events: auto !important;
        }

        body.sidebar-mini .sidebar-brand p,
        body.sidebar-mini .sidebar-brand .brand-label,
        body.sidebar-gone .sidebar-brand p,
        body.sidebar-gone .sidebar-brand .brand-label {
            display: block !important;
        }

        body.sidebar-mini .sidebar-brand,
        body.sidebar-gone .sidebar-brand {
            justify-content: flex-start !important;
            padding: 8px 15px !important;
        }

        body.sidebar-mini .sidebar-brand img,
        body.sidebar-gone .sidebar-brand img {
            width: 100px !important;
        }

        /* Konten utama full width di mobile */
        #main-wrapper,
        .main-content,
        .page-wrapper {
            margin-left: 0 !important;
        }

        /* Tampilkan tombol close */
        .sidebar-close-btn {
            display: flex !important;
        }

        /* Sembunyikan flyout di mobile */
        .mini-flyout {
            display: none !important;
        }
    }

    /* ══════════════════════════════════════
       FLYOUT PANEL (mode mini)
    ══════════════════════════════════════ */
    .mini-flyout {
        display: none;
        position: fixed !important;
        min-width: 220px;
        background: #1e4a5f;
        border-left: 3px solid #4C9BC8;
        border-radius: 0 8px 8px 0;
        box-shadow: 6px 4px 24px rgba(0, 0, 0, 0.45);
        z-index: 999999 !important;
        overflow: hidden;
        padding: 6px 0;
        pointer-events: auto;
    }

    .mini-flyout .flyout-header {
        display: block;
        padding: 10px 16px 8px;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        color: #4C9BC8;
        border-bottom: 1px solid rgba(76, 155, 200, 0.25);
        white-space: nowrap;
        pointer-events: none;
        background: rgba(0, 0, 0, 0.15);
    }

    .mini-flyout a.flyout-link {
        display: flex !important;
        align-items: center;
        gap: 10px;
        padding: 10px 18px;
        font-size: 0.875rem;
        color: rgba(255, 255, 255, 0.88) !important;
        text-decoration: none !important;
        white-space: nowrap;
        transition: background 0.15s, color 0.15s;
    }

    .mini-flyout a.flyout-link:hover {
        background: rgba(76, 155, 200, 0.22) !important;
        color: #fff !important;
    }

    .mini-flyout a.flyout-link i {
        font-size: 14px;
        width: 18px;
        text-align: center;
        color: #4C9BC8;
        flex-shrink: 0;
    }

    .mini-flyout .flyout-group-title {
        display: block;
        padding: 8px 18px 4px;
        font-size: 0.68rem;
        font-weight: 700;
        color: #4C9BC8;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        white-space: nowrap;
        opacity: 0.9;
    }

    .mini-flyout a.flyout-sub-link {
        display: flex !important;
        align-items: center;
        gap: 8px;
        padding: 8px 18px 8px 32px;
        font-size: 0.82rem;
        color: rgba(255, 255, 255, 0.78) !important;
        text-decoration: none !important;
        white-space: nowrap;
        transition: background 0.15s, color 0.15s;
    }

    .mini-flyout a.flyout-sub-link:hover {
        background: rgba(76, 155, 200, 0.22) !important;
        color: #fff !important;
    }

    .mini-flyout a.flyout-sub-link i {
        font-size: 12px;
        width: 14px;
        text-align: center;
        color: rgba(76, 155, 200, 0.75);
        flex-shrink: 0;
    }

    .mini-flyout .flyout-divider {
        height: 1px;
        background: rgba(76, 155, 200, 0.2);
        margin: 4px 0;
    }
</style>


<div class="sidebar-overlay" id="sidebarOverlay"></div>

<div class="main-sidebar">
    
    <button class="sidebar-close-btn" id="sidebarCloseBtn" aria-label="Tutup Sidebar">&#x2715;</button>

    <aside id="sidebar-wrapper">
        <div class="sidebar-brand mt-3">
            <?php
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
            ?>

            <a href="<?php echo e($dashboardRoute); ?>" style="text-decoration: none; cursor: pointer;">
                <img src="<?php echo e(asset('assets/img/Logovokasi.png')); ?>" style="width: 130px;" alt="Logo Vokasi"
                    title="Kembali ke Dashboard">
            </a>

            <?php if(session('isLoggin')): ?>
                <p class="brand-label">User Logged In as: <?php echo e(session('role')); ?></p>
            <?php else: ?>
                <p class="brand-label">Tidak ada pengguna yang login.</p>
            <?php endif; ?>
        </div>

        <?php if(session('isLoggin')): ?>
            <ul class="sidebar-menu">

                <?php if(session('role') == 'Dosen'): ?>
                    <?php $dosenRoles = session('dosen_roles'); ?>

                    
                    <?php if(in_array(1, $dosenRoles)): ?>
                        <li class="menu-header">AI Assistant</li>
                        <li class="nav-item <?php echo e(request()->is('ai*') ? 'active' : ''); ?>"
                            data-flyout-label="AI Assistant">
                            <a class="nav-link" href="<?php echo e(route('ai.kelompok')); ?>">
                                <img src="<?php echo e(asset('assets/img/logoagent.png')); ?>" alt="VokasiTera Agent"
                                    style="width: 20px; height: 20px; object-fit: contain; display: inline-block; margin-right: 8px;">
                                <span>VokasiTera Agent</span>
                            </a>
                            <div class="mini-flyout">
                                <span class="flyout-header">AI Assistant</span>
                                <a class="flyout-link" href="<?php echo e(route('ai.kelompok')); ?>">
                                    <i class="fas fa-robot"></i> VokasiTera Agent
                                </a>
                            </div>
                        </li>

                        <li class="menu-header">Koordinator</li>

                        <li class="nav-item <?php echo e(request()->routeIs('dashboard.koordinator') ? 'active' : ''); ?>">
                            <a class="nav-link" href="<?php echo e(route('dashboard.koordinator')); ?>">
                                <i class="fas fa-columns"></i><span>Dashboard</span>
                            </a>
                            <div class="mini-flyout">
                                <a class="flyout-link" href="<?php echo e(route('dashboard.koordinator')); ?>">
                                    <i class="fas fa-columns"></i> Dashboard
                                </a>
                            </div>
                        </li>

                        <li class="nav-item <?php echo e(request()->routeIs('koordinator.tugas.*') ? 'active' : ''); ?>">
                            <a class="nav-link" href="<?php echo e(route('koordinator.tugas.index')); ?>">
                                <i class="fas fa-file"></i><span>Tugas</span>
                            </a>
                            <div class="mini-flyout">
                                <a class="flyout-link" href="<?php echo e(route('koordinator.tugas.index')); ?>">
                                    <i class="fas fa-file"></i> Tugas
                                </a>
                            </div>
                        </li>

                        <li class="nav-item <?php echo e(request()->routeIs('kelompok.*') ? 'active' : ''); ?>">
                            <a class="nav-link" href="<?php echo e(route('kelompok.index')); ?>">
                                <i class="fas fa-users"></i><span>Kelompok</span>
                            </a>
                            <div class="mini-flyout">
                                <a class="flyout-link" href="<?php echo e(route('kelompok.index')); ?>">
                                    <i class="fas fa-users"></i> Kelompok
                                </a>
                            </div>
                        </li>

                        <li
                            class="nav-item <?php echo e(request()->routeIs('jadwal.index') || request()->routeIs('jadwal.create') || request()->routeIs('jadwal.edit') || request()->routeIs('jadwal.show') || request()->routeIs('jadwal.store') || request()->routeIs('jadwal.update') || request()->routeIs('jadwal.destroy') ? 'active' : ''); ?>">
                            <a class="nav-link" href="<?php echo e(route('jadwal.index')); ?>">
                                <i class="fas fa-calendar"></i><span>Jadwal</span>
                            </a>
                            <div class="mini-flyout">
                                <a class="flyout-link" href="<?php echo e(route('jadwal.index')); ?>">
                                    <i class="fas fa-calendar"></i> Jadwal
                                </a>
                            </div>
                        </li>

                        <li
                            class="nav-item <?php echo e((request()->routeIs('pembimbing.index') || request()->routeIs('pembimbing.create') || request()->routeIs('pembimbing.store') || request()->routeIs('pembimbing.edit') || request()->routeIs('pembimbing.update') || request()->routeIs('pembimbing.destroy') || request()->routeIs('pembimbing.show') || request()->routeIs('pembimbing2.index') || request()->routeIs('pembimbing2.create') || request()->routeIs('pembimbing2.store') || request()->routeIs('pembimbing2.edit') || request()->routeIs('pembimbing2.update') || request()->routeIs('pembimbing2.destroy') || request()->routeIs('pembimbing2.show')) &&!request()->routeIs('pembimbing.tugas.*') &&!request()->routeIs('pembimbing.bimbingan.*') &&!request()->routeIs('pembimbing.jadwal.*') &&!request()->routeIs('pembimbing1.Nilai*') &&!request()->routeIs('pembimbing2.Nilai*') &&!request()->routeIs('pembimbing1.NilaiBimbingan*') &&!request()->routeIs('pembimbing2.NilaiBimbingan*') &&!request()->routeIs('PembimbingPengajuanSeminar.*')? 'active': ''); ?>">
                            <a href="<?php echo e(route('pembimbing.index')); ?>" class="nav-link">
                                <i class="fas fa-user"></i><span>Pembimbing</span>
                            </a>
                            <div class="mini-flyout">
                                <a class="flyout-link" href="<?php echo e(route('pembimbing.index')); ?>">
                                    <i class="fas fa-user"></i> Pembimbing
                                </a>
                            </div>
                        </li>

                        <li
                            class="nav-item <?php echo e((request()->routeIs('penguji.index') || request()->routeIs('penguji.create') || request()->routeIs('penguji.store') || request()->routeIs('penguji.edit') || request()->routeIs('penguji.update') || request()->routeIs('penguji.destroy') || request()->routeIs('penguji.show') || request()->routeIs('penguji2.index') || request()->routeIs('penguji2.create') || request()->routeIs('penguji2.store') || request()->routeIs('penguji2.edit') || request()->routeIs('penguji2.update') || request()->routeIs('penguji2.destroy') || request()->routeIs('penguji2.show')) && !request()->routeIs('penguji.tugas.*') && !request()->routeIs('penguji.jadwal.*') && !request()->routeIs('penguji.show.submitan') && !request()->routeIs('penguji.feedback.*') && !request()->routeIs('penguji1.Nilai*') && !request()->routeIs('penguji2.Nilai*') ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('penguji.index')); ?>" class="nav-link">
                                <i class="fas fa-user"></i><span>Penguji</span>
                            </a>
                            <div class="mini-flyout">
                                <a class="flyout-link" href="<?php echo e(route('penguji.index')); ?>">
                                    <i class="fas fa-user"></i> Penguji
                                </a>
                            </div>
                        </li>

                        <li
                            class="nav-item dropdown <?php echo e(request()->routeIs('koordinator.NilaiAdministrasi.*') || request()->routeIs('NilaiAkhir.*') || request()->routeIs('koordinator.NilaiMatkul.*') ? 'active' : ''); ?>">
                            <a href="#" class="nav-link has-dropdown">
                                <i class="fas fa-clipboard-check"></i><span>Nilai</span>
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="nav-link <?php echo e(request()->routeIs('koordinator.NilaiAdministrasi.*') ? 'active' : ''); ?>"
                                        href="<?php echo e(route('koordinator.NilaiAdministrasi.index')); ?>">Nilai
                                        Administrasi</a>
                                </li>
                                <li>
                                    <a class="nav-link <?php echo e(request()->routeIs('NilaiAkhir.*') ? 'active' : ''); ?>"
                                        href="<?php echo e(route('NilaiAkhir.index')); ?>">Nilai Akhir PA Mahasiswa</a>
                                </li>
                            </ul>
                            <div class="mini-flyout">
                                <span class="flyout-header">Nilai</span>
                                <a class="flyout-link" href="<?php echo e(route('koordinator.NilaiAdministrasi.index')); ?>">
                                    <i class="fas fa-file-alt"></i> Nilai Administrasi
                                </a>
                                <a class="flyout-link" href="<?php echo e(route('NilaiAkhir.index')); ?>">
                                    <i class="fas fa-graduation-cap"></i> Nilai PA Mahasiswa
                                </a>
                            </div>
                        </li>

                        <li
                            class="nav-item <?php echo e(request()->routeIs('pengumuman.index') || request()->routeIs('pengumuman.create') || request()->routeIs('pengumuman.store') || request()->routeIs('pengumuman.edit') || request()->routeIs('pengumuman.update') || request()->routeIs('pengumuman.destroy') || request()->routeIs('pengumuman.show') ? 'active' : ''); ?>">
                            <a class="nav-link" href="<?php echo e(route('pengumuman.index')); ?>">
                                <i class="fas fa-bell"></i><span>Pengumuman</span>
                            </a>
                            <div class="mini-flyout">
                                <a class="flyout-link" href="<?php echo e(route('pengumuman.index')); ?>">
                                    <i class="fas fa-bell"></i> Pengumuman
                                </a>
                            </div>
                        </li>
                    <?php endif; ?>

                    
                    <?php if(in_array(2, $dosenRoles) || in_array(4, $dosenRoles)): ?>
                        <li class="menu-header">Penguji</li>

                        <li class="nav-item <?php echo e(request()->routeIs('dashboard.penguji') ? 'active' : ''); ?>">
                            <a class="nav-link" href="<?php echo e(route('dashboard.penguji')); ?>">
                                <i class="fas fa-columns"></i><span>Dashboard</span>
                            </a>
                            <div class="mini-flyout">
                                <a class="flyout-link" href="<?php echo e(route('dashboard.penguji')); ?>">
                                    <i class="fas fa-columns"></i> Dashboard
                                </a>
                            </div>
                        </li>

                        <li
                            class="nav-item <?php echo e(request()->routeIs('penguji.tugas.*') || request()->routeIs('penguji.show.submitan') || request()->routeIs('penguji.feedback.*') ? 'active' : ''); ?>">
                            <a class="nav-link" href="<?php echo e(route('penguji.tugas.index')); ?>">
                                <i class="fas fa-file"></i><span>Tugas</span>
                            </a>
                            <div class="mini-flyout">
                                <a class="flyout-link" href="<?php echo e(route('penguji.tugas.index')); ?>">
                                    <i class="fas fa-file"></i> Tugas
                                </a>
                            </div>
                        </li>

                        <li class="nav-item <?php echo e(request()->routeIs('penguji.jadwal.*') ? 'active' : ''); ?>">
                            <a class="nav-link" href="<?php echo e(route('penguji.jadwal.index')); ?>">
                                <i class="fas fa-calendar"></i><span>Jadwal</span>
                            </a>
                            <div class="mini-flyout">
                                <a class="flyout-link" href="<?php echo e(route('penguji.jadwal.index')); ?>">
                                    <i class="fas fa-calendar"></i> Jadwal
                                </a>
                            </div>
                        </li>

                        <li
                            class="nav-item dropdown <?php echo e(request()->routeIs('penguji1.Nilai*') || request()->routeIs('penguji2.Nilai*') ? 'active' : ''); ?>">
                            <a href="#" class="nav-link has-dropdown">
                                <i class="fas fa-clipboard-check"></i><span>Nilai</span>
                            </a>
                            <ul class="dropdown-menu level-1-menu">
                                <?php if(in_array(2, $dosenRoles)): ?>
                                    <li
                                        class="dropdown-submenu <?php echo e(request()->routeIs('penguji1.Nilai*') ? 'open' : ''); ?>">
                                        <a href="#" class="submenu-toggle"><span>Dosen Penguji 1</span></a>
                                        <ul class="submenu-level">
                                            <li><a href="<?php echo e(route('penguji1.NilaiIndividu.index')); ?>"
                                                    class="<?php echo e(request()->routeIs('penguji1.NilaiIndividu.index') ? 'active' : ''); ?>">Nilai
                                                    Individu</a></li>
                                            <li><a href="<?php echo e(route('penguji1.NilaiKelompok.index')); ?>"
                                                    class="<?php echo e(request()->routeIs('penguji1.NilaiKelompok.index') ? 'active' : ''); ?>">Nilai
                                                    Kelompok</a></li>
                                        </ul>
                                    </li>
                                <?php endif; ?>
                                <?php if(in_array(4, $dosenRoles)): ?>
                                    <li
                                        class="dropdown-submenu <?php echo e(request()->routeIs('penguji2.Nilai*') ? 'open' : ''); ?>">
                                        <a href="#" class="submenu-toggle"><span>Dosen Penguji 2</span></a>
                                        <ul class="submenu-level">
                                            <li><a href="<?php echo e(route('penguji2.NilaiIndividu.index')); ?>"
                                                    class="<?php echo e(request()->routeIs('penguji2.NilaiIndividu.index') ? 'active' : ''); ?>">Nilai
                                                    Individu</a></li>
                                            <li><a href="<?php echo e(route('penguji2.NilaiKelompok.index')); ?>"
                                                    class="<?php echo e(request()->routeIs('penguji2.NilaiKelompok.index') ? 'active' : ''); ?>">Nilai
                                                    Kelompok</a></li>
                                        </ul>
                                    </li>
                                <?php endif; ?>
                            </ul>
                            <div class="mini-flyout">
                                <span class="flyout-header">Nilai</span>
                                <?php if(in_array(2, $dosenRoles)): ?>
                                    <span class="flyout-group-title">Dosen Penguji 1</span>
                                    <a class="flyout-sub-link" href="<?php echo e(route('penguji1.NilaiIndividu.index')); ?>"><i
                                            class="fas fa-user"></i> Nilai Individu</a>
                                    <a class="flyout-sub-link" href="<?php echo e(route('penguji1.NilaiKelompok.index')); ?>"><i
                                            class="fas fa-users"></i> Nilai Kelompok</a>
                                <?php endif; ?>
                                <?php if(in_array(2, $dosenRoles) && in_array(4, $dosenRoles)): ?>
                                    <div class="flyout-divider"></div>
                                <?php endif; ?>
                                <?php if(in_array(4, $dosenRoles)): ?>
                                    <span class="flyout-group-title">Dosen Penguji 2</span>
                                    <a class="flyout-sub-link" href="<?php echo e(route('penguji2.NilaiIndividu.index')); ?>"><i
                                            class="fas fa-user"></i> Nilai Individu</a>
                                    <a class="flyout-sub-link" href="<?php echo e(route('penguji2.NilaiKelompok.index')); ?>"><i
                                            class="fas fa-users"></i> Nilai Kelompok</a>
                                <?php endif; ?>
                            </div>
                        </li>
                    <?php endif; ?>

                    
                    <?php if(in_array(3, $dosenRoles) || in_array(5, $dosenRoles)): ?>
                        <li class="menu-header">Pembimbing</li>

                        <li class="nav-item <?php echo e(request()->routeIs('dashboard.pembimbing') ? 'active' : ''); ?>">
                            <a class="nav-link" href="<?php echo e(route('dashboard.pembimbing')); ?>">
                                <i class="fas fa-columns"></i><span>Dashboard</span>
                            </a>
                            <div class="mini-flyout">
                                <a class="flyout-link" href="<?php echo e(route('dashboard.pembimbing')); ?>">
                                    <i class="fas fa-columns"></i> Dashboard
                                </a>
                            </div>
                        </li>

                        <li
                            class="nav-item <?php echo e(request()->routeIs('pembimbing.tugas.*') || request()->routeIs('pembimbing.show.submitan') || request()->routeIs('pembimbing.feedback.*') ? 'active' : ''); ?>">
                            <a class="nav-link" href="<?php echo e(route('pembimbing.tugas.index')); ?>">
                                <i class="fas fa-file"></i><span>Tugas</span>
                            </a>
                            <div class="mini-flyout">
                                <a class="flyout-link" href="<?php echo e(route('pembimbing.tugas.index')); ?>">
                                    <i class="fas fa-file"></i> Tugas
                                </a>
                            </div>
                        </li>

                        <li class="nav-item <?php echo e(request()->routeIs('pembimbing.bimbingan.*') ? 'active' : ''); ?>">
                            <a class="nav-link" href="<?php echo e(route('pembimbing.bimbingan.index')); ?>">
                                <i class="fas fa-bullhorn"></i><span>Bimbingan</span>
                            </a>
                            <div class="mini-flyout">
                                <a class="flyout-link" href="<?php echo e(route('pembimbing.bimbingan.index')); ?>">
                                    <i class="fas fa-bullhorn"></i> Bimbingan
                                </a>
                            </div>
                        </li>

                        <li class="nav-item <?php echo e(request()->routeIs('pembimbing.jadwal.*') ? 'active' : ''); ?>">
                            <a class="nav-link" href="<?php echo e(route('pembimbing.jadwal.index')); ?>">
                                <i class="fas fa-calendar"></i><span>Jadwal</span>
                            </a>
                            <div class="mini-flyout">
                                <a class="flyout-link" href="<?php echo e(route('pembimbing.jadwal.index')); ?>">
                                    <i class="fas fa-calendar"></i> Jadwal
                                </a>
                            </div>
                        </li>

                        <li
                            class="nav-item dropdown <?php echo e(request()->routeIs('pembimbing1.NilaiIndividu*') || request()->routeIs('pembimbing2.NilaiIndividu*') || request()->routeIs('pembimbing1.NilaiKelompok*') || request()->routeIs('pembimbing2.NilaiKelompok*') ? 'active' : ''); ?>">
                            <a href="#" class="nav-link has-dropdown">
                                <i class="fas fa-clipboard-check"></i><span>Nilai Seminar</span>
                            </a>
                            <ul class="dropdown-menu level-1-menu">
                                <?php if(in_array(3, $dosenRoles)): ?>
                                    <li
                                        class="dropdown-submenu <?php echo e(request()->routeIs('pembimbing1.NilaiIndividu*') || request()->routeIs('pembimbing1.NilaiKelompok*') ? 'open' : ''); ?>">
                                        <a href="#" class="submenu-toggle"><span>Dosen Pembimbing 1</span></a>
                                        <ul class="submenu-level">
                                            <li><a href="<?php echo e(route('pembimbing1.NilaiIndividu.index')); ?>"
                                                    class="<?php echo e(request()->routeIs('pembimbing1.NilaiIndividu.index') ? 'active' : ''); ?>">Nilai
                                                    Individu (Seminar)</a></li>
                                            <li><a href="<?php echo e(route('pembimbing1.NilaiKelompok.index')); ?>"
                                                    class="<?php echo e(request()->routeIs('pembimbing1.NilaiKelompok.index') ? 'active' : ''); ?>">Nilai
                                                    Kelompok (Seminar)</a></li>
                                        </ul>
                                    </li>
                                <?php endif; ?>
                                <?php if(in_array(5, $dosenRoles)): ?>
                                    <li
                                        class="dropdown-submenu <?php echo e(request()->routeIs('pembimbing2.NilaiIndividu*') || request()->routeIs('pembimbing2.NilaiKelompok*') ? 'open' : ''); ?>">
                                        <a href="#" class="submenu-toggle"><span>Dosen Pembimbing 2</span></a>
                                        <ul class="submenu-level">
                                            <li><a href="<?php echo e(route('pembimbing2.NilaiIndividu.index')); ?>"
                                                    class="<?php echo e(request()->routeIs('pembimbing2.NilaiIndividu.index') ? 'active' : ''); ?>">Nilai
                                                    Individu (Seminar)</a></li>
                                            <li><a href="<?php echo e(route('pembimbing2.NilaiKelompok.index')); ?>"
                                                    class="<?php echo e(request()->routeIs('pembimbing2.NilaiKelompok.index') ? 'active' : ''); ?>">Nilai
                                                    Kelompok (Seminar)</a></li>
                                        </ul>
                                    </li>
                                <?php endif; ?>
                            </ul>
                            <div class="mini-flyout">
                                <span class="flyout-header">Nilai Seminar</span>
                                <?php if(in_array(3, $dosenRoles)): ?>
                                    <span class="flyout-group-title">Dosen Pembimbing 1</span>
                                    <a class="flyout-sub-link"
                                        href="<?php echo e(route('pembimbing1.NilaiIndividu.index')); ?>"><i
                                            class="fas fa-user"></i> Nilai Individu</a>
                                    <a class="flyout-sub-link"
                                        href="<?php echo e(route('pembimbing1.NilaiKelompok.index')); ?>"><i
                                            class="fas fa-users"></i> Nilai Kelompok</a>
                                <?php endif; ?>
                                <?php if(in_array(3, $dosenRoles) && in_array(5, $dosenRoles)): ?>
                                    <div class="flyout-divider"></div>
                                <?php endif; ?>
                                <?php if(in_array(5, $dosenRoles)): ?>
                                    <span class="flyout-group-title">Dosen Pembimbing 2</span>
                                    <a class="flyout-sub-link"
                                        href="<?php echo e(route('pembimbing2.NilaiIndividu.index')); ?>"><i
                                            class="fas fa-user"></i> Nilai Individu</a>
                                    <a class="flyout-sub-link"
                                        href="<?php echo e(route('pembimbing2.NilaiKelompok.index')); ?>"><i
                                            class="fas fa-users"></i> Nilai Kelompok</a>
                                <?php endif; ?>
                            </div>
                        </li>

                        <li
                            class="nav-item <?php echo e(request()->routeIs('pembimbing1.NilaiBimbingan*') || request()->routeIs('pembimbing2.NilaiBimbingan*') ? 'active' : ''); ?>">
                            <a class="nav-link"
                                href="<?php echo e(in_array(3, $dosenRoles) ? route('pembimbing1.NilaiBimbingan.index') : route('pembimbing2.NilaiBimbingan.index')); ?>">
                                <i class="fas fa-star-half-alt"></i><span>Nilai Bimbingan</span>
                            </a>
                            <div class="mini-flyout">
                                <a class="flyout-link"
                                    href="<?php echo e(in_array(3, $dosenRoles) ? route('pembimbing1.NilaiBimbingan.index') : route('pembimbing2.NilaiBimbingan.index')); ?>">
                                    <i class="fas fa-star-half-alt"></i> Nilai Bimbingan
                                </a>
                            </div>
                        </li>

                        <li class="nav-item <?php echo e(request()->routeIs('PembimbingPengajuanSeminar.*') ? 'active' : ''); ?>">
                            <a class="nav-link" href="<?php echo e(route('PembimbingPengajuanSeminar.index')); ?>">
                                <i class="fas fa-calendar-check"></i><span>Pengajuan Seminar</span>
                            </a>
                            <div class="mini-flyout">
                                <a class="flyout-link" href="<?php echo e(route('PembimbingPengajuanSeminar.index')); ?>">
                                    <i class="fas fa-calendar-check"></i> Pengajuan Seminar
                                </a>
                            </div>
                        </li>
                    <?php endif; ?>
                <?php elseif(session('role') == 'Mahasiswa'): ?>
                    <li class="menu-header">Mahasiswa</li>

                    <li class="nav-item <?php echo e(request()->routeIs('dashboard.mahasiswa') ? 'active' : ''); ?>">
                        <a class="nav-link" href="<?php echo e(route('dashboard.mahasiswa')); ?>">
                            <i class="fas fa-columns"></i><span>Dashboard</span>
                        </a>
                        <div class="mini-flyout">
                            <a class="flyout-link" href="<?php echo e(route('dashboard.mahasiswa')); ?>"><i
                                    class="fas fa-columns"></i> Dashboard</a>
                        </div>
                    </li>
                    <li class="nav-item <?php echo e(request()->routeIs('Mahasiswa.tugas.*') ? 'active' : ''); ?>">
                        <a class="nav-link" href="<?php echo e(route('Mahasiswa.tugas.index')); ?>">
                            <i class="fas fa-file"></i><span>Tugas</span>
                        </a>
                        <div class="mini-flyout">
                            <a class="flyout-link" href="<?php echo e(route('Mahasiswa.tugas.index')); ?>"><i
                                    class="fas fa-file"></i> Tugas</a>
                        </div>
                    </li>
                    <li class="nav-item <?php echo e(request()->routeIs('artefak.*') ? 'active' : ''); ?>">
                        <a class="nav-link" href="<?php echo e(route('artefak.index')); ?>">
                            <i class="fas fa-file"></i><span>Artefak</span>
                        </a>
                        <div class="mini-flyout">
                            <a class="flyout-link" href="<?php echo e(route('artefak.index')); ?>"><i class="fas fa-file"></i>
                                Artefak</a>
                        </div>
                    </li>
                    
                    <li class="nav-item <?php echo e(request()->routeIs('bimbingan.*') ? 'active' : ''); ?>">
                        <a class="nav-link" href="<?php echo e(route('bimbingan.index')); ?>">
                            <i class="fas fa-list"></i><span>Bimbingan</span>
                        </a>
                        <div class="mini-flyout">
                            <a class="flyout-link" href="<?php echo e(route('bimbingan.index')); ?>"><i
                                    class="fas fa-list"></i> Bimbingan</a>
                        </div>
                    </li>
                    <li class="nav-item <?php echo e(request()->routeIs('pengumuman.mahasiswa.*') ? 'active' : ''); ?>">
                        <a class="nav-link" href="<?php echo e(route('pengumuman.mahasiswa.index')); ?>">
                            <i class="fas fa-bell"></i><span>Pengumuman</span>
                        </a>
                        <div class="mini-flyout">
                            <a class="flyout-link" href="<?php echo e(route('pengumuman.mahasiswa.index')); ?>"><i
                                    class="fas fa-bell"></i> Pengumuman</a>
                        </div>
                    </li>
                    <li class="nav-item <?php echo e(request()->routeIs('mahasiswa.jadwal.*') ? 'active' : ''); ?>">
                        <a class="nav-link" href="<?php echo e(route('mahasiswa.jadwal.index')); ?>">
                            <i class="fas fa-calendar"></i><span>Jadwal</span>
                        </a>
                        <div class="mini-flyout">
                            <a class="flyout-link" href="<?php echo e(route('mahasiswa.jadwal.index')); ?>"><i
                                    class="fas fa-calendar"></i> Jadwal</a>
                        </div>
                    </li>
                    <li class="nav-item <?php echo e(request()->routeIs('Histori.*') ? 'active' : ''); ?>">
                        <a class="nav-link" href="<?php echo e(route('Histori.index')); ?>">
                            <i class="fas fa-history"></i><span>Histori</span>
                        </a>
                        <div class="mini-flyout">
                            <a class="flyout-link" href="<?php echo e(route('Histori.index')); ?>"><i
                                    class="fas fa-history"></i> Histori</a>
                        </div>
                    </li>
                <?php elseif(session('role') == 'Staff'): ?>
                    <li class="menu-header">Staff</li>

                    <li class="nav-item <?php echo e(request()->routeIs('dashboard.BAAK') ? 'active' : ''); ?>">
                        <a class="nav-link" href="<?php echo e(route('dashboard.BAAK')); ?>">
                            <i class="fas fa-columns"></i><span>Dashboard</span>
                        </a>
                        <div class="mini-flyout">
                            <a class="flyout-link" href="<?php echo e(route('dashboard.BAAK')); ?>"><i
                                    class="fas fa-columns"></i> Dashboard</a>
                        </div>
                    </li>
                    <li class="nav-item <?php echo e(request()->routeIs('manajemen-role.*') ? 'active' : ''); ?>">
                        <a class="nav-link" href="<?php echo e(route('manajemen-role.index')); ?>">
                            <i class="fas fa-user"></i><span>Manajemen-Role</span>
                        </a>
                        <div class="mini-flyout">
                            <a class="flyout-link" href="<?php echo e(route('manajemen-role.index')); ?>"><i
                                    class="fas fa-user"></i> Manajemen-Role</a>
                        </div>
                    </li>
                    <li class="nav-item <?php echo e(request()->routeIs('baak.jadwal.*') ? 'active' : ''); ?>">
                        <a class="nav-link" href="<?php echo e(route('baak.jadwal.index')); ?>">
                            <i class="fas fa-calendar"></i><span>Jadwal</span>
                        </a>
                        <div class="mini-flyout">
                            <a class="flyout-link" href="<?php echo e(route('baak.jadwal.index')); ?>"><i
                                    class="fas fa-calendar"></i> Jadwal</a>
                        </div>
                    </li>
                    <li class="nav-item <?php echo e(request()->routeIs('pengumuman.BAAK.*') ? 'active' : ''); ?>">
                        <a class="nav-link" href="<?php echo e(route('pengumuman.BAAK.index')); ?>">
                            <i class="fas fa-bell"></i><span>Pengumuman</span>
                        </a>
                        <div class="mini-flyout">
                            <a class="flyout-link" href="<?php echo e(route('pengumuman.BAAK.index')); ?>"><i
                                    class="fas fa-bell"></i> Pengumuman</a>
                        </div>
                    </li>
                    <li class="nav-item <?php echo e(request()->routeIs('TahunMasuk.*') ? 'active' : ''); ?>">
                        <a class="nav-link" href="<?php echo e(route('TahunMasuk.index')); ?>">
                            <i class="fas fa-graduation-cap"></i><span>Tahun Masuk</span>
                        </a>
                        <div class="mini-flyout">
                            <a class="flyout-link" href="<?php echo e(route('TahunMasuk.index')); ?>"><i
                                    class="fas fa-graduation-cap"></i> Tahun Masuk</a>
                        </div>
                    </li>
                <?php endif; ?>

            </ul>
        <?php endif; ?>
    </aside>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {

        /* ── Helper animasi ── */
        function openMenu(el) {
            el.style.display = "block";
            requestAnimationFrame(() => {
                el.style.transition = "max-height 0.3s ease";
                el.style.maxHeight = el.scrollHeight + "px";
                el.addEventListener("transitionend", function h() {
                    if (el.style.maxHeight !== "0px") el.style.maxHeight = "none";
                    el.removeEventListener("transitionend", h);
                });
            });
        }

        function closeMenu(el) {
            el.style.maxHeight = el.scrollHeight + "px";
            requestAnimationFrame(() => {
                el.style.transition = "max-height 0.3s ease";
                el.style.maxHeight = "0px";
                setTimeout(() => {
                    el.style.display = "none";
                }, 310);
            });
        }

        /* ── Cek mode sidebar ── */
        function isMobile() {
            return window.innerWidth < 768;
        }

        function isTablet() {
            return window.innerWidth >= 768 && window.innerWidth < 1024;
        }

        function isSidebarMini() {
            return document.body.classList.contains('sidebar-mini') ||
                document.body.classList.contains('sidebar-gone') ||
                isTablet();
        }

        /* ══════════════════════════════════════
           MOBILE DRAWER LOGIC
        ══════════════════════════════════════ */
        const sidebar = document.querySelector('.main-sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const closeBtn = document.getElementById('sidebarCloseBtn');

        function openMobileSidebar() {
            sidebar.classList.add('mobile-open');
            overlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeMobileSidebar() {
            sidebar.classList.remove('mobile-open');
            overlay.classList.remove('active');
            document.body.style.overflow = '';
        }

        if (overlay) overlay.addEventListener('click', closeMobileSidebar);
        if (closeBtn) closeBtn.addEventListener('click', closeMobileSidebar);

        /* Tombol hamburger eksternal membuka drawer di mobile */
        document.querySelectorAll('[data-toggle="sidebar"]').forEach(btn => {
            btn.addEventListener('click', function() {
                if (isMobile()) {
                    if (sidebar.classList.contains('mobile-open')) {
                        closeMobileSidebar();
                    } else {
                        openMobileSidebar();
                    }
                    return;
                }

                /* Perilaku asli (desktop / tablet) */
                setTimeout(() => {
                    if (activeFlyout) hideFlyout(activeFlyout);
                    if (isSidebarMini()) {
                        document.querySelectorAll(".nav-item.dropdown.open").forEach(
                            item => {
                                item.classList.remove("open");
                                const menu = item.querySelector(
                                    ":scope > .dropdown-menu");
                                if (menu) {
                                    menu.style.display = "none";
                                    menu.style.maxHeight = "0px";
                                }
                            });
                    }
                }, 50);
            });
        });

        /* Tutup drawer saat resize ke desktop */
        window.addEventListener('resize', function() {
            if (!isMobile()) {
                closeMobileSidebar();
            }
        });

        /* ── Toggle LEVEL 1 ── */
        document.querySelectorAll(".nav-link.has-dropdown").forEach(link => {
            link.addEventListener("click", function(e) {
                e.preventDefault();
                e.stopPropagation();
                if (isSidebarMini() && !isMobile()) return;

                const parentLi = this.closest(".nav-item.dropdown");
                if (!parentLi) return;
                const menu = parentLi.querySelector(":scope > .dropdown-menu");
                if (!menu) return;
                const isOpen = parentLi.classList.contains("open");

                document.querySelectorAll(".nav-item.dropdown.open").forEach(item => {
                    if (item !== parentLi) {
                        item.classList.remove("open");
                        const m = item.querySelector(":scope > .dropdown-menu");
                        if (m) closeMenu(m);
                        item.querySelectorAll(".dropdown-submenu.open").forEach(sub => {
                            sub.classList.remove("open");
                            const sm = sub.querySelector(
                                ":scope > .submenu-level");
                            if (sm) closeMenu(sm);
                        });
                    }
                });

                if (isOpen) {
                    parentLi.classList.remove("open");
                    closeMenu(menu);
                    parentLi.querySelectorAll(".dropdown-submenu.open").forEach(sub => {
                        sub.classList.remove("open");
                        const sm = sub.querySelector(":scope > .submenu-level");
                        if (sm) closeMenu(sm);
                    });
                } else {
                    parentLi.classList.add("open");
                    openMenu(menu);
                }
            });
        });

        /* ── Toggle LEVEL 2 ── */
        document.querySelectorAll(".submenu-toggle").forEach(toggle => {
            toggle.addEventListener("click", function(e) {
                e.preventDefault();
                e.stopPropagation();
                const parent = this.closest(".dropdown-submenu");
                const submenu = parent.querySelector(":scope > .submenu-level");
                if (!submenu) return;
                const isOpen = parent.classList.contains("open");

                parent.parentElement.querySelectorAll(":scope > .dropdown-submenu.open")
                    .forEach(item => {
                        if (item !== parent) {
                            item.classList.remove("open");
                            const s = item.querySelector(":scope > .submenu-level");
                            if (s) closeMenu(s);
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

        /* ── Auto-open saat page load ── */
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

        /* ══════════════════════════════════════
           FLYOUT SYSTEM
        ══════════════════════════════════════ */
        document.querySelectorAll(".sidebar-menu .nav-item").forEach((item, idx) => {
            const flyout = item.querySelector(":scope > .mini-flyout");
            if (!flyout) return;
            flyout.dataset.owner = "flyout-" + idx;
            item.dataset.flyoutId = "flyout-" + idx;
            document.body.appendChild(flyout);
        });

        let activeFlyout = null;

        function getFlyout(item) {
            const id = item.dataset.flyoutId;
            return id ? document.body.querySelector(`.mini-flyout[data-owner="${id}"]`) : null;
        }

        function showFlyout(item) {
            if (!isSidebarMini() || isMobile()) return;
            const flyout = getFlyout(item);
            if (!flyout) return;

            if (activeFlyout && activeFlyout !== flyout) {
                activeFlyout.style.display = "none";
            }

            flyout.style.display = "block";

            const rect = item.getBoundingClientRect();
            flyout.style.left = rect.right + "px";

            const flyH = flyout.offsetHeight;
            const winH = window.innerHeight;
            let top = rect.top;
            if (top + flyH > winH - 8) top = winH - flyH - 8;
            if (top < 8) top = 8;
            flyout.style.top = top + "px";

            activeFlyout = flyout;
        }

        function hideFlyout(flyout) {
            if (!flyout) return;
            flyout.style.display = "none";
            if (activeFlyout === flyout) activeFlyout = null;
        }

        document.querySelectorAll(".sidebar-menu .nav-item").forEach(item => {
            const flyout = getFlyout(item);

            item.addEventListener("mouseenter", () => showFlyout(item));

            item.addEventListener("mouseleave", function(e) {
                if (!flyout) return;
                if (e.relatedTarget && flyout.contains(e.relatedTarget)) return;
                hideFlyout(flyout);
            });

            if (flyout) {
                flyout.addEventListener("mouseleave", function(e) {
                    if (item.contains(e.relatedTarget)) return;
                    hideFlyout(flyout);
                });
            }
        });

        document.addEventListener("click", function(e) {
            if (activeFlyout &&
                !e.target.closest(".sidebar-menu") &&
                !e.target.closest(".mini-flyout")) {
                hideFlyout(activeFlyout);
            }
        });

    });
</script>
<?php /**PATH E:\Semester 6\PA III\Project\Proyek-Kelompok-07-PA-3\ui laravel\resources\views/partials/sidebar.blade.php ENDPATH**/ ?>