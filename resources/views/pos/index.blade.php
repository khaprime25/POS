@extends('layouts.app')
@section('title', 'POS')
@section('page-title', 'Point Of Sale')
@section('content')

{{-- Success Alert --}}
@if(session('success'))
<div class="custom-alert-success">
    <div class="alert-icon">
        <i class="fa-solid fa-circle-check"></i>
    </div>

    <div class="alert-content">
        <h6>Success</h6>
        <p>{{ session('success') }}</p>
    </div>

    <button class="alert-close" onclick="this.parentElement.remove()">
        <i class="fa-solid fa-xmark"></i>
    </button>
</div>
@endif

{{-- CATEGORY FILTERS --}}
<div class=" mb-3">
    <div class="filter-group">
        <a href="{{ route('pos.index') }}"
            class="filter-btn {{ !request('category') ? 'active' : '' }}">
            All
        </a>

        @foreach($categories as $category)
        <a href="{{ route('pos.index', [
                'category' => $category->id,
                'search' => request('search')
            ]) }}"
            class="filter-btn {{ request('category') == $category->id ? 'active' : '' }}">
            {{ $category->name }}
        </a>
        @endforeach
    </div>
</div>

{{-- POS LAYOUT --}}
<div class="pos-layout">

    {{-- Products --}}
    <div class="pos-products card">

        <div class="section-header">
            <h4>Products</h4>
        </div>

        @if($products->count())

        <div class="product-grid">
            @foreach($products as $product)
            <div class="product-card" onclick="openProductModal('{{ $product->id }}')">
                <div class="product-image">
                    <img src="{{ asset($product->image) }}" alt="{{ $product->name }}">
                </div>

                <div class="product-info">
                    <h4>{{ $product->name }}</h4>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Hidden Product Data --}}
        <div style="display:none;">
            @foreach($products as $product)

            <div id="product-{{ $product->id }}"
                data-name="{{ $product->name }}"
                data-image="{{ asset($product->image) }}"
                data-description="{{ $product->description ?? '' }}"
                data-modifiers='@json($product->modifiers)'>

                @foreach($product->variants as $variant)
                <div class="variant-data"
                    data-id="{{ $variant->id }}"
                    data-name="{{ $variant->name }}"
                    data-price="{{ $variant->price }}">
                </div>
                @endforeach
            </div>

            @endforeach
        </div>

        @else

        <div class="empty-state">
            <i class="fa-solid fa-mug-hot"></i>
            <p>No products found.</p>
        </div>

        @endif

    </div>

    {{-- Cart --}}
    <div class="pos-cart card">
        <div class="section-header">
            <h4>Cart</h4>
        </div>

        @if(count($cart))

        <!-- Cart Items -->

        <div class="cart-items">

            @foreach($cart as $item)

            <div class="cart-item">

                {{-- Product --}}
                <div class="cart-item-header">

                    <h5 class="cart-product">
                        {{ $item['product_name'] }}
                    </h5>

                    <span class="cart-variant">
                        {{ $item['variant_name'] }}
                    </span>

                </div>


                {{-- Modifier List --}}
                @if(!empty($item['modifiers']))

                <div class="cart-modifiers">

                    @foreach($item['modifiers'] as $modifier)

                    <div class="cart-modifier">

                        <span>

                            {{ $modifier['title'] }}
                            :
                            {{ $modifier['option'] }}

                        </span>

                        @if($modifier['extra_charge'] > 0)

                        <small>

                            +{{ number_format($modifier['extra_charge']) }} Ks

                        </small>

                        @endif

                    </div>

                    @endforeach

                </div>

                @endif


                {{-- Price Row --}}
                <div class="cart-line">

                    <span>

                        {{ number_format($item['unit_price']) }}

                        Ks

                        ×

                        {{ $item['quantity'] }}

                    </span>

                    <strong>

                        {{ number_format($item['subtotal']) }}

                        Ks

                    </strong>

                </div>


                {{-- Controls --}}
                <div class="cart-actions">

                    <div class="qty-controls">

                        <form
                            method="POST"
                            action="{{ route('pos.cart.decrease',$item['cart_key']) }}">

                            @csrf

                            <button type="submit">

                                -

                            </button>

                        </form>


                        <span>

                            {{ $item['quantity'] }}

                        </span>


                        <form
                            method="POST"
                            action="{{ route('pos.cart.increase',$item['cart_key']) }}">

                            @csrf

                            <button type="submit">

                                +

                            </button>

                        </form>

                    </div>


                    <form
                        method="POST"
                        action="{{ route('pos.cart.remove',$item['cart_key']) }}">

                        @csrf

                        <button
                            type="submit"
                            class="remove-item-btn">

                            <i class="fa-solid fa-trash"></i>

                        </button>

                    </form>

                </div>

            </div>

            @endforeach

        </div>

        @else

        <div class="cart-empty">
            <i class="fa-solid fa-cart-shopping"></i>
            <p>No items added.</p>
        </div>

        @endif


        <!-- Amounts Section -->
        @php

        $subtotal = collect($cart)->sum('subtotal');

        $discount = $subtotal * (($setting->discount_percentage ?? 0) / 100);

        $tax = ($subtotal - $discount)
        * (($setting->tax_percentage ?? 0) / 100);

        $grandTotal = $subtotal - $discount + $tax;

        @endphp

        <div class="cart-footer">
            <div class="cart-summary">
                <div class="summary-row">
                    <span>Subtotal</span>
                    {{ number_format($subtotal) }} Ks</strong>
                </div>

                <div class="summary-row">
                    <span>Discount</span>
                    {{ number_format($discount) }} Ks</strong>
                </div>

                <div class="summary-row">
                    <span>Tax</span>
                    {{ number_format($tax) }} Ks</strong>
                </div>

                <div class="summary-row total">
                    <span>Total</span>
                    {{ number_format($grandTotal) }} Ks</strong>
                </div>
            </div>

            <button style="width: 100%;" type="button" class="btn btn-primary mt-2"
                onclick="openCheckoutModal()">
                Checkout
            </button>
        </div>
    </div>

</div>

<!-- Product Details Modal -->
<div class="product-modal" id="productModal">
    <div class="product-modal-content">
        <button type="button" class="modal-close" onclick="closeProductModal()">
            <i class="fa-solid fa-xmark"></i>
        </button>

        <div id="modalContent">

        </div>
    </div>
</div>

<!-- Sale Modal -->
<div id="checkoutModal" class="checkout-modal">
    <div class="checkout-modal-content">
        <button type="button" class="modal-close" onclick="closeCheckoutModal()">
            <i class="fa-solid fa-xmark"></i>
        </button>

        <h4 class="mb-2">Checkout</h4>

        <form action="{{ route('sales.store') }}" method="POST">
            @csrf

            {{-- SERVICE TYPE --}}
            <div class="form-group">
                <label class="form-label">
                    Service Type
                </label>

                <div class="checkout-options">
                    <label>
                        <input type="radio" name="service_type" value="dine_in" checked>
                        Dine In
                    </label>

                    <label>
                        <input type="radio" name="service_type" value="take_away">
                        Take Away
                    </label>
                </div>
            </div>

            {{-- TABLE NAME --}}
            <div id="tableNameWrapper" class="form-group">
                <label class="form-label mb-2">
                    Table Name
                </label>

                <input @error('table_name') style="border: 1px solid red;" @enderror"
                    type="text" name="table_name" class="form-input" placeholder="A1, A2, ...">
                @error('table_name')
                <small style="color: red;">
                    {{ $message }}
                </small>
                @enderror
            </div>

            {{-- PAYMENT METHOD --}}
            <div class="form-group">
                <label class="form-label">
                    Payment Method
                </label>

                <select name="payment_method" id="paymentMethod" class="form-select">
                    <option value="cash">
                        Cash
                    </option>

                    <option value="kpay">
                        KBZ Pay
                    </option>

                    <option value="wave">
                        Wave Pay
                    </option>
                </select>
            </div>

            {{-- TOTALS --}}
            <div class="checkout-summary">
                <div>
                    <span>Subtotal</span>
                    <strong>{{ number_format($subtotal) }} Ks</strong>
                </div>

                <div>
                    <span>Discount</span>
                    <strong>{{ number_format($discount) }} Ks</strong>
                </div>

                <div>
                    <span>Tax</span>
                    <strong>{{ number_format($tax) }} Ks</strong>
                </div>

                <div class="checkout-total">
                    <span>Total</span>
                    <strong id="grandTotal">
                        {{ number_format($grandTotal) }}
                    </strong>
                </div>

                <input type="hidden" id="grandTotalValue" value="{{ $grandTotal }}">
            </div>

            {{-- CASH SECTION --}}
            <div id="cashSection">
                <div class="form-group">
                    <label class="form-label">
                        Cash Received
                    </label>

                    <input @error('cash_received') style="border: 1px solid red;" @enderror"
                        type="number" id="cashReceived" name="cash_received" class="form-input">
                    @error('cash_received')
                    <small style="color: red;">
                        {{ $message }}
                    </small>
                    @enderror
                </div>

                <div class="change-row">
                    <span>Change</span>
                    <strong id="changeGiven">0 Ks</strong>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100">
                Confirm Sale
            </button>
        </form>
    </div>
</div>

<script>
    function openProductModal(productId) {
        const product = document.getElementById(`product-${productId}`);

        if (!product) return;

        const name = product.dataset.name;
        const image = product.dataset.image;
        const description = product.dataset.description;
        const modifiers = JSON.parse(product.dataset.modifiers);

        let variantsHtml = '';
        let modifiersHtml = "";

        const groupedModifiers = {};

        modifiers.forEach(modifier => {
            if (!groupedModifiers[modifier.title]) {
                groupedModifiers[modifier.title] = [];
            }
            groupedModifiers[modifier.title].push(modifier);
        });

        Object.keys(groupedModifiers).forEach(title => {
            modifiersHtml += `
                <div class="modifier-section">
                    <h5 class="modifier-title">
                        ${title}
                    </h5>
                    <div class="variant-list">
            `;

            groupedModifiers[title].forEach((modifier, index) => {
                modifiersHtml += `
                    <label class="variant-option">
                        <input type="radio" name="modifier_${title}" value="${modifier.id}" ${index === 0 ? "checked" : ""}>
                        <span>
                            <strong>
                                ${modifier.option}
                            </strong>
                            <small>
                                ${Number(modifier.extra_charge) > 0 ? Number(modifier.extra_charge).toLocaleString() + " Ks" : "Free"}
                            </small>
                        </span>
                    </label>
                    `;
            });
        });

        product.querySelectorAll('.variant-data').forEach(variant => {

            variantsHtml += `
                <label class="variant-option">

                    <input type="radio" name="variant_choice" value="${variant.dataset.id}">
                    <span>
                        <strong>
                            ${variant.dataset.name}
                        </strong>

                        <small>
                            ${Number(variant.dataset.price).toLocaleString()} Ks
                        </small>
                    </span>
                </label>
            `;
        });

        document.getElementById('modalContent').innerHTML = `
            <h4 class="modal-product-title">
                ${name}
            </h4>

            <div class="modal-body-layout">
                <div class="pos-modal-image">
                    <img src="${image}" alt="${name}">
                </div>

                <div class="modal-details">
                    <p class="pos-description">
                        ${description}
                    </p>

                    <div class="variant-section">
                        <div class="variant-list">
                            ${variantsHtml}
                        </div>
                    </div>

                    <div class="modifier-wrapper">
                        ${modifiersHtml}
                    </div>

                    <form method="POST" action="{{ route('pos.cart.add') }}">
                        @csrf

                        <input type="hidden" name="variant_id" id="selectedVariantId">
                        <input type="hidden" name="quantity" id="selectedQuantity" value="1">
                        <div id="modifierInputs"></div>

                        <button type="submit" class="btn btn-primary">
                            Add To Cart
                        </button>
                    </form>

                </div>
            </div>
        `;

        const variantRadios = document.querySelectorAll('input[name="variant_choice"]');

        variantRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                document.getElementById('selectedVariantId').value = this.value;
            });
        });

        const form = document.querySelector('#modalContent form');

        form.addEventListener('submit', function() {
            document.querySelectorAll('input[name^="modifier_"]:checked').forEach(input => {
                const hidden = document.createElement('input');
                hidden.type = 'hidden';
                hidden.name = 'modifiers[]';
                hidden.value = input.value;
                form.appendChild(hidden);
            });
        });

        document.getElementById('productModal').classList.add('active');
    }

    function closeProductModal() {
        document.getElementById('productModal').classList.remove('active');
    }

    function openCheckoutModal() {
        document.getElementById('checkoutModal').classList.add('active');
    }

    function closeCheckoutModal() {
        document.getElementById('checkoutModal').classList.remove('active');
    }

    document.querySelectorAll('input[name="service_type"]').forEach(radio => {
        radio.addEventListener('change', function() {
            document.getElementById('tableNameWrapper').style.display =
                this.value === 'dine_in' ? 'block' : 'none';
        });
    });

    document.getElementById('paymentMethod').addEventListener('change', function() {
        document.getElementById('cashSection').style.display =
            this.value === 'cash' ? 'block' : 'none';
    });

    const cashReceived = document.getElementById('cashReceived');

    if (cashReceived) {

        cashReceived.addEventListener('input', function() {
            const total = parseFloat(document.getElementById('grandTotalValue').value);
            const cash = parseFloat(this.value) || 0;
            const change = cash - total;

            document.getElementById('changeGiven').textContent = change > 0 ? change.toLocaleString() + ' Ks' : '0 Ks';
        });
    };
</script>
@endsection