# 🧪 TESTING GUIDE - DosenRole Auto-Create Implementation

## ✅ Status
- ✅ Laravel server: http://127.0.0.1:8000
- ✅ Python API: http://127.0.0.1:8001
- ✅ Implementasi Code: Selesai

---

## 📋 LANGKAH-LANGKAH TESTING

### **STEP 1: Test Database Auto-Create (Quick Test)**

**Di Terminal PowerShell baru:**

```powershell
cd "d:\Semester 6-IT DEL\Manajemen Proyek\Week 16\Proyek-Kelompok-07-PA-3\ui laravel"

# Run Laravel Tinker dengan test script
php artisan tinker
>>> include 'test_implementasi.php'
```

**Expected Output:**
```
✅ TEST 1: Check Pembimbing Table
   Latest Pembimbing ID: [ID]
   User ID: [USER_ID]

✅ TEST 2: Check DosenRole Auto-Create
   Total Aktif Roles: 2 (atau lebih)
   ✅ SUCCESS: DosenRole auto-created!

✅ TEST 3: Query SEMUA Roles
   Array: [1,3] (atau kombinasi lain)
   ✅ SUCCESS: Multiple roles found
```

---

### **STEP 2: Manual Test di Browser (Full Test)**

#### **A. Login sebagai Koordinator**

1. Buka: http://127.0.0.1:8000
2. Login:
   - Username: `dosen`
   - Password: `password123`
3. Pilih yang punya PA/Kategori (Koordinator)

#### **B. Assign Pembimbing**

1. Menu: **Koordinator → Pembimbing**
2. Klik tombol **"Tambah Pembimbing"**
3. Pilih:
   - **Kelompok**: Pilih kelompok apapun
   - **Pembimbing 1**: Pilih seorang dosen (e.g., "Pak Oppir" atau dosen apapun)
   - **Pembimbing 2**: Pilih dosen berbeda (optional)
4. Klik **"Simpan"**
5. ✅ Harusnya success: "Pembimbing berhasil disimpan"

#### **C. Verify Database**

**Di Terminal PowerShell:**

```powershell
cd "d:\Semester 6-IT DEL\Manajemen Proyek\Week 16\Proyek-Kelompok-07-PA-3\ui laravel"
php artisan tinker

# Query pembimbing terbaru
>>> use App\Models\pembimbing;
>>> $pb = pembimbing::latest('id')->first();
>>> $pb->user_id  # Lihat user_id pembimbing yang baru diassign

# Query DosenRole auto-created (ganti XXXXX dengan user_id)
>>> use App\Models\DosenRole;
>>> DosenRole::where('user_id', XXXXX)->where('status', 'Aktif')->get();

# Expected output:
# Collection {
#   #items: [
#     DosenRole {
#       role_id: 3,   # ← Pembimbing 1 auto-created!
#       user_id: XXXXX,
#       status: "Aktif",
#       ...
#     }
#   ]
# }
```

#### **D. Login sebagai Dosen yang Baru Di-Assign**

1. **Logout** dari Koordinator
2. Login dengan akun dosen yang baru di-assign pembimbing
   - Username: `dosen`
   - Password: `password123`
3. **Harapan: "BELAH 2" MUNCUL!**
   - Menu **Koordinator** muncul ← (jika dia sudah punya role koordinator)
   - Menu **Pembimbing** muncul ← (NEW! hasil auto-create)

#### **E. Verifikasi Session di Browser**

**Di Chrome DevTools (F12):**

1. Buka: **Network tab** atau **Application tab**
2. Cari cookie bernama: `XSRF-TOKEN` atau check localStorage
3. Atau buat test page sederhana untuk print session:

```php
// Buat file: resources/views/test-session.blade.php
<pre>
Session dosen_roles: {{ json_encode(session('dosen_roles')) }}
Session user_id: {{ session('user_id') }}
Session role_id: {{ session('role_id') }}
</pre>
```

---

## 🐛 **TROUBLESHOOTING**

### **Problem: "Pembimbing berhasil disimpan" tapi DosenRole tidak ada**

**Debug:**
```powershell
# Cek error log Laravel
tail -f "d:\Semester 6-IT DEL\Manajemen Proyek\Week 16\Proyek-Kelompok-07-PA-3\ui laravel\storage\logs\laravel.log"

# Run test script
php artisan tinker
>>> include 'test_implementasi.php'
```

**Kemungkinan:**
- ❌ Session tidak ada (`prodi_id`, `KPA_id`, dll)
- ❌ Database constraint error
- ❌ Role ID tidak valid

---

### **Problem: Menu Pembimbing tidak muncul**

**Debug:**
```powershell
# Query session value saat login
php artisan tinker
>>> $user_id = XXXX; # ganti dengan user_id
>>> use App\Models\DosenRole;
>>> DosenRole::where('user_id', $user_id)->where('status', 'Aktif')->pluck('role_id')->toArray();

# Harusnya return: [1, 3] atau [1, 3, 5] (multiple roles)
```

**Kemungkinan:**
- ❌ Role query masih ambil hanya `.first()` (belum ter-update)
- ❌ Status role bukan 'Aktif'
- ❌ Sidebar logic error

---

### **Problem: Laravel error 500**

```powershell
# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Run serve lagi
php artisan serve
```

---

## ✅ **CHECKLIST SUKSES**

- [ ] Laravel server jalan di port 8000
- [ ] Python API server jalan di port 8001
- [ ] Login berhasil sebagai Koordinator
- [ ] Assign pembimbing berhasil
- [ ] Database query menunjukkan DosenRole auto-created (2+ roles)
- [ ] Login sebagai dosen yang di-assign → Menu "belah 2" muncul
- [ ] Klik menu Koordinator → Dashboard Koordinator
- [ ] Klik menu Pembimbing → Dashboard Pembimbing

---

## 📞 **Kalau Masih Error**

Screenshot error message dan share di sini, saya akan help debug! 👍

---

## 🎯 **Expected Result: "Belah 2"**

```
Login sebagai Pak Oppir (Koordinator + Pembimbing)
    ↓
Dashboard Koordinator (default redirect)
    ↓
Sidebar muncul:
   ✅ Menu KOORDINATOR
   ✅ Menu PEMBIMBING    ← NEW!
    ↓
Klik "Dashboard Pembimbing"
    ↓
Pindah ke Dashboard Pembimbing ✅
```

---

**Good luck! Report back dengan hasil testing! 🚀**
