@extends('layouts.main')
@section('title', 'AI Agent Chatbot')



@section('content')
    <link rel="stylesheet" href="{{ asset('assets/css/agent-kelompok.css') }}">

    <section class="chatbox-container" data-ai-route="{{ route('ai.callAgent') }}"
        data-save-route="{{ route('ai.saveGroups') }}" data-save-pembimbing-route="{{ route('ai.savePembimbing') }}"
        data-check-pembimbing-route="{{ route('ai.cekPembimbing') }}" data-save-penguji-route="{{ route('ai.savePenguji') }}"
        data-check-penguji-route="{{ route('ai.cekPenguji') }}" data-check-route="{{ route('ai.cekKelompok') }}"
        data-delete-route="{{ route('ai.deleteForContext') }}"
        data-kategori-pa-id="{{ request()->query('kategori_pa_id', 1) }}"
        data-prodi-id="{{ request()->query('prodi_id', 1) }}"
        data-tahun-masuk-id="{{ request()->query('tahun_masuk_id', 1) }}">

        <div class="agent-page" id="vtWrap">

            {{-- TOP BAR --}}
            <header class="topbar">
                <div class="topbar-brand">
                    <div class="topbar-icon">
                        <img src="{{ asset('assets/img/logoagent1.jpeg') }}" alt="VokasiTera Agent Logo"
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

            {{-- VERIFICATION SIDEBAR --}}
            <aside class="verification-sidebar">
                <div class="sidebar-header">
                    <h3>Alur Verifikasi</h3>
                </div>

                <div class="verification-steps">
                    {{-- Step 1: Pembagian Kelompok --}}
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

                    {{-- Step 2: Verifikasi Pembimbing --}}
                    <div class="step-item" data-step="pembimbing">
                        <div class="step-line"></div>
                        <div class="step-content">
                            <div class="step-indicator">
                                <div class="step-number">2</div>
                            </div>
                            <div class="step-info">
                                <div class="step-title">Verifikasi Pembimbing</div>
                                <div class="step-subtitle">Tentukan dosen pembimbing</div>
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

                    {{-- Step 3: Verifikasi Penguji --}}
                    <div class="step-item" data-step="penguji">
                        <div class="step-line"></div>
                        <div class="step-content">
                            <div class="step-indicator">
                                <div class="step-number">3</div>
                            </div>
                            <div class="step-info">
                                <div class="step-title">Verifikasi Penguji</div>
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

                    {{-- Step 4: Jadwal Seminar --}}
                    <div class="step-item last" data-step="jadwal">
                        <div class="step-line"></div>
                        <div class="step-content">
                            <div class="step-indicator">
                                <div class="step-number">4</div>
                            </div>
                            <div class="step-info">
                                <div class="step-title">Jadwal Seminar</div>
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

            {{-- LANDING VIEW --}}
            <div class="landing-view" id="landingView">
                <div class="landing-icon-wrap">
                    <img src="{{ asset('assets/img/logoagent1.jpeg') }}" alt="VokasiTera Agent Logo"
                        style="width: 100px; height: 100px; object-fit: contain;">
                </div>

                @php
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
                @endphp

                <h1 class="landing-title">{{ $greeting }}, {{ $userName }}! 👋</h1>
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
                    <button class="chip" data-instruction="Buat 5 kelompok mahasiswa">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="11"
                            height="11">
                            <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" />
                            <circle cx="9" cy="7" r="4" />
                            <path d="M23 21v-2a4 4 0 00-3-3.87" />
                            <path d="M16 3.13a4 4 0 010 7.75" />
                        </svg>
                        Buat kelompok
                    </button>

                    <button class="chip" data-instruction="Bagi mahasiswa menjadi 6 orang per kelompok">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="11"
                            height="11">
                            <rect x="3" y="3" width="7" height="7" rx="1" />
                            <rect x="14" y="3" width="7" height="7" rx="1" />
                            <rect x="3" y="14" width="7" height="7" rx="1" />
                            <rect x="14" y="14" width="7" height="7" rx="1" />
                        </svg>
                        6 orang/kelompok
                    </button>

                    <button class="chip" data-instruction="Tentukan dosen pembimbing untuk setiap kelompok">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="11"
                            height="11">
                            <path d="M20 7H4a2 2 0 00-2 2v6a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2z" />
                            <path d="M16 21V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v16" />
                        </svg>
                        Set pembimbing
                    </button>

                    <button class="chip" data-instruction="Tentukan dosen penguji untuk setiap kelompok">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="11"
                            height="11">
                            <path d="M9 11l3 3L22 4" />
                            <path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11" />
                        </svg>
                        Set penguji
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

            {{-- CHAT VIEW --}}
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

            {{-- PANDUAN MODAL --}}
            <div class="panduan-overlay" id="panduanOverlay">
                <div class="panduan-modal">
                    <h3>Panduan Penggunaan</h3>

                    <div class="panduan-item">
                        <strong>Pembagian kelompok</strong>
                        <span>"Buat 5 kelompok" atau "6 orang per kelompok"</span>
                    </div>

                    <div class="panduan-item">
                        <strong>Dosen pembimbing</strong>
                        <span>"Tentukan dosen pembimbing untuk setiap kelompok"</span>
                    </div>

                    <div class="panduan-item">
                        <strong>Dosen penguji</strong>
                        <span>"Tentukan dosen penguji untuk setiap kelompok"</span>
                    </div>

                    <div class="panduan-item">
                        <strong>Gabungan otomatis</strong>
                        <span>"Bagi kelompok sekaligus tentukan pembimbing dan penguji"</span>
                    </div>

                    <button class="btn-close-modal" id="closePanduan">Tutup</button>
                </div>
            </div>

        </div>


    </section>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
        let latestUserPrompt = "";
        let isSavingGeneratedGroups = false;
        let isSavingGeneratedPembimbing = false;
        let isSavingGeneratedPenguji = false;
        let isDeletingForContext = false;
        let isLoadingPembimbingCheck = false;
        let isLoadingPengujiCheck = false;

        // ============== VERIFICATION SIDEBAR FUNCTIONS ==============

        /**
         * Load verification status dari database saat page load
         */
        async function loadVerificationStatusFromDatabase() {
            try {
                const response = await fetch('/ai-agent/verification-status');
                if (!response.ok) throw new Error('Failed to fetch verification status');

                const data = await response.json();
                if (data.success && data.data) {
                    console.log('[sidebar] Loaded verification status from database:', data.data);

                    // Update setiap step dengan status dari database
                    updateSidebarStatus('kelompok', data.data.kelompok);
                    updateSidebarStatus('pembimbing', data.data.pembimbing);
                    updateSidebarStatus('penguji', data.data.penguji);
                    updateSidebarStatus('jadwal', data.data.jadwal);
                }
            } catch (error) {
                console.warn('[sidebar] Error loading verification status:', error.message);
            }
        }

        /**
         * Update status sidebar untuk tahapan verifikasi
         * @param {string} step - 'kelompok' | 'pembimbing' | 'penguji' | 'jadwal'
         * @param {string} status - 'pending' | 'warning' | 'success'
         */
        function updateSidebarStatus(step, status) {
            const stepElement = document.querySelector(`.step-item[data-step="${step}"]`);
            if (!stepElement) return;

            const stepStatus = stepElement.querySelector('.step-status');
            const pendingIcon = stepElement.querySelector('.pending-icon');
            const successIcon = stepElement.querySelector('.success-icon');

            // Update data-status attribute
            stepElement.setAttribute('data-status', status);
            stepStatus.setAttribute('data-status', status);

            // Update icons based on status
            if (status === 'success' && successIcon && pendingIcon) {
                pendingIcon.style.display = 'none';
                successIcon.style.display = 'block';
            } else if (status === 'warning' || status === 'pending') {
                if (pendingIcon) pendingIcon.style.display = 'block';
                if (successIcon) successIcon.style.display = 'none';
            }

            console.log(`[sidebar] Updated ${step} status to ${status}`);
        }

        /**
         * Get current status dari sidebar
         * @param {string} step
         * @returns {string} current status
         */
        function getSidebarStatus(step) {
            const stepElement = document.querySelector(`.step-item[data-step="${step}"]`);
            if (!stepElement) return 'pending';
            return stepElement.getAttribute('data-status') || 'pending';
        }

        /**
         * Check status kelompok dan update sidebar
         * Return true jika semua mahasiswa sudah dapat kelompok
         */
        function checkKelompokStatus() {
            // Implementasi bisa melalui API atau check dari data
            // Untuk sekarang, return true jika grouping tersimpan tanpa error
            if (latestGroupingPayload && !isSavingGeneratedGroups) {
                updateSidebarStatus('kelompok', 'success');
                return true;
            }
            return false;
        }

        /**
         * Check status pembimbing dan update sidebar
         */
        function checkPembimbingStatus() {
            if (latestPembimbingPayload && !isSavingGeneratedPembimbing) {
                updateSidebarStatus('pembimbing', 'success');
                return true;
            }
            return false;
        }

        /**
         * Check status penguji dan update sidebar
         */
        function checkPengujiStatus() {
            if (latestPengujiPayload && !isSavingGeneratedPenguji) {
                updateSidebarStatus('penguji', 'success');
                return true;
            }
            return false;
        }

        /**
         * Update sidebar status ketika ada warning/issue
         * Misalnya ada mahasiswa yang belum dapat kelompok
         */
        function setSidebarWarning(step, hasWarning = true) {
            if (hasWarning) {
                updateSidebarStatus(step, 'warning');
            }
        }

        // Scroll to bottom smoothly
        function scrollToBottom() {
            const chatBox = document.getElementById("chatBox");
            if (chatBox) {
                setTimeout(() => {
                    chatBox.scrollTop = chatBox.scrollHeight;
                }, 10);
            }
        }

        const AI_ICON = `<svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="15" height="15">
    <rect x="4" y="7" width="16" height="12" rx="3" />
    <circle cx="9" cy="13" r="1" />
    <circle cx="15" cy="13" r="1" />
    <path d="M9 17h6" />
    <path d="M12 3v4" />
    <circle cx="12" cy="2" r="1" />
</svg>`;

        const USER_ICON = `<svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="15" height="15">
    <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/>
    <circle cx="12" cy="7" r="4"/>
</svg>`;

        // Append message with proper bubble styling
        // ===== MESSAGE RENDER (FIXED CSS MATCH) =====
        function appendMessage(sender, text) {
            const chatBox = document.getElementById("chatBox");

            const row = document.createElement("div");
            row.className = `msg-row ${sender === "user" ? "user-row" : ""}`;

            // Avatar
            const avatar = document.createElement("div");
            avatar.className = `msg-av ${sender === "user" ? "user-av" : "ai-av"}`;
            avatar.innerHTML = sender === "user" ? USER_ICON : AI_ICON;

            // Body
            const body = document.createElement("div");
            body.className = "msg-body";

            // Meta
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

            // Bubble
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
            <button class="btn btn-sm btn-primary save-generated-groups-btn">
                💾 Simpan ke Database
            </button>
        </div>
    `;

            body.appendChild(bubble);
            row.appendChild(avatar);
            row.appendChild(body);

            chatBox.appendChild(row);
            scrollToBottom();
        }

        function appendPembimbingActions() {
            const chatBox = document.getElementById("chatBox");
            if (!chatBox || !latestPembimbingPayload || !Array.isArray(latestPembimbingPayload.groups) ||
                latestPembimbingPayload.groups.length === 0) {
                return;
            }

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
        </div>
    `;

            wrapper.appendChild(bubble);
            chatBox.appendChild(wrapper);

            const saveButton = wrapper.querySelector('.save-generated-pembimbing-btn');
            if (saveButton) {
                saveButton.onclick = async function(event) {
                    event.preventDefault();
                    event.stopPropagation();
                    if (typeof window.__savePembimbingInline === 'function') {
                        await window.__savePembimbingInline(event);
                    }
                };
            }

            const regenerateButton = wrapper.querySelector('.regenerate-pembimbing-btn');
            if (regenerateButton) {
                regenerateButton.onclick = async function(event) {
                    event.preventDefault();
                    event.stopPropagation();
                    if (typeof window.__regeneratePembimbingInline === 'function') {
                        await window.__regeneratePembimbingInline(event);
                    }
                };
            }

            scrollToBottom();
        }

        function appendPengujiActions() {
            const chatBox = document.getElementById("chatBox");
            if (!chatBox || !latestPengujiPayload || !Array.isArray(latestPengujiPayload.groups) || latestPengujiPayload
                .groups.length === 0) {
                return;
            }

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
        </div>
    `;

            wrapper.appendChild(bubble);
            chatBox.appendChild(wrapper);

            const saveButton = wrapper.querySelector('.save-generated-penguji-btn');
            if (saveButton) {
                saveButton.onclick = async function(event) {
                    event.preventDefault();
                    event.stopPropagation();
                    if (typeof window.__savePengujiInline === 'function') {
                        await window.__savePengujiInline(event);
                    }
                };
            }

            const regenerateButton = wrapper.querySelector('.regenerate-penguji-btn');
            if (regenerateButton) {
                regenerateButton.onclick = async function(event) {
                    event.preventDefault();
                    event.stopPropagation();
                    if (typeof window.__regeneratePengujiInline === 'function') {
                        await window.__regeneratePengujiInline(event);
                    }
                };
            }

            scrollToBottom();
        }

        function appendExcelDownloadButton() {
            const chatBox = document.getElementById("chatBox");
            if (!chatBox || !latestExcelFilename) {
                return;
            }

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
        </div>
    `;

            wrapper.appendChild(bubble);
            chatBox.appendChild(wrapper);
            scrollToBottom();
        }

        function downloadExcel(filename) {
            const section = document.querySelector('[data-ai-route]');
            const downloadRoute = '{{ route('ai.downloadExcel') }}';

            // Create form for download
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
            if (isSavingGeneratedGroups) {
                return;
            }

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
                if (!response.ok || !data.success) {
                    throw new Error(data.message || "Gagal menyimpan kelompok.");
                }

                // Update sidebar status
                updateSidebarStatus('kelompok', 'success');

                Swal.fire({
                    title: "Berhasil",
                    html: `
                Kelompok berhasil disimpan.<br>
                <strong>${data.saved_kelompok || 0}</strong> kelompok dan
                <strong>${data.saved_members || 0}</strong> anggota tersimpan.
                ${(data.skipped_existing_members || 0) > 0 ? `<br><small>${data.skipped_existing_members} mahasiswa dilewati karena sudah punya kelompok.</small>` : ''}
            `,
                    icon: "success"
                });

                // Hindari submit ganda pada payload yang sama.
                latestGroupingPayload = null;
                latestGroupingMeta = null;

                const saveButtons = document.querySelectorAll(".save-generated-groups-btn");
                saveButtons.forEach((btn) => {
                    btn.disabled = true;
                    btn.classList.add("disabled");
                    btn.innerHTML = '<i class="fas fa-check"></i> Sudah Disimpan';
                });
            } catch (error) {
                // Update sidebar status to warning
                updateSidebarStatus('kelompok', 'warning');
                Swal.fire("Error", error.message || "Terjadi kesalahan saat menyimpan.", "error");
            } finally {
                isSavingGeneratedGroups = false;
            }
        }

        function switchToChat() {
            document.getElementById("landingView").style.display = "none";
            document.getElementById("chatView").style.display = "flex";
            document.getElementById("btnNewChat").style.display = "flex";
        }

        async function saveGeneratedPembimbing() {
            if (isSavingGeneratedPembimbing) {
                return;
            }

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

                    if (!confirm.isConfirmed) {
                        return;
                    }

                    response = await submitSave(true);
                    data = await response.json();
                }

                if (!response.ok || !data.success) {
                    throw new Error(data.message || "Gagal menyimpan pembimbing.");
                }

                // Update sidebar status
                updateSidebarStatus('pembimbing', 'success');

                Swal.fire({
                    title: "Berhasil",
                    html: `
                Pembimbing berhasil disimpan.<br>
                <strong>${data.saved_assignments || 0}</strong> assignment tersimpan.
            `,
                    icon: "success"
                });

                latestPembimbingPayload = null;
                latestPembimbingMeta = null;

                const saveButtons = document.querySelectorAll(".save-generated-pembimbing-btn");
                saveButtons.forEach((btn) => {
                    btn.disabled = true;
                    btn.classList.add("disabled");
                    btn.innerHTML = '<i class="fas fa-check"></i> Pembimbing Sudah Disimpan';
                });
            } catch (error) {
                // Update sidebar status to warning
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
            if (isSavingGeneratedPenguji) {
                return;
            }

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

                    if (!confirm.isConfirmed) {
                        return;
                    }

                    response = await submitSave(true);
                    data = await response.json();
                }

                if (!response.ok || !data.success) {
                    throw new Error(data.message || "Gagal menyimpan penguji.");
                }

                // Update sidebar status
                updateSidebarStatus('penguji', 'success');

                Swal.fire({
                    title: "Berhasil",
                    html: `
                Penguji berhasil disimpan.<br>
                <strong>${data.saved_assignments || 0}</strong> assignment tersimpan.
            `,
                    icon: "success"
                });

                latestPengujiPayload = null;
                latestPengujiMeta = null;

                const saveButtons = document.querySelectorAll(".save-generated-penguji-btn");
                saveButtons.forEach((btn) => {
                    btn.disabled = true;
                    btn.classList.add("disabled");
                    btn.innerHTML = '<i class="fas fa-check"></i> Penguji Sudah Disimpan';
                });
            } catch (error) {
                // Update sidebar status to warning
                updateSidebarStatus('penguji', 'warning');
                Swal.fire("Error", error.message || "Terjadi kesalahan saat menyimpan penguji.", "error");
            } finally {
                isSavingGeneratedPenguji = false;
            }
        }

        window.__savePembimbingInline = async function(event) {
            if (event && typeof event.preventDefault === "function") {
                event.preventDefault();
            }
            if (event && typeof event.stopPropagation === "function") {
                event.stopPropagation();
            }
            if (event && typeof event.stopImmediatePropagation === "function") {
                event.stopImmediatePropagation();
            }
            await saveGeneratedPembimbing();
        };

        window.__regeneratePembimbingInline = async function(event) {
            if (event && typeof event.preventDefault === "function") {
                event.preventDefault();
            }
            if (event && typeof event.stopPropagation === "function") {
                event.stopPropagation();
            }
            if (event && typeof event.stopImmediatePropagation === "function") {
                event.stopImmediatePropagation();
            }

            if (isLoadingPembimbingCheck) {
                return;
            }

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

                    if (!confirm.isConfirmed) {
                        return;
                    }
                }

                const input = document.getElementById("userInput");
                const regeneratePrompt = latestPembimbingMeta?.prompt || latestUserPrompt ||
                    "generate pembimbing kelompok";
                if (input) {
                    input.value = regeneratePrompt;
                }
                if (typeof window.sendMessage === 'function') {
                    window.sendMessage();
                }
            } catch (error) {
                Swal.fire("Error", error.message || "Gagal validasi pembimbing.", "error");
            } finally {
                isLoadingPembimbingCheck = false;
            }
        };

        window.__savePengujiInline = async function(event) {
            if (event && typeof event.preventDefault === "function") {
                event.preventDefault();
            }
            if (event && typeof event.stopPropagation === "function") {
                event.stopPropagation();
            }
            if (event && typeof event.stopImmediatePropagation === "function") {
                event.stopImmediatePropagation();
            }
            await saveGeneratedPenguji();
        };

        window.__regeneratePengujiInline = async function(event) {
            if (event && typeof event.preventDefault === "function") {
                event.preventDefault();
            }
            if (event && typeof event.stopPropagation === "function") {
                event.stopPropagation();
            }
            if (event && typeof event.stopImmediatePropagation === "function") {
                event.stopImmediatePropagation();
            }

            if (isLoadingPengujiCheck) {
                return;
            }

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

                    if (!confirm.isConfirmed) {
                        return;
                    }
                }

                const input = document.getElementById("userInput");
                const regeneratePrompt = latestPengujiMeta?.prompt || latestUserPrompt ||
                    "generate penguji kelompok";
                if (input) {
                    input.value = regeneratePrompt;
                }
                if (typeof window.sendMessage === 'function') {
                    window.sendMessage();
                }
            } catch (error) {
                Swal.fire("Error", error.message || "Gagal validasi penguji.", "error");
            } finally {
                isLoadingPengujiCheck = false;
            }
        };

        // Show loading animation with dots
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
    <div class="skeleton-line" style="width:90px;"></div>
`;

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
                    const event = new KeyboardEvent('keydown', {
                        key: 'Enter',
                        code: 'Enter',
                        keyCode: 13,
                        which: 13,
                        bubbles: true
                    });
                    input.dispatchEvent(event);
                }
            }

            function handleConstraintClick() {
                const input = document.getElementById("userInput");
                const instruction = this.getAttribute('data-instruction');
                if (instruction && confirm(`Terapkan: ${instruction}?`)) {
                    input.value = instruction;
                    const event = new KeyboardEvent('keydown', {
                        key: 'Enter',
                        code: 'Enter',
                        keyCode: 13,
                        which: 13,
                        bubbles: true
                    });
                    input.dispatchEvent(event);
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
            const chatBox = document.getElementById("chatBox");
            if (!chatBox || chatBox.dataset.actionsBound === "1") {
                return;
            }

            async function executeDeleteForContext(recreatePrompt) {
                if (isDeletingForContext) {
                    return;
                }

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

                    console.log("[chatbot] delete response status", deleteResponse.status);

                    if (!deleteResponse.ok) {
                        const txt = await deleteResponse.text();
                        console.error("[chatbot] delete response body", txt);
                        throw new Error(`Gagal hapus kelompok: ${txt.substring(0, 200)}`);
                    }

                    const deleteData = await deleteResponse.json();
                    console.log("[chatbot] delete response json", deleteData);

                    if (!deleteData.success) {
                        throw new Error(deleteData.message || "Gagal menghapus kelompok.");
                    }

                    console.log("[chatbot] Groups deleted successfully. Recreate prompt:", recreatePrompt);

                    // If recreate prompt is provided, auto-trigger group creation
                    if (recreatePrompt && recreatePrompt.trim().length > 0) {
                        console.log("[chatbot] Auto-triggering group creation with prompt:", recreatePrompt);

                        // Show loading with a message
                        Swal.fire({
                            title: "Kelompok Dihapus",
                            html: `<strong>${deleteData.deleted_kelompok || 0}</strong> kelompok dan <strong>${deleteData.deleted_members || 0}</strong> anggota telah dihapus.<br><b>Membuat kelompok baru...</b>`,
                            icon: "info",
                            allowOutsideClick: false,
                            didOpen: () => Swal.showLoading()
                        });

                        // Wait a moment then trigger the message send
                        setTimeout(() => {
                            const input = document.getElementById("userInput");
                            if (input) {
                                input.value = recreatePrompt;
                                // Trigger send message
                                if (typeof window.sendMessage === 'function') {
                                    window.sendMessage();
                                }
                            }
                            Swal.close();
                        }, 500);
                    } else {
                        // No recreate prompt, show normal completion message
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

            // Fallback inline handler for dynamic HTML rendered from AI response
            window.__confirmRecreateGroupsFromInline = function(event) {
                if (event && typeof event.preventDefault === "function") {
                    event.preventDefault();
                }
                if (event && typeof event.stopPropagation === "function") {
                    event.stopPropagation();
                }
                if (event && typeof event.stopImmediatePropagation === "function") {
                    event.stopImmediatePropagation();
                }
                // Extract recreate prompt from button's data attribute
                const button = event?.target?.closest?.('.confirm-recreate-groups');
                const recreatePrompt = button?.dataset?.recreatePrompt || '';
                console.log("[chatbot] Inline fallback: recreate prompt=", recreatePrompt);
                executeDeleteForContext(recreatePrompt);
            };

            chatBox.dataset.actionsBound = "1";
            chatBox.addEventListener("click", async function(event) {
                const recreateButton = event.target.closest(".confirm-recreate-groups");
                if (recreateButton) {
                    event.preventDefault();
                    event.stopPropagation();
                    console.log("[chatbot] delegated click matched .confirm-recreate-groups");
                    // Extract recreate prompt from button's data attribute
                    const recreatePrompt = recreateButton.dataset?.recreatePrompt || '';
                    console.log("[chatbot] Event delegation: recreate prompt=", recreatePrompt);
                    executeDeleteForContext(recreatePrompt);
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

                const savePembimbingButton = event.target.closest(".save-generated-pembimbing-btn");
                if (savePembimbingButton) {
                    event.preventDefault();
                    event.stopPropagation();
                    if (typeof window.__savePembimbingInline === 'function') {
                        window.__savePembimbingInline(event);
                    } else {
                        saveGeneratedPembimbing();
                    }
                    return;
                }

                const regeneratePembimbingButton = event.target.closest(".regenerate-pembimbing-btn");
                if (regeneratePembimbingButton) {
                    event.preventDefault();
                    event.stopPropagation();
                    if (typeof window.__regeneratePembimbingInline === 'function') {
                        window.__regeneratePembimbingInline(event);
                    }
                    return;
                }

                const savePengujiButton = event.target.closest(".save-generated-penguji-btn");
                if (savePengujiButton) {
                    event.preventDefault();
                    event.stopPropagation();
                    if (typeof window.__savePengujiInline === 'function') {
                        window.__savePengujiInline(event);
                    } else {
                        saveGeneratedPenguji();
                    }
                    return;
                }

                const regeneratePengujiButton = event.target.closest(".regenerate-penguji-btn");
                if (regeneratePengujiButton) {
                    event.preventDefault();
                    event.stopPropagation();
                    if (typeof window.__regeneratePengujiInline === 'function') {
                        window.__regeneratePengujiInline(event);
                    }
                    return;
                }
            });
        }

        // Initialize chat
        window.initializeAgent = function() {
            console.log("[chatbot] Initializing...")
            const input = document.getElementById("userInput")
            const sendBtn = document.getElementById("sendBtn")

            if (!input || !sendBtn) {
                console.error("[chatbot] Required elements not found!")
                return
            }

            // Enter key to send
            input.addEventListener("keydown", function(e) {
                if (e.key === "Enter" && !e.shiftKey) {
                    e.preventDefault()
                    if (typeof window.sendMessage === 'function') {
                        window.sendMessage()
                    }
                }
            })

            // Button click to send
            sendBtn.addEventListener("click", function(e) {
                e.preventDefault()
                if (typeof window.sendMessage === 'function') {
                    window.sendMessage()
                }
            })

            input.focus()
            console.log("[chatbot] Initialized successfully")
        };

        // Send message to AI
        window.sendMessage = function() {
            const input = document.getElementById("userInput")
            const section = document.querySelector('[data-ai-route]')
            const route = section?.dataset.aiRoute || '/ai/generate'

            let message = input.value.trim()
            if (!message) return

            latestUserPrompt = message

            switchToChat(); // 🔥 penting

            appendMessage("user", message)
            input.value = ""
            input.focus()

            let loadingId = appendLoading()

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

            fetch(route, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": csrfToken
                    },
                    body: JSON.stringify({
                        prompt: message
                    })
                })
                .then(res => {
                    if (!res.ok) {
                        return res.text().then(text => {
                            throw new Error(`HTTP ${res.status}: ${text.substring(0, 200)}`)
                        })
                    }
                    return res.json()
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

                    // Display AI response text saja
                    let displayText = data.result || "Tidak ada respons"
                    appendMessage("ai", displayText)

                    if (latestGroupingPayload && Array.isArray(latestGroupingPayload.groups) &&
                        latestGroupingPayload.groups.length > 0) {
                        appendGroupingActions()
                    }

                    if (latestPembimbingPayload && Array.isArray(latestPembimbingPayload.groups) &&
                        latestPembimbingPayload.groups.length > 0) {
                        appendPembimbingActions()
                    }

                    if (latestPengujiPayload && Array.isArray(latestPengujiPayload.groups) && latestPengujiPayload
                        .groups.length > 0) {
                        appendPengujiActions()
                    }

                    if (latestExcelFilename) {
                        appendExcelDownloadButton()
                    }

                    scrollToBottom()
                })
                .catch(err => {
                    removeLoading(loadingId)
                    const errorMsg = err.message.includes('JSON') ?
                        "Server error. Silakan cek console." :
                        err.message
                    appendMessage("ai", "❌ Terjadi Error: " + errorMsg)
                    console.error("[Error]", err)
                })
        };

        document.getElementById("landingSend")?.addEventListener("click", () => {
            const val = document.getElementById("landingInput").value;
            document.getElementById("userInput").value = val;
            window.sendMessage();
        });

        document.getElementById("landingInput")?.addEventListener("keydown", function(e) {
            if (e.key === "Enter" && !e.shiftKey) {
                e.preventDefault();
                document.getElementById("userInput").value = this.value;
                window.sendMessage();
            }
        });

        // ============== END CHATBOT UI ==============

        document.addEventListener("DOMContentLoaded", function() {
            console.log("[chatbot] DOMContentLoaded")
            bindChatActionDelegation()

            if (typeof window.initializeAgent === 'function') {
                window.initializeAgent()
            }

            // Load verification status dari database
            if (typeof loadVerificationStatusFromDatabase === 'function') {
                loadVerificationStatusFromDatabase()
            }

            // Help button
            const btnHelper = document.getElementById("btnHelper")
            if (btnHelper) {
                btnHelper.addEventListener("click", function() {
                    Swal.fire({
                        title: "📘 Panduan Penggunaan",
                        width: 600,
                        html: `
                    <div style="text-align:left">
                        <b>📌 Anda bebas bertanya apapun!</b><br><br>

                        <div class="alert alert-light" style="border-left: 3px solid #007bff;">
                            <strong>🎯 Instruksi pembagian kelompok:</strong><br>
                            "Buat 5 kelompok", "Buat kelompok isi 4 orang", "Bagi jadi 6 kelompok maksimal 4 orang"
                        </div>

                        <div class="alert alert-light" style="border-left: 3px solid #28a745;">
                            <strong>🎯 Aturan lanjutan:</strong><br>
                            "Buat 4 kelompok minimal 3 orang", "Buat kelompok berdasarkan NIM (tanpa acak)"
                        </div>

                        <div class="alert alert-light" style="border-left: 3px solid #17a2b8;">
                            <strong>🎯 Query data:</strong><br>
                            "Daftar kelompok", "Daftar mahasiswa", "Daftar dosen"
                        </div>

                        <div class="alert alert-light" style="border-left: 3px solid #ffc107;">
                            <strong>🎯 Percakapan umum:</strong><br>
                            Tanyakan pertanyaan apapun yang Anda inginkan!
                        </div>
                    </div>
                `,
                        confirmButtonText: "Tutup",
                        confirmButtonColor: "#007bff"
                    })
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

        // Fungsi pindah dari landing ke chat
        function openChatWithMessage(message) {
            if (!message) return;

            // hide landing
            if (landingView) landingView.style.display = "none";
            if (chatView) chatView.style.display = "flex";

            const input = document.getElementById("userInput");
            if (input) {
                input.value = message;
            }

            if (typeof window.sendMessage === "function") {
                window.sendMessage();
            }
        }

        // ================== CHIP CLICK ==================
        chips.forEach(chip => {
            chip.addEventListener("click", function() {
                const instruction = this.getAttribute("data-instruction");
                openChatWithMessage(instruction);
            });
        });

        // ================== LANDING SEND BUTTON ==================
        if (landingSend) {
            landingSend.addEventListener("click", function() {
                const message = landingInput.value.trim();
                openChatWithMessage(message);
            });
        }

        // ENTER di landing input
        if (landingInput) {
            landingInput.addEventListener("keydown", function(e) {
                if (e.key === "Enter" && !e.shiftKey) {
                    e.preventDefault();
                    const message = landingInput.value.trim();
                    openChatWithMessage(message);
                }
            });
        }

        // ================== BUTTON SESI BARU ==================
        if (btnNewChat) {
            btnNewChat.style.display = "inline-flex"; // tampilkan tombol

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

                        // reset state
                        latestGroupingPayload = null;
                        latestPembimbingPayload = null;
                        latestPengujiPayload = null;
                        latestExcelFilename = null;

                        // kembali ke landing
                        if (landingView) landingView.style.display = "flex";
                        if (chatView) chatView.style.display = "none";

                        if (landingInput) {
                            landingInput.value = "";
                            landingInput.focus();
                        }
                    }
                });
            });
        }
    </script>

@endsection
