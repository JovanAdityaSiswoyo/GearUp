<header class="bg-white shadow-sm sticky top-0 z-10">
    <div class="flex items-center justify-between px-8 py-4">
        <div>
            <h2 class="text-2xl font-semibold text-gray-800">{{ $title ?? 'Dashboard' }}</h2>
            <p class="text-sm text-gray-500">Welcome back, {{ auth()->guard('admin')->user()->name ?? auth()->user()->name }}!</p>
        </div>
        <div class="flex items-center space-x-4">
            <div class="flex items-center space-x-3">
                <div class="text-right">
                    <p class="text-sm font-medium text-gray-800">{{ auth()->guard('admin')->user()->name ?? auth()->user()->name }}</p>
                    <p class="text-xs text-gray-500">
                        {{ auth()->guard('admin')->check() ? 'Administrator' : (auth()->user()->roles->first()->name ?? 'User') }}
                    </p>
                </div>
                <div class="w-10 h-10 bg-linear-to-br from-purple-600 to-pink-500 rounded-full flex items-center justify-center text-white font-semibold">
                    {{ substr(auth()->guard('admin')->user()->name ?? auth()->user()->name, 0, 1) }}
                </div>
                <form action="{{ route('logout') }}" method="POST" class="inline" id="logout-form">
                    @csrf
                    <button type="submit" onclick="confirmLogout(event)" class="text-gray-600 hover:text-red-600 transition">
                        <x-heroicon-o-arrow-right-on-rectangle class="h-6 w-6" />
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>

<script>
    function confirmLogout(event) {
        event.preventDefault();

        if (typeof Swal === 'undefined') {
            document.getElementById('logout-form').submit();
            return;
        }

        Swal.fire({
            title: 'Logout?',
            text: "Are you sure you want to logout?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#9333ea',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, logout!'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('logout-form').submit();
            }
        });
    }
</script>
