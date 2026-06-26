# Sistem Error Handling Komprehensif

## Daftar Isi

1. [Pengenalan](#pengenalan)
2. [Komponen Sistem](#komponen-sistem)
3. [Backend Error Handling](#backend-error-handling)
4. [Frontend Error Handling](#frontend-error-handling)
5. [Network Error Detection](#network-error-detection)
6. [Error Types](#error-types)
7. [Cara Menggunakan](#cara-menggunakan)
8. [Testing](#testing)

---

## Pengenalan

Sistem error handling ini dirancang untuk menangani berbagai jenis error yang mungkin terjadi:

- **Server Down** - Server tidak dapat diakses
- **Network Issues** - Koneksi jaringan bermasalah atau terputus
- **Database Errors** - Masalah koneksi database
- **Validation Errors** - Data input tidak valid
- **Timeout Errors** - Request memakan waktu terlalu lama
- **General Application Errors** - Error aplikasi lainnya

---

## Komponen Sistem

### 1. Backend (Laravel)

#### File: `bootstrap/app.php`

Konfigurasi global exception handler yang menangani semua exception di level aplikasi.

```php
->withExceptions(function (Exceptions $exceptions) {
    // Handles ValidationException
    // Handles QueryException (Database)
    // Handles RequestException (Network)
    // Handles Generic Exceptions
})
```

#### File: `config/logging.php`

Konfigurasi channel logging untuk error tracking:

- `errors` - Log semua error aplikasi
- `client_errors` - Log error dari client-side JavaScript

#### File: `app/Services/ErrorHandlingService.php`

Layanan utilitas untuk menangani berbagai tipe error secara konsisten.

Metode utama:

```php
ErrorHandlingService::handleError($message, $type, $statusCode, $data);
ErrorHandlingService::databaseError($exception);
ErrorHandlingService::validationError($errors);
ErrorHandlingService::networkError($exception);
ErrorHandlingService::authenticationError();
ErrorHandlingService::authorizationError();
ErrorHandlingService::notFoundError($resource);
ErrorHandlingService::timeoutError();
ErrorHandlingService::serverError();
```

#### File: `app/Http/Controllers/ErrorLogController.php`

Controller untuk mencatat error dari client-side ke server.

Endpoints:

- `POST /api/error-log` - Catat error dari JavaScript
- `GET /api/error-log/statistics` - Dapatkan statistik error (admin only)

### 2. Frontend (JavaScript)

#### File: `public/assets/js/custom.js`

Global error handler yang menangani semua jenis error di client-side.

**ErrorHandler Object:**

```javascript
ErrorHandler.init(); // Inisialisasi semua handler
ErrorHandler.setupGlobalErrorHandler(); // Tangkap JavaScript errors
ErrorHandler.setupNetworkListener(); // Deteksi network status
ErrorHandler.setupAjaxErrorHandler(); // Tangkap AJAX errors
ErrorHandler.setupUnhandledRejectionHandler(); // Tangkap promise rejection
```

### 3. Custom Error Pages

#### File: `resources/views/errors/500.blade.php`

Halaman error untuk Server Error (5xx)

#### File: `resources/views/errors/503.blade.php`

Halaman error untuk Service Unavailable (maintenance)

---

## Backend Error Handling

### Menggunakan ErrorHandlingService

**Contoh 1: Database Error**

```php
use App\Services\ErrorHandlingService;

try {
    $user = User::findOrFail($id);
} catch (\Illuminate\Database\QueryException $e) {
    return ErrorHandlingService::databaseError($e);
}
```

**Contoh 2: Validation Error**

```php
$errors = [
    'email' => ['Email sudah terdaftar'],
    'name' => ['Nama harus diisi']
];
return ErrorHandlingService::validationError($errors);
```

**Contoh 3: Network Error**

```php
use Illuminate\Http\Client\RequestException;

try {
    $response = Http::get('https://api.example.com/data');
} catch (RequestException $e) {
    return ErrorHandlingService::networkError($e);
}
```

**Contoh 4: Generic Error**

```php
return ErrorHandlingService::handleError(
    'Tidak dapat memproses permintaan Anda',
    'general',
    400
);
```

### Exception Handler Response Format

Semua error akan mengembalikan JSON response dengan format:

```json
{
    "success": false,
    "message": "Deskripsi error",
    "error_type": "error_type_string",
    "data": null
}
```

---

## Frontend Error Handling

### Inisialisasi

Error handler otomatis diinisialisasi saat document ready:

```javascript
$(document).ready(function () {
    ErrorHandler.init();
});
```

### Error Types di Frontend

```javascript
ErrorHandler.ERROR_TYPES = {
    NETWORK: "network",
    TIMEOUT: "timeout",
    SERVER: "server",
    DATABASE: "database",
    VALIDATION: "validation",
    UNKNOWN: "unknown",
};
```

### Menampilkan Custom Error

```javascript
// Menampilkan generic error
ErrorHandler.showGenericError("Terjadi kesalahan saat mengunduh data");

// Menampilkan notification
ErrorHandler.showNotification("Operasi berhasil", "success");
ErrorHandler.showNotification("Terjadi kesalahan", "error");
```

---

## Network Error Detection

### Deteksi Otomatis

ErrorHandler secara otomatis mendeteksi status jaringan:

```javascript
// Mendengarkan event online/offline
window.addEventListener("online", () => {
    ErrorHandler.hideNetworkError();
});

window.addEventListener("offline", () => {
    ErrorHandler.showNetworkError();
});
```

### Pemeriksaan Manual

```javascript
if (navigator.onLine) {
    // Jaringan aktif
} else {
    // Jaringan offline
    ErrorHandler.showNetworkError();
}
```

---

## Error Types

### 1. Network Error (503)

```
Status: 503
Penyebab: Koneksi jaringan terputus atau API external down
Tampilan: Alert banner dengan warning icon
```

### 2. Timeout Error (504)

```
Status: 504
Penyebab: Request memakan waktu lebih dari timeout limit
Tampilan: SweetAlert dengan opsi "Coba Lagi"
```

### 3. Server Error (500)

```
Status: 500
Penyebab: Error di server (exception, bug, etc)
Tampilan: Halaman error custom atau SweetAlert
File: resources/views/errors/500.blade.php
```

### 4. Database Error (500)

```
Status: 500
Error Type: 'database'
Penyebab: Koneksi database gagal atau query error
Logged to: storage/logs/errors.log
```

### 5. Validation Error (422)

```
Status: 422
Error Type: 'validation'
Response:
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "field1": ["error message"],
        "field2": ["error message"]
    }
}
```

### 6. Authentication Error (401)

```
Status: 401
Error Type: 'authentication'
Action: Redirect ke halaman login
```

### 7. Authorization Error (403)

```
Status: 403
Error Type: 'authorization'
Pesan: "Anda tidak memiliki izin untuk mengakses resource ini"
```

### 8. Not Found Error (404)

```
Status: 404
Error Type: 'not_found'
Pesan: "Resource tidak ditemukan"
```

---

## Cara Menggunakan

### 1. Di Controller

```php
<?php

namespace App\Http\Controllers;

use App\Services\ErrorHandlingService;
use Illuminate\Support\Facades\DB;

class ExampleController extends Controller
{
    public function store(Request $request)
    {
        try {
            // Validasi input
            if (!$request->filled('name')) {
                return ErrorHandlingService::validationError([
                    'name' => ['Nama tidak boleh kosong']
                ]);
            }

            // Database operation
            $user = DB::table('users')->insert([
                'name' => $request->name,
                'email' => $request->email
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User berhasil dibuat',
                'data' => $user
            ]);

        } catch (\Illuminate\Database\QueryException $e) {
            return ErrorHandlingService::databaseError($e);
        } catch (\Exception $e) {
            return ErrorHandlingService::serverError($e->getMessage());
        }
    }
}
```

### 2. Di View (Blade)

Error handling sudah otomatis, tapi Anda bisa menampilkan manual:

```blade
@if (session('error'))
    <script>
        ErrorHandler.showGenericError('{{ session('error') }}');
    </script>
@endif

@if ($errors->any())
    <script>
        ErrorHandler.showValidationError({
            response: {
                errors: @json($errors->getMessages())
            }
        });
    </script>
@endif
```

### 3. Di JavaScript/AJAX

```javascript
// AJAX request dengan error handling otomatis
$.ajax({
    url: "/api/users",
    type: "POST",
    data: {
        name: "John Doe",
        email: "john@example.com",
    },
    success: function (response) {
        if (response.success) {
            ErrorHandler.showNotification(response.message, "success");
        }
    },
    // Error handling dilakukan otomatis oleh ErrorHandler
});
```

### 4. Fetch API

```javascript
fetch("/api/users", {
    method: "POST",
    headers: {
        "Content-Type": "application/json",
    },
    body: JSON.stringify({
        name: "John Doe",
        email: "john@example.com",
    }),
})
    .then((response) => response.json())
    .then((data) => {
        if (data.success) {
            ErrorHandler.showNotification(data.message, "success");
        }
    })
    .catch((error) => {
        // Error handling dilakukan otomatis
        console.error(error);
    });
```

---

## Testing

### 1. Test Network Error

Matikan koneksi internet:

```javascript
// Force network error
ErrorHandler.showNetworkError();

// Atau cek status
console.log(navigator.onLine);
```

### 2. Test Server Error

Akses endpoint yang tidak ada atau error:

```bash
# Akan trigger 500 error
curl http://localhost/api/non-existent-endpoint
```

### 3. Test Validation Error

```javascript
$.ajax({
    url: "/api/users",
    type: "POST",
    data: {}, // kosong, akan validation error
});
```

### 4. Test Timeout

```javascript
// Simulasikan timeout dengan delay besar
$.ajax({
    url: "/api/slow-endpoint",
    timeout: 100, // 100ms timeout
});
```

### 5. Test Database Error

Hentikan database server dan coba query.

### 6. Cek Log Files

```bash
# Error aplikasi
tail -f storage/logs/errors.log

# Client-side errors
tail -f storage/logs/client_errors.log

# General logs
tail -f storage/logs/laravel.log
```

---

## Log Locations

- **Application Errors**: `storage/logs/errors.log`
- **Client-Side Errors**: `storage/logs/client_errors.log`
- **All Logs**: `storage/logs/laravel.log`

---

## Best Practices

1. **Selalu gunakan try-catch** untuk database operations
2. **Validate input** sebelum processing
3. **Log semua errors** untuk debugging kemudian
4. **Berikan user-friendly messages** ke frontend
5. **Jangan expose sensitive info** di error messages
6. **Monitor error logs** secara berkala
7. **Set appropriate timeout** untuk external API calls
8. **Test error scenarios** sebelum production

---

## Support

Untuk pertanyaan atau masalah, hubungi tim development atau cek dokumentasi API lebih lengkap di project wiki.
