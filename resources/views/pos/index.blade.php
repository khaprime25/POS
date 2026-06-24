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
            <div class="product-card">
                <div class="product-image">
                    <img src="{{ asset($product->image) }}" alt="{{ $product->name }}">
                </div>

                <div class="product-info">
                    <h4>{{ $product->name }}</h4>
                </div>
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
@endsection