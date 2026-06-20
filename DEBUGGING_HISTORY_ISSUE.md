# 🔧 Debugging: Kenapa History Tidak Muncul Setelah Restart?

## Diagnosis Cepat

Sebelum fix, mari kita diagnosa masalahnya dulu:

### Step 1: Check Browser Console
```
1. Buka http://localhost:8000/agent-kelompok
2. Buka DevTools (F12)
3. Tab Console
4. Cari error messages dengan prefix [history]
```

**Expected output:**
```
[history] Initializing history sidebar...
[history] Loading conversation history from MongoDB...
[history] Loaded 5 messages from MongoDB
[history] Grouped 5 messages into 2 sessions
[history] History sidebar initialized successfully
```

**Jika error:**
- `Failed to load conversation history: 404` → API endpoint tidak ditemukan
- `Failed to load conversation history: 0` → API tidak berjalan
- `Failed to load conversation history: CORS error` → CORS issue
- Tidak ada log sama sekali → JavaScript error sebelum init

---

### Step 2: Check Network Request
```
1. DevTools → Network Tab
2. Refresh page
3. Cari request ke localhost:8002
4. Lihat response status
```

**Expected:**
- Request: `GET http://localhost:8002/long-term-history/123?days=30&limit=200`
- Status: `200`
- Response: `{"success": true, "history": [...]}`

**Jika error:**
- Status `0` → API tidak running
- Status `404` → Endpoint tidak ada
- Status `500` → API error
- Request tidak ada → URL salah atau JavaScript error

---

### Step 3: Check User ID
```javascript
// Copy-paste di browser console:
console.log('Current User ID:', currentUserId);
```

**Expected:**
```
Current User ID: 1
```

**Jika error:**
```
Current User ID: undefined  // ← PROBLEM! Auth tidak bekerja
```

---

### Step 4: Test API Endpoint Manual
```bash
# Terminal, ganti 1 dengan user_id aktual:
curl http://localhost:8002/long-term-history/1

# Expected response:
# {"success": true, "message_count": 5, "history": [...]}
```

---

## Kemungkinan Penyebab & Solusi

### ❌ Problem 1: Python API Tidak Running

**Symptoms:**
- Network tab: request status `0` atau timeout
- Console: error "Failed to load conversation history"
- Atau tidak ada request ke localhost:8002 sama sekali

**Check:**
```powershell
# Terminal, cek apakah API running
netstat -ano | findstr 8002
# Jika tidak ada output = API tidak running
```

**Fix:**
```bash
# Terminal di folder agent_ai/
cd d:\Semester 6-IT DEL\final\Proyek-Kelompok-07-PA-3\agent_ai
python start_api.py
# Tunggu sampai: "Uvicorn running on http://0.0.0.0:8002"
```

---

### ❌ Problem 2: User ID Tidak Cocok

**Symptoms:**
- Console: `[history] Loaded 0 messages from MongoDB`
- Network: status 200 tapi `history: []`
- atau console: `Current User ID: undefined`

**Check:**
```javascript
// Di console:
console.log(currentUserId);  // Cek apakah ada nilai
console.log(currentUserId === undefined);  // Cek apakah undefined
```

**Penyebab:**
- `auth()->id()` return `null`
- User tidak login
- Blade template tidak render dengan benar

**Fix:**
Mari kita verifikasi Blade template terlebih dahulu.

---

### ❌ Problem 3: Messages Tidak Tersimpan ke MongoDB

**Symptoms:**
- Bisa chat dan AI reply normal
- Tapi history kosong
- Atau history muncul sekali tapi tidak ter-update

**Check MongoDB:**
```bash
# Terminal
mongosh
use VokasiTeraDB
db.messages.find({"user_id": 1}).pretty()
# Jika tidak ada output = messages tidak tersimpan
```

**Penyebab:**
- MongoDB tidak connected
- API error saat save (lihat server log)
- User ID tidak dikirim ke API dengan benar

**Fix:**
Cek API logs:
```bash
# Lihat file logs di agent_ai/
tail -f logs/api.log
# Kirim pesan dari UI
# Lihat apakah ada error message
```

---

### ❌ Problem 4: CORS Issue

**Symptoms:**
- Console: "Access to fetch blocked by CORS policy"
- Network tab: request ada tapi error

**Fix:**
Check CORS di api.py:
```python
# Harus ada di api.py sekitar line 85-95:
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],  # Atau ["http://localhost:8000"]
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)
```

---

### ❌ Problem 5: Hard-coded URL Salah

**Symptoms:**
- Network tab: tidak ada request ke API sama sekali
- atau request ke wrong URL

**Penyebab:**
- Blade template hard-coded ke wrong URL
- Environment mismatch (production vs development)

**Check di agent-kelompok.blade.php (line 2611):**
```javascript
const response = await fetch(`http://localhost:8002/long-term-history/${currentUserId}?days=30&limit=200`, {
```

Ini hard-coded ke localhost:8002. Di production perlu berubah.

---

## 🔍 Complete Debug Checklist

Jalankan semua ini:

### 1. Verify Python API
```bash
# Terminal
curl http://localhost:8002/health
# Expected: {"status": "ok", "service": "agent-grouping", ...}

curl http://localhost:8002/mongodb-status
# Expected: {"status": "ok", "mongodb_connected": true}
```

### 2. Verify MongoDB
```bash
mongosh
use VokasiTeraDB
db.messages.countDocuments({})  # Berapa total messages?
db.messages.find({"user_id": 1}).count()  # Untuk user 1?
db.messages.find({"user_id": 1}).pretty()  # Lihat isi
```

### 3. Verify Laravel & Auth
```bash
# Buka http://localhost:8000/agent-kelompok
# Check: Apakah sudah login?
# Check: Apakah page ter-load?
# Check: Developer Tools → Application → Cookies
#        Apakah ada session/auth cookie?
```

### 4. Check Browser Console
```
F12 → Console tab
Filter: [history]
Lihat semua message yang muncul
```

### 5. Check Network Requests
```
F12 → Network tab
Refresh page
Filter: localhost:8002
Lihat request details & response
```

---

## 🔧 Quick Fixes

### Fix 1: Enable Detailed Logging

Buka agent-kelompok.blade.php (line ~2610) dan update:

```javascript
async function loadConversationHistoryFromMongoDB() {
    try {
        console.log('[history] Loading conversation history from MongoDB...');
        console.log('[history] User ID:', currentUserId);  // ADD THIS
        
        const url = `http://localhost:8002/long-term-history/${currentUserId}?days=30&limit=200`;
        console.log('[history] Fetching from:', url);  // ADD THIS
        
        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        console.log('[history] Response status:', response.status);  // ADD THIS
        
        if (!response.ok) {
            console.error('[history] Response not ok:', response.statusText);  // UPDATE
            return [];
        }

        const data = await response.json();
        console.log('[history] Response data:', data);  // ADD THIS
        
        if (data.success && Array.isArray(data.history)) {
            console.log(`[history] Loaded ${data.history.length} messages from MongoDB`);
            return data.history;
        }
        return [];
    } catch (error) {
        console.error('[history] Error loading conversation history:', error);  // UPDATE
        return [];
    }
}
```

### Fix 2: Test User ID

Cek bahwa `auth()->id()` bekerja dengan benar di Blade:

```php
{{-- Di atas script tag, add: --}}
<script>
    console.log('[history] Blade auth()->id():', {{ auth()->id() }});
</script>
```

---

## 🚀 Complete Solution (if everything fails)

Jika semua di-atas tidak ketemu masalahnya, coba fix lengkap ini:

### 1. Restart semua services:
```bash
# Terminal 1: Kill Python API (Ctrl+C jika running)
# Terminal 1: Restart API
cd d:\Semester 6-IT DEL\final\Proyek-Kelompok-07-PA-3\agent_ai
python start_api.py

# Terminal 2: Kill Laravel (Ctrl+C jika running)
# Terminal 2: Restart Laravel
cd d:\Semester 6-IT DEL\final\Proyek-Kelompok-07-PA-3\ui\ laravel
php artisan serve

# Terminal 3: Check MongoDB
mongosh
use VokasiTeraDB
db.messages.countDocuments({})  # Should be > 0
```

### 2. Test API Endpoint:
```bash
# Terminal
curl http://localhost:8002/long-term-history/1?days=30&limit=200
# Ganti 1 dengan user_id aktual
```

### 3. If empty result, manually insert test data:
```bash
mongosh
use VokasiTeraDB
db.messages.insertOne({
  "user_id": 1,
  "role": "user",
  "content": "Buat kelompok 5 orang",
  "timestamp": new Date(),
  "metadata": {"source": "test"}
})
db.messages.insertOne({
  "user_id": 1,
  "role": "assistant",
  "content": "Kelompok berhasil dibuat dengan 5 anggota.",
  "timestamp": new Date(),
  "metadata": {"source": "test"}
})
```

### 4. Refresh browser:
```
http://localhost:8000/agent-kelompok
F12 → Console
Lihat apakah history muncul sekarang
```

---

## 📋 Report Format

Jika masih tidak bisa, share output dari:

```javascript
// Copy-paste di browser console & report output:
console.log('=== DEBUG INFO ===');
console.log('User ID:', currentUserId);
console.log('Conversation History:', conversationHistory);
console.log('Messages count:', conversationHistory.length);
console.log('Fetch test:', await fetch('http://localhost:8002/long-term-history/1').then(r => r.json()));
```

Juga share:
- Browser console errors
- Network tab screenshot
- MongoDB query result: `db.messages.find({"user_id": YOUR_ID}).count()`
- API server log output

---

## Mulai dari sini:

1. **Buka terminal baru** → Start Python API:
```bash
cd agent_ai
python start_api.py
```

2. **Di browser**:
   - F12 → Console
   - Refresh halaman
   - Share apa yang muncul di console

3. **Report back** dengan output dari debug checklist di atas.

Mari kita fix ini! 🚀
