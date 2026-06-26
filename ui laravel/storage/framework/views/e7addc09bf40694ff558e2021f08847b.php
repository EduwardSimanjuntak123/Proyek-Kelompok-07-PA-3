<?php $__env->startSection('title', 'AI Agent Chatbot'); ?>

<?php $__env->startPush('css'); ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
        integrity="sha512-iecdLmaskl7CVJkEZSMUkrQ6usKu8zIstOWilmQyTjew45OMcvL7tdNT91uUOD4XiWkN9reidYl+aRslnMl+Kw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/agent-kelompok.css')); ?>">

    <section class="chatbox-container" data-ai-route="<?php echo e(route('ai.callAgent')); ?>"
        data-save-route="<?php echo e(route('ai.saveGroups')); ?>" data-save-pembimbing-route="<?php echo e(route('ai.savePembimbing')); ?>"
        data-check-pembimbing-route="<?php echo e(route('ai.cekPembimbing')); ?>"
        data-save-penguji-route="<?php echo e(route('ai.savePenguji')); ?>" data-check-penguji-route="<?php echo e(route('ai.cekPenguji')); ?>"
        data-check-route="<?php echo e(route('ai.cekKelompok')); ?>" data-delete-route="<?php echo e(route('ai.deleteForContext')); ?>"
        data-kategori-pa-id="<?php echo e(request()->query('kategori_pa_id', 1)); ?>"
        data-prodi-id="<?php echo e(request()->query('prodi_id', 1)); ?>"
        data-tahun-masuk-id="<?php echo e(request()->query('tahun_masuk_id', 1)); ?>">

        <div class="agent-page" id="vtWrap">

            
            <header class="topbar">
                <div class="topbar-brand">
                    <div class="topbar-icon">
                        <img src="<?php echo e(asset('assets/img/logoagent.png')); ?>" alt="VokasiTera Agent Logo"
                            style="width: 40px; height: 40px; object-fit: contain;">
                    </div>
                    <div>
                        <div class="topbar-name">VokasiTera Agent</div>
                        <div class="topbar-sub">Asisten Proyek Akhir Mahasiswa</div>
                    </div>
                </div>

                <div class="topbar-right">
                    <button class="topbar-btn" id="btnNewChat" style="display:none">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 5v14M5 12h14" />
                        </svg>
                        Sesi Baru
                    </button>

                    <button class="topbar-btn" id="btnHelper">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10" />
                            <path d="M12 8v4M12 16h.01" />
                        </svg>
                        Panduan
                    </button>

                    <div class="status-online">
                        <div class="status-dot"></div>
                        Online
                    </div>
                </div>
            </header>

            
            <aside class="verification-sidebar">
                <div class="sidebar-header">
                    <h3>Alur Verifikasi</h3>
                </div>

                <div class="verification-steps">
                    
                    <div class="step-item" data-step="kelompok">
                        <div class="step-line"></div>
                        <div class="step-content">
                            <div class="step-indicator">
                                <div class="step-number">1</div>
                            </div>
                            <div class="step-info">
                                <div class="step-title">Pembagian Kelompok</div>
                                <div class="step-subtitle">Buat & generate kelompok</div>
                            </div>
                            <div class="step-status" data-status="pending">
                                <svg class="status-icon pending-icon" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10" />
                                </svg>
                                <svg class="status-icon success-icon" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
                                    style="display: none;">
                                    <polyline points="20 6 9 17 4 12" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    
                    <div class="step-item" data-step="pembimbing">
                        <div class="step-line"></div>
                        <div class="step-content">
                            <div class="step-indicator">
                                <div class="step-number">2</div>
                            </div>
                            <div class="step-info">
                                <div class="step-title">Assign Dosen Pembimbing</div>
                                <div class="step-subtitle">Tentukan dosen pembimbing</div>
                            </div>
                            <div class="step-status" data-status="pending">
                                <svg class="status-icon pending-icon" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10" />
                                </svg>
                                <svg class="status-icon success-icon" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                    stroke-linejoin="round" style="display: none;">
                                    <polyline points="20 6 9 17 4 12" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    
                    <div class="step-item" data-step="penguji">
                        <div class="step-line"></div>
                        <div class="step-content">
                            <div class="step-indicator">
                                <div class="step-number">3</div>
                            </div>
                            <div class="step-info">
                                <div class="step-title">Assign Dosen Penguji</div>
                                <div class="step-subtitle">Tentukan dosen penguji</div>
                            </div>
                            <div class="step-status" data-status="pending">
                                <svg class="status-icon pending-icon" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10" />
                                </svg>
                                <svg class="status-icon success-icon" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                    stroke-linejoin="round" style="display: none;">
                                    <polyline points="20 6 9 17 4 12" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    
                    <div class="step-item last" data-step="jadwal">
                        <div class="step-line"></div>
                        <div class="step-content">
                            <div class="step-indicator">
                                <div class="step-number">4</div>
                            </div>
                            <div class="step-info">
                                <div class="step-title">Assign Jadwal Seminar</div>
                                <div class="step-subtitle">Tentukan waktu seminar</div>
                            </div>
                            <div class="step-status" data-status="pending">
                                <svg class="status-icon pending-icon" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10" />
                                </svg>
                                <svg class="status-icon success-icon" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                    stroke-linejoin="round" style="display: none;">
                                    <polyline points="20 6 9 17 4 12" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="sidebar-footer">
                    <div class="status-legend">
                        <div class="legend-item">
                            <div class="legend-icon pending"></div>
                            <span>Pending</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-icon warning"></div>
                            <span>Ada Issue</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-icon success"></div>
                            <span>Selesai</span>
                        </div>
                    </div>
                </div>
            </aside>

            
            <div class="landing-view" id="landingView">
                <div class="landing-icon-wrap">
                    <img src="<?php echo e(asset('assets/img/logoagent.png')); ?>" alt="VokasiTera Agent Logo"
                        style="width: 100px; height: 100px; object-fit: contain;">
                </div>

                <?php
                    $hour = date('H');
                    $greeting = '';
                    if ($hour >= 5 && $hour < 11) {
                        $greeting = 'Selamat Pagi';
                    } elseif ($hour >= 11 && $hour < 15) {
                        $greeting = 'Selamat Siang';
                    } elseif ($hour >= 15 && $hour < 18) {
                        $greeting = 'Selamat Sore';
                    } else {
                        $greeting = 'Selamat Malam';
                    }
                    $userName = session('name') ?? 'Pengguna';
                ?>

                <h1 class="landing-title"><?php echo e($greeting); ?>, <?php echo e($userName); ?>! 👋</h1>
                <p class="landing-sub">
                    Hari ini ada agenda apa? VokasiTera Agent siap membantu pembagian kelompok, penentuan dosen pembimbing
                    dan penguji untuk proyek akhir mahasiswa Anda.
                </p>

                <div class="landing-input-wrap">
                    <textarea id="landingInput" rows="1" placeholder="Tulis instruksi... contoh: buat 5 kelompok"></textarea>
                    <button class="landing-send" id="landingSend">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5">
                            <line x1="22" y1="2" x2="11" y2="13" />
                            <polygon points="22 2 15 22 11 13 2 9 22 2" />
                        </svg>
                    </button>
                </div>

                <div class="chip-row">
                    <button class="chip" data-instruction="Buat Kelompok dengan 5 orang per kelompok berdasarkan nilai">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="11"
                            height="11">
                            <rect x="3" y="3" width="7" height="7" rx="1" />
                            <rect x="14" y="3" width="7" height="7" rx="1" />
                            <rect x="3" y="14" width="7" height="7" rx="1" />
                            <rect x="14" y="14" width="7" height="7" rx="1" />
                        </svg>
                        5 orang/kelompok
                    </button>

                    <button class="chip" data-instruction="Buat Dosen Pembimbing untuk setiap kelompok">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="11"
                            height="11">
                            <path d="M20 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" />
                            <circle cx="9" cy="7" r="4" />
                            <path d="M23 21v-2a4 4 0 00-3-3.87" />
                            <path d="M16 3.13a4 4 0 010 7.75" />
                        </svg>
                        Buat Dosen Pembimbing
                    </button>

                    <button class="chip" data-instruction="Buat Dosen Penguji untuk setiap kelompok">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="11"
                            height="11">
                            <path d="M9 11l3 3L22 4" />
                            <path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11" />
                        </svg>
                        Buat Dosen Penguji
                    </button>

                    <button class="chip" data-instruction="Apa yang bisa kamu lakukan?">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="11"
                            height="11">
                            <circle cx="12" cy="12" r="10" />
                            <path d="M12 8v4M12 16h.01" />
                        </svg>
                        Kemampuan AI
                    </button>
                </div>
            </div>

            
            <div class="chat-view" id="chatView">
                <main class="chat-area" id="chatBox"></main>

                <div class="chat-input-bar">
                    <div class="typing-indicator" id="typingInd">
                        <div class="typing-dots"><span></span><span></span><span></span></div>
                        AI sedang memproses
                    </div>
                    <div class="input-wrapper">
                        <textarea id="userInput" rows="1"
                            placeholder="Tulis instruksi... contoh: buat kelompok, tentukan pembimbing dan penguji"></textarea>
                        <button id="sendBtn">
                            <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5">
                                <line x1="22" y1="2" x2="11" y2="13" />
                                <polygon points="22 2 15 22 11 13 2 9 22 2" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            
            <div class="panduan-overlay" id="panduanOverlay">

                <div class="panduan-modal">

                    
                    <div class="panduan-header">
                        <div class="panduan-title">
                            <div class="title-icon">
                                <i class="fas fa-robot"></i>
                            </div>

                            <div>
                                <h3>Panduan Penggunaan</h3>
                                <p>Gunakan contoh perintah berikut untuk memulai</p>
                            </div>
                        </div>

                        <button class="close-x" id="closePandauHeader">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    
                    <div class="panduan-content">

                        <div class="panduan-card">
                            <div class="panduan-icon blue">
                                <i class="fas fa-users"></i>
                            </div>

                            <div class="panduan-info">
                                <strong>Pembagian Kelompok</strong>
                                <span>"Buat 5 kelompok"</span>
                                <span>"6 orang per kelompok"</span>
                            </div>
                        </div>

                        <div class="panduan-card">
                            <div class="panduan-icon green">
                                <i class="fas fa-user-tie"></i>
                            </div>

                            <div class="panduan-info">
                                <strong>Dosen Pembimbing</strong>
                                <span>"Tentukan dosen pembimbing untuk setiap kelompok"</span>
                            </div>
                        </div>

                        <div class="panduan-card">
                            <div class="panduan-icon orange">
                                <i class="fas fa-user-check"></i>
                            </div>

                            <div class="panduan-info">
                                <strong>Dosen Penguji</strong>
                                <span>"Tentukan dosen penguji untuk setiap kelompok"</span>
                            </div>
                        </div>

                        <div class="panduan-card">
                            <div class="panduan-icon purple">
                                <i class="fas fa-bolt"></i>
                            </div>

                            <div class="panduan-info">
                                <strong>Gabungan Otomatis</strong>
                                <span>"Bagi kelompok sekaligus tentukan pembimbing dan penguji"</span>
                            </div>
                        </div>

                    </div>

                    
                    <div class="panduan-footer">
                        <button class="btn-close-modal" id="closePanduan">
                            <i class="fas fa-check-circle"></i>
                            Mengerti
                        </button>
                    </div>

                </div>
            </div>
        </div>

    </section>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    
    <script>
        async function initJadwalCalendar() {

            const element = document.querySelector("#jadwal-tanggal");
            if (!element) return;

            if (element._flatpickr) {
                element._flatpickr.destroy();
            }

            // ============================================================
            // Hari libur nasional TETAP Indonesia (MM-DD)
            // Tahun otomatis mengikuti tahun sistem
            // ============================================================
            const hariLiburTetap = [{
                    bulan_hari: "01-01",
                    holiday_name: "Tahun Baru Masehi"
                },
                {
                    bulan_hari: "05-01",
                    holiday_name: "Hari Buruh Internasional"
                },
                {
                    bulan_hari: "06-01",
                    holiday_name: "Hari Lahir Pancasila"
                },
                {
                    bulan_hari: "08-17",
                    holiday_name: "Hari Kemerdekaan Republik Indonesia"
                },
                {
                    bulan_hari: "12-25",
                    holiday_name: "Hari Raya Natal"
                },
                {
                    bulan_hari: "12-26",
                    holiday_name: "Cuti Bersama Natal"
                },
            ];

            // Generate untuk tahun sekarang dan tahun depan
            const tahunSekarang = new Date().getFullYear();
            const dataLibur = [];

            [tahunSekarang, tahunSekarang + 1].forEach(tahun => {
                hariLiburTetap.forEach(item => {
                    dataLibur.push({
                        holiday_date: `${tahun}-${item.bulan_hari}`,
                        holiday_name: item.holiday_name
                    });
                });
            });

            const hariLibur = dataLibur.map(x => x.holiday_date);

            console.log("[HARI LIBUR] Total:", hariLibur.length, "tanggal");

            flatpickr(element, {

                dateFormat: "Y-m-d",
                minDate: "today",

                disable: [

                    // Sabtu & Minggu
                    function(date) {
                        return (date.getDay() === 0 || date.getDay() === 6);
                    },

                    // Hari libur tetap
                    ...hariLibur

                ],

                onDayCreate: function(dObj, dStr, fp, dayElem) {

                    const d = dayElem.dateObj;
                    const yyyy = d.getFullYear();
                    const mm = String(d.getMonth() + 1).padStart(2, '0');
                    const dd = String(d.getDate()).padStart(2, '0');
                    const date = `${yyyy}-${mm}-${dd}`;

                    // Merah untuk Sabtu & Minggu
                    if (d.getDay() === 0 || d.getDay() === 6) {
                        dayElem.style.background = "#fee2e2";
                        dayElem.style.color = "#dc2626";
                        dayElem.style.borderRadius = "50%";
                    }

                    // Kuning + tooltip untuk hari libur tetap
                    if (hariLibur.includes(date)) {
                        dayElem.style.background = "#fef3c7";
                        dayElem.style.color = "#d97706";
                        dayElem.style.border = "2px solid #f59e0b";
                        dayElem.style.fontWeight = "bold";
                        dayElem.style.borderRadius = "50%";

                        const info = dataLibur.find(x => x.holiday_date === date);
                        if (info) {
                            dayElem.title = info.holiday_name;
                        }
                    }

                }

            });

            console.log("[JADWAL] Kalender berhasil dibuat untuk tahun", tahunSekarang);
        }
    </script>
    <script>
        // ============== CHATBOT UI FUNCTIONS ==============

        console.log("[chatbot] Script loaded")

        let latestGroupingPayload = null;
        let latestGroupingMeta = null;
        let latestPembimbingPayload = null;
        let latestPembimbingMeta = null;
        let latestPengujiPayload = null;
        let latestPengujiMeta = null;
        let latestExcelFilename = null;
        let latestJadwalMeta = null;
        let latestJadwalEntries = null;
        let latestJadwalPreviewRow = null;
        let latestUserPrompt = "";
        let isSavingGeneratedGroups = false;
        let isSavingGeneratedPembimbing = false;
        let isSavingGeneratedPenguji = false;
        let isDeletingForContext = false;
        let isLoadingPembimbingCheck = false;
        let isLoadingPengujiCheck = false;

        // ============== JADWAL SEMINAR FUNCTIONS ==============

        function appendJadwalFormActions(wrapper) {
            const timestamp = new Date().toLocaleTimeString();
            console.log(`[${timestamp}] [JADWAL] Appending action buttons...`);

            try {
                const bubble = wrapper.querySelector('.msg-bubble, .chat-message-bubble');
                if (!bubble) {
                    console.error(`[${timestamp}] [JADWAL] ❌ Bubble not found`);
                    return;
                }

                const actionsHost = wrapper.querySelector('#jadwal-form-actions') || bubble;

                const allRuanganOptions = (function() {
                    const firstSel = wrapper.querySelector('.jadwal-ruangan-select');
                    if (!firstSel) return [];
                    return Array.from(firstSel.options).map(function(o) {
                        return {
                            value: o.value,
                            text: o.text
                        };
                    });
                })();

                function syncAllSelects() {
                    const container = wrapper.querySelector('#jadwal-ruangan-container');
                    const allSelects = Array.from(container.querySelectorAll('.jadwal-ruangan-select'));
                    const selectedValues = allSelects.map(function(s) {
                        return s.value;
                    });

                    allSelects.forEach(function(sel, idx) {
                        const currentVal = sel.value;
                        sel.innerHTML = '';
                        allRuanganOptions.forEach(function(opt) {
                            const isPlaceholder = opt.value === '';
                            const isOwnValue = opt.value === currentVal;
                            const isUsedByOther = selectedValues.some(function(v, i) {
                                return i !== idx && v === opt.value && v !== '';
                            });
                            if (isPlaceholder || isOwnValue || !isUsedByOther) {
                                const optEl = document.createElement('option');
                                optEl.value = opt.value;
                                optEl.text = opt.text;
                                if (opt.value === currentVal) optEl.selected = true;
                                sel.appendChild(optEl);
                            }
                        });
                    });

                    const addBtn2 = wrapper.querySelector('#add-ruangan-btn');
                    if (addBtn2) {
                        const usedCount = selectedValues.filter(function(v) {
                            return v !== '';
                        }).length;
                        const totalRooms = allRuanganOptions.filter(function(o) {
                            return o.value !== '';
                        }).length;
                        addBtn2.disabled = usedCount >= totalRooms;
                        addBtn2.style.opacity = addBtn2.disabled ? '0.5' : '1';
                        addBtn2.style.cursor = addBtn2.disabled ? 'not-allowed' : 'pointer';
                    }
                }

                function updateRemoveButtons() {
                    const container = wrapper.querySelector('#jadwal-ruangan-container');
                    const rows = container.querySelectorAll('.ruangan-row');
                    const removeBtns = container.querySelectorAll('.remove-ruangan-btn');
                    removeBtns.forEach(function(btn) {
                        btn.style.display = rows.length > 1 ? 'block' : 'none';
                    });
                }

                function attachSelectChangeListener(sel) {
                    sel.addEventListener('change', function() {
                        syncAllSelects();
                    });
                }

                wrapper.querySelectorAll('.jadwal-ruangan-select').forEach(attachSelectChangeListener);

                const addBtn = wrapper.querySelector('#add-ruangan-btn');
                if (addBtn) {
                    addBtn.onclick = function(event) {
                        event.preventDefault();
                        event.stopPropagation();

                        const container = wrapper.querySelector('#jadwal-ruangan-container');
                        const rows = container.querySelectorAll('.ruangan-row');
                        const rowCount = rows.length;

                        const newRow = document.createElement('div');
                        newRow.className = 'ruangan-row';
                        newRow.style.display = 'flex';
                        newRow.style.gap = '8px';
                        newRow.style.marginBottom = '8px';
                        newRow.style.alignItems = 'center';

                        const select = document.createElement('select');
                        select.className = 'jadwal-ruangan-select';
                        select.style.flex = '1';
                        select.style.padding = '10px';
                        select.style.border = '1px solid #d1d5db';
                        select.style.borderRadius = '4px';
                        select.style.fontSize = '14px';
                        select.style.background = 'white';
                        select.style.cursor = 'pointer';

                        allRuanganOptions.forEach(function(opt) {
                            const optEl = document.createElement('option');
                            optEl.value = opt.value;
                            optEl.text = opt.text;
                            select.appendChild(optEl);
                        });

                        const removeBtn = document.createElement('button');
                        removeBtn.type = 'button';
                        removeBtn.className = 'remove-ruangan-btn';
                        removeBtn.style.padding = '10px 12px';
                        removeBtn.style.background = '#ef4444';
                        removeBtn.style.color = 'white';
                        removeBtn.style.border = 'none';
                        removeBtn.style.borderRadius = '6px';
                        removeBtn.style.cursor = 'pointer';
                        removeBtn.style.fontWeight = 'bold';
                        removeBtn.style.minWidth = '40px';
                        removeBtn.textContent = '✕';

                        removeBtn.onclick = function(e) {
                            e.preventDefault();
                            e.stopPropagation();
                            newRow.remove();
                            updateRemoveButtons();
                            syncAllSelects();
                        };

                        newRow.appendChild(select);
                        newRow.appendChild(removeBtn);
                        container.appendChild(newRow);

                        attachSelectChangeListener(select);
                        updateRemoveButtons();
                        syncAllSelects();
                        console.log(`[${timestamp}] [JADWAL] ✓ Ruangan row #${rowCount + 1} added`);
                    };
                    console.log(`[${timestamp}] [JADWAL] ✓ Add ruangan button listener attached`);
                }

                syncAllSelects();
                updateRemoveButtons();

                const actionsDiv = document.createElement('div');
                actionsDiv.style.display = 'flex';
                actionsDiv.style.flexDirection = 'column';
                actionsDiv.style.gap = '10px';
                actionsDiv.style.marginTop = '0';
                actionsDiv.style.width = '100%';

                const saveBtn = document.createElement('button');
                saveBtn.type = 'button';
                saveBtn.className = 'btn btn-sm btn-primary jadwal-submit-btn';
                saveBtn.style.position = 'relative';
                saveBtn.style.zIndex = '2';
                saveBtn.style.cursor = 'pointer';
                saveBtn.style.pointerEvents = 'auto';
                saveBtn.style.width = '100%';
                saveBtn.innerHTML = '<i class="fas fa-save"></i> Simpan Jadwal Seminar';

                saveBtn.onclick = function(event) {
                    event.preventDefault();
                    event.stopPropagation();
                    console.log(`[${timestamp}] [JADWAL] Save button clicked`);
                    window.__submitJadwal(event);
                };

                actionsDiv.appendChild(saveBtn);
                saveBtn.jadwalFormWrapper = wrapper;
                actionsHost.appendChild(actionsDiv);

                console.log(`[${timestamp}] [JADWAL] ✓ Action buttons appended`);
            } catch (error) {
                console.error(`[${timestamp}] [JADWAL] ❌ Error:`, error);
            }
        }

        function appendJadwalPreviewActions() {
            return;
        }

        window.__jadwalPreviewAction = function(action, orderCsv) {
            return;
        };

        function getJadwalFormElements(formContainer) {
            const tanggalInput = formContainer === document ?
                document.getElementById("jadwal-tanggal") :
                (formContainer?.querySelector("#jadwal-tanggal") || document.getElementById("jadwal-tanggal"));
            const durasiJamInput = formContainer === document ?
                document.getElementById("jadwal-durasi-jam") :
                (formContainer?.querySelector("#jadwal-durasi-jam") || document.getElementById("jadwal-durasi-jam"));
            const durasiMenitInput = formContainer === document ?
                document.getElementById("jadwal-durasi-menit") :
                (formContainer?.querySelector("#jadwal-durasi-menit") || document.getElementById("jadwal-durasi-menit"));
            return {
                tanggalInput,
                durasiJamInput,
                durasiMenitInput
            };
        }

        function getJadwalRuanganList() {
            const ruanganSelects = document.querySelectorAll(".jadwal-ruangan-select");
            const ruanganList = [];
            ruanganSelects.forEach((select) => {
                const ruangan_id = select.value;
                if (ruangan_id) {
                    ruanganList.push(ruangan_id);
                }
            });
            return ruanganList;
        }

        function convertDatePickerToIndonesian(datePickerValue) {
            const timestamp = new Date().toLocaleTimeString();
            console.log(`[${timestamp}] [DATE-CONVERT] Input: '${datePickerValue}' (type: ${typeof datePickerValue})`);

            if (!datePickerValue || datePickerValue.toString().trim() === "") {
                console.log(`[${timestamp}] [DATE-CONVERT] Empty input, returning empty string`);
                return "";
            }

            const bulanMap = [
                "januari", "februari", "maret", "april", "mei", "juni",
                "juli", "agustus", "september", "oktober", "november", "desember"
            ];

            try {
                let tahun, bulan, hari;
                const dateStr = datePickerValue.toString().trim();

                if (dateStr.includes('-')) {
                    const parts = dateStr.split('-');
                    if (parts.length === 3) {
                        [tahun, bulan, hari] = parts;
                    } else {
                        return dateStr;
                    }
                } else if (dateStr.includes('/')) {
                    const parts = dateStr.split('/');
                    if (parts.length === 3) {
                        const first = parseInt(parts[0]);
                        const second = parseInt(parts[1]);
                        const third = parts[2];
                        if (first > 12) {
                            hari = String(first).padStart(2, '0');
                            bulan = String(second).padStart(2, '0');
                            tahun = third;
                        } else {
                            bulan = String(first).padStart(2, '0');
                            hari = String(second).padStart(2, '0');
                            tahun = third;
                        }
                    } else {
                        return dateStr;
                    }
                } else {
                    return dateStr;
                }

                const bulanIdx = parseInt(bulan) - 1;
                const hariInt = parseInt(hari);
                const tahunInt = parseInt(tahun);

                if (bulanIdx < 0 || bulanIdx > 11) {
                    return "";
                }
                if (hariInt < 1 || hariInt > 31) {
                    return "";
                }

                const result = `${hariInt} ${bulanMap[bulanIdx]} ${tahunInt}`;
                console.log(`[${timestamp}] [DATE-CONVERT] SUCCESS: '${result}'`);
                return result;
            } catch (e) {
                console.error(`[${timestamp}] [DATE-CONVERT] Exception:`, e);
                return "";
            }
        }

        window.__submitJadwal = function(event) {
            const timestamp = new Date().toLocaleTimeString();
            console.log(`[${timestamp}] [JADWAL] ▶️  __submitJadwal called`);

            try {
                event?.preventDefault?.();

                let formContainer = event?.target?.closest('.chat-message-wrapper, .msg-container, .chat-message, div');

                if (!formContainer) {
                    const formActions = document.getElementById('jadwal-form-actions');
                    if (formActions) {
                        formContainer = formActions.closest(
                                '.chat-message-wrapper, .msg-container, .chat-message, [data-msg]') ||
                            formActions.parentElement?.parentElement?.parentElement ||
                            formActions.parentElement?.parentElement;
                    }
                }

                if (!formContainer) {
                    formContainer = document;
                }

                let tanggalInput = document.getElementById("jadwal-tanggal");
                if (!tanggalInput && formContainer && formContainer !== document) {
                    tanggalInput = formContainer.querySelector("#jadwal-tanggal");
                }

                let durasiJamInput = document.getElementById("jadwal-durasi-jam");
                if (!durasiJamInput && formContainer && formContainer !== document) {
                    durasiJamInput = formContainer.querySelector("#jadwal-durasi-jam");
                }

                let durasiMenitInput = document.getElementById("jadwal-durasi-menit");
                if (!durasiMenitInput && formContainer && formContainer !== document) {
                    durasiMenitInput = formContainer.querySelector("#jadwal-durasi-menit");
                }

                const tanggalRaw = tanggalInput?.value?.trim() || "";
                let tanggal = convertDatePickerToIndonesian(tanggalRaw);

                if (!tanggal && tanggalRaw) {
                    tanggal = tanggalRaw;
                }

                const jam = parseInt(durasiJamInput?.value || "1");
                const menit = parseInt(durasiMenitInput?.value || "50");

                let ruanganSelects = document.querySelectorAll(".jadwal-ruangan-select");
                const ruanganList = [];
                ruanganSelects.forEach((select, idx) => {
                    const ruangan_id = select.value;
                    if (ruangan_id) {
                        ruanganList.push(ruangan_id);
                    }
                });

                if (!tanggalRaw) {
                    Swal.fire({
                        title: 'Validasi Form',
                        text: 'Tanggal harus diisi. Silakan pilih tanggal dari kalender.',
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    });
                    return;
                }

                if (ruanganList.length === 0) {
                    Swal.fire({
                        title: 'Validasi Form',
                        text: 'Minimal 1 ruangan harus dipilih',
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    });
                    return;
                }

                const totalMenit = (jam * 60) + menit;
                if (totalMenit < 30 || totalMenit > 480) {
                    Swal.fire({
                        title: 'Durasi Terlalu Pendek/Panjang',
                        text: `Durasi harus antara 30 menit hingga 8 jam. Anda input: ${totalMenit} menit`,
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    });
                    return;
                }

                const message =
                    `[jadwal] tanggal: ${tanggal} | ruangan: ${ruanganList.join(",")} | durasi: ${totalMenit}`;
                window.__lastJadwalMessage = message;

                const userInput = document.getElementById("userInput");
                if (userInput) {
                    userInput.value = message;
                    const submitBtn = event.target?.closest('.jadwal-submit-btn');
                    setJadwalSubmitButtonLoading(submitBtn);
                    window.sendMessage();
                } else {
                    restoreJadwalSubmitButton();
                    Swal.fire({
                        title: 'Error',
                        text: 'Terjadi error: userInput element tidak ditemukan',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            } catch (error) {
                console.error(`[${new Date().toLocaleTimeString()}] [JADWAL] ❌ Exception:`, error);
                restoreJadwalSubmitButton();
                Swal.fire({
                    title: 'Error',
                    text: `Terjadi error: ${error.message}`,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        };

        // ============== VERIFICATION SIDEBAR FUNCTIONS ==============

        async function loadVerificationStatusFromDatabase() {
            try {
                const response = await fetch('/ai-agent/verification-status');
                if (!response.ok) throw new Error('Failed to fetch verification status');
                const data = await response.json();
                if (data.success && data.data) {
                    updateSidebarStatus('kelompok', data.data.kelompok);
                    updateSidebarStatus('pembimbing', data.data.pembimbing);
                    updateSidebarStatus('penguji', data.data.penguji);
                    updateSidebarStatus('jadwal', data.data.jadwal);
                }
            } catch (error) {
                console.warn('[sidebar] Error loading verification status:', error.message);
            }
        }

        function updateSidebarStatus(step, status) {
            const stepElement = document.querySelector(`.step-item[data-step="${step}"]`);
            if (!stepElement) return;
            const stepStatus = stepElement.querySelector('.step-status');
            const pendingIcon = stepElement.querySelector('.pending-icon');
            const successIcon = stepElement.querySelector('.success-icon');
            stepElement.setAttribute('data-status', status);
            stepStatus.setAttribute('data-status', status);
            if (status === 'success' && successIcon && pendingIcon) {
                pendingIcon.style.display = 'none';
                successIcon.style.display = 'block';
            } else if (status === 'warning' || status === 'pending') {
                if (pendingIcon) pendingIcon.style.display = 'block';
                if (successIcon) successIcon.style.display = 'none';
            }
            console.log(`[sidebar] Updated ${step} status to ${status}`);
        }

        function getSidebarStatus(step) {
            const stepElement = document.querySelector(`.step-item[data-step="${step}"]`);
            if (!stepElement) return 'pending';
            return stepElement.getAttribute('data-status') || 'pending';
        }

        function setSidebarWarning(step, hasWarning = true) {
            if (hasWarning) {
                updateSidebarStatus(step, 'warning');
            }
        }

        function scrollToBottom() {
            const el = document.getElementById("chatBox");
            if (el) {
                setTimeout(() => {
                    el.scrollTop = el.scrollHeight;
                }, 10);
            }
        }

        const AI_ICON =
            `<img src="<?php echo e(asset('assets/img/logoagent.png')); ?>" alt="VokasiTera Agent Logo" style="width: 24px; height: 24px; object-fit: contain;">`;

        const USER_ICON = `<svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="15" height="15">
    <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/>
    <circle cx="12" cy="7" r="4"/>
</svg>`;

        function appendMessage(sender, text) {
            const chatBox = document.getElementById("chatBox");

            const row = document.createElement("div");
            row.className = `msg-row ${sender === "user" ? "user-row" : ""}`;

            const avatar = document.createElement("div");
            avatar.className = `msg-av ${sender === "user" ? "user-av" : "ai-av"}`;
            avatar.innerHTML = sender === "user" ? USER_ICON : AI_ICON;

            const body = document.createElement("div");
            body.className = "msg-body";

            const meta = document.createElement("div");
            meta.className = "msg-meta";

            const name = document.createElement("div");
            name.className = "msg-name";
            name.textContent = sender === "user" ? "Anda" : "VokasiTera AI";

            const tag = document.createElement("div");
            tag.className = `msg-tag ${sender === "user" ? "user-tag" : "ai-tag"}`;
            tag.textContent = sender === "user" ? "USER" : "AI";

            const time = document.createElement("div");
            time.className = "msg-time";
            time.textContent = new Date().toLocaleTimeString([], {
                hour: '2-digit',
                minute: '2-digit'
            });

            meta.appendChild(name);
            meta.appendChild(tag);
            meta.appendChild(time);

            const bubble = document.createElement("div");
            bubble.className = `msg-bubble ${sender === "user" ? "user-bubble" : "ai-bubble"}`;

            if (sender === "user") {
                bubble.textContent = text;
            } else {
                bubble.innerHTML = text;
            }

            body.appendChild(meta);
            body.appendChild(bubble);
            row.appendChild(avatar);
            row.appendChild(body);
            chatBox.appendChild(row);

            if (sender === "ai" && text.includes('Input Jadwal Seminar')) {
                setTimeout(() => {
                    appendJadwalFormActions(row);
                    console.log("[JADWAL] Initializing calendar...");
                    initJadwalCalendar();
                }, 200);
            }

            // ── Pasang script dari form grouping yang baru di-render ──
            if (sender === "ai") {
                bubble.querySelectorAll('script').forEach(function(oldScript) {
                    const newScript = document.createElement('script');
                    newScript.textContent = oldScript.textContent;
                    oldScript.parentNode.replaceChild(newScript, oldScript);
                });
            }

            scrollToBottom();
        }

        function appendGroupingActions() {
            const chatBox = document.getElementById("chatBox");
            if (!latestGroupingPayload?.groups?.length) return;

            const row = document.createElement("div");
            row.className = "msg-row";

            const avatar = document.createElement("div");
            avatar.className = "msg-av ai-av";
            avatar.innerHTML = AI_ICON;

            const body = document.createElement("div");
            body.className = "msg-body";

            const bubble = document.createElement("div");
            bubble.className = "msg-bubble ai-bubble";
            bubble.innerHTML = `
        <div style="padding:8px">
            <b>Aksi Hasil Generate</b><br><br>
            <div style="display:flex; gap:8px; flex-wrap:wrap;">
                <button class="btn btn-sm btn-primary save-generated-groups-btn">
                    💾 Simpan ke Database
                </button>
                <button type="button" class="btn btn-sm btn-warning regenerate-groups-btn">
                    <i class="fas fa-random"></i> Acak Ulang Mahasiswa
                </button>
            </div>
        </div>`;

            body.appendChild(bubble);
            row.appendChild(avatar);
            row.appendChild(body);
            chatBox.appendChild(row);
            scrollToBottom();
        }

        function formatJadwalPromptDisplay(message) {
            if (typeof message !== "string") return message;
            const trimmed = message.trim();
            if (!trimmed.toLowerCase().startsWith("[jadwal]")) return message;

            const fields = {};
            trimmed.replace(/^\[jadwal\]\s*/i, "").split("|").forEach((segment) => {
                const part = segment.trim();
                if (!part) return;
                const separatorIndex = part.indexOf(":");
                if (separatorIndex === -1) return;
                const key = part.slice(0, separatorIndex).trim().toLowerCase();
                const value = part.slice(separatorIndex + 1).trim();
                if (key) fields[key] = value;
            });

            const action = (fields.action || "").toLowerCase();
            const prefix = (action === "save" || action === "simpan" || action === "persist") ?
                "Simpan jadwal seminar" :
                (action === "acak" || action === "shuffle" || action === "random") ?
                "Acak ulang jadwal seminar" : "Jadwal seminar";

            const parts = [];
            if (fields.tanggal) parts.push(`tanggal ${fields.tanggal}`);

            if (fields.ruangan) {
                let ruanganDisplay = fields.ruangan;
                try {
                    const maybeIds = String(fields.ruangan).split(',').map(s => s.trim()).filter(Boolean);
                    const allNumeric = maybeIds.length > 0 && maybeIds.every(s => /^\d+$/.test(s));
                    if (allNumeric) {
                        const idToName = {};
                        if (typeof latestJadwalEntries !== 'undefined' && Array.isArray(latestJadwalEntries) &&
                            latestJadwalEntries.length) {
                            latestJadwalEntries.forEach(e => {
                                if (e && (e.ruangan_id || e.ruangan_id === 0)) {
                                    idToName[String(e.ruangan_id)] = e.ruangan_name || e.ruangan || String(e
                                        .ruangan_id);
                                }
                            });
                        }
                        if (Object.keys(idToName).length === 0) {
                            try {
                                const opts = document.querySelectorAll('select.jadwal-ruangan-select option');
                                opts.forEach(o => {
                                    if (o && o.value) idToName[String(o.value)] = o.textContent.trim();
                                });
                            } catch (e) {}
                        }
                        ruanganDisplay = maybeIds.map(id => idToName[id] || (`Ruangan ${id}`)).join(', ');
                    }
                } catch (e) {
                    ruanganDisplay = fields.ruangan;
                }
                parts.push(`ruangan ${ruanganDisplay}`);
            }

            if (fields.durasi) parts.push(`durasi ${fields.durasi} menit`);
            if (fields.order) parts.push(`urutan kelompok ${fields.order}`);
            if (!parts.length) return prefix;
            return `${prefix} pada ${parts.join(", ")}.`;
        }

        function restoreJadwalSubmitButton() {
            const state = window.__jadwalSubmitState;
            if (!state || !state.button) return;
            if (typeof state.originalHtml === 'string') state.button.innerHTML = state.originalHtml;
            state.button.disabled = !!state.originalDisabled;
            window.__jadwalSubmitState = null;
        }

        function setJadwalSubmitButtonLoading(button) {
            if (!button) return;
            window.__jadwalSubmitState = {
                button,
                originalHtml: button.innerHTML,
                originalDisabled: button.disabled
            };
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
        }

        function appendPembimbingActions() {
            const chatBox = document.getElementById("chatBox");
            if (!chatBox || !latestPembimbingPayload || !Array.isArray(latestPembimbingPayload.groups) ||
                latestPembimbingPayload.groups.length === 0) return;

            const wrapper = document.createElement("div");
            wrapper.className = "message-wrapper ai";

            const bubble = document.createElement("div");
            bubble.className = "chat-message-bubble ai";

            const existingCount = Number(latestPembimbingMeta?.existing_assignments_count || 0);
            const existingNote = existingCount > 0 ?
                `<p style="margin:0 0 8px 0; color:#b45309;"><strong>Perhatian:</strong> Sudah ada ${existingCount} assignment pembimbing pada konteks ini. Simpan akan meminta konfirmasi replace.</p>` :
                `<p style="margin:0 0 8px 0; color:#166534;">Belum ada assignment pembimbing pada konteks ini.</p>`;

            bubble.innerHTML = `
        <div style="border:1px solid #fcd34d; background:#fffbeb; border-radius:10px; padding:10px;">
            <h6 style="margin:0 0 8px 0;">Aksi Hasil Generate Pembimbing</h6>
            ${existingNote}
            <div style="display:flex; gap:8px; flex-wrap:wrap;">
                <button type="button" class="btn btn-sm btn-primary save-generated-pembimbing-btn" style="position:relative; z-index:2; cursor:pointer; pointer-events:auto;">
                    <i class="fas fa-database"></i> Simpan Pembimbing ke Database
                </button>
                <button type="button" class="btn btn-sm btn-warning regenerate-pembimbing-btn" style="position:relative; z-index:2; cursor:pointer; pointer-events:auto;">
                    <i class="fas fa-random"></i> Acak Ulang Pembimbing
                </button>
            </div>
        </div>`;

            wrapper.appendChild(bubble);
            chatBox.appendChild(wrapper);

            const saveButton = wrapper.querySelector('.save-generated-pembimbing-btn');
            if (saveButton) {
                saveButton.onclick = async function(event) {
                    event.preventDefault();
                    event.stopPropagation();
                    if (typeof window.__savePembimbingInline === 'function') await window.__savePembimbingInline(
                        event);
                };
            }

            const regenerateButton = wrapper.querySelector('.regenerate-pembimbing-btn');
            if (regenerateButton) {
                regenerateButton.onclick = async function(event) {
                    event.preventDefault();
                    event.stopPropagation();
                    if (typeof window.__regeneratePembimbingInline === 'function') await window
                        .__regeneratePembimbingInline(event);
                };
            }

            scrollToBottom();
        }

        function appendPengujiActions() {
            const chatBox = document.getElementById("chatBox");
            if (!chatBox || !latestPengujiPayload || !Array.isArray(latestPengujiPayload.groups) || latestPengujiPayload
                .groups.length === 0) return;

            const wrapper = document.createElement("div");
            wrapper.className = "message-wrapper ai";

            const bubble = document.createElement("div");
            bubble.className = "chat-message-bubble ai";

            const existingCount = Number(latestPengujiMeta?.existing_assignments_count || 0);
            const existingNote = existingCount > 0 ?
                `<p style="margin:0 0 8px 0; color:#b45309;"><strong>Perhatian:</strong> Sudah ada ${existingCount} assignment penguji pada konteks ini. Simpan akan meminta konfirmasi replace.</p>` :
                `<p style="margin:0 0 8px 0; color:#166534;">Belum ada assignment penguji pada konteks ini.</p>`;

            bubble.innerHTML = `
        <div style="border:1px solid #86efac; background:#f0fdf4; border-radius:10px; padding:10px;">
            <h6 style="margin:0 0 8px 0;">Aksi Hasil Generate Penguji</h6>
            ${existingNote}
            <div style="display:flex; gap:8px; flex-wrap:wrap;">
                <button type="button" class="btn btn-sm btn-success save-generated-penguji-btn" style="position:relative; z-index:2; cursor:pointer; pointer-events:auto;">
                    <i class="fas fa-database"></i> Simpan Penguji ke Database
                </button>
                <button type="button" class="btn btn-sm btn-warning regenerate-penguji-btn" style="position:relative; z-index:2; cursor:pointer; pointer-events:auto;">
                    <i class="fas fa-random"></i> Acak Ulang Penguji
                </button>
            </div>
        </div>`;

            wrapper.appendChild(bubble);
            chatBox.appendChild(wrapper);

            const saveButton = wrapper.querySelector('.save-generated-penguji-btn');
            if (saveButton) {
                saveButton.onclick = async function(event) {
                    event.preventDefault();
                    event.stopPropagation();
                    if (typeof window.__savePengujiInline === 'function') await window.__savePengujiInline(event);
                };
            }

            const regenerateButton = wrapper.querySelector('.regenerate-penguji-btn');
            if (regenerateButton) {
                regenerateButton.onclick = async function(event) {
                    event.preventDefault();
                    event.stopPropagation();
                    if (typeof window.__regeneratePengujiInline === 'function') await window
                        .__regeneratePengujiInline(event);
                };
            }

            scrollToBottom();
        }

        function appendExcelDownloadButton() {
            const chatBox = document.getElementById("chatBox");
            if (!chatBox || !latestExcelFilename) return;

            const wrapper = document.createElement("div");
            wrapper.className = "message-wrapper ai";

            const bubble = document.createElement("div");
            bubble.className = "chat-message-bubble ai";
            bubble.innerHTML = `
        <div style="border:1px solid #93c5fd; background:#eff6ff; border-radius:10px; padding:10px;">
            <h6 style="margin:0 0 8px 0;">📥 Unduh File Excel</h6>
            <p style="margin:0 0 8px 0; font-size:14px;">File Excel siap untuk diunduh</p>
            <button type="button" class="btn btn-sm btn-info" onclick="downloadExcel('${latestExcelFilename}')">
                <i class="fas fa-file-excel" style="color:#10b981;"></i> Unduh Excel
            </button>
        </div>`;

            wrapper.appendChild(bubble);
            chatBox.appendChild(wrapper);
            scrollToBottom();
        }

        function downloadExcel(filename) {
            const downloadRoute = '<?php echo e(route('ai.downloadExcel')); ?>';
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = downloadRoute;
            form.style.display = 'none';

            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = document.querySelector('meta[name="csrf-token"]')?.content || '';

            const filenameInput = document.createElement('input');
            filenameInput.type = 'hidden';
            filenameInput.name = 'filename';
            filenameInput.value = filename;

            form.appendChild(csrfInput);
            form.appendChild(filenameInput);
            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);
        }

        async function checkExistingGroups() {
            const section = document.querySelector('[data-ai-route]');
            const checkRoute = section?.dataset.checkRoute;
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
            const response = await fetch(checkRoute, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken
                },
                body: JSON.stringify({})
            });
            if (!response.ok) {
                const txt = await response.text();
                throw new Error(`Gagal cek kelompok: ${txt.substring(0, 200)}`);
            }
            return response.json();
        }

        async function saveGeneratedGroups() {
            if (isSavingGeneratedGroups) return;
            if (!latestGroupingPayload || !Array.isArray(latestGroupingPayload.groups) || latestGroupingPayload.groups
                .length === 0) {
                Swal.fire("Tidak ada data", "Generate kelompok terlebih dahulu sebelum menyimpan.", "warning");
                return;
            }

            const section = document.querySelector('[data-ai-route]');
            const saveRoute = section?.dataset.saveRoute;
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

            try {
                isSavingGeneratedGroups = true;
                let replaceExisting = false;
                const checkResult = await checkExistingGroups();

                if (checkResult?.exists) {
                    const confirm = await Swal.fire({
                        title: "Kelompok Sudah Ada",
                        html: `Ditemukan <strong>${checkResult.total}</strong> kelompok pada konteks ini.<br><br>Anda bisa <b>tambah kelompok baru saja</b> (tanpa menghapus yang lama), atau pilih replace jika ingin reset.`,
                        icon: "question",
                        showCancelButton: true,
                        showDenyButton: true,
                        confirmButtonText: "Hapus lama & Simpan baru",
                        denyButtonText: "Tambah saja (tanpa hapus)",
                        cancelButtonText: "Batal"
                    });
                    if (confirm.isConfirmed) {
                        replaceExisting = true;
                    } else if (confirm.isDenied) {
                        replaceExisting = false;
                    } else {
                        return;
                    }
                }

                const response = await fetch(saveRoute, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": csrfToken
                    },
                    body: JSON.stringify({
                        grouping_payload: latestGroupingPayload,
                        grouping_meta: latestGroupingMeta,
                        replace_existing: replaceExisting
                    })
                });

                const data = await response.json();
                if (!response.ok || !data.success) throw new Error(data.message || "Gagal menyimpan kelompok.");

                updateSidebarStatus('kelompok', 'success');
                Swal.fire({
                    title: "Berhasil",
                    html: `Kelompok berhasil disimpan.<br><strong>${data.saved_kelompok || 0}</strong> kelompok dan <strong>${data.saved_members || 0}</strong> anggota tersimpan.${(data.skipped_existing_members || 0) > 0 ? `<br><small>${data.skipped_existing_members} mahasiswa dilewati karena sudah punya kelompok.</small>` : ''}`,
                    icon: "success"
                });

                latestGroupingPayload = null;
                latestGroupingMeta = null;

                document.querySelectorAll(".save-generated-groups-btn").forEach((btn) => {
                    btn.disabled = true;
                    btn.classList.add("disabled");
                    btn.innerHTML = '<i class="fas fa-check"></i> Sudah Disimpan';
                });
                document.querySelectorAll(".regenerate-groups-btn").forEach((btn) => {
                    btn.disabled = true;
                    btn.classList.add("disabled");
                    btn.innerHTML = '<i class="fas fa-check"></i> Acak Ulang Dinonaktifkan';
                });
            } catch (error) {
                updateSidebarStatus('kelompok', 'warning');
                Swal.fire("Error", error.message || "Terjadi kesalahan saat menyimpan.", "error");
            } finally {
                isSavingGeneratedGroups = false;
            }
        }

        window.__regenerateGroupsInline = async function(event) {
            event?.preventDefault?.();
            event?.stopPropagation?.();
            event?.stopImmediatePropagation?.();

            if (!latestGroupingPayload || !Array.isArray(latestGroupingPayload.groups) || latestGroupingPayload
                .groups.length === 0) {
                Swal.fire("Tidak ada data", "Generate kelompok terlebih dahulu sebelum acak ulang.", "warning");
                return;
            }

            const input = document.getElementById("userInput");
            const basePrompt = latestGroupingMeta?.prompt || latestUserPrompt || "buat kelompok mahasiswa";
            const isByGrades = String(latestGroupingMeta?.method || '').toLowerCase().includes('grade') ||
                /berdasarkan\s+nilai|by\s+grade|by\s+grades/i.test(basePrompt);
            const regeneratePrompt = isByGrades ?
                `${basePrompt} dan acak ulang komposisi mahasiswa antar kelompok` :
                `${basePrompt} dan acak ulang mahasiswa`;

            if (input) input.value = regeneratePrompt;
            if (typeof window.sendMessage === 'function') window.sendMessage();
        };

        function switchToChat() {
            document.getElementById("landingView").style.display = "none";
            document.getElementById("chatView").style.display = "flex";
            document.getElementById("btnNewChat").style.display = "flex";
        }

        async function saveGeneratedPembimbing() {
            if (isSavingGeneratedPembimbing) return;
            if (!latestPembimbingPayload || !Array.isArray(latestPembimbingPayload.groups) || latestPembimbingPayload
                .groups.length === 0) {
                Swal.fire("Tidak ada data", "Generate pembimbing terlebih dahulu sebelum menyimpan.", "warning");
                return;
            }

            const section = document.querySelector('[data-ai-route]');
            const saveRoute = section?.dataset.savePembimbingRoute;
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

            async function submitSave(replaceExisting = false) {
                return fetch(saveRoute, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": csrfToken
                    },
                    body: JSON.stringify({
                        pembimbing_payload: latestPembimbingPayload,
                        pembimbing_meta: latestPembimbingMeta,
                        replace_existing: replaceExisting
                    })
                });
            }

            try {
                isSavingGeneratedPembimbing = true;
                let response = await submitSave(false);
                let data = await response.json();

                if (response.status === 409 && data?.requires_confirmation) {
                    const confirm = await Swal.fire({
                        title: "Pembimbing Sudah Ada",
                        html: `Ditemukan <strong>${data.existing_count || 0}</strong> assignment pembimbing pada konteks ini.<br>Pilih <b>Hapus lama & Simpan baru</b> untuk replace data.`,
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonText: "Hapus lama & Simpan baru",
                        cancelButtonText: "Batal"
                    });
                    if (!confirm.isConfirmed) return;
                    response = await submitSave(true);
                    data = await response.json();
                }

                if (!response.ok || !data.success) throw new Error(data.message || "Gagal menyimpan pembimbing.");

                updateSidebarStatus('pembimbing', 'success');
                Swal.fire({
                    title: "Berhasil",
                    html: `Pembimbing berhasil disimpan.<br><strong>${data.saved_assignments || 0}</strong> assignment tersimpan.`,
                    icon: "success"
                });

                latestPembimbingPayload = null;
                latestPembimbingMeta = null;
                document.querySelectorAll(".save-generated-pembimbing-btn").forEach((btn) => {
                    btn.disabled = true;
                    btn.classList.add("disabled");
                    btn.innerHTML = '<i class="fas fa-check"></i> Pembimbing Sudah Disimpan';
                });
            } catch (error) {
                updateSidebarStatus('pembimbing', 'warning');
                Swal.fire("Error", error.message || "Terjadi kesalahan saat menyimpan pembimbing.", "error");
            } finally {
                isSavingGeneratedPembimbing = false;
            }
        }

        async function checkExistingPembimbing() {
            const section = document.querySelector('[data-ai-route]');
            const checkRoute = section?.dataset.checkPembimbingRoute || '/ai-agent/ai-pembimbing/check';
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
            const response = await fetch(checkRoute, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken
                },
                body: JSON.stringify({})
            });
            if (!response.ok) {
                const txt = await response.text();
                throw new Error(`Gagal cek pembimbing: ${txt.substring(0, 200)}`);
            }
            return response.json();
        }

        async function checkExistingPenguji() {
            const section = document.querySelector('[data-ai-route]');
            const checkRoute = section?.dataset.checkPengujiRoute || '/ai-agent/ai-penguji/check';
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
            const response = await fetch(checkRoute, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken
                },
                body: JSON.stringify({})
            });
            if (!response.ok) {
                const txt = await response.text();
                throw new Error(`Gagal cek penguji: ${txt.substring(0, 200)}`);
            }
            return response.json();
        }

        async function saveGeneratedPenguji() {
            if (isSavingGeneratedPenguji) return;
            if (!latestPengujiPayload || !Array.isArray(latestPengujiPayload.groups) || latestPengujiPayload.groups
                .length === 0) {
                Swal.fire("Tidak ada data", "Generate penguji terlebih dahulu sebelum menyimpan.", "warning");
                return;
            }

            const section = document.querySelector('[data-ai-route]');
            const saveRoute = section?.dataset.savePengujiRoute;
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

            async function submitSave(replaceExisting = false) {
                return fetch(saveRoute, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": csrfToken
                    },
                    body: JSON.stringify({
                        penguji_payload: latestPengujiPayload,
                        penguji_meta: latestPengujiMeta,
                        replace_existing: replaceExisting
                    })
                });
            }

            try {
                isSavingGeneratedPenguji = true;
                let response = await submitSave(false);
                let data = await response.json();

                if (response.status === 409 && data?.requires_confirmation) {
                    const confirm = await Swal.fire({
                        title: "Penguji Sudah Ada",
                        html: `Ditemukan <strong>${data.existing_count || 0}</strong> assignment penguji pada konteks ini.<br>Pilih <b>Hapus lama & Simpan baru</b> untuk replace data.`,
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonText: "Hapus lama & Simpan baru",
                        cancelButtonText: "Batal"
                    });
                    if (!confirm.isConfirmed) return;
                    response = await submitSave(true);
                    data = await response.json();
                }

                if (!response.ok || !data.success) throw new Error(data.message || "Gagal menyimpan penguji.");

                updateSidebarStatus('penguji', 'success');
                Swal.fire({
                    title: "Berhasil",
                    html: `Penguji berhasil disimpan.<br><strong>${data.saved_assignments || 0}</strong> assignment tersimpan.`,
                    icon: "success"
                });

                latestPengujiPayload = null;
                latestPengujiMeta = null;
                document.querySelectorAll(".save-generated-penguji-btn").forEach((btn) => {
                    btn.disabled = true;
                    btn.classList.add("disabled");
                    btn.innerHTML = '<i class="fas fa-check"></i> Penguji Sudah Disimpan';
                });
            } catch (error) {
                updateSidebarStatus('penguji', 'warning');
                Swal.fire("Error", error.message || "Terjadi kesalahan saat menyimpan penguji.", "error");
            } finally {
                isSavingGeneratedPenguji = false;
            }
        }

        window.__savePembimbingInline = async function(event) {
            event?.preventDefault?.();
            event?.stopPropagation?.();
            event?.stopImmediatePropagation?.();
            await saveGeneratedPembimbing();
        };

        window.__regeneratePembimbingInline = async function(event) {
            event?.preventDefault?.();
            event?.stopPropagation?.();
            event?.stopImmediatePropagation?.();
            if (isLoadingPembimbingCheck) return;
            try {
                isLoadingPembimbingCheck = true;
                const checkResult = await checkExistingPembimbing();
                if (checkResult?.exists) {
                    const confirm = await Swal.fire({
                        title: "Validasi Pembimbing",
                        html: `Sudah ada <strong>${checkResult.total || 0}</strong> assignment pembimbing pada konteks koordinator ini.<br>Jika lanjut acak ulang, data baru akan menggantikan hasil yang ada setelah disimpan.`,
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonText: "Lanjut Acak Ulang",
                        cancelButtonText: "Batal"
                    });
                    if (!confirm.isConfirmed) return;
                }
                const input = document.getElementById("userInput");
                const regeneratePrompt = latestPembimbingMeta?.prompt || latestUserPrompt ||
                    "generate pembimbing kelompok";
                if (input) input.value = regeneratePrompt;
                if (typeof window.sendMessage === 'function') window.sendMessage();
            } catch (error) {
                Swal.fire("Error", error.message || "Gagal validasi pembimbing.", "error");
            } finally {
                isLoadingPembimbingCheck = false;
            }
        };

        window.__savePengujiInline = async function(event) {
            event?.preventDefault?.();
            event?.stopPropagation?.();
            event?.stopImmediatePropagation?.();
            await saveGeneratedPenguji();
        };

        window.__regeneratePengujiInline = async function(event) {
            event?.preventDefault?.();
            event?.stopPropagation?.();
            event?.stopImmediatePropagation?.();
            if (isLoadingPengujiCheck) return;
            try {
                isLoadingPengujiCheck = true;
                const checkResult = await checkExistingPenguji();
                if (checkResult?.exists) {
                    const confirm = await Swal.fire({
                        title: "Validasi Penguji",
                        html: `Sudah ada <strong>${checkResult.total || 0}</strong> assignment penguji pada konteks koordinator ini.<br>Jika lanjut acak ulang, data baru akan menggantikan hasil yang ada setelah disimpan.`,
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonText: "Lanjut Acak Ulang",
                        cancelButtonText: "Batal"
                    });
                    if (!confirm.isConfirmed) return;
                }
                const input = document.getElementById("userInput");
                const regeneratePrompt = latestPengujiMeta?.prompt || latestUserPrompt ||
                    "generate penguji kelompok";
                if (input) input.value = regeneratePrompt;
                if (typeof window.sendMessage === 'function') window.sendMessage();
            } catch (error) {
                Swal.fire("Error", error.message || "Gagal validasi penguji.", "error");
            } finally {
                isLoadingPengujiCheck = false;
            }
        };

        function appendLoading() {
            const chatBox = document.getElementById("chatBox");
            const id = "loading-" + Date.now();

            const row = document.createElement("div");
            row.className = "msg-row";

            const avatar = document.createElement("div");
            avatar.className = "msg-av ai-av";
            avatar.innerHTML = AI_ICON;

            const skeleton = document.createElement("div");
            skeleton.className = "skeleton-wrap";
            skeleton.id = id;
            skeleton.innerHTML = `
    <div class="ai-thinking">
        <span class="ai-thinking-icon">${AI_ICON}</span>
        AI sedang berpikir...
    </div>
    <div class="skeleton-line" style="width:120px;"></div>
    <div class="skeleton-line" style="width:180px;"></div>
    <div class="skeleton-line" style="width:90px;"></div>`;

            row.appendChild(avatar);
            row.appendChild(skeleton);
            chatBox.appendChild(row);
            scrollToBottom();
            return id;
        }

        function removeLoading(id) {
            const el = document.getElementById(id);
            if (el) {
                const row = el.closest(".msg-row");
                if (row) row.remove();
            }
        }

        function attachRecommendationListeners() {
            function handleActionClick(e) {
                e.preventDefault();
                const input = document.getElementById("userInput");
                const instruction = this.getAttribute('data-instruction');
                if (instruction) {
                    input.value = instruction;
                    input.dispatchEvent(new KeyboardEvent('keydown', {
                        key: 'Enter',
                        code: 'Enter',
                        keyCode: 13,
                        which: 13,
                        bubbles: true
                    }));
                }
            }

            function handleConstraintClick() {
                const input = document.getElementById("userInput");
                const instruction = this.getAttribute('data-instruction');
                if (instruction && confirm(`Terapkan: ${instruction}?`)) {
                    input.value = instruction;
                    input.dispatchEvent(new KeyboardEvent('keydown', {
                        key: 'Enter',
                        code: 'Enter',
                        keyCode: 13,
                        which: 13,
                        bubbles: true
                    }));
                }
            }

            document.querySelectorAll('.recommendation-action').forEach(btn => {
                btn.removeEventListener('click', handleActionClick);
                btn.addEventListener('click', handleActionClick);
            });
            document.querySelectorAll('.recommendation-constraint').forEach(btn => {
                btn.removeEventListener('click', handleConstraintClick);
                btn.addEventListener('click', handleConstraintClick);
            });
        }

        function bindChatActionDelegation() {
            // ── deklarasi chatBox DI SINI, paling atas fungsi ──
            const chatBox = document.getElementById("chatBox");
            if (!chatBox || chatBox.dataset.actionsBound === "1") return;

            async function executeDeleteForContext(recreatePrompt) {
                if (isDeletingForContext) return;

                const section = document.querySelector('[data-ai-route]');
                const deleteRoute = section?.dataset.deleteRoute || '/ai-kelompok/delete-for-context';
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

                console.log("[chatbot] confirm-recreate click", {
                    deleteRoute,
                    hasCsrf: !!csrfToken,
                    hasRecreatePrompt: !!recreatePrompt
                });

                try {
                    isDeletingForContext = true;
                    const deleteResponse = await fetch(deleteRoute, {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": csrfToken
                        },
                        body: JSON.stringify({})
                    });

                    if (!deleteResponse.ok) {
                        const txt = await deleteResponse.text();
                        throw new Error(`Gagal hapus kelompok: ${txt.substring(0, 200)}`);
                    }

                    const deleteData = await deleteResponse.json();
                    if (!deleteData.success) throw new Error(deleteData.message || "Gagal menghapus kelompok.");

                    if (recreatePrompt && recreatePrompt.trim().length > 0) {
                        Swal.fire({
                            title: "Kelompok Dihapus",
                            html: `<strong>${deleteData.deleted_kelompok || 0}</strong> kelompok dan <strong>${deleteData.deleted_members || 0}</strong> anggota telah dihapus.<br><b>Membuat kelompok baru...</b>`,
                            icon: "info",
                            allowOutsideClick: false,
                            didOpen: () => Swal.showLoading()
                        });
                        setTimeout(() => {
                            const input = document.getElementById("userInput");
                            if (input) {
                                input.value = recreatePrompt;
                                if (typeof window.sendMessage === 'function') window.sendMessage();
                            }
                            Swal.close();
                        }, 500);
                    } else {
                        Swal.fire({
                            title: "Berhasil Dihapus",
                            html: `<strong>${deleteData.deleted_kelompok || 0}</strong> kelompok dan <strong>${deleteData.deleted_members || 0}</strong> anggota telah dihapus.<br><b>Silakan kirim instruksi generate kelompok baru.</b>`,
                            icon: "success"
                        });
                        const input = document.getElementById("userInput");
                        if (input) {
                            input.value = "";
                            input.focus();
                        }
                    }
                } catch (error) {
                    console.error("[chatbot] delete error", error);
                    Swal.fire("Error", error.message || "Terjadi kesalahan saat menghapus kelompok.", "error");
                } finally {
                    isDeletingForContext = false;
                }
            }

            window.__confirmRecreateGroupsFromInline = function(event) {
                event?.preventDefault?.();
                event?.stopPropagation?.();
                event?.stopImmediatePropagation?.();
                const button = event?.target?.closest?.('.confirm-recreate-groups');
                const recreatePrompt = button?.dataset?.recreatePrompt || '';
                executeDeleteForContext(recreatePrompt);
            };

            chatBox.dataset.actionsBound = "1";

            chatBox.addEventListener("click", async function(event) {
                const recreateButton = event.target.closest(".confirm-recreate-groups");
                if (recreateButton) {
                    event.preventDefault();
                    event.stopPropagation();
                    executeDeleteForContext(recreateButton.dataset?.recreatePrompt || '');
                    return;
                }

                const cancelButton = event.target.closest(".cancel-recreate");
                if (cancelButton) {
                    const input = document.getElementById("userInput");
                    if (input) {
                        input.value = "";
                        input.focus();
                    }
                    return;
                }

                const saveButton = event.target.closest(".save-generated-groups-btn");
                if (saveButton) {
                    event.preventDefault();
                    event.stopPropagation();
                    saveGeneratedGroups();
                    return;
                }

                const regenerateGroupsButton = event.target.closest(".regenerate-groups-btn");
                if (regenerateGroupsButton) {
                    event.preventDefault();
                    event.stopPropagation();
                    if (typeof window.__regenerateGroupsInline === 'function') window.__regenerateGroupsInline(
                        event);
                    return;
                }

                const savePembimbingButton = event.target.closest(".save-generated-pembimbing-btn");
                if (savePembimbingButton) {
                    event.preventDefault();
                    event.stopPropagation();
                    if (typeof window.__savePembimbingInline === 'function') window.__savePembimbingInline(
                        event);
                    else saveGeneratedPembimbing();
                    return;
                }

                const regeneratePembimbingButton = event.target.closest(".regenerate-pembimbing-btn");
                if (regeneratePembimbingButton) {
                    event.preventDefault();
                    event.stopPropagation();
                    if (typeof window.__regeneratePembimbingInline === 'function') window
                        .__regeneratePembimbingInline(event);
                    return;
                }

                const savePengujiButton = event.target.closest(".save-generated-penguji-btn");
                if (savePengujiButton) {
                    event.preventDefault();
                    event.stopPropagation();
                    if (typeof window.__savePengujiInline === 'function') window.__savePengujiInline(event);
                    else saveGeneratedPenguji();
                    return;
                }

                const regeneratePengujiButton = event.target.closest(".regenerate-penguji-btn");
                if (regeneratePengujiButton) {
                    event.preventDefault();
                    event.stopPropagation();
                    if (typeof window.__regeneratePengujiInline === 'function') window
                        .__regeneratePengujiInline(event);
                    return;
                }
            });
        }

        function autoResizeTextarea(textarea, minHeight = 0) {
            if (!textarea) return;
            textarea.style.height = 'auto';
            const nextHeight = Math.max(textarea.scrollHeight, minHeight);
            textarea.style.height = `${nextHeight}px`;
        }

        window.initializeAgent = function() {
            console.log("[chatbot] Initializing...")
            const input = document.getElementById("userInput")
            const sendBtn = document.getElementById("sendBtn")

            if (!input || !sendBtn) {
                console.error("[chatbot] Required elements not found!");
                return;
            }

            input.addEventListener("keydown", function(e) {
                if (e.key === "Enter" && !e.shiftKey) {
                    e.preventDefault();
                    if (typeof window.sendMessage === 'function') window.sendMessage();
                }
            });

            input.addEventListener("input", function() {
                autoResizeTextarea(input, 20);
            });
            autoResizeTextarea(input, 20);

            sendBtn.addEventListener("click", function(e) {
                e.preventDefault();
                if (typeof window.sendMessage === 'function') window.sendMessage();
            });

            input.focus();
            console.log("[chatbot] Initialized successfully")
        };

        window.sendMessage = function() {
            const input = document.getElementById("userInput")
            const section = document.querySelector('[data-ai-route]')
            const route = section?.dataset.aiRoute || '/ai/generate'

            let message = input.value.trim()
            if (!message) return

            latestUserPrompt = message
            const displayMessage = formatJadwalPromptDisplay(message)

            switchToChat();

            if (!window.__jadwalSaveInProgress) {
                appendMessage("user", displayMessage);
            }
            input.value = ""
            autoResizeTextarea(input, 20)
            input.focus()

            let loadingId = appendLoading()

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

            const jadwalPayload = {};
            if (latestJadwalMeta) jadwalPayload.jadwal_meta = latestJadwalMeta;
            if (latestJadwalEntries && latestJadwalEntries.length) jadwalPayload.jadwal_entries = latestJadwalEntries;

            fetch(route, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": csrfToken
                    },
                    body: JSON.stringify({
                        prompt: message,
                        ...jadwalPayload
                    })
                })
                .then(res => {
                    if (!res.ok) {
                        return res.text().then(text => {
                            throw new Error(`HTTP ${res.status}: ${text.substring(0, 200)}`);
                        });
                    }
                    return res.json();
                })
                .then(data => {
                    removeLoading(loadingId)
                    console.log("[API Response]", data)

                    latestGroupingPayload = data.grouping_payload || null
                    latestGroupingMeta = data.grouping_meta || null
                    latestPembimbingPayload = data.pembimbing_payload || null
                    latestPembimbingMeta = data.pembimbing_meta || null
                    latestPengujiPayload = data.penguji_payload || null
                    latestPengujiMeta = data.penguji_meta || null
                    latestExcelFilename = data.excel_filename || null
                    latestJadwalMeta = data.jadwal_meta || null
                    latestJadwalEntries = data.jadwal_entries || null

                    let displayText = data.result || "Tidak ada respons"
                    appendMessage("ai", displayText)

                    if (latestGroupingPayload && Array.isArray(latestGroupingPayload.groups) &&
                        latestGroupingPayload.groups.length > 0) appendGroupingActions();
                    if (latestPembimbingPayload && Array.isArray(latestPembimbingPayload.groups) &&
                        latestPembimbingPayload.groups.length > 0) appendPembimbingActions();
                    if (latestPengujiPayload && Array.isArray(latestPengujiPayload.groups) && latestPengujiPayload
                        .groups.length > 0) appendPengujiActions();
                    if (latestExcelFilename) appendExcelDownloadButton();

                    scrollToBottom()

                    if (data.jadwal_stage === 'completed' && data.jadwal_entries) {
                        if (typeof updateSidebarStatus === 'function') updateSidebarStatus('jadwal', 'success');
                        setTimeout(() => {
                            Swal.fire({
                                title: '✅ Jadwal Seminar Berhasil Dibuat!',
                                html: `<strong>${data.jadwal_entries.length}</strong> jadwal seminar telah berhasil disimpan ke database.<br><br><small style="color:#6b7280;">Jadwal dapat diverifikasi melalui halaman Data Jadwal Seminar.</small>`,
                                icon: 'success',
                                confirmButtonText: 'Lanjut',
                                confirmButtonColor: '#10b981'
                            });
                        }, 500);
                    } else if (data.jadwal_stage === 'input_form') {
                        console.log('[JADWAL] Form input displayed');
                    } else if (data.jadwal_stage === 'preview' && data.jadwal_entries) {
                        if (typeof updateSidebarStatus === 'function') updateSidebarStatus('jadwal', 'preview');
                        appendJadwalPreviewActions();
                        setTimeout(() => {
                            Swal.fire({
                                title: '🔎 Preview Jadwal Seminar (Belum Disimpan)',
                                html: data.result ||
                                    'Preview jadwal dibuat. Periksa sebelum menyimpan.',
                                icon: 'info',
                                showCancelButton: false,
                                showConfirmButton: true,
                                confirmButtonText: '✕ Tutup',
                                confirmButtonColor: '#6b7280',
                                width: 900
                            });
                        }, 300);
                    } else if (message.includes('[jadwal]') && message.includes('durasi')) {
                        if (displayText.includes('❌') || displayText.includes('Error')) {
                            if (typeof updateSidebarStatus === 'function') updateSidebarStatus('jadwal', 'warning');
                            setTimeout(() => {
                                Swal.fire({
                                    title: '⚠️  Gagal Membuat Jadwal',
                                    html: displayText ||
                                        'Terjadi kesalahan saat membuat jadwal. Silakan periksa input Anda.',
                                    icon: 'error',
                                    confirmButtonText: 'Coba Lagi',
                                    confirmButtonColor: '#ef4444'
                                });
                            }, 500);
                        }
                    }
                })
                .catch(err => {
                    removeLoading(loadingId)
                    const errorMsg = err.message.includes('JSON') ? "Server error. Silakan cek console." : err
                        .message;
                    appendMessage("ai", "❌ Terjadi Error: " + errorMsg)
                    console.error("[Error]", err)
                })
                .finally(() => {
                    if (message.toLowerCase().startsWith('[jadwal]')) restoreJadwalSubmitButton();
                });
        };

        // ============== END CHATBOT UI ==============

        document.addEventListener("DOMContentLoaded", function() {
            console.log("[chatbot] DOMContentLoaded")
            bindChatActionDelegation()

            if (typeof window.initializeAgent === 'function') window.initializeAgent();
            if (typeof loadVerificationStatusFromDatabase === 'function') loadVerificationStatusFromDatabase();

            const btnHelper = document.getElementById("btnHelper")
            if (btnHelper) {
                btnHelper.addEventListener("click", function() {
                    const panduanOverlay = document.getElementById("panduanOverlay")
                    if (panduanOverlay) panduanOverlay.classList.add("active")
                })
            }

            const closePandauHeader = document.getElementById("closePandauHeader")
            if (closePandauHeader) {
                closePandauHeader.addEventListener("click", function() {
                    const panduanOverlay = document.getElementById("panduanOverlay")
                    if (panduanOverlay) panduanOverlay.classList.remove("active")
                })
            }

            const closePanduan = document.getElementById("closePanduan")
            if (closePanduan) {
                closePanduan.addEventListener("click", function() {
                    const panduanOverlay = document.getElementById("panduanOverlay")
                    if (panduanOverlay) panduanOverlay.classList.remove("active")
                })
            }

            const panduanOverlay = document.getElementById("panduanOverlay")
            if (panduanOverlay) {
                panduanOverlay.addEventListener("click", function(e) {
                    if (e.target === panduanOverlay) panduanOverlay.classList.remove("active")
                })
            }
        })

        // ================== LANDING & CHIP INTERACTION ==================
        const landingInput = document.getElementById("landingInput");
        const landingSend = document.getElementById("landingSend");
        const chips = document.querySelectorAll(".chip");
        const btnNewChat = document.getElementById("btnNewChat");
        const landingView = document.getElementById("landingView");
        const chatView = document.getElementById("chatView");

        function openChatWithMessage(message) {
            if (!message) return;
            if (landingView) landingView.style.display = "none";
            if (chatView) chatView.style.display = "flex";
            const input = document.getElementById("userInput");
            if (input) input.value = message;
            if (typeof window.sendMessage === "function") window.sendMessage();
        }

        chips.forEach(chip => {
            chip.addEventListener("click", function() {
                openChatWithMessage(this.getAttribute("data-instruction"));
            });
        });

        if (landingSend) {
            landingSend.addEventListener("click", function() {
                openChatWithMessage(landingInput.value.trim());
            });
        }

        if (landingInput) {
            landingInput.addEventListener("input", function() {
                autoResizeTextarea(landingInput, 22);
            });
            autoResizeTextarea(landingInput, 22);
            landingInput.addEventListener("keydown", function(e) {
                if (e.key === "Enter" && !e.shiftKey) {
                    e.preventDefault();
                    openChatWithMessage(landingInput.value.trim());
                }
            });
        }

        // ================== GROUPING FORM SUBMIT LISTENER ==================
        window.addEventListener('grouping-form-submit', function(e) {
            var detail = e.detail || {};

            // Gunakan prompt yang sudah dibangun oleh form Python
            var prompt = detail.prompt || '';

            // Fallback manual jika prompt kosong
            if (!prompt) {
                var methodLabel = {
                    'auto': 'Acak Otomatis',
                    'by_grades': 'Berdasarkan Nilai',
                    'by_nim': 'Berdasarkan NIM'
                } [detail.method] || 'Acak Otomatis';

                prompt = 'Buatkan kelompok dengan spesifikasi berikut:\n';
                prompt += '- Metode: ' + methodLabel + '\n';

                if (detail.method === 'by_nim' && detail.nim_start && detail.nim_end) {
                    prompt += '- Dari NIM ' + detail.nim_start + ' sampai NIM ' + detail.nim_end + '\n';
                }
                if (detail.size_mode === 'exact') {
                    prompt += '- Ukuran: ' + (detail.exact_size || 5) + ' orang per kelompok\n';
                } else if (detail.size_mode === 'range') {
                    prompt += '- Ukuran: minimal ' + (detail.min_size || 4) + ' orang, maksimal ' + (detail
                        .max_size || 6) + ' orang per kelompok\n';
                }
                if (detail.constraints) {
                    prompt += '- Constraint: ' + detail.constraints.replace(/\n/g, ' | ') + '\n';
                }
            }

            if (!prompt.trim()) {
                console.warn('[grouping-form] Prompt kosong, diabaikan.');
                return;
            }

            console.log('[grouping-form] Received submit event, prompt:', prompt);

            switchToChat();

            var input = document.getElementById('userInput');
            if (input) input.value = prompt.trim();
            if (typeof window.sendMessage === 'function') window.sendMessage();
            else console.error('[grouping-form] window.sendMessage tidak ditemukan!');
        });

        // ================== JADWAL SEMINAR HANDLERS ==================
        window.__lastJadwalMessage = null;

        window.__saveJadwalDb = function(event) {
            event?.preventDefault?.();
            const timestamp = new Date().toLocaleTimeString();

            try {
                let formContainer = event?.target?.closest('.chat-message-wrapper, .msg-container, .chat-message, div');
                if (!formContainer) {
                    const formActions = document.getElementById('jadwal-form-actions');
                    if (formActions) {
                        formContainer = formActions.closest(
                                '.chat-message-wrapper, .msg-container, .chat-message, [data-msg]') ||
                            formActions.parentElement?.parentElement?.parentElement ||
                            formActions.parentElement?.parentElement;
                    }
                }
                if (!formContainer) formContainer = document;

                const userInput = document.getElementById("userInput");
                if (!userInput) {
                    console.error(`[${timestamp}] [JADWAL-SAVE] userInput not found`);
                    return;
                }

                let message = window.__lastJadwalMessage;

                if (!message) {
                    let tanggalInput = document.getElementById("jadwal-tanggal");
                    let durasiJamInput = document.getElementById("jadwal-durasi-jam");
                    let durasiMenitInput = document.getElementById("jadwal-durasi-menit");

                    const tanggalRaw = tanggalInput?.value?.trim() || "";
                    let tanggal = convertDatePickerToIndonesian(tanggalRaw);
                    if (!tanggal && tanggalRaw) tanggal = tanggalRaw;

                    const jam = parseInt(durasiJamInput?.value || "1");
                    const menit = parseInt(durasiMenitInput?.value || "50");

                    const ruanganSelects = document.querySelectorAll(".jadwal-ruangan-select");
                    const ruanganList = [];
                    ruanganSelects.forEach((select) => {
                        if (select.value) ruanganList.push(select.value);
                    });

                    const totalMenit = (jam * 60) + menit;

                    if (!tanggalRaw || ruanganList.length === 0) {
                        Swal.fire({
                            title: 'Validasi Form',
                            text: 'Tanggal dan minimal 1 ruangan harus dipilih. Mohon coba lagi.',
                            icon: 'warning',
                            confirmButtonText: 'OK'
                        });
                        return;
                    }

                    message =
                    `[jadwal] tanggal: ${tanggal} | ruangan: ${ruanganList.join(",")} | durasi: ${totalMenit}`;
                }

                let orderStr = "";
                if (latestJadwalEntries && latestJadwalEntries.length > 0) {
                    orderStr = " | order: " + latestJadwalEntries.map(e => e.kelompok_id).join(",");
                }

                let baseMsg = message;
                if (latestJadwalMeta && (!baseMsg.includes("tanggal") || !baseMsg.includes("ruangan"))) {
                    const m = latestJadwalMeta;
                    baseMsg =
                        `[jadwal] tanggal: ${m.tanggal || ""} | ruangan: ${(m.ruangan_list || []).join(",")} | durasi: ${m.durasi_menit || 110}`;
                }

                const finalMessage = baseMsg.replace(/action[:\s]*\w+/gi, "").trim() + " | action: save" + orderStr;
                userInput.value = finalMessage;

                const btn = event?.target?.closest('.save-jadwal-btn');
                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML = 'Menyimpan...';
                }

                Swal.fire({
                    title: 'Menyimpan Jadwal...',
                    text: 'Harap tunggu, jadwal sedang disimpan ke database.',
                    icon: 'info',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => Swal.showLoading()
                });

                window.__jadwalSaveInProgress = true;
                window.sendMessage();
            } catch (error) {
                Swal.fire({
                    title: 'Error',
                    text: `Terjadi error saat menyimpan: ${error.message}`,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        };

        window.__reshuffleJadwal = function(event) {
            event?.preventDefault?.();
            const timestamp = new Date().toLocaleTimeString();

            try {
                const userInput = document.getElementById("userInput");
                if (!userInput) {
                    console.error(`[${timestamp}] [JADWAL-RESHUFFLE] userInput not found`);
                    return;
                }

                let baseMessage = window.__lastJadwalMessage;
                if (latestJadwalMeta) {
                    const m = latestJadwalMeta;
                    baseMessage =
                        `[jadwal] tanggal: ${m.tanggal || ""} | ruangan: ${(m.ruangan_list || []).join(",")} | durasi: ${m.durasi_menit || 110}`;
                }

                if (!baseMessage) {
                    Swal.fire({
                        title: 'Tidak Ada Data Preview',
                        text: 'Silakan generate jadwal terlebih dahulu sebelum mengacak ulang.',
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    });
                    return;
                }

                const finalMessage = baseMessage.replace(/\|\s*action[:\s]*\w+/gi, "").trim() + " | action: shuffle";
                userInput.value = finalMessage;

                const btn = event?.target?.closest('.reshuffle-jadwal-btn');
                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML = '⏳ Mengacak...';
                }

                window.sendMessage();
            } catch (error) {
                Swal.fire({
                    title: 'Error',
                    text: `Terjadi error saat mengacak: ${error.message}`,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        };

        // ================== BUTTON SESI BARU ==================
        if (btnNewChat) {
            btnNewChat.style.display = "inline-flex";
            btnNewChat.addEventListener("click", function() {
                Swal.fire({
                    title: "Mulai Sesi Baru?",
                    text: "Chat akan dikosongkan.",
                    icon: "question",
                    showCancelButton: true,
                    confirmButtonText: "Ya, mulai baru",
                    cancelButtonText: "Batal"
                }).then((result) => {
                    if (result.isConfirmed) {
                        const chatBox = document.getElementById("chatBox");
                        if (chatBox) chatBox.innerHTML = "";

                        latestGroupingPayload = null;
                        latestPembimbingPayload = null;
                        latestPengujiPayload = null;
                        latestExcelFilename = null;

                        if (landingView) landingView.style.display = "flex";
                        if (chatView) chatView.style.display = "none";

                        if (landingInput) {
                            landingInput.value = "";
                            autoResizeTextarea(landingInput, 22);
                            landingInput.focus();
                        }
                    }
                });
            });
        }

        // ================== OBSERVER UNTUK JADWAL SAVE RESPONSE ==================
        const chatBoxForObserver = document.getElementById('chatBox');
        if (chatBoxForObserver) {
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                        mutation.addedNodes.forEach(function(node) {
                            if (node.nodeType === 1) {
                                const messageText = node.textContent || '';
                                if (window.__jadwalSaveInProgress) {
                                    if (messageText.includes('Berhasil Disimpan') || messageText
                                        .includes('jadwal berhasil')) {
                                        window.__jadwalSaveInProgress = false;
                                        Swal.close();
                                        const match = messageText.match(/(\d+)\s+Ruangan/);
                                        Swal.fire({
                                            title: 'Jadwal Berhasil Disimpan!',
                                            text: `Jadwal seminar untuk ${match ? match[1] : 'jadwal'} ruangan telah berhasil disimpan ke database.`,
                                            icon: 'success',
                                            confirmButtonText: 'OK',
                                            confirmButtonColor: '#10b981'
                                        });
                                    } else if (messageText.includes('Gagal') || messageText
                                        .includes('Error') || messageText.includes('error')) {
                                        window.__jadwalSaveInProgress = false;
                                        Swal.close();
                                        Swal.fire({
                                            title: 'Gagal Menyimpan Jadwal',
                                            text: messageText.substring(0, 200),
                                            icon: 'error',
                                            confirmButtonText: 'OK'
                                        });
                                    }
                                }
                            }
                        });
                    }
                });
            });
            observer.observe(chatBoxForObserver, {
                childList: true,
                subtree: true
            });
        }
    </script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.main', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\Semester 6\PA III\Project\Proyek-Kelompok-07-PA-3\ui laravel\resources\views/pages/Koordinator/agent/agent-kelompok.blade.php ENDPATH**/ ?>