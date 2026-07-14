@extends('layouts.app')
@section('title', 'Product Variants')
@section('page-title', 'Product Variants')
@section('content')

{{-- Success Alert --}}
@if (session('success'))
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


{{-- Variant Header --}}
<div class="mb-3">
    <p class="section-subtitle">
        Manage product variants, pricing and stock levels.
    </p>
</div>

{{-- Variant Form --}}
<div class="card mb-3">

    <form method="POST"
        action="{{ $editingVariant ? route('variants.update', $editingVariant) : route('variants.store') }}">

        @csrf
        @if($editingVariant)
        @method('PUT')
        @endif

        {{-- Product --}}
        <div class="form-group mb-2">
            <label class="form-label">
                Product
            </label>

            <input @error('product_id') style="border: 1px solid red;" @enderror" type="text" id="productSearch" class="form-input" placeholder="Search product..."
                value="{{ old('product_name', $editingVariant->product->name ?? '') }}">
            @error('product_id')
            <small style="color: red;">
                {{ $message }}
            </small>
            @enderror

            <input type="hidden" name="product_id" id="selectedProductId"
                value="{{ old('product_id', $editingVariant->product_id ?? '') }}">

            <div class="search-results" id="productResults">
                @foreach($products->take(5) as $product)
                <div class="search-item" data-id="{{ $product->id }}">
                    {{ $product->name }}
                </div>
                @endforeach
            </div>
        </div>


        {{-- Variant Name --}}
        <div class="form-group mb-2">
            <label class="form-label">
                Variant Name
            </label>

            <input @error('name') style="border: 1px solid red;" @enderror" type="text" name="name" class="form-input" placeholder="Large, Medium, Small, 1 Slice..."
                value="{{ old('name', $editingVariant->name ?? '') }}">
            @error('name')
            <small style="color: red;">
                {{ $message }}
            </small>
            @enderror
        </div>

        <div class="grid-3 mb-2">

            {{-- Selling Price --}}
            <div class="form-group">
                <label class="form-label">
                    Selling Price
                </label>

                <input @error('price') style="border: 1px solid red;" @enderror" type="number" name="price" class="form-input"
                    value="{{ old('price', $editingVariant->price ?? '') }}">
                @error('price')
                <small style="color: red;">
                    {{ $message }}
                </small>
                @enderror
            </div>

            {{-- Cost Price --}}
            <div class="form-group">
                <label class="form-label">
                    Cost Price
                </label>

                <input @error('cost_price') style="border: 1px solid red;" @enderror" type="number" name="cost_price" class="form-input"
                    value="{{ old('cost_price', $editingVariant->cost_price ?? '') }}">
                @error('cost_price')
                <small style="color: red;">
                    {{ $message }}
                </small>
                @enderror
            </div>

            {{-- Stock --}}
            <div class="form-group">
                <label class="form-label">
                    Stock
                </label>

                <input @error('stock') style="border: 1px solid red;" @enderror" type="number" name="stock" class="form-input"
                    value="{{ old('stock', $editingVariant->stock ?? '') }}">
                @error('stock')
                <small style="color: red;">
                    {{ $message }}
                </small>
                @enderror
            </div>

        </div>

        {{-- Status --}}
        <div class="form-group mb-2">
            <label class="form-label">
                Status
            </label>

            <select name="status" class="form-select">
                <option value="1"
                    @selected(old( 'status' , $editingVariant->status ?? 1) == 1)>
                    Active
                </option>

                <option value="0"
                    @selected(old( 'status' , $editingVariant->status ?? 1) == 0)>
                    Inactive
                </option>
            </select>
        </div>

        <div class="table-actions">
            <button type="submit" class="btn btn-primary">
                {{ $editingVariant ? 'Update Variant' : 'Create Variant' }}
            </button>

            @if($editingVariant)
            <a href="{{ route('variants.index') }}" class="btn btn-primary">
                Cancel
            </a>
            @endif
        </div>
    </form>
</div>

{{-- Category Filters --}}
<div class="mb-3">
    <div class="filter-group">
        <a href="{{ route('variants.index') }}"
            class="filter-btn {{ request('category') ? '' : 'active' }}">
            All
        </a>

        @foreach($categories as $category)
        <a href="{{ route('variants.index', ['category' => $category->id]) }}"
            class="filter-btn {{ request('category') == $category->id ? 'active' : '' }}">
            {{ $category->name }}
        </a>
        @endforeach
    </div>
</div>

{{-- Variants Table --}}
<div class="card">
    <div class="table-responsive">
        <table class="dashboard-table">
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Product</th>
                    <th>Variant</th>
                    <th>Price</th>
                    <th>Cost</th>
                    <th>Stocks</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse($variants as $variant)
                <tr>
                    <td>
                        {{ $variant->product->category->name }}
                    </td>

                    <td>
                        {{ $variant->product->name }}
                    </td>

                    <td>
                        {{ $variant->name }}
                    </td>

                    <td>
                        {{ number_format($variant->price) }}
                    </td>

                    <td>
                        {{ number_format($variant->cost_price) }}
                    </td>

                    <td>
                        {{ $variant->stock }}
                    </td>

                    <td>
                        <span class="role-badge">
                            {{ $variant->status ? 'Active' : 'Inactive' }}
                        </span>
                    </td>

                    <td>
                        <div class="table-actions">
                            <a href="{{ route('variants.edit', $variant) }}" class="btn btn-primary">
                                Edit
                            </a>

                            <form method="POST"
                                action="{{ route('variants.destroy', $variant) }}">

                                @csrf
                                @method('DELETE')

                                <button type="submit" class="btn btn-primary">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty

                <tr>
                    <td colspan="8">
                        No variants found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<script>
    const searchInput = document.getElementById('productSearch');
    const resultsBox = document.getElementById('productResults');
    const hiddenInput = document.getElementById('selectedProductId');

    searchInput.addEventListener('focus', () => {
        resultsBox.style.display = 'block';
    });

    searchInput.addEventListener('input', function() {

        const value = this.value.toLowerCase();
        document.querySelectorAll('.search-item').forEach(item => {
            item.style.display = item.textContent.toLowerCase()
                .includes(value) ? 'block' : 'none';
        });
    });

    document.querySelectorAll('.search-item').forEach(item => {
        item.addEventListener('click', function() {
            searchInput.value = this.textContent.trim();
            hiddenInput.value = this.dataset.id;
            resultsBox.style.display = 'none';
        });
    });

    document.addEventListener('click', function(e) {
        if (!e.target.closest('.form-group')) {
            resultsBox.style.display = 'none';
        }
    });
</script>

@endsection