<!-- Courier Navigation Menu -->
<div class="space-y-2">
    <a href="{{ route('courier.deliveries.index') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg hover:bg-gray-100 {{ request()->routeIs('courier.deliveries.index') ? 'bg-blue-50 text-blue-600' : 'text-gray-700' }}">
        <x-heroicon-o-truck class="h-5 w-5" />
        <span>Pengiriman Aktif</span>
        @if($readyForPickupCount ?? 0 > 0)
            <span class="ml-auto bg-red-500 text-white text-xs font-bold rounded-full h-6 w-6 flex items-center justify-center">
                {{ $readyForPickupCount }}
            </span>
        @endif
    </a>

    <a href="{{ route('courier.deliveries.history') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg hover:bg-gray-100 {{ request()->routeIs('courier.deliveries.history') ? 'bg-blue-50 text-blue-600' : 'text-gray-700' }}">
        <x-heroicon-o-clock class="h-5 w-5" />
        <span>History Pengiriman</span>
    </a>
</div>
