@extends('layouts.app')
@section('title', 'POS')
@section('page-title', 'Best Sellers')
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

<div class="overview-page">

    {{-- Header --}}
    <div class="overview-header">
        <div>
            <h2 class="section-title">
                Top Selling Variants
            </h2>

            <p class="section-subtitle">
                Discover your best performing menu variants based on total quantity sold.
            </p>

        </div>

        <div class="overview-search">
            <form method="GET">
                <input type="text" name="search" class="form-input" value="{{ request('search') }}" placeholder="Search product or variant...">
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
                Best Sellers
            </p>

            <h2>{{ $bestSellers }}</h2>
        </div>

        <div class="card">
            <p class="section-subtitle">
                Moderate
            </p>

            <h2>{{ $moderateSellers }}</h2>
        </div>

        <div class="card">
            <p class="section-subtitle">
                Slow Sellers
            </p>

            <h2>{{ $slowSellers }}</h2>
        </div>
    </div>

    {{-- Filters --}}
    <div class="overview-filters">
        <a href="{{ route('dashboard.top',['search'=>request('search')]) }}"
            class="btn {{ request('status') ? '' : 'btn-primary' }}">
            All
        </a>

        <a href="{{ route('dashboard.top',['status'=>'best','search'=>request('search')]) }}"
            class="btn {{ request('status')=='best' ? 'btn-primary' : '' }}">
            Best Sellers
        </a>

        <a href="{{ route('dashboard.top',['status'=>'moderate','search'=>request('search')]) }}"
            class="btn {{ request('status')=='moderate' ? 'btn-primary' : '' }}">
            Moderate
        </a>

        <a href="{{ route('dashboard.top',['status'=>'slow', 'search'=>request('search')]) }}"
            class="btn {{ request('status')=='slow' ? 'btn-primary' : '' }}">
            Slow
        </a>
    </div>

    {{-- Cards --}}
    <div class="grid-4">
        @forelse($variants as $variant)

        <div class="card seller-card">
            <div class="seller-top">

                <div>
                    <h4 class="seller-product">
                        {{ $variant->product_name }}
                    </h4>
                    <p class="seller-variant">
                        {{ $variant->variant_name }}
                    </p>
                </div>

                @if($variant->total_sold >= 10)
                <span class="badge badge-good">
                    Best Seller
                </span>

                @elseif($variant->total_sold >=5)
                <span class="badge badge-low">
                    Moderate
                </span>

                @else
                <span class="badge badge-out">
                    Slow
                </span>
                @endif
            </div>

            <div class="seller-body">
                <h1>
                    {{ $variant->total_sold }}
                </h1>
                <p class="section-subtitle">
                    Total Sold
                </p>
            </div>

            <div class="seller-footer">
                <a href="{{ route('sales.index')}}"
                    class="btn btn-primary btn-sm">
                    <i class="fa-solid fa-chart-line"></i>
                    View Sales
                </a>
            </div>

        </div>
        @empty
        <div class="card">
            No selling variants found.
        </div>
        @endforelse

    </div>

    <div class="pagination-wrapper">
        {{ $variants->links() }}
    </div>

</div>

@endsection