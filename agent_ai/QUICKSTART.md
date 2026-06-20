# 🚀 Conversation History - Quick Start

## Setup ✅ (Sudah Selesai)

System sudah fully implemented dan siap pakai. Tidak perlu konfigurasi tambahan.

---

## Usage

### 1. **Display Chat History** (di UI/Laravel)

```php
// Get semua percakapan
$response = Http::get("http://localhost:8002/conversation/{$userId}/full");
$messages = $response->json()['messages'];

// Display di Blade
@foreach($messages as $msg)
    <div class="message message-{{ $msg['role'] }}">
        {{ $msg['role'] == 'user' ? 'Anda' : 'Agent' }}: {{ $msg['content'] }}
    </div>
@endforeach
```

### 2. **Search Conversation**

```php
// Cari percakapan dengan keyword
$response = Http::get("http://localhost:8002/conversation/{$userId}/search", [
    'keyword' => 'pembimbing'
]);

$results = $response->json()['results'];
```

### 3. **Get Quick Stats**

```php
// Last activity, total messages, etc
$response = Http::get("http://localhost:8002/conversation/{$userId}/summary");
$stats = $response->json();

echo "Last activity: " . $stats['last_activity'];
echo "Total messages: " . $stats['total_messages'];
```

### 4. **Export for Backup**

```php
// Export as JSON
$response = Http::get("http://localhost:8002/conversation/{$userId}/export", [
    'format' => 'json',
    'days' => 30
]);

$backup = $response->json();
```

---

## API Endpoints

| Endpoint | Purpose | Response |
|----------|---------|----------|
| `GET /conversation/{id}/full` | Semua messages dengan metadata | messages[], summary, actions |
| `GET /conversation/{id}/summary` | Quick stats saja | total_messages, last_activity |
| `GET /conversation/{id}/search?keyword=X` | Cari by keyword | matching messages |
| `GET /conversation/{id}/export?format=json` | Export conversation | formatted export |

---

## Workflow

```
User Input
    ↓
API: POST /agent
    ↓
[Otomatis] Load history dari MongoDB
    ↓
Run Agent dengan context
    ↓
[Otomatis] Store user + assistant messages to MongoDB
    ↓
Return response
    ↓
[Optional] Retrieve history via new endpoints
```

---

## Testing

```bash
# Get history (50 messages, last 7 days)
curl http://localhost:8002/conversation/1/full?limit=50&days=7

# Quick summary
curl http://localhost:8002/conversation/1/summary

# Search for "pembimbing"
curl "http://localhost:8002/conversation/1/search?keyword=pembimbing"

# Export as JSON
curl http://localhost:8002/conversation/1/export?format=json
```

---

## Features

✅ **Auto-store** semua percakapan ke MongoDB  
✅ **Auto-load** history untuk context awareness  
✅ **Searchable** dengan keyword search  
✅ **Exportable** dalam JSON/text format  
✅ **Trackable** dengan metadata lengkap  
✅ **Fast** dengan MongoDB indexing  

---

## Documentation

- **Full Guide:** [CONVERSATION_HISTORY_GUIDE.md](CONVERSATION_HISTORY_GUIDE.md)
- **Implementation Details:** [IMPLEMENTATION_SUMMARY.md](IMPLEMENTATION_SUMMARY.md)

---

**Ready to use! 🎉**
