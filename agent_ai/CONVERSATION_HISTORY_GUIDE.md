# Panduan Conversation History Agent AI

## 📋 Ringkasan Sistem

Sistem Agent AI Anda sekarang memiliki **conversation history management** yang komprehensif menggunakan MongoDB. Semua percakapan antara user dan agent disimpan otomatis dan dapat diambil kembali kapan saja.

### Komponen Utama:
1. **MongoDB Storage** - Menyimpan semua pesan dalam koleksi `messages`
2. **Conversation Retrieval** - Multiple endpoints untuk berbagai use case
3. **Search & Export** - Pencarian dan export percakapan
4. **Analytics** - Tracking dan analysis percakapan

---

## 🔧 Bagaimana Cara Kerjanya?

### 1. **Automatic Message Storage** (Di `api.py`)

Setiap request ke endpoint `/agent`:
```python
# User message disimpan
mongo_mem.store_message(
    request.user_id, "user", request.prompt,
    metadata={"source": "api", "timestamp": datetime.now().isoformat()}
)

# Assistant response disimpan
mongo_mem.store_message(
    request.user_id, "assistant", assistant_response,
    metadata={
        "action": agent_result.get("action"),
        "model": "gpt-4",
        "timestamp": datetime.now().isoformat()
    }
)
```

### 2. **MongoDB Collections**

```
VokasiTeraDB
├── messages           # Semua chat messages
│   ├── user_id
│   ├── role (user/assistant)
│   ├── content
│   ├── timestamp
│   └── metadata
├── executor_logs      # Aksi yang dilakukan agent
│   ├── user_id
│   ├── action_type
│   ├── details
│   └── timestamp
├── planner_logs       # Planning/reasoning logs
├── sessions           # Session management
└── metrics           # Performance metrics
```

---

## 📡 API Endpoints untuk Conversation History

### 1. **GET /conversation/{user_id}/full**
Ambil full conversation history dengan metadata lengkap

**Query Parameters:**
- `limit` (default: 100, max: 1000) - Jumlah messages
- `days` (default: 30) - Retrieve dari last n days

**Response:**
```json
{
  "success": true,
  "user_id": 123,
  "messages": [
    {
      "_id": "507f1f77bcf86cd799439011",
      "user_id": 123,
      "role": "user",
      "content": "Buat grup berdasarkan nilai...",
      "timestamp": "2024-06-20T10:30:00",
      "metadata": { "source": "api" }
    },
    {
      "_id": "507f1f77bcf86cd799439012",
      "user_id": 123,
      "role": "assistant",
      "content": "Baik, saya akan membuat grup...",
      "timestamp": "2024-06-20T10:30:05",
      "metadata": { "action": "grouping", "model": "gpt-4" }
    }
  ],
  "summary": {
    "total_messages": 24,
    "user_messages": 12,
    "assistant_messages": 12,
    "start_time": "2024-06-20T09:00:00",
    "end_time": "2024-06-20T10:30:00",
    "period_days": 30
  },
  "actions": [
    {
      "type": "grouping",
      "status": "success",
      "timestamp": "2024-06-20T10:30:05",
      "details": { "num_groups": 5, "students": 50 }
    }
  ]
}
```

**Contoh penggunaan di Laravel:**
```php
$response = Http::get('http://localhost:8002/conversation/' . auth()->id() . '/full', [
    'limit' => 50,
    'days' => 7
]);

$conversation = $response->json();
foreach ($conversation['messages'] as $msg) {
    echo $msg['role'] . ": " . $msg['content'] . "\n";
}
```

---

### 2. **GET /conversation/{user_id}/summary**
Quick overview tanpa full message content

**Response:**
```json
{
  "success": true,
  "user_id": 123,
  "period_days": 30,
  "total_messages": 24,
  "role_breakdown": {
    "user": {
      "count": 12,
      "first": "2024-06-20T09:00:00",
      "last": "2024-06-20T10:30:00"
    },
    "assistant": {
      "count": 12,
      "first": "2024-06-20T09:00:30",
      "last": "2024-06-20T10:30:05"
    }
  },
  "last_activity": "2024-06-20T10:30:05"
}
```

**Use case:**
- Cek apakah ada percakapan baru
- Quick statistics
- Last activity check

---

### 3. **GET /conversation/{user_id}/search**
Search dalam conversation history berdasarkan keyword

**Query Parameters:**
- `keyword` (required) - Search term (case-insensitive)
- `days` (default: 30) - Scope pencarian
- `limit` (default: 50, max: 500) - Max results

**Example:**
```
GET /conversation/123/search?keyword=pembimbing&days=7&limit=20
```

**Response:**
```json
{
  "success": true,
  "user_id": 123,
  "keyword": "pembimbing",
  "result_count": 3,
  "results": [
    {
      "_id": "507f1f77bcf86cd799439011",
      "role": "user",
      "content": "Siapa pembimbing kelompok A?",
      "timestamp": "2024-06-20T10:00:00"
    }
  ]
}
```

---

### 4. **GET /conversation/{user_id}/export**
Export conversation dalam format JSON atau text

**Query Parameters:**
- `days` (default: 30) - Export dari last n days
- `format` (default: json) - "json" atau "text"

**Example untuk export text:**
```
GET /conversation/123/export?format=text&days=30
```

**Response (format=text):**
```
Conversation History for User 123
Exported: 2024-06-20T10:35:00
================================================================================

[2024-06-20T10:30:00] USER:
Buat grup berdasarkan nilai dengan pembimbing dari prodi IPA

[2024-06-20T10:30:05] ASSISTANT:
Baik, saya akan membuat grup berdasarkan kriteria yang Anda sebutkan...
```

**Response (format=json):**
```json
{
  "success": true,
  "format": "json",
  "user_id": 123,
  "export_time": "2024-06-20T10:35:00",
  "message_count": 24,
  "messages": [...]
}
```

---

### 5. **GET /long-term-history/{user_id}**
Ambil percakapan dari jangka waktu panjang

**Query Parameters:**
- `days` (default: 30) - Retrieve dari last n days
- `limit` (default: 100) - Max messages

```
GET /long-term-history/123?days=90&limit=200
```

---

### 6. **GET /conversation/{user_id}/full** vs Other Endpoints

| Endpoint | Use Case | Performance |
|----------|----------|-------------|
| `/conversation/{id}/full` | Complete history dengan actions | Heavier (includes all metadata) |
| `/conversation/{id}/summary` | Quick overview | Fast (aggregation only) |
| `/conversation/{id}/search` | Find specific messages | Medium (indexed search) |
| `/long-term-history/{id}` | Historical data | Medium (range query) |
| `/conversation/{id}/export` | Backup/share | Medium-Heavy (full export) |

---

## 💾 Praktik Terbaik untuk Menggunakan History

### 1. **Pada Request Agent Baru**
```python
# Di main.py - load conversation history
def run_agent_chat(prompt, user_id, conversation_history=None):
    # Jika conversation_history None, bisa ambil dari MongoDB
    if conversation_history is None:
        mongo = get_mongo_memory()
        messages = mongo.get_messages(user_id, limit=20)  # Last 20 messages
        conversation_history = messages
    
    # Gunakan untuk context awareness
    initial_messages = _trim_messages(conversation_history)
    initial_messages.append({"role": "user", "content": prompt})
    
    # ... run agent dengan context
```

### 2. **Di Frontend (Laravel Blade)**
```blade
<!-- Tampilkan conversation history -->
<div class="chat-history">
    @forelse($conversation as $msg)
        <div class="message message-{{ $msg['role'] }}">
            <strong>{{ $msg['role'] == 'user' ? 'Anda' : 'Agent' }}:</strong>
            <p>{{ $msg['content'] }}</p>
            <small>{{ $msg['timestamp'] ?? 'N/A' }}</small>
        </div>
    @empty
        <p>Tidak ada percakapan sebelumnya</p>
    @endforelse
</div>
```

### 3. **Retrieve History dengan Filtering**
```python
from datetime import datetime, timedelta

# Get messages dari last 7 hari
history = mongo_mem.get_conversation_history(user_id, days=7)

# Search specific keywords
grouping_messages = mongo_mem.search_conversations(
    user_id, 
    keyword="pembimbing",
    days=30
)

# Get summary untuk UI
summary = mongo_mem.get_conversation_summary(user_id, days=30)
print(f"Last activity: {summary['last_activity']}")
```

---

## 🔍 Monitoring Conversation Health

### Check Last Activity
```bash
curl http://localhost:8002/conversation/123/summary
```

### Search Conversation Problems
```bash
curl "http://localhost:8002/conversation/123/search?keyword=error"
```

### Export for Analysis
```bash
curl "http://localhost:8002/conversation/123/export?format=json&days=30" > conversation_backup.json
```

---

## ⚙️ Konfigurasi & Tuning

### 1. **Message Trimming** (di `main.py`)
Saat ini membatasi 8 messages untuk context window:
```python
def _trim_messages(messages, max_messages: int = 8):
    # Untuk percakapan panjang, naikkan max_messages
    # Tp perhatikan token limit OpenAI (4096)
```

### 2. **MongoDB Indexing**
Untuk performa optimal, pastikan indexes sudah ada:
```javascript
// Di MongoDB:
db.messages.createIndex({"user_id": 1, "timestamp": -1})
db.messages.createIndex({"content": "text"})  // Text search
db.executor_logs.createIndex({"user_id": 1, "timestamp": -1})
```

### 3. **Data Cleanup**
```python
# Di mongo_memory.py - remove old data after 90 days:
mongo_mem.cleanup_old_data(days=90)
```

---

## 📊 Menggunakan Conversation untuk Analytics

### 1. **User Behavior Analysis**
```python
# Get all actions taken by user
logs = mongo_mem.get_executor_logs(user_id, limit=100)
action_types = {}
for log in logs:
    action = log['action_type']
    action_types[action] = action_types.get(action, 0) + 1

print(f"User {user_id} actions: {action_types}")
# Output: {'grouping': 15, 'jadwal_seminar': 8, 'pembimbing': 12}
```

### 2. **Response Quality Tracking**
```python
# Get quality metrics
metrics = mongo_mem.get_metrics(user_id, "response_quality", days=30)
avg_quality = sum(m['value'] for m in metrics) / len(metrics) if metrics else 0

print(f"Average response quality: {avg_quality:.2%}")
```

### 3. **Performance Monitoring**
```python
# Track response times
response_times = mongo_mem.get_metrics(user_id, "response_time_ms", days=7)
stats = mongo_mem.get_metric_stats(user_id, "response_time_ms", days=7)

print(f"Avg response time: {stats['avg']:.2f}ms")
print(f"Max response time: {stats['max']:.2f}ms")
```

---

## 🚨 Troubleshooting

### 1. **Conversation tidak tersimpan**
```python
# Check MongoDB connection
from core.mongo_memory import get_mongo_memory
mongo = get_mongo_memory()
print(mongo.is_connected())  # Should be True
```

### 2. **History terlalu besar/lambat**
```python
# Increase limit gradually
history = mongo_mem.get_messages(user_id, limit=50)  # Start with 50

# Gunakan summary jika hanya butuh overview
summary = mongo_mem.get_conversation_summary(user_id)
```

### 3. **Search tidak menemukan hasil**
```python
# Pastikan keyword ada di messages
results = mongo_mem.search_conversations(
    user_id, 
    keyword="pembimbing",
    days=30,
    limit=100  # Increase limit to be sure
)

print(f"Found {len(results)} messages")
for r in results:
    print(f"  - {r['timestamp']}: {r['content'][:50]}...")
```

---

## 📝 Summary

Sistem conversation history sekarang:

✅ **Otomatis menyimpan** semua pesan ke MongoDB  
✅ **Cepat diambil** via multiple endpoints  
✅ **Searchable** dengan keyword search  
✅ **Exportable** dalam JSON/text format  
✅ **Trackable** dengan metadata lengkap  
✅ **Analyzable** untuk insights  

Gunakan endpoints ini untuk:
- **Display** chat history di UI
- **Context** untuk agent awareness
- **Analytics** untuk user behavior
- **Search** untuk finding info
- **Export** untuk backup/share

---

## 🔗 Quick Reference

```bash
# Get full history with metadata
curl http://localhost:8002/conversation/123/full?limit=50

# Get quick summary
curl http://localhost:8002/conversation/123/summary

# Search
curl "http://localhost:8002/conversation/123/search?keyword=pembimbing"

# Export
curl "http://localhost:8002/conversation/123/export?format=json"

# Analytics
curl http://localhost:8002/analytics/123
```

---

**Dokumentasi Updated:** 2024-06-20  
**Untuk pertanyaan/issue:** Lihat MongoDB logs atau response error di API
