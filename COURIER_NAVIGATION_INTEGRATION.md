# Courier Navigation Layout Integration

## Layout File Location
`resources/layouts/app.blade.php` atau `resources/views/layouts/courier.blade.php`

## Integration Steps

### Step 1: Add Courier Navigation Menu

Tambahkan ke sidebar atau navigation area:

```blade
@if(auth()->guard('courier')->check())
    <!-- Courier Navigation -->
    <nav class="space-y-2">
        <div class="px-4 py-2 text-sm font-semibold text-gray-900">
            Dashboard Kurir
        </div>
        
        <!-- Dashboard Link -->
        <a href="{{ route('courier.dashboard') }}" 
           class="flex items-center space-x-3 px-4 py-2 rounded-lg {{ request()->routeIs('courier.dashboard') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-100' }}">
            <x-heroicon-o-home class="h-5 w-5" />
            <span>Dashboard</span>
        </a>
        
        <!-- Deliveries Link -->
        <a href="{{ route('courier.deliveries.index') }}" 
           class="flex items-center space-x-3 px-4 py-2 rounded-lg {{ request()->routeIs('courier.deliveries.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-100' }}">
            <x-heroicon-o-truck class="h-5 w-5" />
            <span>Pengiriman</span>
            @if($readyForPickupCount ?? 0 > 0)
                <span class="ml-auto bg-red-500 text-white text-xs font-bold rounded-full px-2 py-1">
                    {{ $readyForPickupCount }}
                </span>
            @endif
        </a>
        
        <!-- History Link -->
        <a href="{{ route('courier.deliveries.history') }}" 
           class="flex items-center space-x-3 px-4 py-2 rounded-lg {{ request()->routeIs('courier.deliveries.history') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-100' }}">
            <x-heroicon-o-history class="h-5 w-5" />
            <span>History</span>
        </a>
    </nav>
@endif
```

### Step 2: Pass Variables to Layout

Dari CourierDeliveryController, pastikan `readyForPickupCount` dipass:

```php
public function index(): View
{
    // ... existing code ...
    
    return view('courier.index', [
        // ... data ...
    ])->with([
        'readyForPickupCount' => $readyForPickupCount,
    ]);
}
```

Atau di layout controller/middleware:

```php
view()->share('readyForPickupCount', 
    auth()->user()->courier 
        ? BookProduct::where('id_courier', auth()->user()->courier->id)
            ->where('order_status', OrderStatus::READY_FOR_PICKUP)
            ->count() 
        : 0
);
```

### Step 3: Add User Menu in Header

Tambahkan di header/profile dropdown:

```blade
@if(auth()->guard('courier')->check())
    <div class="border-b border-gray-200 mb-2">
        <div class="px-4 py-2 text-sm text-gray-500">
            Kurir: <span class="font-semibold text-gray-900">
                {{ auth()->user()->courier->nama ?? auth()->user()->name }}
            </span>
        </div>
    </div>
@endif

<a href="{{ route('courier.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
    Dashboard
</a>

<a href="{{ route('courier.deliveries.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
    Pengiriman
</a>

<a href="{{ route('courier.deliveries.history') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
    History
</a>
```

---

## Layout Structure Example

```html
<!DOCTYPE html>
<html>
<head>
    <!-- Head content -->
</head>
<body>
    <!-- Navigation Header -->
    <nav class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4">
            <!-- Logo -->
            <!-- User Menu with Courier Info -->
        </div>
    </nav>

    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-white border-r border-gray-200">
            <!-- Courier Navigation Menu -->
            <!-- Other menus -->
        </aside>

        <!-- Main Content -->
        <main class="flex-1">
            <!-- Page Content -->
            @yield('content')
        </main>
    </div>

    <!-- Scripts -->
    @include('components.booking-status-actions-scripts')
</body>
</html>
```

---

## Mobile Navigation Example

```blade
<!-- Mobile Menu Button -->
<button @click="sidebarOpen = !sidebarOpen" class="md:hidden">
    <x-heroicon-o-menu class="h-6 w-6" />
</button>

<!-- Mobile Sidebar -->
<div x-show="sidebarOpen" class="md:hidden fixed inset-0 bg-white z-40">
    <div class="p-4">
        <!-- Same navigation menu as above -->
    </div>
</div>
```

---

## Profile Information Integration

```blade
<!-- User Profile Card in Sidebar -->
<div class="p-4 border-b border-gray-200">
    <div class="flex items-center space-x-3">
        @if(auth()->user()->photo)
            <img src="{{ asset(auth()->user()->photo) }}" 
                 alt="{{ auth()->user()->name }}"
                 class="h-10 w-10 rounded-full object-cover">
        @else
            <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                <x-heroicon-o-user class="h-6 w-6 text-gray-600" />
            </div>
        @endif
        
        <div class="flex-1">
            <p class="font-semibold text-sm text-gray-900">
                {{ auth()->user()->courier->nama ?? auth()->user()->name }}
            </p>
            <p class="text-xs text-gray-500">Kurir</p>
        </div>
    </div>
</div>
```

---

## Active Route Highlighting

```blade
<!-- Highlight active routes -->
<a href="{{ route('courier.dashboard') }}" 
   @class([
       'flex items-center space-x-3 px-4 py-2 rounded-lg',
       'bg-blue-50 text-blue-600' => request()->routeIs('courier.dashboard'),
       'text-gray-700 hover:bg-gray-100' => !request()->routeIs('courier.dashboard'),
   ])>
    <x-heroicon-o-home class="h-5 w-5" />
    <span>Dashboard</span>
</a>
```

---

## Breadcrumb Integration

Setiap halaman menunjukkan:

```blade
<nav class="flex" aria-label="Breadcrumb">
    <ol class="flex items-center space-x-2">
        <li>
            <a href="{{ route('courier.dashboard') }}" class="text-gray-500 hover:text-gray-700">
                Courier
            </a>
        </li>
        <li>
            <x-heroicon-o-chevron-right class="h-4 w-4 text-gray-400" />
        </li>
        <li class="text-gray-900 font-medium">
            @if(request()->routeIs('courier.deliveries.index'))
                Pengiriman
            @elseif(request()->routeIs('courier.deliveries.show'))
                Detail Pengiriman
            @elseif(request()->routeIs('courier.deliveries.history'))
                History Pengiriman
            @endif
        </li>
    </ol>
</nav>
```

---

## Script Integration

Pastikan script untuk SweetAlert2 dan foto upload ada:

```blade
@push('scripts')
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Photo Upload Dialog Handler -->
    <script>
        async function openPhotoUploadDialog(action, bookingId, type) {
            // Implementation in booking-status-actions component
        }
        
        // Courier specific actions
        async function courierPickupDelivery(bookingId, type) {
            // Implementation
        }
        
        // ... other functions
    </script>
@endpush
```

---

## Notifications Integration

Tambahkan notifikasi untuk delivery updates:

```blade
<!-- Notification Toast -->
@if($message = session('success'))
    <div class="fixed top-4 right-4 bg-green-500 text-white px-4 py-3 rounded-lg shadow-lg" x-data="{ open: true }" x-show="open">
        {{ $message }}
    </div>
@endif

@if($error = session('error'))
    <div class="fixed top-4 right-4 bg-red-500 text-white px-4 py-3 rounded-lg shadow-lg">
        {{ $error }}
    </div>
@endif
```

---

## Dropdown Menu Integration

```blade
<!-- In header user menu -->
@if(auth()->guard('courier')->check())
    <div class="border-t border-gray-200 pt-2">
        <span class="block px-4 py-2 text-xs font-semibold text-gray-500 uppercase">
            Kurir
        </span>
        
        <a href="{{ route('courier.dashboard') }}" 
           class="block px-4 py-2 text-sm hover:bg-gray-100">
            🏠 Dashboard
        </a>
        
        <a href="{{ route('courier.deliveries.index') }}" 
           class="block px-4 py-2 text-sm hover:bg-gray-100">
            📦 Pengiriman
        </a>
        
        <a href="{{ route('courier.deliveries.history') }}" 
           class="block px-4 py-2 text-sm hover:bg-gray-100">
            📋 History
        </a>
    </div>
@endif
```

---

## Layout Variables to Pass

Dari controller atau middleware, pass:

```php
view()->share([
    'readyForPickupCount' => $courier ? 
        BookProduct::where('id_courier', $courier->id)
            ->where('order_status', OrderStatus::READY_FOR_PICKUP)
            ->count() : 0,
    'activeCourier' => auth()->guard('courier')->check(),
    'courierName' => auth()->user()->courier->nama ?? null,
]);
```

---

## CSS Classes Reference

Tailwind classes used:

```
Grid: grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4
Padding: px-4 py-2, p-6
Borders: border border-gray-200
Colors: text-gray-700, bg-blue-50, text-blue-600
Hover: hover:bg-gray-100
Responsive: md:hidden, lg:block
```

---

## Testing Navigation

```php
// Test courier can access dashboard
$courier = Courier::factory()->create();
$user = User::factory()->create(['courier_id' => $courier->id]);

$response = $this->actingAs($user, 'web')
    ->get(route('courier.dashboard'));

$response->assertStatus(200)
    ->assertViewIs('courier.index');
```

---

**Last Updated**: 2024  
**Version**: 1.0  
**Status**: Ready for Integration
