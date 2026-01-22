# 👤 User Profile - Quick Start Guide

## 🎯 Apa Itu?

Fitur profile user yang memungkinkan:
- 📸 Upload dan display foto profile
- ✏️ Edit data profil (nama, email, nomor telepon)
- 🔗 Profile section di home page (clickable)

## 🚀 Cara Menggunakan

### Step 1: Login
1. Login dengan akun user Anda
2. Kembali ke home page

### Step 2: Lihat Profile Section
Di navbar home page, kanan atas akan terlihat:
- **Foto profile Anda** (circular image atau initial)
- **Nama Anda**
- **Keluar button**

### Step 3: Klik Profile
Klik pada foto atau nama untuk navigasi ke profile page

## 📸 Upload Foto Profile

### Langkah-langkah:
1. Buka profile page (`/profile`)
2. Lihat foto besar di bagian atas
3. **Hover** pada foto - akan muncul icon camera
4. **Klik** icon camera
5. **Pilih** file gambar dari komputer
6. **Tunggu** upload selesai (auto refresh)
7. ✅ Foto ter-update!

### Requirements:
- **Format:** JPG, PNG, GIF
- **Ukuran:** Max 2MB
- **Dimensi:** Tidak terbatas (akan di-crop circular)

## ✏️ Edit Profil

### Langkah-langkah:
1. Scroll ke bagian "Edit Profil"
2. Update salah satu atau semua field:
   - **Nama Lengkap**
   - **Email**
   - **Nomor Telepon**
3. Klik **"Simpan Perubahan"**
4. ✅ Data ter-update!

### Validasi:
- **Nama:** Required, max 255 karakter
- **Email:** Required, must be valid email, unique
- **Telepon:** Optional, max 20 karakter

## 📍 Profile Section di Home

Setelah login, di home page navbar (top-right) akan terlihat:

```
[FOTO] Nama User  [Keluar]
```

**Interaksi:**
- Klik foto/nama → Navigate ke profile page
- Hover → Opacity change (visual feedback)
- Hidden di mobile → Visible di tablet+

## 🎨 UI Elements

### Profile Page Struktur:
```
┌─ Header
│  ├─ Logo
│  ├─ Back Button
│  └─ Logout Button
│
├─ Profile Photo Section
│  ├─ Large Circular Foto
│  ├─ Camera Upload Icon
│  └─ Nama + Email
│
├─ Profile Info Cards
│  ├─ Nama Lengkap
│  ├─ Email
│  ├─ Nomor Telepon
│  └─ Status Verifikasi
│
├─ Edit Profile Form
│  ├─ Nama Field
│  ├─ Email Field
│  ├─ Telepon Field
│  └─ Simpan Button
│
└─ Account Info Card
   ├─ User ID
   ├─ Join Date
   └─ Last Updated
```

## 🔒 Security Features

✅ Login required (middleware auth)
✅ File validation (image only, max 2MB)
✅ CSRF protection (form token)
✅ Old photo cleanup (auto delete)
✅ Email uniqueness validation

## ⚡ Tips & Tricks

1. **Foto Default:** Jika belum upload, tampil initial nama (A, B, C, etc.)
2. **Foto Circular:** Semua foto di-crop circular untuk consistency
3. **Quick Profile:** Klik foto di navbar untuk quick access ke profile
4. **Mobile Friendly:** Design responsive untuk semua ukuran screen
5. **Success Message:** Feedback otomatis setelah perubahan

## 🐛 Troubleshooting

### Foto Tidak Upload
- Check file size (max 2MB)
- Check file format (JPG, PNG, GIF only)
- Check internet connection
- Try refresh page

### Edit Profil Error
- Check email belum digunakan user lain
- Check field tidak kosong (kecuali phone)
- Check format email valid
- Try refresh page

### Foto Tidak Tampil
- Check storage link (`php artisan storage:link`)
- Check file permission
- Clear browser cache
- Try logout & login

### Profile Page Blank
- Check browser console untuk error
- Clear browser cache
- Check Laravel logs (`storage/logs/`)
- Try different browser

## 📱 Responsive Design

- **Mobile (xs-sm):** Stacked layout, hidden profile in navbar
- **Tablet (md):** 2-column grid, profile visible in navbar
- **Desktop (lg+):** Full layout, profile prominent in navbar

## 🔗 Routes

| Method | Route | Purpose |
|--------|-------|---------|
| GET | `/profile` | View profile page |
| PUT | `/profile` | Update profile data |
| POST | `/profile/photo` | Upload profile photo |

## 📂 File Locations

- **Profile Page:** `resources/views/profile/show.blade.php`
- **Controller:** `app/Http/Controllers/ProfileController.php`
- **Uploaded Photos:** `storage/app/public/profiles/`
- **Routes:** `routes/web.php` (profile group)

## ✨ Features

✅ View profile data
✅ Upload foto profile
✅ Edit nama, email, telepon
✅ View account info
✅ Email verification status
✅ Responsive design
✅ Success notifications
✅ Validation errors
✅ Circular foto display
✅ Auto photo cleanup

## 🎯 Next Steps

1. **Test Upload:** Upload foto dari profile page
2. **Test Edit:** Edit profil data dan save
3. **Check Home:** Lihat foto di navbar home page
4. **Mobile Test:** Test di mobile device

---

**Created:** January 22, 2026  
**Status:** ✅ Ready to Use
