# 🎯 Modal Auth Implementation - Visual Guide

## User Flow Diagram

```
┌─────────────────────────────────────────────────────────────┐
│                    Landing Page (Home)                       │
├─────────────────────────────────────────────────────────────┤
│                                                               │
│  [Logo]  [Kategori ▼] [Cara Sewa] [Kontak]     [Masuk]      │
│                                                      ↓        │
│                                                   Tombol      │
│                                                   Masuk       │
│                                                               │
│  (Hero Section dengan background image)                      │
│  (Search Bar)                                                │
│  (Products Grid)                                             │
│  (Brands Carousel)                                           │
│  (Packages)                                                  │
│  (Footer)                                                    │
│                                                               │
└─────────────────────────────────────────────────────────────┘
                           ↓ Click "Masuk"
                           
┌──────────────────────────────────────────────────────────────┐
│                  MODAL POPUP APPEARS                          │
├──────────────────────────────────────────────────────────────┤
│                                                                │
│  ╔════════════════════════════════════════════════════════╗  │
│  ║  Masuk ke Akun                                    [X]  ║  │
│  ║─────────────────────────────────────────────────────  ║  │
│  ║  [Masuk]  [Daftar]                                    ║  │
│  ║─────────────────────────────────────────────────────  ║  │
│  ║                                                        ║  │
│  ║  📧 Email atau Nomor Telepon                         ║  │
│  ║  [_______________________________]                     ║  │
│  ║                                                        ║  │
│  ║  🔐 Kata Sandi                                        ║  │
│  ║  [_______________________________]                     ║  │
│  ║                                                        ║  │
│  ║  ☑️  Ingat saya                                       ║  │
│  ║                                                        ║  │
│  ║  [             MASUK              ]                   ║  │
│  ║                                                        ║  │
│  ║  Lupa kata sandi?                                     ║  │
│  ║                                                        ║  │
│  ║  ─────────────── atau ───────────────                ║  │
│  ║                                                        ║  │
│  ║  [🔍 Google]  [f Facebook]                           ║  │
│  ║                                                        ║  │
│  ╚════════════════════════════════════════════════════════╝  │
│                      Modal Overlay (blur)                      │
└──────────────────────────────────────────────────────────────┘
```

---

## Tab Switching

```
┌─────────────────────────────────────┐
│  [Masuk] ─── [Daftar]               │  ← Click Daftar
│═════════                             │
│  (Login Form visible)                │
└─────────────────────────────────────┘
                 ↓ Click
┌─────────────────────────────────────┐
│  [Masuk] ─── [Daftar]               │
│         ════════════                 │
│  (Register Form visible)             │
└─────────────────────────────────────┘
```

---

## Component Architecture

```
Landing Page (Livewire: Home\Landing)
    ├── navbar (with Masuk button)
    │   └── @click="$dispatch('openAuthModal')"
    ├── hero section
    ├── products grid
    ├── brands carousel
    ├── packages
    └── footer
         └── <livewire:auth-modal /> ← HERE

                        ↓

            AuthModal Component (Livewire)
                ├── State Management
                │   ├── isOpen (boolean)
                │   ├── activeTab (login/register)
                │   └── Form Fields
                │
                ├── Event Listeners
                │   ├── #[On('openAuthModal')]
                │   ├── #[On('authModal:openLogin')]
                │   └── #[On('authModal:openRegister')]
                │
                ├── Methods
                │   ├── openModal($tab)
                │   ├── closeModal()
                │   ├── setActiveTab($tab)
                │   ├── handleLogin()
                │   └── handleRegister()
                │
                └── Rendered View (auth-modal.blade.php)
                    ├── Modal Overlay
                    ├── Modal Container
                    ├── Header (with close button)
                    ├── Tab Navigation
                    ├── Login Form (conditional)
                    ├── Register Form (conditional)
                    └── Social Buttons
```

---

## Data Flow

### Login Process
```
User fills form
    ↓
Click "Masuk" button
    ↓
wire:submit="handleLogin" triggered
    ↓
Validation (server-side)
    ├─ Valid → Attempt authentication
    │   ├─ Check web guard (User)
    │   ├─ Check officer guard
    │   ├─ Check admin guard
    │   └─ Check courier guard
    │
    └─ Invalid → Show error messages
    
    ↓
Success → Redirect to appropriate dashboard
    ├─ Admin/Super-admin → /admin/dashboard
    ├─ Officer → /officer/dashboard
    ├─ Courier → /courier/dashboard
    └─ User → /home
    
    Modal auto-closes
```

### Register Process
```
User fills form
    ↓
Click "Daftar" button
    ↓
wire:submit="handleRegister" triggered
    ↓
Validation (server-side)
    ├─ Name: required, string, max 255
    ├─ Email: required, email, unique
    ├─ Password: required, min 8
    ├─ Confirm: required, match password
    └─ Terms: required, accepted
    
    ↓
Valid → Create user & auto-login
    ↓
Success → Redirect to /home
    
    Modal auto-closes
```

---

## Event Flow (Alpine.js + Livewire)

```
Browser Event
    ↓
$dispatch('openAuthModal', {tab: 'login'})
    ↓
@openAuthModal.window listener (Alpine.js in auth-modal.blade.php)
    ↓
$wire.handleOpenAuthModal($event.detail.tab)  ← Calls Livewire method
    ↓
AuthModal::handleOpenAuthModal($tab)  ← PHP method
    ↓
$this->openModal($tab)  ← Updates state
    ↓
Livewire re-renders view with isOpen = true
    ↓
Alpine.js detects change
    ↓
Modal animates in (scale: 95% → 100%, opacity: 0 → 1)
```

---

## File Relationships

```
resources/js/app.js
  ├── Exports: window.openAuthModal()
  │            window.openLoginModal()
  │            window.openRegisterModal()
  └── → Dispatches CustomEvent 'openAuthModal'

resources/views/livewire/home/landing.blade.php
  ├── Contains: @click="$dispatch('openAuthModal')" button
  ├── Mounts: <livewire:auth-modal />
  └── → Triggers event listeners

app/Http/Livewire/AuthModal.php
  ├── Listens: #[On('openAuthModal')]
  ├── Manages: State (isOpen, activeTab, formFields)
  ├── Methods: handleLogin(), handleRegister()
  └── Renders: resources/views/livewire/auth-modal.blade.php

resources/views/livewire/auth-modal.blade.php
  ├── Alpine.js: @openAuthModal.window
  ├── Forms: Login & Register
  ├── Validation: Real-time feedback
  └── Actions: wire:submit to Livewire methods
```

---

## Authentication Guards

The `handleLogin()` method tries these in order:

1. **web guard** (default Users table)
   - ✓ Regular users
   - ✓ Admins with role 'admin' or 'super-admin'
   - ✓ Officers with role 'officer'

2. **officer guard** (dedicated Officers table)
   - ✓ Officers table entries

3. **admin guard** (dedicated Admins table)
   - ✓ Admins table entries

4. **courier guard** (couriers)
   - ✓ Courier role users

---

## Styling Classes (Tailwind)

```
Modal Container
  ├── fixed, inset-0, z-50      ← Full screen positioning
  ├── flex, items-center        ← Center vertically
  ├── justify-center            ← Center horizontally
  └── pointer-events-none       ← Don't block clicks

Modal Box
  ├── bg-white                  ← White background
  ├── rounded-2xl               ← Rounded corners
  ├── shadow-2xl                ← Large shadow
  ├── max-w-md                  ← Max width 448px
  ├── max-h-[90vh]              ← Max height 90% viewport
  ├── overflow-y-auto           ← Scrollable content
  └── transform, transition     ← Smooth animations

Overlay
  ├── fixed, inset-0, z-40      ← Cover entire screen
  ├── bg-black/40               ← 40% transparent black
  ├── backdrop-blur-sm          ← Blur effect
  └── cursor-pointer            ← Click to close
```

---

## Responsive Breakpoints

```
Mobile (< 768px)
  ├── Modal: full width - 32px padding
  ├── Modal: max-h-[90vh]
  └── Stacked layout

Tablet (768px - 1024px)
  ├── Modal: similar to mobile
  └── Better spacing

Desktop (> 1024px)
  ├── Modal: max-w-md (448px)
  ├── Centered on screen
  └── Full features visible
```

---

## Color Scheme (Current)

```
Primary: Green (#10b981)
  ├── bg-green-500   #10b981
  ├── bg-green-600   #059669
  └── Used for: Buttons, active states

Secondary: Gray (#6b7280)
  ├── text-gray-500  #6b7280
  ├── text-gray-600  #4b5563
  └── Used for: Text, labels, secondary content

Success: Green (same as primary)
  └── Used for: Form success states

Danger: Red (#dc2626)
  ├── text-red-600   #dc2626
  └── Used for: Error messages

Borders: Gray (#d1d5db)
  └── border-gray-300 #d1d5db
```

---

## Animation Timings

```
Modal Entrance
  ├── Scale: 95% → 100%    (smooth growth)
  ├── Opacity: 0 → 1       (fade in)
  ├── Duration: 300ms
  └── Easing: ease-in-out

Form Submission
  ├── Button opacity: 1 → 0.5 (disabled)
  ├── Show spinner: fade in
  ├── Duration: instant
  └── Revert: on response

Tab Switch
  ├── Form fade out: instant
  ├── Form fade in: instant
  └── Smooth content switch
```

---

## Performance Considerations

✅ **Optimized for**
- Livewire 4 (reactive, minimal full page reload)
- Alpine.js (lightweight, no jQuery)
- Tailwind CSS (utility-first, minimal CSS)
- Single component mounting (modal)
- Form validation (real-time feedback)

⚡ **Performance Benefits**
- Modal reused (not destroyed/recreated)
- Single Livewire component (minimal overhead)
- No page reload on login/register
- Smooth animations (GPU accelerated)
- Lazy loading of non-critical assets

---

## Conclusion

The modal authentication system is:
- ✅ **Fully Integrated** - Works seamlessly with existing system
- ✅ **User-Friendly** - Modern Tokopedia-style UX
- ✅ **Developer-Friendly** - Easy to customize and extend
- ✅ **Secure** - All auth guards and validations in place
- ✅ **Responsive** - Works on all devices
- ✅ **Performant** - Optimized for speed

Ready for production! 🚀
