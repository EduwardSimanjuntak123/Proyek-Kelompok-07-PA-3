# Panduan Implementasi Error Handling - Contoh Praktis

## 1. Error Handling di Controller

### Contoh 1: CRUD Operations dengan Error Handling

```php
<?php

namespace App\Http\Controllers;

use App\Services\ErrorHandlingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Create a new user with comprehensive error handling
     */
    public function store(Request $request)
    {
        try {
            // 1. Validasi input
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:8|confirmed',
            ]);

            // 2. Hash password
            $validated['password'] = bcrypt($validated['password']);

            // 3. Create user
            $user = User::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'User berhasil dibuat',
                'data' => $user
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Error handling untuk validation
            return ErrorHandlingService::validationError(
                $e->validator->errors()->getMessages()
            );

        } catch (\Illuminate\Database\QueryException $e) {
            // Error handling untuk database
            return ErrorHandlingService::databaseError($e);

        } catch (\Exception $e) {
            // Error handling untuk generic exceptions
            \Log::error('User creation error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return ErrorHandlingService::serverError(
                'Gagal membuat user. Silakan coba lagi.'
            );
        }
    }

    /**
     * Update user
     */
    public function update(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);

            $validated = $request->validate([
                'name' => 'sometimes|string|max:255',
                'email' => 'sometimes|email|unique:users,email,' . $id,
            ]);

            $user->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'User berhasil diupdate',
                'data' => $user
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {
            return ErrorHandlingService::notFoundError('User');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return ErrorHandlingService::validationError(
                $e->validator->errors()->getMessages()
            );

        } catch (\Illuminate\Database\QueryException $e) {
            return ErrorHandlingService::databaseError($e);

        } catch (\Exception $e) {
            return ErrorHandlingService::serverError();
        }
    }

    /**
     * Delete user
     */
    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'User berhasil dihapus'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {
            return ErrorHandlingService::notFoundError('User');

        } catch (\Exception $e) {
            return ErrorHandlingService::serverError(
                'Gagal menghapus user'
            );
        }
    }
}
```

---

### Contoh 2: External API Integration dengan Error Handling

```php
<?php

namespace App\Http\Controllers\Agent;

use App\Services\ErrorHandlingService;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;

class AgentController extends Controller
{
    /**
     * Fetch data dari AI Agent API dengan error handling
     */
    public function fetchAgentData(Request $request)
    {
        try {
            // Timeout setting
            $timeout = 30; // seconds

            // Make request ke external API
            $response = Http::timeout($timeout)
                ->retry(3, 100) // Retry 3 kali jika gagal
                ->get('https://api-agent.example.com/api/kelompok', [
                    'pa_id' => $request->pa_id,
                    'tahun_akademik' => $request->tahun_akademik
                ]);

            // Check response status
            if (!$response->successful()) {
                throw new RequestException($response);
            }

            return response()->json([
                'success' => true,
                'data' => $response->json()
            ]);

        } catch (RequestException $e) {
            // Network/API error
            \Log::error('Agent API Error', [
                'status' => $e->response?->status(),
                'message' => $e->getMessage()
            ]);

            return ErrorHandlingService::networkError($e);

        } catch (\Exception $e) {
            \Log::error('Unexpected error in fetchAgentData', [
                'message' => $e->getMessage()
            ]);

            return ErrorHandlingService::serverError(
                'Gagal mengambil data dari AI Agent'
            );
        }
    }

    /**
     * Send data ke Agent dengan timeout handling
     */
    public function sendToAgent(Request $request)
    {
        try {
            $startTime = microtime(true);

            $response = Http::timeout(30)
                ->post('https://api-agent.example.com/api/process', [
                    'data' => $request->all()
                ]);

            $executionTime = microtime(true) - $startTime;

            // Log execution time
            if ($executionTime > 10) {
                \Log::warning('Slow Agent API call', [
                    'execution_time' => $executionTime,
                    'request' => $request->all()
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil dikirim ke Agent',
                'execution_time' => $executionTime
            ]);

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            // Connection error
            return ErrorHandlingService::networkError($e);

        } catch (\Illuminate\Http\Client\RequestTimeoutException $e) {
            // Timeout error
            return ErrorHandlingService::timeoutError();

        } catch (RequestException $e) {
            return ErrorHandlingService::networkError($e);

        } catch (\Exception $e) {
            return ErrorHandlingService::serverError(
                'Gagal mengirim data ke Agent'
            );
        }
    }
}
```

---

## 2. Error Handling di Frontend (Blade)

### Contoh 1: Form dengan Client-Side Error Handling

```blade
@extends('layouts.main')

@section('content')
<div class="section">
    <div class="section-header">
        <h1>Buat Kelompok Baru</h1>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4>Form Kelompok</h4>
                    </div>

                    <form id="groupForm" class="card-body">
                        @csrf

                        <div class="form-group">
                            <label>Nama Kelompok</label>
                            <input type="text" name="nama" class="form-control" required>
                            <small class="form-text text-muted">Masukkan nama kelompok</small>
                        </div>

                        <div class="form-group">
                            <label>Deskripsi</label>
                            <textarea name="deskripsi" class="form-control" rows="4"></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-2"></i> Simpan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('script')
<script>
$(document).ready(function() {
    // Form submission dengan error handling
    $('#groupForm').on('submit', function(e) {
        e.preventDefault();

        // Disable button saat loading
        const $btn = $(this).find('button[type="submit"]');
        const originalHtml = $btn.html();
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');

        $.ajax({
            url: '{{ route('kelompok.store') }}',
            type: 'POST',
            data: $(this).serialize(),
            timeout: 30000, // 30 second timeout

            success: function(response) {
                if (response.success) {
                    ErrorHandler.showNotification(response.message, 'success');

                    // Redirect setelah 2 detik
                    setTimeout(() => {
                        window.location.href = '{{ route('kelompok.index') }}';
                    }, 2000);
                }
            },

            error: function(xhr) {
                // Error handling dilakukan otomatis oleh ErrorHandler
                // Tapi kita bisa tambah log tambahan jika diperlukan
                console.error('Form submission error:', xhr);
            },

            complete: function() {
                // Re-enable button
                $btn.prop('disabled', false).html(originalHtml);
            }
        });

        return false;
    });
});
</script>
@endpush
```

---

### Contoh 2: AJAX Request dengan Loading State

```blade
<div id="dataContainer">
    <button id="loadBtn" class="btn btn-primary">
        <i class="fas fa-download mr-2"></i> Load Data
    </button>
    <div id="loadingSpinner" style="display: none;">
        <div class="spinner-border" role="status">
            <span class="sr-only">Loading...</span>
        </div>
        <p>Sedang memuat data...</p>
    </div>
    <table id="dataTable" class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama</th>
                <th>Email</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

@push('script')
<script>
$(document).ready(function() {
    let isLoading = false;

    $('#loadBtn').on('click', function() {
        if (isLoading) return;

        isLoading = true;
        $('#loadingSpinner').show();
        $('#dataTable').hide();

        $.ajax({
            url: '{{ route('api.data') }}',
            type: 'GET',
            timeout: 15000,

            success: function(response) {
                if (response.success) {
                    // Update table
                    const tbody = $('#dataTable tbody');
                    tbody.empty();

                    response.data.forEach(item => {
                        tbody.append(`
                            <tr>
                                <td>${item.id}</td>
                                <td>${item.name}</td>
                                <td>${item.email}</td>
                            </tr>
                        `);
                    });

                    $('#dataTable').show();
                    ErrorHandler.showNotification('Data berhasil dimuat', 'success');
                }
            },

            error: function(xhr) {
                // ErrorHandler akan menampilkan error secara otomatis
            },

            complete: function() {
                isLoading = false;
                $('#loadingSpinner').hide();
            }
        });
    });
});
</script>
@endpush
```

---

## 3. Custom Error Pages

### Contoh: Maintenance Mode Error

Buat file `resources/views/errors/503.blade.php` (sudah ada):

```blade
<!-- Sudah dibuat dan dapat dikustomisasi sesuai kebutuhan -->
```

---

## 4. Monitoring dan Logging

### Contoh: Check Error Logs

```bash
# Real-time monitoring error logs
tail -f storage/logs/errors.log

# Lihat 50 error terakhir
head -50 storage/logs/errors.log

# Search specific error
grep "Database Error" storage/logs/errors.log

# Count error types
grep -o '"type":"[^"]*"' storage/logs/errors.log | sort | uniq -c
```

---

## 5. Testing Error Scenarios

### Contoh 1: Test Network Error

```javascript
// Di browser console
fetch("https://invalid-domain-12345.com/api/test").catch((error) =>
    console.error(error),
);
```

### Contoh 2: Test Validation Error

```javascript
$.ajax({
    url: "/api/users",
    type: "POST",
    data: {
        name: "", // kosong, akan trigger validation error
        email: "invalid-email", // invalid email format
    },
});
```

### Contoh 3: Test Timeout

```javascript
$.ajax({
    url: "/api/slow-endpoint",
    timeout: 100, // 100ms timeout (akan timeout)
    success: function () {
        console.log("Success");
    },
});
```

---

## 6. Best Practices Checklist

- [ ] Selalu gunakan try-catch untuk database operations
- [ ] Validate input di controller
- [ ] Log semua errors dengan context yang jelas
- [ ] Berikan user-friendly messages
- [ ] Jangan expose sensitive info di error messages
- [ ] Monitor error logs secara berkala
- [ ] Set timeout untuk external API calls
- [ ] Test semua error scenarios sebelum production
- [ ] Update error logs retention policy
- [ ] Setup alerts untuk critical errors

---

## 7. Performance Tips

1. **Database Queries:**

    ```php
    // Gunakan try-catch untuk queries
    try {
        $users = User::where('active', 1)->paginate(15);
    } catch (QueryException $e) {
        return ErrorHandlingService::databaseError($e);
    }
    ```

2. **Caching untuk API:**

    ```php
    // Cache response untuk mengurangi API calls
    $data = Cache::remember('api_data', 3600, function () {
        return Http::get('https://api.example.com/data');
    });
    ```

3. **Queue untuk Long Operations:**
    ```php
    // Gunakan queue untuk long-running operations
    ProcessLargeDataJob::dispatch($data)->onQueue('long-running');
    ```

---

Untuk pertanyaan lebih lanjut, cek `ERROR_HANDLING_DOCUMENTATION.md` untuk dokumentasi lengkap.
