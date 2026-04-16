@extends('layouts.main')
@section('title', 'AI Agent Chatbot')



@section('content')
    <link rel="stylesheet" href="{{ asset('css/agent-kelompok.css') }}">

<section class="chatbox-container" data-ai-route="{{ route('ai.callAgent') }}" 
    data-save-route="{{ route('ai.saveGroups') }}"
    data-save-pembimbing-route="{{ route('ai.savePembimbing') }}"
    data-check-pembimbing-route="{{ route('ai.cekPembimbing') }}"
    data-save-penguji-route="{{ route('ai.savePenguji') }}"
    data-check-penguji-route="{{ route('ai.cekPenguji') }}"
    data-check-route="{{ route('ai.cekKelompok') }}"
    data-delete-route="{{ route('ai.deleteForContext') }}"
    data-kategori-pa-id="{{ request()->query('kategori_pa_id', 1) }}"
    data-prodi-id="{{ request()->query('prodi_id', 1) }}"
    data-tahun-masuk-id="{{ request()->query('tahun_masuk_id', 1) }}">

    <!-- HEADER -->
    <div class="chat-header">
        <h4><i class="fas fa-robot"></i> AI Agent Kelompok</h4>
        <button id="btnHelper" class="btn btn-sm" style="padding: 4px 8px; font-size: 12px;">
            <i class="fas fa-question-circle"></i> Panduan
        </button>
    </div>

    <!-- CHAT BOX -->
    <div id="chatBox">
        <div class="message-wrapper ai">
            <div class="welcome-message">
                <strong>👋 Halo! Saya AI Assistant.</strong>
                <i>Selamat datang! Anda bebas bertanya apapun kepada saya. Saya siap membantu Anda dengan berbagai pertanyaan.</i>
            </div>
        </div>
    </div>

    <!-- INPUT AREA -->
    <div class="chat-input-area">
        <div class="input-container">
            <input 
                type="text" 
                id="userInput" 
                placeholder="Tulis instruksi... contoh: buat 5 kelompok, maksimal 4 orang" 
                autocomplete="off"
            >
            <button type="button" id="sendBtn">
                <i class="fas fa-paper-plane"></i> Kirim
            </button>
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

// Scroll to bottom smoothly
function scrollToBottom() {
    const chatBox = document.getElementById("chatBox");
    if (chatBox) {
        setTimeout(() => {
            chatBox.scrollTop = chatBox.scrollHeight;
        }, 10);
    }
}

// Append message with proper bubble styling
function appendMessage(sender, text) {
    const chatBox = document.getElementById("chatBox");
    
    let wrapper = document.createElement("div");
    wrapper.className = `message-wrapper ${sender}`;
    
    let bubble = document.createElement("div");
    bubble.className = `chat-message-bubble ${sender}`;
    
    if (sender === "user") {
        bubble.textContent = text;
    } else {
        bubble.innerHTML = text;
    }
    
    wrapper.appendChild(bubble);
    chatBox.appendChild(wrapper);
    scrollToBottom();
}

function appendGroupingActions() {
    const chatBox = document.getElementById("chatBox");
    if (!chatBox || !latestGroupingPayload || !Array.isArray(latestGroupingPayload.groups) || latestGroupingPayload.groups.length === 0) {
        return;
    }

    const wrapper = document.createElement("div");
    wrapper.className = "message-wrapper ai";

    const bubble = document.createElement("div");
    bubble.className = "chat-message-bubble ai";

    const existingCount = Number(latestGroupingMeta?.existing_groups_count || 0);
    const existingNote = existingCount > 0
        ? `<p style="margin:0 0 8px 0; color:#b45309;"><strong>Perhatian:</strong> Sudah ada ${existingCount} kelompok pada konteks ini. Simpan akan meminta konfirmasi replace.</p>`
        : `<p style="margin:0 0 8px 0; color:#166534;">Belum ada kelompok existing pada konteks ini.</p>`;

    bubble.innerHTML = `
        <div style="border:1px solid #dbeafe; background:#eff6ff; border-radius:10px; padding:10px;">
            <h6 style="margin:0 0 8px 0;">Aksi Hasil Generate</h6>
            ${existingNote}
            <button type="button" class="btn btn-sm btn-primary save-generated-groups-btn">
                <i class="fas fa-database"></i> Simpan ke Database
            </button>
        </div>
    `;

    wrapper.appendChild(bubble);
    chatBox.appendChild(wrapper);
    scrollToBottom();

}

function appendPembimbingActions() {
    const chatBox = document.getElementById("chatBox");
    if (!chatBox || !latestPembimbingPayload || !Array.isArray(latestPembimbingPayload.groups) || latestPembimbingPayload.groups.length === 0) {
        return;
    }

    const wrapper = document.createElement("div");
    wrapper.className = "message-wrapper ai";

    const bubble = document.createElement("div");
    bubble.className = "chat-message-bubble ai";

    const existingCount = Number(latestPembimbingMeta?.existing_assignments_count || 0);
    const existingNote = existingCount > 0
        ? `<p style="margin:0 0 8px 0; color:#b45309;"><strong>Perhatian:</strong> Sudah ada ${existingCount} assignment pembimbing pada konteks ini. Simpan akan meminta konfirmasi replace.</p>`
        : `<p style="margin:0 0 8px 0; color:#166534;">Belum ada assignment pembimbing pada konteks ini.</p>`;

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
    if (!chatBox || !latestPengujiPayload || !Array.isArray(latestPengujiPayload.groups) || latestPengujiPayload.groups.length === 0) {
        return;
    }

    const wrapper = document.createElement("div");
    wrapper.className = "message-wrapper ai";

    const bubble = document.createElement("div");
    bubble.className = "chat-message-bubble ai";

    const existingCount = Number(latestPengujiMeta?.existing_assignments_count || 0);
    const existingNote = existingCount > 0
        ? `<p style="margin:0 0 8px 0; color:#b45309;"><strong>Perhatian:</strong> Sudah ada ${existingCount} assignment penguji pada konteks ini. Simpan akan meminta konfirmasi replace.</p>`
        : `<p style="margin:0 0 8px 0; color:#166534;">Belum ada assignment penguji pada konteks ini.</p>`;

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
    const downloadRoute = '{{ route("ai.downloadExcel") }}';
    
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

    if (!latestGroupingPayload || !Array.isArray(latestGroupingPayload.groups) || latestGroupingPayload.groups.length === 0) {
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
        Swal.fire("Error", error.message || "Terjadi kesalahan saat menyimpan.", "error");
    } finally {
        isSavingGeneratedGroups = false;
    }
}

async function saveGeneratedPembimbing() {
    if (isSavingGeneratedPembimbing) {
        return;
    }

    if (!latestPembimbingPayload || !Array.isArray(latestPembimbingPayload.groups) || latestPembimbingPayload.groups.length === 0) {
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

    if (!latestPengujiPayload || !Array.isArray(latestPengujiPayload.groups) || latestPengujiPayload.groups.length === 0) {
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
        const regeneratePrompt = latestPembimbingMeta?.prompt || latestUserPrompt || "generate pembimbing kelompok";
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
        const regeneratePrompt = latestPengujiMeta?.prompt || latestUserPrompt || "generate penguji kelompok";
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
    let id = "loading-" + Date.now();

    let wrapper = document.createElement("div");
    wrapper.className = "message-wrapper ai";
    
    let loading = document.createElement("div");
    loading.id = id;
    loading.className = "loading-bubble";
    
    loading.innerHTML = `
        <span class="robot-icon">🤖</span>
        <span>AI sedang berpikir</span>
        <span class="dot"></span>
        <span class="dot"></span>
        <span class="dot"></span>
    `;
    
    wrapper.appendChild(loading);
    chatBox.appendChild(wrapper);
    scrollToBottom();
    
    return id;
}

function removeLoading(id) {
    let wrapper = document.querySelector(`#${id}`)?.parentElement;
    if (wrapper) wrapper.remove();
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

        console.log("[chatbot] confirm-recreate click", { deleteRoute, hasCsrf: !!csrfToken, hasRecreatePrompt: !!recreatePrompt });

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

        if (latestGroupingPayload && Array.isArray(latestGroupingPayload.groups) && latestGroupingPayload.groups.length > 0) {
            appendGroupingActions()
        }

        if (latestPembimbingPayload && Array.isArray(latestPembimbingPayload.groups) && latestPembimbingPayload.groups.length > 0) {
            appendPembimbingActions()
        }

        if (latestPengujiPayload && Array.isArray(latestPengujiPayload.groups) && latestPengujiPayload.groups.length > 0) {
            appendPengujiActions()
        }

        if (latestExcelFilename) {
            appendExcelDownloadButton()
        }
        
        scrollToBottom()
    })
    .catch(err => {
        removeLoading(loadingId)
        const errorMsg = err.message.includes('JSON') 
            ? "Server error. Silakan cek console." 
            : err.message
        appendMessage("ai", "❌ Terjadi Error: " + errorMsg)
        console.error("[Error]", err)
    })
};

// ============== END CHATBOT UI ==============

document.addEventListener("DOMContentLoaded", function() {
    console.log("[chatbot] DOMContentLoaded")
    bindChatActionDelegation()
    
    if (typeof window.initializeAgent === 'function') {
        window.initializeAgent()
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
</script>

@endsection 