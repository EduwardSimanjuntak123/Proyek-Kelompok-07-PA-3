// ============================================
// AI AGENT CHATBOT - CLIENT SIDE SCRIPT
// ============================================

console.log("[chatbot-ui] Script loaded")

/**
 * Scroll chat to bottom smoothly
 */
function scrollToBottom() {
    const chatBox = document.getElementById("chatBox");
    if (chatBox) {
        setTimeout(() => {
            chatBox.scrollTop = chatBox.scrollHeight;
        }, 10);
    }
}

/**
 * Helper function to set HTML and execute scripts
 */
function setHTMLWithScripts(element, html) {
    console.log('[JADWAL] setHTMLWithScripts called');
    
    // First, extract script content before setting innerHTML
    const scriptRegex = /<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi;
    const scripts = [];
    let match;
    while ((match = scriptRegex.exec(html)) !== null) {
        scripts.push(match[0]);
    }
    
    // Remove scripts from HTML before setting
    const cleanHtml = html.replace(scriptRegex, '');
    
    // Set the clean HTML
    element.innerHTML = cleanHtml;
    console.log('[JADWAL] HTML set, found ' + scripts.length + ' scripts');
    
    // Now execute scripts after a micro-task to ensure DOM is ready
    Promise.resolve().then(() => {
        scripts.forEach((scriptTag, idx) => {
            try {
                // Extract content between <script> tags
                const contentMatch = /<script\b[^>]*>([\s\S]*?)<\/script>/i.exec(scriptTag);
                if (contentMatch && contentMatch[1]) {
                    const scriptContent = contentMatch[1];
                    console.log('[JADWAL] Executing script ' + (idx + 1));
                    
                    // Create and execute script
                    const scriptEl = document.createElement('script');
                    scriptEl.textContent = scriptContent;
                    document.body.appendChild(scriptEl);
                    
                    console.log('[JADWAL] Script ' + (idx + 1) + ' executed');
                }
            } catch (e) {
                console.error('[JADWAL] Error executing script ' + (idx + 1) + ':', e);
            }
        });
        console.log('[JADWAL] All scripts executed');
    });
}

/**
 * Append message bubble to chat
 */
function appendMessage(sender, text) {
    const chatBox = document.getElementById("chatBox");
    if (!chatBox) return;
    
    let wrapper = document.createElement("div");
    wrapper.className = `message-wrapper ${sender}`;
    
    let bubble = document.createElement("div");
    bubble.className = `chat-message-bubble ${sender}`;
    
    if (sender === "user") {
        bubble.textContent = text;
    } else {
        // Use helper function to set HTML and execute scripts
        setHTMLWithScripts(bubble, text);
    }
    
    wrapper.appendChild(bubble);
    chatBox.appendChild(wrapper);
    scrollToBottom();
}

/**
 * Show thinking animation
 */
function appendLoading() {
    const chatBox = document.getElementById("chatBox");
    if (!chatBox) return;
    
    let id = "loading-" + Date.now();

    let wrapper = document.createElement("div");
    wrapper.className = "message-wrapper ai";
    
    let loading = document.createElement("div");
    loading.id = id;
    loading.className = "loading-bubble";
    
    loading.innerHTML = `
        <span class="robot-icon">🤖</span>
        <span style="color: #666;">AI sedang berpikir</span>
        <span class="dot"></span>
        <span class="dot"></span>
        <span class="dot"></span>
    `;
    
    wrapper.appendChild(loading);
    chatBox.appendChild(wrapper);
    scrollToBottom();
    
    return id;
}

/**
 * Remove loading animation
 */
function removeLoading(id) {
    let wrapper = document.querySelector(`#${id}`)?.parentElement;
    if (wrapper) wrapper.remove();
}

/**
 * Attach listeners to recommendation buttons
 */
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

/**
 * Export functions to window for global access
 */
window.scrollToBottom = scrollToBottom;
window.appendMessage = appendMessage;
window.appendLoading = appendLoading;
window.removeLoading = removeLoading;
window.attachRecommendationListeners = attachRecommendationListeners;

/**
 * Initialize chatbot
 */
window.initializeAgent = function() {
    console.log("[chatbot] Initializing...")
    const input = document.getElementById("userInput")
    const sendBtn = document.getElementById("sendBtn")

    if (!input || !sendBtn) {
        console.error("[chatbot] Required elements not found!")
        return
    }

    // Handle Enter key
    input.addEventListener("keydown", function(e) {
        if (e.key === "Enter" && !e.shiftKey) {
            e.preventDefault()
            if (typeof window.sendMessage === 'function') {
                window.sendMessage()
            }
        }
    })

    // Handle button click
    sendBtn.addEventListener("click", function(e) {
        e.preventDefault()
        if (typeof window.sendMessage === 'function') {
            window.sendMessage()
        }
    })

    input.focus()
    console.log("[chatbot] Initialized successfully")
};

/**
 * Send message to AI API
 */
window.sendMessage = function() {
    const input = document.getElementById("userInput")
    const section = document.querySelector('[data-ai-route]')
    const route = section?.dataset.aiRoute || '/ai/generate'
    
    let message = input.value.trim()
    if (!message) return

    // Show user message
    appendMessage("user", message)
    input.value = ""
    input.focus()

    // Show loading
    let loadingId = appendLoading()

    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
    
    // Send to server
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
        
        // Show AI response
        let displayText = data.result || "Response tidak memiliki result"
        appendMessage("ai", displayText)
        
        // Show data if available
        if (data.data) {
            setTimeout(() => {
                appendMessage("ai", data.data)
            }, 200)
        }
        
        // Attach listeners to recommendations
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

/**
 * Handle jadwal seminar form submission
 */
window.__submitJadwal = function(event) {
    const timestamp = new Date().toLocaleTimeString();
    console.log(`[${timestamp}] [JADWAL] ▶️  __submitJadwal called`);
    
    try {
        event.preventDefault();
        
        // Get form values
        const tanggalInput = document.getElementById("jadwal-tanggal");
        const durasiJamInput = document.getElementById("jadwal-durasi-jam");
        const durasiMenitInput = document.getElementById("jadwal-durasi-menit");
        
        console.log(`[${timestamp}] [JADWAL] ✓ Form elements found`);
        
        const tanggal = tanggalInput?.value?.trim() || "";
        const jam = parseInt(durasiJamInput?.value || "1");
        const menit = parseInt(durasiMenitInput?.value || "50");
        
        // Get all selected ruangan
        const ruanganSelects = document.querySelectorAll(".jadwal-ruangan-select");
        const ruanganList = [];
        ruanganSelects.forEach((select, idx) => {
            const ruangan_id = select.value;
            if (ruangan_id) {
                ruanganList.push(ruangan_id);
            }
        });
        
        console.log(`[${timestamp}] [JADWAL] Values: tanggal='${tanggal}', ruangan_list='${ruanganList.join(",")}', durasi='${jam}j ${menit}m'`);
        
        // Validate
        if (!tanggal) {
            alert("❌ Tanggal harus diisi (contoh: 15 mei 2026)");
            return;
        }
        
        if (ruanganList.length === 0) {
            alert("❌ Minimal 1 ruangan harus dipilih");
            return;
        }
        
        // Convert jam + menit to total menit
        const totalMenit = (jam * 60) + menit;
        
        // Build message in format: [jadwal] tanggal: 15 mei 2026 | ruangan: 1,2,3 | durasi: 110
        const message = `[jadwal] tanggal: ${tanggal} | ruangan: ${ruanganList.join(",")} | durasi: ${totalMenit}`;
        console.log(`[${timestamp}] [JADWAL] Message: ${message}`);
        
        // Set to userInput and send
        const userInput = document.getElementById("userInput");
        if (userInput) {
            userInput.value = message;
            console.log(`[${timestamp}] [JADWAL] Calling sendMessage()...`);
            window.sendMessage();
        } else {
            console.error(`[${timestamp}] [JADWAL] ❌ userInput element not found`);
            alert("❌ Error: userInput element not found");
        }
    } catch (error) {
        console.error(`[${timestamp}] [JADWAL] ❌ Exception:`, error);
        alert(`❌ Error: ${error.message}`);
    }
};

