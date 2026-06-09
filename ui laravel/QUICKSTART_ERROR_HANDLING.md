# Quick Start Guide - Error Handling System

## 🚀 Penggunaan Cepat

Sistem error handling sudah terintegrasi secara otomatis di seluruh aplikasi. Tidak perlu konfigurasi tambahan!

---

## 📋 Checklist Setup

- ✅ Exception handler di `bootstrap/app.php`
- ✅ Error logging channels di `config/logging.php`
- ✅ JavaScript error handler di `public/assets/js/custom.js`
- ✅ Custom error pages di `resources/views/errors/`
- ✅ Error service di `app/Services/ErrorHandlingService.php`
- ✅ Error logging controller di `app/Http/Controllers/ErrorLogController.php`
- ✅ CSS styling di `public/assets/css/error-handling.css`

---

## 📚 Di Mana Menggunakan?

### 1. **Di Controller - Menggunakan ErrorHandlingService**

```php
use App\Services\ErrorHandlingService;

// Database Error
catch (\Illuminate\Database\QueryException $e) {
    return ErrorHandlingService::databaseError($e);
}

// Validation Error
catch (\Illuminate\Validation\ValidationException $e) {
    return ErrorHandlingService::validationError($e->errors());
}

// Network Error
catch (\Illuminate\Http\Client\RequestException $e) {
    return ErrorHandlingService::networkError($e);
}

// Generic Error
catch (\Exception $e) {
    return ErrorHandlingService::serverError('Message');
}
```

### 2. **Di Frontend - Otomatis dengan ErrorHandler**

JavaScript error handling **sudah berjalan otomatis**!

```javascript
// Tidak perlu kode tambahan untuk:
// - AJAX errors
// - Network errors
// - JavaScript errors
// - Promise rejections

// Tapi Anda bisa gunakan ErrorHandler untuk display custom:
ErrorHandler.showNotification("Berhasil!", "success");
ErrorHandler.showGenericError("Terjadi kesalahan");
```

---

## 🔍 Jenis Error & Penanganannya

| Error Type   | Status | Handler            | Display        |
| ------------ | ------ | ------------------ | -------------- |
| Network      | 503    | Network listener   | Alert banner   |
| Timeout      | 504    | AJAX error handler | SweetAlert     |
| Server       | 500    | Exception handler  | Error page     |
| Database     | 500    | Exception handler  | JSON response  |
| Validation   | 422    | Exception handler  | SweetAlert     |
| Not Found    | 404    | Exception handler  | JSON response  |
| Unauthorized | 401    | Exception handler  | Redirect login |
| Forbidden    | 403    | Exception handler  | JSON response  |

---

## 📖 Dokumentasi Lengkap

Baca `ERROR_HANDLING_DOCUMENTATION.md` untuk dokumentasi lengkap dengan semua API dan options.

---

## 💡 Contoh Praktis

Baca `ERROR_HANDLING_EXAMPLES.md` untuk contoh implementasi di:

- CRUD Controller
- External API Integration
- Form Submission
- AJAX Requests
- Testing

---

## 🧪 Testing Error Scenarios

### Test 1: Network Error

Matikan internet → Akan tampil banner jaringan error

### Test 2: Server Error

```bash
curl http://localhost/api/non-existent
```

Akan menampilkan error page 500

### Test 3: Validation Error

```javascript
$.ajax({
    url: "/api/users",
    type: "POST",
    data: {}, // invalid
});
```

### Test 4: Timeout

```javascript
$.ajax({
    url: "/api/endpoint",
    timeout: 100,
});
```

---

## 📊 Monitoring

### View Error Logs

```bash
# Real-time
tail -f storage/logs/errors.log

# Client-side errors
tail -f storage/logs/client_errors.log
```

### API Statistics

```
GET /api/error-log/statistics (require auth)
```

---

## 🎯 Best Practices

1. **Always use try-catch** dalam database operations
2. **Validate input** di controller sebelum process
3. **Log dengan context** untuk debugging
4. **User-friendly messages** di response
5. **Jangan expose sensitive info**
6. **Monitor logs** secara rutin
7. **Set timeout** untuk external API
8. **Test error cases** sebelum production

---

## 🔗 Related Files

| File                                    | Purpose                  |
| --------------------------------------- | ------------------------ |
| `bootstrap/app.php`                     | Global exception handler |
| `config/logging.php`                    | Logging configuration    |
| `app/Services/ErrorHandlingService.php` | Error utility service    |
| `public/assets/js/custom.js`            | JavaScript error handler |
| `public/assets/css/error-handling.css`  | Error styling            |
| `resources/views/errors/500.blade.php`  | Server error page        |
| `resources/views/errors/503.blade.php`  | Maintenance page         |

---

## ❓ FAQ

**Q: Apakah saya perlu configure apa-apa?**
A: Tidak! Sudah terintegrasi penuh. Tinggal gunakan ErrorHandlingService di controller.

**Q: Bagaimana jika aplikasi tidak butuh error handling?**
A: Gunakan tanpa error handling service, akan tetap jalan normal.

**Q: Bisa customize error messages?**
A: Ya, pass message di ErrorHandlingService method, contoh:

```php
ErrorHandlingService::serverError('Custom message di sini');
```

**Q: Kemana error logs disimpan?**
A: Di `storage/logs/errors.log` dan `storage/logs/client_errors.log`

**Q: Bagaimana monitoring production errors?**
A: Baca logs regularly atau setup log shipping ke external service.

---

## 📞 Support

Untuk bantuan lebih lanjut:

1. Baca dokumentasi lengkap: `ERROR_HANDLING_DOCUMENTATION.md`
2. Lihat contoh: `ERROR_HANDLING_EXAMPLES.md`
3. Check log files di `storage/logs/`
4. Hubungi tim development

---

**Happy error handling! 🎉**
