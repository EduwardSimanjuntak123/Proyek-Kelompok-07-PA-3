@extends('layouts.main')
@section('title', 'AI Agent Chatbot')



@section('content')
    <link rel="stylesheet" href="{{ asset('css/agent-kelompok.css') }}">

<section class="chatbox-container" data-ai-route="{{ route('ai.generate') }}">

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
                <strong>👋 Halo! Saya AI Agent Kelompok.</strong>
                <i>Saya membantu Anda membagi mahasiswa ke dalam kelompok. Coba perintah seperti: "Bagi mahasiswa menjadi 6 orang per kelompok" atau "Buat 5 kelompok"</i>
            </div>
        </div>
    </div>

    <!-- INPUT AREA -->
    <div class="chat-input-area">
        <div class="input-container">
            <input 
                type="text" 
                id="userInput" 
                placeholder="Tulis instruksi... contoh: buat 5 kelompok" 
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
        
        // Display result
        let displayText = data.result || "Response tidak memiliki result"
        appendMessage("ai", displayText)
        
        // Display data if available
        if (data.data) {
            setTimeout(() => {
                appendMessage("ai", data.data)
            }, 200)
        }
        
        if (data.recommendations) {
            attachRecommendationListeners()
        }
    })
    .catch(err => {
        removeLoading(loadingId)
        const errorMsg = err.message.includes('JSON') 
            ? "Server error. Silakan cek console." 
            : err.message
        appendMessage("ai", "❌ Terjadi Error: " + errorMsg)
    })
};

// ============== END CHATBOT UI ==============

document.addEventListener("DOMContentLoaded", function() {
    console.log("[chatbot] DOMContentLoaded")
    
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
                        <b>📌 Contoh Perintah:</b><br><br>

                        <div class="alert alert-light" style="border-left: 3px solid #007bff;">
                            <strong>🎯 Buat kelompok dengan ukuran tertentu:</strong><br>
                            "Bagi mahasiswa menjadi 6 orang per kelompok"
                        </div>

                        <div class="alert alert-light" style="border-left: 3px solid #28a745;">
                            <strong>🎯 Buat jumlah kelompok tertentu:</strong><br>
                            "Buat kelompok dengan 3 orang"<br>
                            "Buat kelompok dengan 3 orang berdasarkan kategori matakuliah"
                        </div>

                        <div class="alert alert-light" style="border-left: 3px solid #17a2b8;">
                            <strong>🎯 Lihat daftar mahasiswa:</strong><br>
                            "Tampilkan daftar mahasiswa"
                        </div>

                        <div class="alert alert-light" style="border-left: 3px solid #ffc107;">
                            <strong>🎯 Pertanyaan umum:</strong><br>
                            "Siapa kamu?" atau "Apa yang bisa kamu lakukan?"
                        </div>
                    </div>
                `,
                confirmButtonText: "Tutup",
                confirmButtonColor: "#007bff"
            })
        })
    }

    // ================= SAVE GROUP CONFIRMATION =================
    window.confirmSaveGroup = function() {
        let confirmDialog = document.querySelector('[style*="background-color: #fff3cd"]')
        if (confirmDialog) {
            confirmDialog.style.display = 'none'
        }

        let loadingId = appendLoading()
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

        fetch("{{ route('ai.save-groups') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken
            },
            body: JSON.stringify({
                prompt: "lanjutkan menyimpan kelompok"
            })
        })
        .then(res => {
            if (!res.ok) {
                return res.text().then(text => {
                    throw new Error(`HTTP ${res.status}`)
                })
            }
            return res.json()
        })
        .then(data => {
            removeLoading(loadingId)
            appendMessage("ai", data.result || "✅ Kelompok berhasil disimpan ke database")
            attachRecommendationListeners()
            scrollToBottom()
        })
        .catch(err => {
            removeLoading(loadingId)
            console.error("[ConfirmSave] Error:", err)
            appendMessage("ai", "❌ Error saat menyimpan: " + (err.message || "Kesalahan tidak diketahui"))
        })
    }

    window.cancelSaveGroup = function() {
        let confirmDialog = document.querySelector('[style*="background-color: #fff3cd"]')
        if (confirmDialog) {
            confirmDialog.style.display = 'none'
        }
        appendMessage("ai", "❌ Pembatalan: Penyimpanan kelompok dibatalkan. Kelompok lama tetap disimpan.")
        scrollToBottom()
    }

})
</script>

@endsection 