# Modal Auth - Quick Reference

## Yang Berubah

Halaman login dan register **tidak ada lagi sebagai halaman terpisah**. Mereka sekarang adalah **modal popup** yang muncul di halaman home, seperti di Tokopedia.

## Cara Pakai

### User Experience
1. User buka website → landing page terbuka
2. User click tombol **"Masuk"** di navbar
3. Modal popup muncul dengan form login
4. User bisa switch ke tab **"Daftar"** untuk register
5. Setelah login sukses → modal menutup, user ter-redirect ke dashboard

### Developer - Membuka Modal Dari Blade
```blade
<!-- Button yang buka modal login -->
<button @click="$dispatch('openAuthModal', {tab: 'login'})">
    Masuk
</button>

<!-- Button yang buka modal register -->
<button @click="$dispatch('openAuthModal', {tab: 'register'})">
    Daftar
</button>
```

### Developer - Membuka Modal Dari JavaScript
```javascript
openAuthModal('login')      // Buka login
openAuthModal('register')   // Buka register
openLoginModal()            // Shortcut login
openRegisterModal()         // Shortcut register
```

### Developer - Membuka Modal Dari Livewire Component
```php
#[On('openAuthModal')]
public function handleOpenAuthModal($tab = 'login')
{
    // Handle opening modal
}
```

## File yang Diubah

| File | Status | Perubahan |
|------|--------|-----------|
| `routes/web.php` | Modified | Login/register routes redirect ke home |
| `resources/views/livewire/home/landing.blade.php` | Modified | Tombol login trigger modal |
| `resources/js/app.js` | Modified | Helper functions untuk dispatch events |
| `app/Http/Livewire/AuthModal.php` | NEW | Component untuk manage modal state |
| `resources/views/livewire/auth-modal.blade.php` | NEW | Modal UI dan form |

## Features

✅ **Login Form**
- Email/password validation
- Remember me checkbox
- Multi-role support (User, Admin, Officer, Courier)
- Social login buttons
- Forgot password link

✅ **Register Form**
- Name, email, password inputs
- Confirm password validation
- Terms & conditions checkbox
- Auto-login setelah register
- Social login buttons

✅ **UI/UX**
- Smooth animations (Alpine.js)
- Modal dengan blur background
- Tab navigation (Login/Register)
- Responsive design (mobile-friendly)
- Loading states

## Customization

### Edit Colors
Edit di `resources/views/livewire/auth-modal.blade.php`:
```blade
<!-- Change dari -->
<button class="bg-green-500 hover:bg-green-600">

<!-- Menjadi (contoh: blue) -->
<button class="bg-blue-500 hover:bg-blue-600">
```

### Edit Text/Labels
Di file yang sama, ubah text sesuai kebutuhan:
```blade
<h2>Masuk ke Akun</h2>        <!-- Edit text ini -->
<label>Email atau Nomor Telepon</label>  <!-- Edit ini -->
```

### Edit Form Fields
Add/remove input fields di form login atau register section di `auth-modal.blade.php`

## Troubleshooting

### Modal tidak muncul
1. Clear cache: `php artisan cache:clear`
2. Check browser console (F12)
3. Ensure Livewire loaded: check HTML source

### Form tidak submit
1. Check console untuk validation errors
2. Check server logs: `storage/logs/laravel.log`
3. Ensure database connection OK

### Styling tidak sesuai
1. Rebuild assets: `npm run build`
2. Clear browser cache: Ctrl+Shift+Delete
3. Hard refresh: Ctrl+F5

## Next Steps

1. **Test**: Buka website, coba login/register
2. **Customize**: Edit colors, text, styling sesuai brand
3. **Add Features**: Email verification, password reset, OAuth
4. **Monitor**: Check analytics untuk modal usage

## Links

- Implementation docs: `documentation/MODAL_AUTH_IMPLEMENTATION.md`
- Livewire: https://livewire.laravel.com
- Alpine.js: https://alpinejs.dev
