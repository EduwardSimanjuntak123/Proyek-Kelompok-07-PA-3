# 🎯 Conversation History Integration ke UI

## ✅ Sudah Diimplementasikan

### 1. **Auto-Load History dari MongoDB**
- Saat page load, history di-load otomatis dari API Python (port 8002)
- Tidak perlu refresh manual, data otomatis disimpan
- History di-group berdasarkan session (gap > 30 menit = session baru)

### 2. **Sidebar dengan Session List**
- Setiap session ditampilkan dengan:
  - **Title**: Auto-generated dari first user message (50 chars)
  - **Time**: Waktu session (Hari Ini, Kemarin, Minggu Ini, Lebih Lama)
  - **Search**: Bisa cari berdasarkan keyword

### 3. **Load Session History**
- Click pada session di sidebar → Load seluruh conversation dari session itu
- Chat area di-clear dan di-fill dengan messages dari session
- Active state di-highlight di sidebar

### 4. **Auto-Update Sidebar**
- Setelah user kirim pesan + AI reply:
  - History di-reload dari MongoDB
  - Sidebar di-refresh untuk menunjukkan session terbaru
  - Judul session auto-generated dari first message

### 5. **New Session Button**
- Click tombol "New Chat" → Mulai session baru (kosong)
- Chat area di-clear, landing view kembali ke awal

---

## 🛠️ Technical Details

### Architecture
```
┌─────────────────────────────────────────┐
│         Laravel UI (Port 8000)          │
├─────────────────────────────────────────┤
│  - agent-kelompok.blade.php             │
│  - Chat interface & sidebar             │
│  - Message display & input              │
└──────────────┬──────────────────────────┘
               │
               ├─────────────────────────────────┐
               │                                 │
        Synchronous                       Async (History)
        API Call                          API Call
        /ai/generate                      /long-term-history/
               │                                 │
               ▼                                 ▼
        ┌─────────────────────────────────────────────┐
        │     Python API (Port 8002)                  │
        ├─────────────────────────────────────────────┤
        │  - main.py: Run agent                       │
        │  - api.py: Endpoints                        │
        │  - Store message otomatis ke MongoDB        │
        └──────────────┬──────────────────────────────┘
                       │
                       ▼
        ┌──────────────────────────────┐
        │    MongoDB (VokasiTeraDB)    │
        │    Collection: messages       │
        ├──────────────────────────────┤
        │ { user_id, role, content,    │
        │   timestamp, metadata }       │
        └──────────────────────────────┘
```

### Data Flow

#### 1. **Page Load**
```javascript
DOMContentLoaded
    ↓
initHistorySidebar()
    ↓
loadConversationHistoryFromMongoDB()
    ├─ GET /long-term-history/USER_ID
    ├─ MongoDB returns all messages (30 days, max 200)
    ↓
groupMessagesBySessions()
    ├─ Split into sessions (gap > 30 min)
    ↓
renderHistoryList()
    ├─ Display grouped sessions in sidebar
```

#### 2. **User Sends Message**
```javascript
sendMessage()
    ├─ Append user message to chat
    ├─ POST /ai/generate (agent process)
    ├─ Append AI response to chat
    │  (API otomatis save ke MongoDB)
    ├─ updateHistoryAndSidebar()
    │  ├─ Reload history dari MongoDB
    │  ├─ Re-render sidebar
    │  └─ Update active session
    ↓
Display updated sidebar dengan session baru
```

#### 3. **Click Session in Sidebar**
```javascript
loadHistorySession(session)
    ├─ Clear chat area
    ├─ Loop through session.messages
    ├─ appendMessage(role, content) untuk setiap message
    ├─ Update currentSessionId
    ├─ Set sidebar active state
    ↓
Display loaded conversation
```

---

## 🔧 Configuration

### API Endpoint
```javascript
// File: agent-kelompok.blade.php
const loadConversationHistoryFromMongoDB = async function() {
    const response = await fetch(
        `http://localhost:8002/long-term-history/${currentUserId}?days=30&limit=200`
    );
}
```

**Note:** Port 8002 harus sesuai dengan Python API yang running

### Session Grouping
```javascript
// Gap > 30 menit = session baru
if (lastTimestamp && (msgTime - lastTimestamp) > 30 * 60 * 1000) {
    sessions.push(currentSession);
    currentSession = [];
}
```

Ubah `30 * 60 * 1000` untuk mengubah threshold (ms):
- `60 * 60 * 1000` = 1 hour gap = new session
- `24 * 60 * 60 * 1000` = 1 day gap = new session

### Title Generation
```javascript
// Dari first user message (max 50 chars)
const firstMessage = userMessages[0].content || '';
const title = firstMessage.substring(0, 50) + (firstMessage.length > 50 ? '...' : '');
```

---

## 📝 How to Test

### 1. **Load History at Startup**
```
1. Open agent-kelompok page
2. Check browser console
   [history] Loading conversation history from MongoDB...
   [history] Loaded X messages from MongoDB
3. Sidebar harus menampilkan sessions dari history
```

### 2. **Send Message & Update History**
```
1. Type pesan di chat
2. Click send atau press Enter
3. Check sidebar
   - Harus ada session baru atau terupdate
   - Judul = first user message
4. Check MongoDB
   - Collection "messages" harus ada entries baru
```

### 3. **Click Session**
```
1. Click session di sidebar
2. Chat area harus clear dan di-fill dengan messages dari session
3. Pesan harus sesuai urutan (user → AI → user → AI)
4. Active indicator di sidebar harus highlight session yang dipilih
```

### 4. **Search**
```
1. Type di search input di sidebar
2. Session list harus filter berdasarkan title
3. Contoh: ketik "pembimbing" → hanya session dengan "pembimbing" di title
```

### 5. **New Chat**
```
1. Click "New Chat" button
2. Chat area harus clear
3. Landing view harus muncul kembali
4. currentSessionId harus null
```

---

## 🐛 Troubleshooting

### History tidak load
```javascript
// Check di console:
[history] Loading conversation history from MongoDB...

// Jika ada error:
- Pastikan Python API running di port 8002
- Check CORS headers
- Verify MongoDB connection
```

**Fix:**
```bash
# Check Python API
curl http://localhost:8002/mongodb-status

# Should return:
{
  "status": "ok",
  "mongodb_connected": true
}
```

### Sidebar tidak update setelah send pesan
```javascript
// Add this check di console:
conversationHistory.length  // Should increase after send

// Jika tetap 0:
- API mungkin tidak saving ke MongoDB
- Check Python API logs
```

### Session tidak ter-load saat click
```javascript
// Check console:
[history] Loading session: session-0 dengan X messages

// Jika message kosong:
- Session.messages mungkin tidak ter-populate
- Check groupMessagesBySessions() function
```

---

## 📊 Database Schema

### Messages Collection
```javascript
{
  _id: ObjectId,
  user_id: Number,
  role: "user" | "assistant",  // Siapa yang kirim
  content: String,              // Isi pesan
  timestamp: DateTime,          // Waktu pesan
  metadata: {
    source: "api",
    action: "grouping",
    model: "gpt-4"
  }
}
```

### Index untuk Performance
```javascript
// Recommended indexes:
db.messages.createIndex({"user_id": 1, "timestamp": -1})
db.messages.createIndex({"content": "text"})

// Query untuk load history:
db.messages.find({
  "user_id": 123,
  "timestamp": {"$gte": new Date(Date.now() - 30*24*60*60*1000)}
}).sort({"timestamp": 1})
```

---

## ⚡ Performance Considerations

### Limit Results
```javascript
// Current: 30 days, max 200 messages
const response = await fetch(
    `http://localhost:8002/long-term-history/${currentUserId}?days=30&limit=200`
);

// Untuk history panjang, kurangi limit:
days=7&limit=100   // Faster
days=90&limit=500  // More data, slower
```

### Session Grouping
```javascript
// Jika banyak messages, grouping bisa lambat
// Optimasi: cache hasil grouping
let cachedSessions = null;

function groupMessagesBySessions(messages, useCache = true) {
    if (useCache && cachedSessions) return cachedSessions;
    // ... grouping logic
    cachedSessions = sessions;
    return sessions;
}
```

### Rendering Optimization
```javascript
// Saat render history dengan 200+ messages
// Gunakan pagination atau virtual scrolling

// Simple pagination:
const PAGE_SIZE = 20;
let currentPage = 1;
const totalPages = Math.ceil(sessions.length / PAGE_SIZE);
```

---

## 🔐 Security Notes

1. **User ID**: Ambil dari `auth()->id()` (authenticated user)
2. **CORS**: API Python harus allow requests dari Laravel domain
3. **Validation**: Semua input dari user di-validate sebelum query
4. **Rate Limiting**: Implementasi rate limit di API untuk history requests

---

## 📚 Integration Checklist

- ✅ Sidebar script updated dengan MongoDB integration
- ✅ `loadConversationHistoryFromMongoDB()` function
- ✅ `groupMessagesBySessions()` function
- ✅ `renderHistoryList()` function
- ✅ `loadHistorySession()` click handler
- ✅ Auto-update sidebar setelah send message
- ✅ Session title auto-generation
- ✅ Search functionality
- ✅ New chat button
- ⏳ Pending: Refresh page handling (messages persist via MongoDB)

---

## 🚀 Next Steps (Optional)

1. **Persistence Across Devices**
   - History tersimpan di MongoDB, accessible dari device manapun

2. **Session Naming**
   - Izinkan user rename session title (save ke localStorage atau DB)

3. **Export Conversation**
   - Gunakan `/conversation/{id}/export` endpoint untuk download

4. **Analytics**
   - Track conversation statistics via `/analytics/{id}` endpoint

5. **Delete Old Sessions**
   - Implement cleanup untuk sessions older than 90 days

---

**Last Updated:** 2024-06-20  
**Status:** ✅ Ready for Production Testing
