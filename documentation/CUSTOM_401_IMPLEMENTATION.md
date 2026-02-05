# Custom 401 Unauthorized Page Implementation

## ✅ Implementasi Selesai

Custom 401 unauthorized page telah dibuat dan dikonfigurasi untuk menangani akses unauthenticated ke protected routes.

## 📁 File yang Dibuat/Dimodifikasi

### 1. **FILE BARU: `resources/views/errors/401.blade.php`** (70 lines)

Custom 401 error page dengan fitur:
- Design yang menarik dengan gradient red-pink background
- Icon visual 401 error
- Pesan yang jelas dalam Bahasa Indonesia
- 2 action buttons:
  - **"Login Sekarang"** - Membuka auth modal dengan tab login
  - **"Kembali ke Beranda"** - Redirect ke halaman utama
- Info box dengan penjelasan tentang registrasi
- Integrasi dengan `<livewire:auth-modal />` component
- Fully responsive design

### 2. **MODIFIED: `bootstrap/app.php`**

Menambahkan exception handler untuk menangani `AuthenticationException`:

```php
->withExceptions(function (Exceptions $exceptions): void {
    // Handle unauthenticated access to protected routes
    $exceptions->render(function (Throwable $e, $request) {
        // Handle authentication exception for all guards
        if ($e instanceof \Illuminate\Auth\AuthenticationException) {
            // For JSON requests (API), return JSON response
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Unauthenticated'
                ], 401);
            }
            
            // For HTML requests, show the custom 401 page
            return response()->view('errors.401', [], 401);
        }
    });
})->create();
```

**Fitur:**
- Automatically catch semua `AuthenticationException` 
- Redirect HTML requests ke custom 401 page
- Return JSON response untuk API requests
- Works dengan semua guards (web, admin, officer, courier)

---

## 🔒 Protected Routes yang Affected

Semua routes berikut sudah protected dengan `auth` middleware dan akan redirect ke 401 page:

### User Routes (auth middleware)
```
GET    /profile                      # Show user profile
PUT    /profile                      # Update profile
POST   /profile/photo                # Update photo
POST   /profile/language             # Switch language
GET    /my-booking                   # View my bookings
POST   /cart/checkout                # Checkout cart
GET    /booking/cart                 # View cart booking
POST   /booking/cart                 # Process cart booking
GET    /booking/create/{product}     # Create booking for product
GET    /booking/create-multi         # Create multi-product booking
POST   /booking                      # Store booking
GET    /booking/package/{package}    # Create package booking
POST   /booking/package/{package}    # Store package booking
```

### Admin Routes (auth:web,admin middleware)
```
/admin/* - All admin routes
```

### Officer Routes (auth:web,officer middleware)
```
/officer/* - All officer routes
```

### Courier Routes (auth:web,courier middleware)
```
/courier/* - All courier routes
```

---

## 🎯 Cara Kerja

1. **User yang tidak authenticated** mencoba akses route protected
   ```
   GET /profile  (tanpa login)
   ```

2. **Laravel throws `AuthenticationException`**
   ```
   Illuminate\Auth\AuthenticationException
   ```

3. **Exception handler** menangkap dan merender custom 401 page
   ```php
   return response()->view('errors.401', [], 401);
   ```

4. **User melihat 401 page** dengan:
   - Pesan "Anda harus login terlebih dahulu"
   - Button "Login Sekarang" yang membuka modal auth
   - Button "Kembali ke Beranda"

5. **User klik "Login Sekarang"**
   ```javascript
   onclick="openAuthModal('login')"
   ```

6. **Auth modal terbuka** dengan form login/register

---

## 🚀 Testing

### Test 1: Akses Profile Tanpa Login
```bash
# Buka di browser (tanpa login)
http://localhost:8000/profile

# Expected: Custom 401 page akan ditampilkan
```

### Test 2: Klik Login Button di 401 Page
```bash
# Di 401 page, klik button "Login Sekarang"

# Expected: Auth modal akan terbuka dengan tab login
```

### Test 3: JSON API Request (Unauthenticated)
```bash
curl -H "Accept: application/json" \
     http://localhost:8000/profile

# Expected: JSON response
{
  "message": "Unauthenticated"
}
```

### Test 4: Login Kemudian Akses Profile
```bash
# 1. Login via auth modal
# 2. Navigate to /profile
# 3. Expected: Profile page ditampilkan (tidak 401)
```

---

## 📋 Setup Checklist

- [x] Custom 401 error page dibuat (`resources/views/errors/401.blade.php`)
- [x] Exception handler dikonfigurasi (`bootstrap/app.php`)
- [x] Integration dengan auth modal sudah ready
- [x] PHP syntax validated
- [x] Assets built successfully
- [x] Responsive design implemented
- [x] Support untuk JSON API requests
- [x] Support untuk multiple guards (web, admin, officer, courier)

---

## 💡 Fitur Tambahan

### Automatic Redirect untuk Setiap Guard
Exception handler bekerja untuk semua guards:
- `auth:web` - User guard (default)
- `auth:web,admin` - Admin guard
- `auth:web,officer` - Officer guard  
- `auth:web,courier` - Courier guard

### JSON API Support
Jika request adalah JSON request (Content-Type: application/json atau Accept header), akan return JSON response:
```json
{
  "message": "Unauthenticated"
}
```

### Modal Auth Integration
401 page sudah include `<livewire:auth-modal />` component, jadi:
- User bisa langsung login dari 401 page
- Modal supports login dan register
- Automatic redirect after successful login

---

## 🔧 Customization

### Ubah Pesan di 401 Page
Edit di `resources/views/errors/401.blade.php`, cari dan ganti text:
```blade
<!-- Ubah pesan utama -->
<p class="text-gray-600 text-base mb-8 leading-relaxed">
    Anda harus login terlebih dahulu untuk mengakses halaman ini. Silakan login dengan akun Anda untuk melanjutkan.
</p>

<!-- Ubah button text -->
<span>Login Sekarang</span>
<span>Kembali ke Beranda</span>
```

### Ubah Warna/Design
Edit color classes di 401 page:
```blade
<!-- From -->
class="bg-green-500 hover:bg-green-600"

<!-- To (contoh: blue) -->
class="bg-blue-500 hover:bg-blue-600"
```

### Redirect ke Page Lain (Custom)
Di `bootstrap/app.php`, modify exception handler:
```php
return response()->view('custom-error-page', [], 401);
// atau
return redirect()->route('custom.error.page');
```

---

## ✨ Hasil Akhir

✅ **Custom 401 unauthorized page**
- Menampilkan pesan yang jelas dan user-friendly
- Integrasi button langsung ke auth modal
- Design yang menarik dan responsive

✅ **Automatic handling untuk semua protected routes**
- User yang tidak authenticated akan redirect ke 401 page
- Supports multiple guards
- JSON API support

✅ **No placeholder pages needed**
- Menggunakan existing modal auth component
- Tidak ada file baru di routes
- Semua existing user pages tetap lengkap

---

## 📞 Notes

- Jangan ubah `bootstrap/app.php` exception handler jika ada exception handling lainnya
- Pastikan `resources/views/errors/401.blade.php` selalu menginclude `<livewire:auth-modal />`
- Modal auth component (`AuthModal.php`) sudah exist dan fully functional
- Testing dilakukan dengan browser atau curl untuk memverifikasi 401 page

