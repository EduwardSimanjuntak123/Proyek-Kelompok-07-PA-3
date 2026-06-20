# 🔧 QUICK DEBUGGING - History & Sesi Baru Button Issue

## Step 1: Open Browser DevTools (F12)

1. **Press F12** (or Ctrl+Shift+I)
2. **Go to Console Tab**
3. **Refresh page** (Ctrl+R)
4. **Look for messages starting with `[history]`**

---

## Step 2: Check What You See

### Scenario A: Many [history] logs
If console is full of logs like:
```
[history] Auth Status - User ID: 1 - Authenticated: true
[history] Loading conversation history from MongoDB...
[history] Response status: 200 OK
[history] ✅ Loaded 0 messages from MongoDB
```

**Then:**
- JavaScript is working
- But MongoDB is empty (no messages saved yet)
- Send a test message first, then refresh

### Scenario B: Few or no [history] logs
If console shows very few logs or none:
```
[history] Auth Status - User ID: 1 - Authenticated: true
```

**Then:**
- JavaScript partially loaded
- There's an error blocking history load
- **Copy ALL console errors and share**

### Scenario C: Error about undefined
```
[history] ⚠️ User not authenticated! History will not load.
[history] User ID: undefined
```

**Then:**
- Auth not working
- Clear cookies and login again

---

## Step 3: Test "Sesi Baru" Button

1. **In Console, type:**
```javascript
document.getElementById('newSessionBtn')
```

2. **Should return:**
```javascript
<button class="new-session-btn" id="newSessionBtn">...</button>
```

**If returns `null`:**
- Element tidak ditemukan
- Check DOM structure

3. **Try clicking button manually in console:**
```javascript
document.getElementById('newSessionBtn').click()
```

**If works in console but not in UI:**
- Event listener issue
- Click handler problem

---

## Step 4: Run Diagnostic Script

**Copy-paste di console sekali:**

```javascript
console.log('=== DIAGNOSTIC START ===');

// Check user
console.log('User ID:', currentUserId);
console.log('Is authenticated:', currentUserId !== null && currentUserId !== undefined);

// Check elements
console.log('Chat box exists:', !!document.getElementById('chatBox'));
console.log('History list exists:', !!document.getElementById('historyList'));
console.log('New session button exists:', !!document.getElementById('newSessionBtn'));
console.log('Search input exists:', !!document.getElementById('historySearchInput'));

// Check conversation history
console.log('Conversation history:', conversationHistory);
console.log('Message count:', conversationHistory.length);

// Check sessions
const sessions = groupMessagesBySessions(conversationHistory);
console.log('Sessions:', sessions);
console.log('Session count:', sessions.length);

// Test API
console.log('Testing API fetch...');
fetch('http://localhost:8002/long-term-history/' + currentUserId)
  .then(r => r.json())
  .then(data => {
    console.log('API Response:', data);
    console.log('API Status:', data.success ? '✅ OK' : '❌ Failed');
    console.log('Message count from API:', data.message_count);
  })
  .catch(e => console.error('API Error:', e));

console.log('=== DIAGNOSTIC END ===');
```

---

## Step 5: Share All Output

After running diagnostic, share:

1. **Console output** - Copy everything
2. **Network tab** - Screenshot showing request to localhost:8002
3. **MongoDB count** - Run in terminal:
```bash
mongosh
use VokasiTeraDB
db.messages.count()
db.messages.find({"user_id": YOUR_USER_ID}).count()
```

---

## Common Issues & Quick Fixes

### Issue: "Loaded 0 messages from MongoDB"
**Solution:** Send a test message first
```
1. Type: "test"
2. Click Send
3. Wait for AI response
4. Refresh page
5. History should appear
```

### Issue: "Response status: 0" or "Failed to fetch"
**Solution:** Python API not running
```bash
# Terminal
cd agent_ai
python start_api.py
# Wait for: "Uvicorn running on http://0.0.0.0:8002"
```

### Issue: "Response status: 500"
**Solution:** API error - check server logs
```bash
# Terminal where API running
# Look for red error messages
# Report error to me
```

### Issue: Sesi Baru button not clickable
**Solution:** Run in console:
```javascript
// Test if button works
document.getElementById('newSessionBtn').click();
// If works, reload page
location.reload();
```

---

## Step-by-Step Test Instructions

### Test 1: Manual Send Message
```
1. Type in chat: "test message"
2. Click Send button
3. Wait for AI response
4. Check console for new [history] logs
5. Check Network tab for new requests
```

### Test 2: Check Message in MongoDB
```bash
# After sending message:
mongosh
use VokasiTeraDB
db.messages.find({"role": "user", "content": "test message"}).pretty()
# Should show your message with timestamp
```

### Test 3: Manual Refresh History
```javascript
// In console:
conversationHistory = await loadConversationHistoryFromMongoDB();
console.log('Loaded:', conversationHistory.length, 'messages');
renderHistoryList('');
// Sidebar should update
```

### Test 4: Send & Refresh
```
1. Send a message
2. Ctrl+R (refresh)
3. Wait for page to load
4. Check sidebar
5. History should persist
```

---

## What to Report

When something doesn't work, provide:

1. **Browser Console Output**
   - F12 → Console
   - Screenshot or copy-paste everything

2. **Network Request Details**
   - F12 → Network
   - Refresh page
   - Look for "long-term-history" request
   - Share status code & response

3. **User ID & Auth Status**
   - Run: `console.log(currentUserId)`
   - Run: `console.log(currentUserId !== null)`

4. **MongoDB Query Result**
   ```bash
   mongosh
   use VokasiTeraDB
   db.messages.find({"user_id": YOUR_ID}).count()
   ```

5. **Python API Status**
   ```bash
   curl http://localhost:8002/mongodb-status
   ```

---

## Next: Run Diagnostic Now

1. **Open page:** http://localhost:8000/agent-kelompok
2. **Press F12**
3. **Go to Console**
4. **Copy-paste the diagnostic script above**
5. **Wait for results**
6. **Share the output with me**

This will help me identify exactly what's wrong! 🚀
