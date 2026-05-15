// ============================================
// AI AGENT CHATBOT - CLIENT SIDE SCRIPT
// ============================================

console.log("[chatbot-ui] Script loaded");

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
    element.innerHTML = html;
    // Find and execute all script tags
    const scripts = element.querySelectorAll("script");
    scripts.forEach((script) => {
        try {
            const newScript = document.createElement("script");
            newScript.textContent = script.textContent;
            element.appendChild(newScript);
            newScript.remove(); // Remove after execution
        } catch (e) {
            console.error("[JADWAL] Error executing script:", e);
        }
    });
}

function formatJadwalPromptDisplay(message) {
    if (typeof message !== "string") {
        return message;
    }

    const trimmed = message.trim();
    if (!trimmed.toLowerCase().startsWith("[jadwal]")) {
        return message;
    }

    const fields = {};
    trimmed
        .replace(/^\[jadwal\]\s*/i, "")
        .split("|")
        .forEach((segment) => {
            const part = segment.trim();
            if (!part) return;

            const separatorIndex = part.indexOf(":");
            if (separatorIndex === -1) return;

            const key = part.slice(0, separatorIndex).trim().toLowerCase();
            const value = part.slice(separatorIndex + 1).trim();
            if (key) {
                fields[key] = value;
            }
        });

    const action = (fields.action || "").toLowerCase();
    const prefix =
        action === "save" || action === "simpan" || action === "persist"
            ? "Simpan jadwal seminar"
            : action === "acak" || action === "shuffle" || action === "random"
              ? "Acak ulang jadwal seminar"
              : "Jadwal seminar";

    const parts = [];
    if (fields.tanggal) parts.push(`tanggal ${fields.tanggal}`);
    if (fields.ruangan) parts.push(`ruangan ${fields.ruangan}`);
    if (fields.ruangan) {
        // Try to map numeric IDs to names using latestJadwalEntries if available
        let ruanganDisplay = fields.ruangan;
        try {
            const maybeIds = String(fields.ruangan)
                .split(",")
                .map((s) => s.trim())
                .filter(Boolean);
            const allNumeric =
                maybeIds.length > 0 && maybeIds.every((s) => /^\d+$/.test(s));
            if (allNumeric) {
                const idToName = {};
                if (
                    typeof latestJadwalEntries !== "undefined" &&
                    Array.isArray(latestJadwalEntries) &&
                    latestJadwalEntries.length
                ) {
                    latestJadwalEntries.forEach((e) => {
                        if (e && (e.ruangan_id || e.ruangan_id === 0)) {
                            idToName[String(e.ruangan_id)] =
                                e.ruangan_name ||
                                e.ruangan ||
                                String(e.ruangan_id);
                        }
                    });
                }

                // If mapping missing, try DOM select options
                if (Object.keys(idToName).length === 0) {
                    try {
                        const opts = document.querySelectorAll(
                            "select.jadwal-ruangan-select option",
                        );
                        opts.forEach((o) => {
                            if (o && o.value)
                                idToName[String(o.value)] =
                                    o.textContent.trim();
                        });
                    } catch (e) {
                        // ignore
                    }
                }

                const names = maybeIds.map(
                    (id) => idToName[id] || `Ruangan ${id}`,
                );
                ruanganDisplay = names.join(", ");
            }
        } catch (e) {
            ruanganDisplay = fields.ruangan;
        }
        parts.push(`ruangan ${ruanganDisplay}`);
    }
    if (fields.durasi) parts.push(`durasi ${fields.durasi} menit`);
    if (fields.order) parts.push(`urutan kelompok ${fields.order}`);

    if (!parts.length) {
        return prefix;
    }

    return `${prefix} pada ${parts.join(", ")}.`;
}

/**
 * Append jadwal form action buttons (Simpan + Buat Ulang)
 */
function appendJadwalFormActions(wrapper) {
    const timestamp = new Date().toLocaleTimeString();
    console.log(`[${timestamp}] [JADWAL] Appending action buttons...`);

    try {
        const bubble = wrapper.querySelector(".chat-message-bubble");
        if (!bubble) {
            console.error(`[${timestamp}] [JADWAL] ❌ Bubble not found`);
            return;
        }

        const actionsHost =
            wrapper.querySelector("#jadwal-form-actions") || bubble;

        // First, attach event listener to "+ Tambah Ruangan" button
        const addBtn = wrapper.querySelector("#add-ruangan-btn");
        if (addBtn) {
            addBtn.onclick = function (event) {
                event.preventDefault();
                event.stopPropagation();

                const container = wrapper.querySelector(
                    "#jadwal-ruangan-container",
                );
                const rows = container.querySelectorAll(".ruangan-row");
                const rowCount = rows.length;

                // Create new row
                const newRow = document.createElement("div");
                newRow.className = "ruangan-row";
                newRow.style.display = "flex";
                newRow.style.gap = "8px";
                newRow.style.marginBottom = "8px";
                newRow.style.alignItems = "center";

                // Create select element
                const select = document.createElement("select");
                select.className = "jadwal-ruangan-select";
                select.style.flex = "1";
                select.style.padding = "8px";
                select.style.border = "1px solid #ccc";
                select.style.borderRadius = "4px";
                select.style.fontSize = "14px";

                // Copy options from first select
                const firstSelect = container.querySelector(
                    ".jadwal-ruangan-select",
                );
                if (firstSelect) {
                    select.innerHTML = firstSelect.innerHTML;
                }

                // Create remove button
                const removeBtn = document.createElement("button");
                removeBtn.type = "button";
                removeBtn.className = "remove-ruangan-btn";
                removeBtn.style.padding = "8px 12px";
                removeBtn.style.background = "#ef4444";
                removeBtn.style.color = "white";
                removeBtn.style.border = "none";
                removeBtn.style.borderRadius = "4px";
                removeBtn.style.cursor = "pointer";
                removeBtn.style.fontWeight = "bold";
                removeBtn.style.minWidth = "40px";
                removeBtn.textContent = "✕";

                removeBtn.onclick = function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    newRow.remove();
                    updateRemoveButtons();
                };

                newRow.appendChild(select);
                newRow.appendChild(removeBtn);
                container.appendChild(newRow);

                updateRemoveButtons();
                console.log(
                    `[${timestamp}] [JADWAL] ✓ Ruangan row #${rowCount + 1} added`,
                );
            };
            console.log(
                `[${timestamp}] [JADWAL] ✓ Add ruangan button listener attached`,
            );
        }

        // Helper function to update remove buttons visibility
        function updateRemoveButtons() {
            const container = wrapper.querySelector(
                "#jadwal-ruangan-container",
            );
            const rows = container.querySelectorAll(".ruangan-row");
            const removeBtns = container.querySelectorAll(
                ".remove-ruangan-btn",
            );
            removeBtns.forEach((btn) => {
                btn.style.display = rows.length > 1 ? "block" : "none";
            });
        }

        updateRemoveButtons();

        // Create action buttons container
        const actionsDiv = document.createElement("div");
        actionsDiv.style.display = "flex";
        actionsDiv.style.flexDirection = "column";
        actionsDiv.style.gap = "10px";
        actionsDiv.style.marginTop = "0";
        actionsDiv.style.width = "100%";

        // Simpan button
        const saveBtn = document.createElement("button");
        saveBtn.type = "button";
        saveBtn.className = "btn btn-sm btn-primary";
        saveBtn.style.position = "relative";
        saveBtn.style.zIndex = "2";
        saveBtn.style.cursor = "pointer";
        saveBtn.style.pointerEvents = "auto";
        saveBtn.style.width = "100%";
        saveBtn.innerHTML =
            '<i class="fas fa-check"></i> Simpan Jadwal Seminar';

        saveBtn.onclick = function (event) {
            event.preventDefault();
            event.stopPropagation();
            console.log(`[${timestamp}] [JADWAL] Save button clicked`);
            window.__submitJadwal(event);
        };

        // Reset button
        const resetBtn = document.createElement("button");
        resetBtn.type = "button";
        resetBtn.className = "btn btn-sm btn-secondary";
        resetBtn.style.position = "relative";
        resetBtn.style.zIndex = "2";
        resetBtn.style.cursor = "pointer";
        resetBtn.style.pointerEvents = "auto";
        resetBtn.style.width = "100%";
        resetBtn.innerHTML = '<i class="fas fa-redo"></i> Buat Ulang';

        resetBtn.onclick = function (event) {
            event.preventDefault();
            event.stopPropagation();
            console.log(`[${timestamp}] [JADWAL] Reset button clicked`);
            // Reset form inputs
            const tanggalInput = wrapper.querySelector("#jadwal-tanggal");
            const jamInput = wrapper.querySelector("#jadwal-durasi-jam");
            const menitInput = wrapper.querySelector("#jadwal-durasi-menit");

            if (tanggalInput) tanggalInput.value = "";
            if (jamInput) jamInput.value = "1";
            if (menitInput) menitInput.value = "50";

            // Reset ruangan to single select
            const container = wrapper.querySelector(
                "#jadwal-ruangan-container",
            );
            if (container) {
                const rows = container.querySelectorAll(".ruangan-row");
                if (rows.length > 1) {
                    rows.forEach((row, idx) => {
                        if (idx > 0) row.remove();
                    });
                }
                // Hide remove button
                const removeBtn = container.querySelector(
                    ".remove-ruangan-btn",
                );
                if (removeBtn) removeBtn.style.display = "none";
            }

            console.log(`[${timestamp}] [JADWAL] Form reset`);
        };

        actionsDiv.appendChild(saveBtn);
        actionsDiv.appendChild(resetBtn);
        actionsHost.appendChild(actionsDiv);

        console.log(`[${timestamp}] [JADWAL] ✓ Action buttons appended`);
    } catch (error) {
        console.error(`[${timestamp}] [JADWAL] ❌ Error:`, error);
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

        // Add action buttons jika form jadwal present
        setTimeout(() => {
            if (text.includes("Input Jadwal Seminar")) {
                appendJadwalFormActions(wrapper);
            }
        }, 50);
    }

    wrapper.appendChild(bubble);
    chatBox.appendChild(wrapper);
    scrollToBottom();
}

function restoreJadwalSubmitButton() {
    const state = window.__jadwalSubmitState;
    if (!state || !state.button) {
        return;
    }

    if (typeof state.originalHtml === "string") {
        state.button.innerHTML = state.originalHtml;
    }

    state.button.disabled = !!state.originalDisabled;
    window.__jadwalSubmitState = null;
}

function setJadwalSubmitButtonLoading(button) {
    if (!button) {
        return;
    }

    window.__jadwalSubmitState = {
        button: button,
        originalHtml: button.innerHTML,
        originalDisabled: button.disabled,
    };

    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
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
        const instruction = this.getAttribute("data-instruction");
        if (instruction) {
            input.value = instruction;
            const event = new KeyboardEvent("keydown", {
                key: "Enter",
                code: "Enter",
                keyCode: 13,
                which: 13,
                bubbles: true,
            });
            input.dispatchEvent(event);
        }
    }

    function handleConstraintClick() {
        const input = document.getElementById("userInput");
        const instruction = this.getAttribute("data-instruction");
        if (instruction && confirm(`Terapkan: ${instruction}?`)) {
            input.value = instruction;
            const event = new KeyboardEvent("keydown", {
                key: "Enter",
                code: "Enter",
                keyCode: 13,
                which: 13,
                bubbles: true,
            });
            input.dispatchEvent(event);
        }
    }

    document.querySelectorAll(".recommendation-action").forEach((btn) => {
        btn.removeEventListener("click", handleActionClick);
        btn.addEventListener("click", handleActionClick);
    });

    document.querySelectorAll(".recommendation-constraint").forEach((btn) => {
        btn.removeEventListener("click", handleConstraintClick);
        btn.addEventListener("click", handleConstraintClick);
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
window.appendJadwalFormActions = appendJadwalFormActions;

/**
 * Initialize chatbot
 */
window.initializeAgent = function () {
    console.log("[chatbot] Initializing...");
    const input = document.getElementById("userInput");
    const sendBtn = document.getElementById("sendBtn");

    if (!input || !sendBtn) {
        console.error("[chatbot] Required elements not found!");
        return;
    }

    // Handle Enter key
    input.addEventListener("keydown", function (e) {
        if (e.key === "Enter" && !e.shiftKey) {
            e.preventDefault();
            if (typeof window.sendMessage === "function") {
                window.sendMessage();
            }
        }
    });

    // Handle button click
    sendBtn.addEventListener("click", function (e) {
        e.preventDefault();
        if (typeof window.sendMessage === "function") {
            window.sendMessage();
        }
    });

    input.focus();
    console.log("[chatbot] Initialized successfully");
};

/**
 * Send message to AI API
 */
window.sendMessage = function () {
    const input = document.getElementById("userInput");
    const section = document.querySelector("[data-ai-route]");
    const route = section?.dataset.aiRoute || "/ai/generate";

    let message = input.value.trim();
    if (!message) return;

    const isJadwalPrompt = message.toLowerCase().startsWith("[jadwal]");
    const displayMessage = formatJadwalPromptDisplay(message);

    // Show user message
    appendMessage("user", displayMessage);
    input.value = "";
    input.focus();

    // Show loading
    let loadingId = appendLoading();

    // Get CSRF token
    const csrfToken =
        document.querySelector('meta[name="csrf-token"]')?.content || "";

    // Send to server
    fetch(route, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": csrfToken,
        },
        body: JSON.stringify({
            prompt: message,
        }),
    })
        .then((res) => {
            if (!res.ok) {
                return res.text().then((text) => {
                    throw new Error(
                        `HTTP ${res.status}: ${text.substring(0, 200)}`,
                    );
                });
            }
            return res.json();
        })
        .then((data) => {
            removeLoading(loadingId);

            // Show AI response
            let displayText = data.result || "Response tidak memiliki result";
            appendMessage("ai", displayText);

            // Show data if available
            if (data.data) {
                setTimeout(() => {
                    appendMessage("ai", data.data);
                }, 200);
            }

            // Attach listeners to recommendations
            if (data.recommendations) {
                attachRecommendationListeners();
            }
        })
        .catch((err) => {
            removeLoading(loadingId);
            const errorMsg = err.message.includes("JSON")
                ? "Server error. Silakan cek console."
                : err.message;
            appendMessage("ai", "❌ Terjadi Error: " + errorMsg);
        })
        .finally(() => {
            if (isJadwalPrompt) {
                restoreJadwalSubmitButton();
            }
        });
};

/**
 * Convert date picker value (YYYY-MM-DD) to Indonesian format (DD bulan YYYY)
 * @param {string} datePickerValue - ISO date format (YYYY-MM-DD)
 * @returns {string} Indonesian formatted date (DD bulan YYYY)
 */
function convertDatePickerToIndonesian(datePickerValue) {
    if (!datePickerValue) return "";

    const bulanMap = [
        "januari",
        "februari",
        "maret",
        "april",
        "mei",
        "juni",
        "juli",
        "agustus",
        "september",
        "oktober",
        "november",
        "desember",
    ];

    try {
        const [tahun, bulan, hari] = datePickerValue.split("-");
        const bulanIdx = parseInt(bulan) - 1;
        const hariInt = parseInt(hari);

        return `${hariInt} ${bulanMap[bulanIdx]} ${tahun}`;
    } catch (e) {
        console.error("[JADWAL] Error converting date:", e);
        return datePickerValue;
    }
}

/**
 * Handle jadwal seminar form submission
 */
window.__submitJadwal = function (event) {
    const timestamp = new Date().toLocaleTimeString();
    console.log(`[${timestamp}] [JADWAL] ▶️  __submitJadwal called`);

    try {
        event.preventDefault();

        // Get form values
        const tanggalInput = document.getElementById("jadwal-tanggal");
        const durasiJamInput = document.getElementById("jadwal-durasi-jam");
        const durasiMenitInput = document.getElementById("jadwal-durasi-menit");

        console.log(`[${timestamp}] [JADWAL] ✓ Form elements found`);

        const tanggalRaw = tanggalInput?.value?.trim() || "";
        const tanggal = convertDatePickerToIndonesian(tanggalRaw);
        const jam = parseInt(durasiJamInput?.value || "1");
        const menit = parseInt(durasiMenitInput?.value || "50");

        // Get all selected ruangan
        const ruanganSelects = document.querySelectorAll(
            ".jadwal-ruangan-select",
        );
        const ruanganList = [];
        ruanganSelects.forEach((select, idx) => {
            const ruangan_id = select.value;
            if (ruangan_id) {
                ruanganList.push(ruangan_id);
            }
        });

        console.log(
            `[${timestamp}] [JADWAL] Values: tanggal='${tanggal}', ruangan_list='${ruanganList.join(",")}', durasi='${jam}j ${menit}m'`,
        );

        // Validate
        if (!tanggal) {
            alert(
                "❌ Tanggal harus diisi. Silakan pilih tanggal dari kalender.",
            );
            return;
        }

        if (ruanganList.length === 0) {
            alert("❌ Minimal 1 ruangan harus dipilih");
            return;
        }

        // Convert jam + menit to total menit
        const totalMenit = jam * 60 + menit;

        // Build message in format: [jadwal] tanggal: 15 mei 2026 | ruangan: 1,2,3 | durasi: 110
        const message = `[jadwal] tanggal: ${tanggal} | ruangan: ${ruanganList.join(",")} | durasi: ${totalMenit}`;
        console.log(`[${timestamp}] [JADWAL] Message: ${message}`);

        // Set to userInput and send
        const userInput = document.getElementById("userInput");
        if (userInput) {
            userInput.value = message;
            console.log(`[${timestamp}] [JADWAL] Calling sendMessage()...`);

            const submitBtn = event.target?.closest(".jadwal-submit-btn");
            setJadwalSubmitButtonLoading(submitBtn);

            window.sendMessage();
        } else {
            console.error(
                `[${timestamp}] [JADWAL] ❌ userInput element not found`,
            );
            restoreJadwalSubmitButton();
            alert("❌ Error: userInput element not found");
        }
    } catch (error) {
        console.error(`[${timestamp}] [JADWAL] ❌ Exception:`, error);
        restoreJadwalSubmitButton();
        alert(`❌ Error: ${error.message}`);
    }
};
