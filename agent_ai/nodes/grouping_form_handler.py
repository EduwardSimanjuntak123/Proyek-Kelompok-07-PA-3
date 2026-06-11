"""
Handler untuk Form Grouping - FIXED v6 (State Persistence)
Perbaikan:
- State form tersimpan di localStorage
- Radio button dan card selection sinkron
- Form restore saat dimuat ulang
"""

import json
import re
from typing import Dict, List

NIM_PATTERN = r'(?:nim\s*)?(\d{5,})'

try:
    from core.database import SessionLocal
    from models.mahasiswa import Mahasiswa
    _DB_AVAILABLE = True
except Exception:
    _DB_AVAILABLE = False


def _count_available_students(prodi_id) -> int:
    if not _DB_AVAILABLE or not prodi_id:
        return 0
    try:
        session = SessionLocal()
        count = session.query(Mahasiswa).filter(
            Mahasiswa.prodi_id == prodi_id,
            Mahasiswa.status == "Aktif"
        ).count()
        session.close()
        return count
    except Exception:
        return 0


_FORM_SCRIPT = """
<script>
(function () {
    "use strict";

    /* ── Storage keys ─────────────────────────────────────────────── */
    var STORAGE_KEY = 'gf_form_state';
    
    /* ── State management ─────────────────────────────────────────── */
    
    function saveFormState(state) {
        try {
            localStorage.setItem(STORAGE_KEY, JSON.stringify(state));
        } catch(e) {
            console.warn('Failed to save form state:', e);
        }
    }
    
    function loadFormState() {
        try {
            var saved = localStorage.getItem(STORAGE_KEY);
            if (saved) {
                return JSON.parse(saved);
            }
        } catch(e) {
            console.warn('Failed to load form state:', e);
        }
        return null;
    }
    
    function getCurrentFormState(form) {
        var method = getChecked("gf_method") || "auto";
        var rangeVal = getChecked("gf_group_range") || "3-5";
        var constraints = form.querySelector(".gf-constraints") ? form.querySelector(".gf-constraints").value : "";
        
        return {
            method: method,
            group_range: rangeVal,
            constraints: constraints,
            timestamp: Date.now()
        };
    }
    
    function restoreFormState(form, state) {
        if (!state) return false;
        
        // Restore method
        var methodInput = form.querySelector('input[name="gf_method"][value="' + state.method + '"]');
        if (methodInput) {
            methodInput.checked = true;
        }
        
        // Restore group range
        var rangeInput = form.querySelector('input[name="gf_group_range"][value="' + state.group_range + '"]');
        if (rangeInput) {
            rangeInput.checked = true;
        }
        
        // Restore constraints text
        if (state.constraints !== undefined) {
            var textarea = form.querySelector(".gf-constraints");
            if (textarea) {
                textarea.value = state.constraints;
            }
        }
        
        // Refresh visual cards
        refreshMethodCards();
        refreshRangeCards();
        
        return true;
    }

    /* ── Helper untuk SweetAlert dengan fallback ─────────────────────────── */
    
    function showConfirmDialog(options) {
        return new Promise(function(resolve, reject) {
            if (typeof Swal !== 'undefined' && Swal && typeof Swal.fire === 'function') {
                Swal.fire(options).then(function(result) {
                    resolve(result);
                }).catch(function(error) {
                    console.warn('SweetAlert error:', error);
                    showNativeConfirm(options).then(resolve);
                });
            } else {
                console.warn('SweetAlert tidak tersedia, menggunakan confirm biasa');
                showNativeConfirm(options).then(resolve);
            }
        });
    }
    
    function showNativeConfirm(options) {
        return new Promise(function(resolve) {
            var message = '';
            if (options.html) {
                var tempDiv = document.createElement('div');
                tempDiv.innerHTML = options.html;
                message = tempDiv.textContent || tempDiv.innerText || '';
            } else if (options.text) {
                message = options.text;
            } else if (options.title) {
                message = options.title;
            }
            
            var confirmed = confirm(message + '\\n\\nKlik OK untuk melanjutkan, Cancel untuk membatalkan.');
            resolve({ isConfirmed: confirmed });
        });
    }

    /* ── Helpers ─────────────────────────────────────────────────── */

    function getEl(id) {
        return document.getElementById(id);
    }

    function getChecked(name) {
        var node = document.querySelector(
            'input[name="' + name + '"]:checked'
        );
        return node ? node.value : null;
    }

    /* ── Card highlight ──────────────────────────────────────────── */

    function refreshMethodCards() {
        var val = getChecked("gf_method");
        document.querySelectorAll(".gf-method-card").forEach(function (card) {
            var input = card.querySelector("input");
            if (input) {
                card.classList.toggle("selected", input.value === val);
            }
        });
    }

    function refreshRangeCards() {
        var val = getChecked("gf_group_range");
        document.querySelectorAll(".gf-range-card").forEach(function (card) {
            var input = card.querySelector("input");
            if (input) {
                card.classList.toggle("selected", input.value === val);
            }
        });
    }
    
    function refreshAllCards() {
        refreshMethodCards();
        refreshRangeCards();
    }

    /* ── Event listeners untuk cards ───────────────────────────────── */
    
    function attachCardEvents() {
        document.querySelectorAll('input[name="gf_method"]').forEach(function (r) {
            r.removeEventListener("change", onMethodChange);
            r.addEventListener("change", onMethodChange);
        });

        document.querySelectorAll('input[name="gf_group_range"]').forEach(function (r) {
            r.removeEventListener("change", onRangeChange);
            r.addEventListener("change", onRangeChange);
        });
    }
    
    function onMethodChange() {
        refreshMethodCards();
        // Save state after change
        var form = document.querySelector('.grouping-config-form');
        if (form) {
            var state = getCurrentFormState(form);
            saveFormState(state);
        }
    }
    
    function onRangeChange() {
        refreshRangeCards();
        // Save state after change
        var form = document.querySelector('.grouping-config-form');
        if (form) {
            var state = getCurrentFormState(form);
            saveFormState(state);
        }
    }
    
    // Function to save constraints on input
    function onConstraintsChange() {
        var form = document.querySelector('.grouping-config-form');
        if (form) {
            var state = getCurrentFormState(form);
            saveFormState(state);
        }
    }

    /* ── Normalize constraints ───────────────────────────────────── */

    function normalize(raw) {
        if (!raw) return "";
        return raw
            .replace(/mahasiswa\\s+dengan\\s+nim\\s+/gi, "NIM ")
            .replace(/mahasiswa\\s+nim\\s+/gi, "NIM ")
            .replace(
                /buat\\s+satu\\s+kelompok\\s+dengan\\s+(?:mahasiswa\\s+)?(?:nim\\s+)?/gi,
                "harus sekelompok dengan NIM "
            )
            .replace(
                /harus\\s+satu\\s+kelompok\\s+dengan\\s+nim\\s+/gi,
                "harus sekelompok dengan NIM "
            )
            .replace(
                /harus\\s+satu\\s+kelompok\\s+dengan\\s+/gi,
                "harus sekelompok dengan "
            );
    }

    /* ── Build prompt ────────────────────────────────────────────── */

    function buildPrompt(method, minSize, maxSize, constraints) {
        var label =
            method === "by_grades" ? "berdasarkan nilai" : "acak otomatis";

        var prompt = "Buatkan kelompok metode: " + label;
        prompt +=
            " minimal " +
            minSize +
            " orang, maksimal " +
            maxSize +
            " orang per kelompok";

        if (constraints) {
            var lines = constraints.split("\\n");
            for (var i = 0; i < lines.length; i++) {
                var line = lines[i].trim();
                if (line) prompt += " " + line;
            }
        }

        return prompt;
    }

    /* ── Send to chat ────────────────────────────────────────────── */

    function sendToChat(prompt, payload) {
        if (typeof window.__sendChatMessage === 'function') {
            window.__sendChatMessage(prompt);
            return;
        }

        window.dispatchEvent(
            new CustomEvent(
                'grouping-form-submit',
                {
                    detail: payload,
                    bubbles: true
                }
            )
        );

        var chatInput = document.querySelector(
            'textarea[data-id="chat-input"], textarea[placeholder*="chat"], textarea[placeholder*="pesan"], textarea'
        );

        if (!chatInput) {
            console.warn('Chat input tidak ditemukan');
            showNotification('Chat input tidak ditemukan, silakan coba lagi.', 'error');
            return;
        }

        var nativeSetter = Object.getOwnPropertyDescriptor(
            HTMLTextAreaElement.prototype,
            'value'
        );

        if (nativeSetter && nativeSetter.set) {
            nativeSetter.set.call(chatInput, prompt);
        } else {
            chatInput.value = prompt;
        }

        chatInput.dispatchEvent(new Event('input', { bubbles: true }));
        chatInput.dispatchEvent(new Event('change', { bubbles: true }));

        var sendBtn = document.querySelector(
            'button[type="submit"], button[aria-label*="kirim"], button[aria-label*="send"], .send-button'
        );

        if (sendBtn && !sendBtn.disabled) {
            setTimeout(function() {
                sendBtn.click();
            }, 100);
        } else {
            console.warn('Tombol send tidak ditemukan');
        }
    }
    
    function showNotification(message, type) {
        if (typeof Swal !== 'undefined' && Swal && typeof Swal.fire === 'function') {
            Swal.fire({
                icon: type === 'error' ? 'error' : 'info',
                title: type === 'error' ? 'Error' : 'Informasi',
                text: message,
                confirmButtonColor: '#4C9BC8',
                timer: 2000,
                showConfirmButton: false
            });
        } else {
            alert(message);
        }
    }

    /* ── Reset form ───────────────────────────────────────────────────── */

    function resetForm(form) {
        // Reset method ke default
        var defaultMethod = form.querySelector('input[name="gf_method"][value="auto"]');
        if (defaultMethod) {
            defaultMethod.checked = true;
        }
        
        // Reset range ke default
        var defaultRange = form.querySelector('input[name="gf_group_range"][value="3-5"]');
        if (defaultRange) {
            defaultRange.checked = true;
        }
        
        // Reset textarea
        var textarea = form.querySelector(".gf-constraints");
        if (textarea) textarea.value = "";
        
        // Refresh visual
        refreshAllCards();
        
        // Clear saved state
        localStorage.removeItem(STORAGE_KEY);
        
        showNotification('Form telah direset ke default', 'info');
    }

    /* ── Reset button handler ───────────────────────────────────── */

    document.addEventListener("click", function (e) {
        var resetBtn = e.target.closest(".gf-reset-btn");
        if (!resetBtn) return;

        var form = resetBtn.closest(".grouping-config-form");
        if (form) {
            resetForm(form);
        }
    });

    /* ── Submit handler ──────────────────────────────────────────────────── */

    document.addEventListener("click", function (e) {
        var submitBtn = e.target.closest(".gf-submit-btn");
        if (!submitBtn) return;

        var form = submitBtn.closest(".grouping-config-form");
        if (!form) return;

        var method = getChecked("gf_method") || "auto";
        var rangeVal = getChecked("gf_group_range") || "3-5";
        var parts = rangeVal.split("-");
        var minSize = parseInt(parts[0], 10);
        var maxSize = parseInt(parts[1], 10);
        
        if (isNaN(minSize) || isNaN(maxSize) || minSize > maxSize) {
            showNotification('Ukuran kelompok tidak valid', 'error');
            return;
        }

        var ta = form.querySelector(".gf-constraints");
        var rawConstraints = ta ? ta.value.trim() : "";
        var constraints = normalize(rawConstraints);
        var prompt = buildPrompt(method, minSize, maxSize, constraints);

        var payload = {
            method: method,
            min_size: minSize,
            max_size: maxSize,
            constraints: constraints,
            prompt: prompt,
        };

        var metodeTeks = method === "by_grades" ? "Berdasarkan nilai" : "Acak otomatis";
        var ukuranTeks = minSize + " - " + maxSize + " orang per kelompok";
        var constraintHtml = constraints ? "<br><b>Permintaan khusus:</b> " + constraints.split("\\n").join(", ") : "";

        showConfirmDialog({
            icon: "question",
            title: "Konfirmasi konfigurasi",
            html: '<div style="font-size:13px;line-height:1.9;color:#374151">' +
                  "<b>Metode:</b> " + metodeTeks + "<br>" +
                  "<b>Ukuran kelompok:</b> " + ukuranTeks +
                  constraintHtml + "</div>",
            confirmButtonText: "Generate kelompok",
            confirmButtonColor: "#4C9BC8",
            showCancelButton: true,
            cancelButtonText: "Batal",
            cancelButtonColor: "#9ca3af",
            reverseButtons: true
        }).then(function (result) {
            if (result.isConfirmed) {
                sendToChat(prompt, payload);
            }
        });
    });
    
    /* ── Initialization ─────────────────────────────────────────────────── */
    
    function initForm() {
        var form = document.querySelector('.grouping-config-form');
        if (!form) return;
        
        // Attach events
        attachCardEvents();
        
        // Attach save event for textarea
        var textarea = form.querySelector(".gf-constraints");
        if (textarea) {
            textarea.removeEventListener('input', onConstraintsChange);
            textarea.addEventListener('input', onConstraintsChange);
        }
        
        // Try to restore saved state
        var savedState = loadFormState();
        if (savedState) {
            restoreFormState(form, savedState);
        } else {
            // Ensure default selections are visually correct
            refreshAllCards();
        }
    }
    
    // Run initialization when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initForm);
    } else {
        initForm();
    }
    
})();
</script>
"""


class GroupingFormHandler:

    @staticmethod
    def detect_grouping_request(prompt: str) -> bool:
        prompt_lower = prompt.lower().strip()

        grouping_keywords = [
            "buatkan kelompok",
            "buat kelompok",
            "bagi kelompok",
            "kelompokkan mahasiswa",
            "pembagian kelompok",
            "susun kelompok",
            "buat grup",
            "buatkan grup",
            "buat satu kelompok",
        ]

        if not any(kw in prompt_lower for kw in grouping_keywords):
            return False

        detail_indicators = [
            "berdasarkan nilai",
            r"\d+\s+orang",
            "minimal",
            "maksimal",
            "min ",
            "max ",
            "dari nim",
            "sampai nim",
            "harus satu",
            "tidak boleh sekelompok",
            "sekelompok dengan",
            "per kelompok",
            r"\d+\s+kelompok",
            "metode:",
            "ukuran:",
            "acak otomatis",
        ]

        for indicator in detail_indicators:
            if re.search(indicator, prompt_lower):
                return False

        return True

    @staticmethod
    def generate_form_html(context: Dict) -> str:
        prodi_id    = context.get("prodi_id")
        kategori_pa = context.get("kategori_pa", "PA")
        total       = _count_available_students(prodi_id)
        total_label = (
            f"{total} mahasiswa tersedia"
            if total > 0
            else "Data mahasiswa tersedia"
        )

        html_part = f"""
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
.gf-wrap{{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;max-width:560px;margin:8px 0 16px}}
.gf-head{{display:flex;align-items:center;gap:12px;padding:18px 20px;background:#f9fafb;border:0.5px solid #e5e7eb;border-radius:12px 12px 0 0;border-bottom:none}}
.gf-head-icon{{width:40px;height:40px;border-radius:8px;background:#e0f0fa;display:flex;align-items:center;justify-content:center;flex-shrink:0}}
.gf-head-icon i{{font-size:20px;color:#4C9BC8}}
.gf-head-title{{font-size:15px;font-weight:500;color:#111827;margin:0}}
.gf-head-sub{{font-size:12px;color:#6b7280;margin:3px 0 0}}
.gf-body{{background:#fff;border:0.5px solid #e5e7eb;border-radius:0 0 12px 12px;padding:20px}}
.gf-section{{margin-bottom:20px}}
.gf-section-label{{display:flex;align-items:center;gap:8px;font-size:13px;font-weight:500;color:#6b7280;margin:0 0 10px}}
.gf-section-label i{{font-size:16px}}
.gf-optional{{font-weight:400;color:#9ca3af;font-size:12px;margin-left:4px}}
.gf-divider{{border:none;border-top:0.5px solid #e5e7eb;margin:0 0 20px}}
.gf-method-card,.gf-range-card{{display:flex;align-items:center;gap:10px;padding:11px 14px;border:0.5px solid #e5e7eb;border-radius:8px;cursor:pointer;margin-bottom:8px;transition:all 0.2s ease}}
.gf-method-card{{align-items:flex-start}}
.gf-method-card:hover,.gf-range-card:hover{{border-color:#4C9BC8;background:#E6F1FB;transform:translateY(-1px)}}
.gf-method-card.selected,.gf-range-card.selected{{border-color:#4C9BC8;background:#E6F1FB;box-shadow:0 2px 4px rgba(76,155,200,0.1)}}
.gf-method-card input,.gf-range-card input{{accent-color:#4C9BC8;flex-shrink:0;margin-top:2px}}
.gf-range-card input{{margin-top:0}}
.gf-card-title{{font-size:13px;font-weight:500;color:#111827;margin:0}}
.gf-card-desc{{font-size:12px;color:#6b7280;margin:2px 0 0}}
.gf-method-card.selected .gf-card-title{{color:#185FA5}}
.gf-range-card.selected span{{color:#185FA5;font-weight:500}}
.gf-range-card span{{font-size:13px;color:#374151}}
.gf-hint{{font-size:12px;color:#6b7280;margin:0 0 8px;line-height:1.6}}
.gf-hint code{{font-size:11px;background:#f3f4f6;padding:1px 5px;border-radius:4px;font-family:monospace}}
.gf-textarea{{width:100%;padding:10px;border:0.5px solid #d1d5db;border-radius:8px;font-size:13px;resize:vertical;box-sizing:border-box;font-family:inherit;color:#374151;background:#fff;outline:none;line-height:1.5;min-height:76px}}
.gf-textarea:focus{{border-color:#4C9BC8;box-shadow:0 0 0 3px #E6F1FB}}
.gf-actions{{display:flex;gap:8px;justify-content:flex-end;margin-top:8px}}
.gf-reset-btn{{display:inline-flex;align-items:center;gap:6px;padding:8px 18px;background:transparent;color:#6b7280;border:0.5px solid #d1d5db;border-radius:8px;cursor:pointer;font-size:13px;font-weight:500;transition:all 0.2s ease}}
.gf-reset-btn:hover{{background:#f9fafb;color:#111827;border-color:#4C9BC8}}
.gf-submit-btn{{display:inline-flex;align-items:center;gap:6px;padding:8px 22px;background:#4C9BC8;color:#fff;border:none;border-radius:8px;cursor:pointer;font-size:13px;font-weight:500;transition:all 0.2s ease}}
.gf-submit-btn:hover{{background:#185FA5;transform:translateY(-1px);box-shadow:0 2px 8px rgba(76,155,200,0.3)}}
</style>

<div class="gf-wrap" data-component="grouping-form">
    <div class="gf-head">
        <div class="gf-head-icon">
            <i class="ti ti-users-group" aria-hidden="true"></i>
        </div>
        <div>
            <p class="gf-head-title">Konfigurasi Kelompok</p>
            <p class="gf-head-sub">PA - {kategori_pa}</p>
        </div>
    </div>

    <div class="gf-body">
        <form class="grouping-config-form" onsubmit="return false">
            <div class="gf-section">
                <p class="gf-section-label">
                    <i class="ti ti-adjustments-horizontal" aria-hidden="true"></i>
                    Metode pembagian
                </p>

                <label class="gf-method-card selected" for="m_auto">
                    <input type="radio" id="m_auto" name="gf_method" value="auto" checked>
                    <div>
                        <p class="gf-card-title">
                            <i class="ti ti-shuffle" style="font-size:14px;vertical-align:-1px;margin-right:4px" aria-hidden="true"></i>
                            Acak otomatis
                        </p>
                        <p class="gf-card-desc">Bagi mahasiswa secara acak ke dalam kelompok</p>
                    </div>
                </label>

                <label class="gf-method-card" for="m_grades">
                    <input type="radio" id="m_grades" name="gf_method" value="by_grades">
                    <div>
                        <p class="gf-card-title">
                            <i class="ti ti-chart-bar" style="font-size:14px;vertical-align:-1px;margin-right:4px" aria-hidden="true"></i>
                            Berdasarkan nilai
                        </p>
                        <p class="gf-card-desc">Kelompok seimbang berdasarkan rata-rata nilai mahasiswa</p>
                    </div>
                </label>
            </div>

            <hr class="gf-divider">

            <div class="gf-section">
                <p class="gf-section-label">
                    <i class="ti ti-users" aria-hidden="true"></i>
                    Jumlah anggota per kelompok
                </p>

                <label class="gf-range-card selected" for="r_3_5">
                    <input type="radio" id="r_3_5" name="gf_group_range" value="3-5" checked>
                    <span>3 - 5 orang per kelompok</span>
                </label>

                <label class="gf-range-card" for="r_4_5">
                    <input type="radio" id="r_4_5" name="gf_group_range" value="4-5">
                    <span>4 - 5 orang per kelompok</span>
                </label>

                <label class="gf-range-card" for="r_4_6">
                    <input type="radio" id="r_4_6" name="gf_group_range" value="4-6">
                    <span>4 - 6 orang per kelompok</span>
                </label>

                <label class="gf-range-card" for="r_5_6">
                    <input type="radio" id="r_5_6" name="gf_group_range" value="5-6">
                    <span>5 - 6 orang per kelompok</span>
                </label>
            </div>

            <hr class="gf-divider">

            <div class="gf-section" style="margin-bottom:16px">
                <p class="gf-section-label">
                    <i class="ti ti-lock-open" aria-hidden="true"></i>
                    Permintaan khusus
                    <span class="gf-optional">(opsional)</span>
                </p>
                <p class="gf-hint">
                    Contoh:<br>
                    &bull; <code>NIM001 harus sekelompok dengan NIM002</code><br>
                    &bull; <code>NIM003 tidak boleh sekelompok dengan NIM004</code>
                </p>
                <textarea
                    class="gf-textarea gf-constraints"
                    rows="3"
                    placeholder="Ketik permintaan khusus di sini..."></textarea>
            </div>

            <div class="gf-actions">
                <button type="button" class="gf-reset-btn">
                    <i class="ti ti-rotate" aria-hidden="true"></i>
                    Reset
                </button>
                <button type="button" class="gf-submit-btn">
                    <i class="ti ti-arrow-right" aria-hidden="true"></i>
                    Generate kelompok
                </button>
            </div>
        </form>
    </div>
</div>
"""
        return html_part + _FORM_SCRIPT

    @staticmethod
    def parse_form_submission(form_data: Dict) -> Dict:
        method = (
            form_data.get("method")
            or form_data.get("gf_method")
            or "auto"
        )

        def _int(keys, default):
            for k in keys:
                v = form_data.get(k)
                if v is not None:
                    try:
                        return int(v)
                    except (ValueError, TypeError):
                        pass
            return default

        min_size = _int(["min_size", "minSize", "gf_min_size"], 4)
        max_size = _int(["max_size", "maxSize", "gf_max_size"], 6)

        constraints_text = (
            form_data.get("constraints")
            or form_data.get("gf_constraints")
            or ""
        )

        return {
            "method": method,
            "min_size": min_size,
            "max_size": max_size,
            "constraints": GroupingFormHandler._parse_constraints(constraints_text),
        }

    @staticmethod
    def _normalise_constraint_text(text: str) -> str:
        t = text
        t = re.sub(r'\bmahasiswa\s+dengan\s+nim\b', 'NIM', t, flags=re.IGNORECASE)
        t = re.sub(r'\bmahasiswa\s+nim\b', 'NIM', t, flags=re.IGNORECASE)

        for pattern in [
            r'buat\s+satu\s+kelompok\s+dengan',
            r'buat\s+sekelompok\s+dengan',
            r'gabungkan\s+dengan',
            r'satu\s+kelompok\s+dengan',
            r'satu\s+grup\s+dengan',
            r'satu\s+tim\s+dengan',
            r'bersama\s+dengan',
            r'digabung\s+dengan',
            r'dalam\s+satu\s+kelompok\s+dengan',
            r'dalam\s+satu\s+grup\s+dengan',
            r'dalam\s+satu\s+tim\s+dengan',
            r'harus\s+(?:satu|1)\s+kelompok\s+dengan',
        ]:
            t = re.sub(pattern, 'harus sekelompok dengan', t, flags=re.IGNORECASE)

        for pattern in [
            r'tidak\s+boleh\s+satu\s+kelompok\s+dengan',
            r'tidak\s+boleh\s+satu\s+grup\s+dengan',
            r'tidak\s+boleh\s+satu\s+tim\s+dengan',
            r'jangan\s+satu\s+kelompok\s+dengan',
            r'jangan\s+satu\s+grup\s+dengan',
            r'jangan\s+satu\s+tim\s+dengan',
            r'pisahkan\s+dengan',
            r'berbeda\s+kelompok\s+dengan',
            r'tidak\s+bisa\s+(?:sekelompok|satu\s+kelompok)',
        ]:
            t = re.sub(pattern, 'tidak boleh sekelompok dengan', t, flags=re.IGNORECASE)

        return t

    @staticmethod
    def _parse_constraints(text: str) -> List[Dict]:
        constraints: List[Dict] = []
        if not text:
            return constraints

        for line in re.split(r"\n|\s*\|\s*", text.strip()):
            line = line.strip()
            if not line or line.startswith("#"):
                continue

            norm = GroupingFormHandler._normalise_constraint_text(line)

            m = re.search(
                r'(?:nim\s*)?(\d{5,}).*?tidak\s+boleh\s+sekelompok\s+dengan.*?(?:nim\s*)?(\d{5,})',
                norm, re.IGNORECASE
            )
            if m:
                constraints.append({
                    "type": "must_apart",
                    "student1": _clean_nim(m.group(1)),
                    "student2": _clean_nim(m.group(2)),
                })
                continue

            m = re.search(
                r'(?:nim\s*)?(\d{5,}).*?harus\s+berbeda\s+kelompok.*?(?:nim\s*)?(\d{5,})',
                norm, re.IGNORECASE
            )
            if m:
                constraints.append({
                    "type": "must_apart",
                    "student1": _clean_nim(m.group(1)),
                    "student2": _clean_nim(m.group(2)),
                })
                continue

            m = re.search(
                r'(?:nim\s*)?(\d{5,}).*?harus\s+sekelompok\s+dengan.*?(?:nim\s*)?(\d{5,})',
                norm, re.IGNORECASE
            )
            if m:
                constraints.append({
                    "type": "must_together",
                    "student1": _clean_nim(m.group(1)),
                    "student2": _clean_nim(m.group(2)),
                })
                continue

            numbers = re.findall(r'\d{5,}', norm)
            if len(numbers) >= 3:
                lower = norm.lower()
                if any(kw in lower for kw in ['satu kelompok', 'satu grup', 'satu tim', 'sekelompok']):
                    constraints.append({
                        "type": "must_together_group",
                        "students": [_clean_nim(x) for x in numbers],
                    })
                    continue

            nims = re.findall(r'\d{5,}', line)
            if len(nims) >= 2:
                constraints.append({
                    "type": "must_together",
                    "student1": _clean_nim(nims[0]),
                    "student2": _clean_nim(nims[1]),
                })

        return constraints

    @staticmethod
    def build_grouping_prompt(form_spec: Dict) -> str:
        if form_spec.get("_ready_prompt"):
            return form_spec["_ready_prompt"]

        method = form_spec.get("method", "auto")
        method_label = {"auto": "acak otomatis", "by_grades": "berdasarkan nilai"}.get(
            method, "acak otomatis"
        )

        mn = form_spec.get("min_size", 4)
        mx = form_spec.get("max_size", 6)
        prompt = f"Buatkan kelompok metode: {method_label} minimal {mn} orang, maksimal {mx} orang per kelompok"

        for c in form_spec.get("constraints", []):
            if c["type"] == "must_together":
                prompt += f" NIM {c['student1']} harus sekelompok dengan NIM {c['student2']}"
            elif c["type"] == "must_apart":
                prompt += f" NIM {c['student1']} tidak boleh sekelompok dengan NIM {c['student2']}"

        return prompt


def _clean_nim(raw: str) -> str:
    return re.sub(r'(?i)^nim\s*', '', raw.strip())