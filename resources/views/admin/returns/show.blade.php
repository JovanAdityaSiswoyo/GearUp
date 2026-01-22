<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Return Details - Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    @include('sweetalert::alert')
    <div class="min-h-screen">
        @include('admin.partials.sidebar')

        <div class="ml-64">
            @include('admin.partials.header', ['title' => 'Return Details'])

            <main class="p-8">
                <div class="max-w-4xl mx-auto">
                    <div class="bg-white rounded-xl shadow-sm p-8">
                        <div class="flex justify-between items-start mb-6">
                            <div>
                                <h2 class="text-2xl font-bold text-gray-800">Booking Details</h2>
                                <p class="text-gray-600">{{ $return->book_code }}</p>
                            </div>
                            <span class="px-4 py-2 inline-flex text-sm font-semibold rounded-full 
                                @if($return->status === 'active') bg-green-100 text-green-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ ucfirst($return->status) }}
                            </span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 mb-2">User Information</h3>
                                <div class="space-y-2">
                                    <p class="text-gray-900"><span class="font-medium">Name:</span> {{ $return->user->name ?? 'N/A' }}</p>
                                    <p class="text-gray-900"><span class="font-medium">Email:</span> {{ $return->user->email ?? 'N/A' }}</p>
                                </div>
                            </div>

                            <div>
                                <h3 class="text-sm font-medium text-gray-500 mb-2">Booker Information</h3>
                                <div class="space-y-2">
                                    <p class="text-gray-900"><span class="font-medium">Name:</span> {{ $return->booker_name }}</p>
                                    <p class="text-gray-900"><span class="font-medium">Email:</span> {{ $return->booker_email }}</p>
                                    <p class="text-gray-900"><span class="font-medium">Phone:</span> {{ $return->booker_telp }}</p>
                                </div>
                            </div>

                            <div>
                                <h3 class="text-sm font-medium text-gray-500 mb-2">Product</h3>
                                <div class="space-y-2">
                                    <p class="text-gray-900"><span class="font-medium">Name:</span> {{ $return->product->name ?? 'N/A' }}</p>
                                    <p class="text-gray-900"><span class="font-medium">Amount:</span> {{ $return->amount }} pcs</p>
                                </div>
                            </div>

                            <div>
                                <h3 class="text-sm font-medium text-gray-500 mb-2">Rental Period</h3>
                                <div class="space-y-2">
                                    <p class="text-gray-900"><span class="font-medium">Check-in:</span> {{ $return->checkin_appointment_start->format('M d, Y H:i') }}</p>
                                    <p class="text-gray-900"><span class="font-medium">Check-out:</span> {{ $return->checkout_appointment_end->format('M d, Y H:i') }}</p>
                                    @php
                                        $totalDays = $return->checkin_appointment_start->diffInDays($return->checkout_appointment_end);
                                        $daysLeft = now()->diffInDays($return->checkout_appointment_end, false);
                                    @endphp
                                    <p class="text-gray-900"><span class="font-medium">Total Days:</span> {{ $totalDays }} days</p>
                                    @if($daysLeft < 0)
                                        <p class="text-red-600 font-semibold">⚠️ Overdue by {{ abs($daysLeft) }} days!</p>
                                    @elseif($daysLeft <= 3 && $return->status === 'active')
                                        <p class="text-orange-600 font-semibold">⏰ {{ $daysLeft }} days remaining</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        @if($return->detailBookProducts && $return->detailBookProducts->count() > 0)
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Additional Details</h3>
                            <div class="bg-gray-50 p-4 rounded-lg space-y-2">
                                @foreach($return->detailBookProducts as $detail)
                                <div class="grid grid-cols-2 gap-4">
                                    <p><span class="font-medium">Full Name:</span> {{ $detail->full_name }}</p>
                                    <p><span class="font-medium">Phone:</span> {{ $detail->phone_number }}</p>
                                    <p><span class="font-medium">Emergency Contact:</span> {{ $detail->emergency_phone_number }}</p>
                                    <p><span class="font-medium">Shipping Method:</span> {{ $detail->shipping_method }}</p>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <div class="flex items-center space-x-4">
                            @if($return->status === 'active')
                            <form action="{{ route('admin.returns.process', $return->id) }}" method="POST" id="completeForm">
                                @csrf
                                <input type="hidden" name="status" value="completed">
                                <button type="button" onclick="confirmComplete()" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg transition">
                                    Mark as Completed
                                </button>
                            </form>
                            @endif
                            <a href="{{ route('admin.returns.index') }}" class="text-gray-600 hover:text-gray-800 px-6 py-2">
                                Back to List
                            </a>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmComplete() {
            Swal.fire({
                title: 'Complete Return?',
                text: "This will mark the rental as completed and returned.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, complete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('completeForm').submit();
                }
            });
        }
    </script>
</body>
</html>
