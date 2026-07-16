@extends('layouts.app')
@section('title', 'POS')
@section('page-title', 'Manage Stocks')
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

<div class="stock-page">

    {{-- Header --}}
    <div class="stock-header">

        <div>
            <h2 class="section-title">
                Stock Overview
            </h2>

            <p class="section-subtitle">
                Monitor inventory levels for all product variants.
            </p>
        </div>

        <div class="stock-search">

            <form method="GET">
                @csrf
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    class="form-input"
                    placeholder="Search product or variant...">

            </form>

        </div>

    </div>

    {{-- Summary Cards --}}
    <div class="grid-4">

        <div class="card">

            <p class="section-subtitle">
                Total Variants
            </p>

            <h2>{{ $totalVariants }}</h2>

        </div>

        <div class="card">

            <p class="section-subtitle">
                Healthy Stock
            </p>

            <h2>{{ $healthyStock }}</h2>

        </div>

        <div class="card">

            <p class="section-subtitle">
                Low Stock
            </p>

            <h2>{{ $lowStock }}</h2>

        </div>

        <div class="card">

            <p class="section-subtitle">
                Out of Stock
            </p>

            <h2>{{ $outOfStock }}</h2>

        </div>

    </div>

    {{-- Filters --}}
    <div class="stock-filters">

        {{-- All --}}
        <a href="{{ route('dashboard.stock', [
        'search' => request('search')
    ]) }}"
            class="btn {{ request('status') ? '' : 'btn-primary' }}">

            All

        </a>

        {{-- Healthy --}}
        <a href="{{ route('dashboard.stock', [
        'status' => 'good',
        'search' => request('search')
    ]) }}"
            class="btn {{ request('status') == 'good' ? 'btn-primary' : '' }}">

            Good

        </a>

        {{-- Low Stock --}}
        <a href="{{ route('dashboard.stock', [
        'status' => 'low',
        'search' => request('search')
    ]) }}"
            class="btn {{ request('status') == 'low' ? 'btn-primary' : '' }}">

            Low Stock

        </a>

        {{-- Out of Stock --}}
        <a href="{{ route('dashboard.stock', [
        'status' => 'out',
        'search' => request('search')
    ]) }}"
            class="btn {{ request('status') == 'out' ? 'btn-primary' : '' }}">

            Out of Stock

        </a>

    </div>

    {{-- Variant Grid --}}
    <div class="grid-4">

        @forelse($variants as $variant)

        <div class="card stock-card">

            <div>

                <h4 class="stock-product">
                    {{ $variant->product->name }}
                </h4>

                <p class="stock-variant">
                    {{ $variant->name }}
                </p>

            </div>

            <div class="stock-quantity">

                <h2>
                    {{ $variant->stock }}
                </h2>

                <p class="section-subtitle">
                    In Stock
                </p>

            </div>

            <div class="stock-footer">

                @if($variant->stock == 0)

                <span class="badge badge-out">
                    Out of Stock
                </span>

                @elseif($variant->stock < 10)

                    <span class="badge badge-low">
                    Low Stock
                    </span>

                    @else

                    <span class="badge badge-good">
                        Good
                    </span>

                    @endif

                    <a href="{{ route('variants.edit', $variant) }}"
                        class="btn btn-primary btn-sm">

                        <i class="fa-solid fa-pen-to-square"></i>
                        Edit

                    </a>

            </div>

        </div>

        @empty

        <div class="card">
            <p>No variants found.</p>
        </div>

        @endforelse

    </div>

    <div class="pagination-wrapper">
        {{ $variants->links() }}
    </div>

</div>

@endsection