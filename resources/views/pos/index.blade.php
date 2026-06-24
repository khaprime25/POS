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
                data-description="{{ $product->description ?? '' }}">

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

        <div class="cart-empty">
            <i class="fa-solid fa-cart-shopping"></i>
            <p>No items added.</p>
        </div>

        <div class="cart-footer">
            <div class="cart-total">
                <span>Total</span>
                <strong>0 MMK</strong>
            </div>

            <button class="btn btn-primary w-100" disabled>
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

<script>
    function openProductModal(productId) {
        const product = document.getElementById(`product-${productId}`);

        if (!product) return;

        const name = product.dataset.name;
        const image = product.dataset.image;
        const description = product.dataset.description;

        let variantsHtml = '';

        product.querySelectorAll('.variant-data').forEach(variant => {

            variantsHtml += `
                <label class="variant-option">
                    <input type="radio" name="selectedVariant" value="${variant.dataset.id}">

                    <span>
                        <strong>
                            ${variant.dataset.name}
                        </strong>

                        <small>
                            ${Number(variant.dataset.price).toLocaleString()} MMK
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

                    <button type="button" class="btn btn-primary w-100 mt-4">
                        Add To Cart
                    </button>
                </div>
            </div>
        `;

        document.getElementById('productModal').classList.add('active');
    }

    function closeProductModal() {
        document.getElementById('productModal').classList.remove('active');
    }
</script>
@endsection