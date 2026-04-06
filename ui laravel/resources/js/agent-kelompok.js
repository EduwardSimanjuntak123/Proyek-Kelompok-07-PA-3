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
        bubble.innerHTML = text;
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

