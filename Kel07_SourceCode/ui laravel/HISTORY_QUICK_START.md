# 🚀 History Integration - Quick Start

## Prerequisites

Pastikan semua service sudah running:

```bash
# Terminal 1: Python API (port 8002)
cd agent_ai/
python start_api.py

# Terminal 2: Laravel (port 8000)
cd ui\ laravel/
php artisan serve

# Terminal 3: MongoDB
# Pastikan MongoDB running (biasanya auto di Windows)
```

**Verify Services:**
```bash
# Check Python API
curl http://localhost:8002/health
# Expected: {"status": "ok", "service": "agent-grouping", ...}

# Check MongoDB
curl http://localhost:8002/mongodb-status
# Expected: {"status": "ok", "mongodb_connected": true}

# Check Laravel
curl http://localhost:8000/login
# Expected: 200 OK
```

---

## How It Works Now

### 1. Page Load
```
User opens: http://localhost:8000/agent-kelompok
    ↓
Browser loads JavaScript
    ↓
DOMContentLoaded event triggers
    ↓
initHistorySidebar() runs
    ↓
loadConversationHistoryFromMongoDB() fetches from MongoDB
    ↓
Sidebar displays conversation history (if any)
```

### 2. User Sends Message
```
User types: "Buat kelompok 5 orang"
    ↓
Click Send
    ↓
appendMessage("user", message) → display di chat
    ↓
POST /ai/generate (Laravel route)
    ↓
Python API processes & saves to MongoDB
    ↓
appendMessage("ai", response) → display di chat
    ↓
updateHistoryAndSidebar() → Reload & refresh sidebar
    ↓
Sidebar now shows new session/updated conversation
```

### 3. User Clicks Old Session
```
Click session di sidebar: "Buat kelompok 5 orang"
    ↓
loadHistorySession(session) runs
    ↓
Chat area cleared
    ↓
All messages from session displayed
    ↓
Session highlighted as active
```

### 4. Refresh Page
```
User refreshes page (Ctrl+R)
    ↓
DOMContentLoaded triggers again
    ↓
initHistorySidebar() loads fresh data from MongoDB
    ↓
Sidebar shows previous conversations (NOT deleted)
    ↓
History persists! ✅
```

---

## Testing Steps

### Test 1: Basic Message & Sidebar Update
```
1. Open http://localhost:8000/agent-kelompok
   - Sidebar should show previous conversations (or empty if first time)

2. Type message: "buat kelompok 5 orang dari mahasiswa IT"
   
3. Click Send
   - Message appears in chat
   - Wait for AI response
   - Sidebar should update dengan session baru
   - Title should be "buat kelompok 5 orang dari mahasiswa IT"

4. Check Browser Console
   - [history] Reloading history after new message...
   - [history] Loaded X messages from MongoDB
   - [history] Grouped X messages into Y sessions
```

### Test 2: Load Previous Conversation
```
1. Send another message to create 2nd conversation
   Example: "siapa pembimbing kelompok A?"

2. Click first conversation di sidebar
   - Chat area should clear
   - Previous messages should appear
   - Title should show "buat kelompok..."

3. Click second conversation
   - Messages should switch to 2nd conversation
   - Title should show "siapa pembimbing..."
```

### Test 3: Persist After Refresh
```
1. Have 2+ conversations di sidebar

2. Press Ctrl+R (refresh page)
   - Page reloads
   - Sidebar should show same conversations
   - NO messages lost ✅

3. Click on any conversation
   - Messages should load correctly
```

### Test 4: Search
```
1. Have multiple conversations di sidebar

2. Click search input di sidebar

3. Type: "kelompok"
   - Sidebar should filter to show only conversations with "kelompok"

4. Clear search
   - Should show all conversations again
```

### Test 5: New Chat Button
```
1. Click "New Chat" button (top of sidebar)

2. Chat area should clear
   - Landing view should appear
   - Input field should be empty

3. Send new message
   - Should create new conversation
   - Sidebar should add to "Hari Ini" section
```

---

## Expected Behavior

### Sidebar Display
```
History Sidebar
├─ Search Input
├─ New Chat Button
├─ ─────────────────
├─ "Hari Ini" group
│  ├─ Buat kelompok 5 orang dari... (14:02)
│  └─ Siapa pembimbing kelompok A? (13:45)
├─ "Kemarin" group  
│  ├─ Jadwal seminar ruang lab... (16:20)
│  └─ Generate dosen penguji... (11:05)
└─ "Minggu Ini" group
   └─ Revisi pembimbing kelompok 3... (Sen, 10:12)
```

### Title Auto-Generation
```
Input Message: "Buat kelompok 5 orang dari mahasiswa IT angkatan 2023"
Generated Title: "Buat kelompok 5 orang dari mahasiswa IT..." (50 chars)

Input Message: "Siapa pembimbing kelompok A?"
Generated Title: "Siapa pembimbing kelompok A?"
```

### Time Display
```
Same day (today):    14:02, 13:45, 09:30
Yesterday:           16:20, 11:05
Week ago:            Sen, 10:12 (Monday)
Month ago:           3 Jun, 28 Mei
```

---

## Troubleshooting During Testing

### Issue: Sidebar shows "Belum ada percakapan"
```
Possible causes:
1. First time user - no history yet
2. MongoDB tidak terkoneksi
3. Wrong user_id

Check:
- Send a message first
- Check MongoDB status: curl http://localhost:8002/mongodb-status
- Check browser console for errors
```

### Issue: Sidebar tidak update setelah send pesan
```
Possible causes:
1. API Python tidak save ke MongoDB
2. Network error
3. CORS issue

Check:
1. Open DevTools → Network tab
2. Send message
3. Look for request to http://localhost:8002/long-term-history/
4. Check response status (should be 200)
5. Check response body (should include messages)
```

### Issue: Old conversation tidak ter-load
```
Possible causes:
1. Messages tidak tersimpan dengan benar
2. Session grouping error
3. Timestamp issue

Check in console:
conversationHistory.length  // Should be > 0
```

### Issue: Chat area tidak clear saat click session
```
Possible causes:
1. JavaScript error
2. appendMessage() function error

Check:
1. Open console
2. Click session
3. Look for JS errors
4. Verify appendMessage() exists and working
```

---

## Browser Console Debug Commands

```javascript
// Check current conversation history
console.log(conversationHistory);

// Check current sessions
const sessions = groupMessagesBySessions(conversationHistory);
console.log(sessions);

// Check sidebar active state
document.querySelectorAll('.history-item.active');

// Manually reload history
loadConversationHistoryFromMongoDB().then(data => {
    conversationHistory = data;
    renderHistoryList('');
});

// Check MongoDB connection
fetch('http://localhost:8002/mongodb-status').then(r => r.json()).then(console.log);
```

---

## Expected Console Output

### At Page Load
```
[chatbot] Script loaded
[chatbot] DOMContentLoaded
[history] Initializing history sidebar...
[history] Loading conversation history from MongoDB...
[history] Loaded X messages from MongoDB
[history] Grouped X messages into Y sessions
[history] History sidebar initialized successfully
```

### After Sending Message
```
[API Request]: Sending message to /ai/generate
[API Response]: { success: true, result: "..." }
[history] Reloading history after new message...
[history] Loaded X messages from MongoDB
[history] Grouped X messages into Y sessions
```

### After Clicking Session
```
[history] Loading session: session-0 dengan X messages
```

---

## Performance Notes

### First Load
- Load time: ~500-2000ms (depends on message count)
- Network: 1 request to /long-term-history/USER_ID
- Data size: ~50-200KB for 200 messages

### Subsequent Messages
- Load time: ~2000-5000ms (includes AI processing)
- Network: 2 requests (POST to /ai/generate, GET to /long-term-history)
- Additional data: incrementally increases

### Optimization Tips
```javascript
// Reduce initial load
days=7&limit=100  // Load only last week, 100 messages

// Cache results
let lastLoadTime = Date.now();
if (Date.now() - lastLoadTime < 60000) {
    // Use cached data
}
```

---

## Success Criteria

After implementation, you should see:

✅ Sidebar shows conversation history  
✅ Clicking session loads old messages  
✅ New messages update sidebar automatically  
✅ Page refresh doesn't lose history  
✅ Search filters conversations  
✅ Session titles auto-generated from messages  
✅ Time grouping (Hari Ini, Kemarin, etc)  
✅ No JavaScript errors in console  

---

## Next: Move to Production

When ready to deploy:

1. **Update API URL**
   - Change `http://localhost:8002` to production API URL
   - Update in JavaScript

2. **MongoDB**
   - Ensure production MongoDB is accessible
   - Verify indexes exist

3. **CORS**
   - Update CORS whitelist in Python API
   - Allow production domain

4. **Rate Limiting**
   - Implement rate limit for /long-term-history endpoint
   - Protect against abuse

5. **Caching**
   - Consider Redis for frequently accessed history
   - Reduce MongoDB load

---

**Ready to test! 🎉**

Follow testing steps above and let me know if any issues.
