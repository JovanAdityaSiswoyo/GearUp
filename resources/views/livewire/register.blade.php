<div class="max-w-md mx-auto mt-10 p-6 bg-white rounded shadow">
    <form wire:submit.prevent="register">
        <h2 class="text-2xl font-bold mb-6">Register</h2>

        <div class="mb-4">
            <label for="name" class="block text-sm font-medium">Name</label>
            <input type="text" id="name" wire:model="name" class="w-full border rounded px-3 py-2" required>
            @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div class="mb-4">
            <label for="email" class="block text-sm font-medium">Email</label>
            <input type="email" id="email" wire:model="email" class="w-full border rounded px-3 py-2" required>
            @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div class="mb-4">
            <label for="password" class="block text-sm font-medium">Password</label>
            <input type="password" id="password" wire:model="password" class="w-full border rounded px-3 py-2" required>
            @error('password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div class="mb-4">
            <label for="password_confirmation" class="block text-sm font-medium">Confirm Password</label>
            <input type="password" id="password_confirmation" wire:model="password_confirmation" class="w-full border rounded px-3 py-2" required>
            @error('password_confirmation') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div class="mb-4 flex items-center">
            <input type="checkbox" id="terms" wire:model="terms" class="mr-2">
            <label for="terms" class="text-sm">I agree to the terms and conditions</label>
            @error('terms') <span class="text-red-500 text-xs ml-2">{{ $message }}</span> @enderror
        </div>

        <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">Register</button>
    </form>
</div>
