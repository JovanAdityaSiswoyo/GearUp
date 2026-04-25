@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-10">
    <div class="bg-white rounded-xl shadow p-8">
        <h1 class="text-2xl font-bold mb-6">Cart Produk</h1>
        {{-- <pre>{{ var_export(session('cart'), true) }}</pre> --}}
        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
        @endif
        @if($products->count())
            <div id="cart-content">
            <ul id="cart-list" class="divide-y divide-gray-200 mb-6">
                @foreach($products as $product)
                    <li class="flex items-center py-4 gap-4 cart-item-row" data-product-id="{{ $product->id }}" data-unit-price="{{ (float) $product->price_per_day }}" data-stock="{{ (int) $product->stock }}">
                        <img src="{{ asset('storage/' . $product->image) }}" class="w-20 h-20 object-cover rounded-lg border" alt="{{ $product->name }}">
                        <div class="flex-1">
                            <div class="font-semibold text-lg">{{ $product->name }}</div>
                            <div class="text-gray-500 text-sm">{{ $product->category->categories ?? '-' }}</div>
                            <div class="text-teal-700 font-bold">Rp {{ number_format($product->price_per_day, 0, ',', '.') }}/hari</div>
                            @php
                                $qty = max(1, (int) ($cart[$product->id] ?? 1));
                            @endphp
                            <div class="text-sm text-gray-600 mt-1">Subtotal / hari: <span id="subtotal-{{ $product->id }}" class="font-semibold text-teal-700">Rp {{ number_format($product->price_per_day * $qty, 0, ',', '.') }}</span></div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-gray-600">Qty</span>

                            <form action="{{ route('user.cart.update', $product->id) }}" method="POST" class="js-cart-qty-form" data-product-id="{{ $product->id }}">
                                @csrf
                                <input type="hidden" name="quantity" value="{{ max(1, $qty - 1) }}">
                                <button
                                    type="submit"
                                    class="js-cart-minus w-8 h-8 rounded-md border border-gray-300 text-gray-700 font-bold hover:bg-gray-100 disabled:opacity-40"
                                    @disabled($qty <= 1)
                                    aria-label="Kurangi jumlah {{ $product->name }}"
                                >
                                    -
                                </button>
                            </form>

                            <span id="qty-label-{{ $product->id }}" class="min-w-10 text-center font-semibold text-gray-800">{{ $qty }}</span>

                            <form action="{{ route('user.cart.update', $product->id) }}" method="POST" class="js-cart-qty-form" data-product-id="{{ $product->id }}">
                                @csrf
                                <input type="hidden" name="quantity" value="{{ min($product->stock, $qty + 1) }}">
                                <button
                                    type="submit"
                                    class="js-cart-plus w-8 h-8 rounded-md border border-gray-300 text-gray-700 font-bold hover:bg-gray-100 disabled:opacity-40"
                                    @disabled($qty >= $product->stock)
                                    aria-label="Tambah jumlah {{ $product->name }}"
                                >
                                    +
                                </button>
                            </form>
                        </div>
                        <form action="{{ route('user.cart.remove', $product->id) }}" method="POST" class="form-remove-cart">
                            @csrf
                            <button type="submit" class="text-red-600 hover:text-red-800 font-semibold px-3 py-1 rounded btn-remove-cart">Hapus</button>
                        </form>
                    </li>
                @endforeach
            </ul>
            <!-- Summary -->
            @php
                $totalProduk = $products->count();
                $totalUnit = $products->sum(function ($product) use ($cart) {
                    return max(1, (int) ($cart[$product->id] ?? 1));
                });
                $totalHarga = $products->sum(function ($product) use ($cart) {
                    $qty = max(1, (int) ($cart[$product->id] ?? 1));
                    return (float) $product->price_per_day * $qty;
                });
            @endphp
            <div class="border-t pt-4 mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="text-gray-700 text-base">Total Produk: <span id="summary-total-products" class="font-bold">{{ $totalProduk }}</span></div>
                <div class="text-gray-700 text-base">Total Unit: <span id="summary-total-units" class="font-bold">{{ $totalUnit }}</span></div>
                <div class="text-gray-700 text-base">Total Harga / hari: <span id="summary-total-price" class="font-bold text-teal-700">Rp {{ number_format($totalHarga, 0, ',', '.') }}</span></div>
            </div>
            <form id="checkout-form" action="{{ route('user.cart.checkout') }}" method="POST">
                @csrf
                <button type="submit" class="inline-block bg-teal-600 hover:bg-teal-700 text-white px-8 py-3 rounded-lg font-semibold text-lg shadow transition">Checkout</button>
            </form>
            </div>
            <div id="cart-empty-state" class="text-gray-500 hidden">Cart masih kosong.</div>
        @else
            <div class="text-gray-500">Cart masih kosong.</div>
        @endif
    </div>
</div>
@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function formatRupiah(value) {
        return 'Rp ' + (Number(value) || 0).toLocaleString('id-ID');
    }

    function getCartRows() {
        return Array.from(document.querySelectorAll('.cart-item-row'));
    }

    function updateCartBadge(totalUnits) {
        document.querySelectorAll('a[href="/cart"] span.absolute').forEach((badge) => {
            badge.textContent = totalUnits;
        });
    }

    function toggleCartEmptyState() {
        const rows = getCartRows();
        const content = document.getElementById('cart-content');
        const emptyState = document.getElementById('cart-empty-state');

        if (!content || !emptyState) return;

        if (rows.length === 0) {
            content.classList.add('hidden');
            emptyState.classList.remove('hidden');
        } else {
            content.classList.remove('hidden');
            emptyState.classList.add('hidden');
        }
    }

    function recalculateCartSummary() {
        const rows = getCartRows();
        let totalUnits = 0;
        let totalPrice = 0;

        rows.forEach((row) => {
            const productId = row.dataset.productId;
            const unitPrice = parseFloat(row.dataset.unitPrice || '0');
            const qtyEl = document.getElementById('qty-label-' + productId);
            const qty = Math.max(1, parseInt(qtyEl?.textContent || '1', 10));

            totalUnits += qty;
            totalPrice += qty * unitPrice;
        });

        const totalProductsEl = document.getElementById('summary-total-products');
        const totalUnitsEl = document.getElementById('summary-total-units');
        const totalPriceEl = document.getElementById('summary-total-price');

        if (totalProductsEl) totalProductsEl.textContent = rows.length;
        if (totalUnitsEl) totalUnitsEl.textContent = totalUnits;
        if (totalPriceEl) totalPriceEl.textContent = formatRupiah(totalPrice);
        updateCartBadge(totalUnits);
        toggleCartEmptyState();
    }

    function updateQtyButtonState(productId, qty, stock) {
        const forms = document.querySelectorAll('.js-cart-qty-form[data-product-id="' + productId + '"]');
        forms.forEach((form) => {
            const hiddenQty = form.querySelector('input[name="quantity"]');
            const minusBtn = form.querySelector('.js-cart-minus');
            const plusBtn = form.querySelector('.js-cart-plus');

            if (minusBtn) {
                hiddenQty.value = Math.max(1, qty - 1);
                minusBtn.disabled = qty <= 1;
            }

            if (plusBtn) {
                hiddenQty.value = Math.min(stock, qty + 1);
                plusBtn.disabled = qty >= stock;
            }
        });
    }

    async function submitQtyUpdate(form) {
        const productId = form.dataset.productId;
        const row = document.querySelector('.cart-item-row[data-product-id="' + productId + '"]');
        if (!row) return;

        const stock = parseInt(row.dataset.stock || '1', 10);
        const unitPrice = parseFloat(row.dataset.unitPrice || '0');
        const formData = new FormData(form);
        const requestedQty = Math.max(1, parseInt(formData.get('quantity') || '1', 10));

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: formData,
            });

            if (!response.ok) {
                throw new Error('Gagal update qty');
            }

            const payload = await response.json();
            const finalQty = Math.max(1, parseInt(payload?.data?.quantity ?? requestedQty, 10));

            const qtyLabel = document.getElementById('qty-label-' + productId);
            const subtotalLabel = document.getElementById('subtotal-' + productId);
            if (qtyLabel) qtyLabel.textContent = finalQty;
            if (subtotalLabel) subtotalLabel.textContent = formatRupiah(finalQty * unitPrice);

            updateQtyButtonState(productId, finalQty, stock);
            recalculateCartSummary();
        } catch (error) {
            console.error(error);
            window.location.reload();
        }
    }

    document.querySelectorAll('.js-cart-qty-form').forEach((form) => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            submitQtyUpdate(form);
        });
    });

    document.querySelectorAll('.btn-remove-cart').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const form = btn.closest('form');
            Swal.fire({
                title: 'Hapus produk dari cart?',
                text: 'Produk akan dihapus dari cart Anda.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = new FormData(form);
                    fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: formData,
                    })
                    .then((response) => {
                        if (!response.ok) {
                            throw new Error('Gagal menghapus item');
                        }
                        return response.json();
                    })
                    .then(() => {
                        const row = form.closest('.cart-item-row');
                        if (row) row.remove();
                        recalculateCartSummary();
                    })
                    .catch((error) => {
                        console.error(error);
                        form.submit();
                    });
                }
            });
        });
    });

    recalculateCartSummary();
</script>
@endsection
@endsection
