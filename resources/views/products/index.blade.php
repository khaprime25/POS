@extends('layouts.app')

@section('title', 'Products')
@section('page-title', 'Products')
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

<!-- Semi Header -->
<div class="mb-3">
    <p class="section-subtitle">
        Manage all cafe products
    </p>
</div>

<!-- Product Form -->
<div class="card input-card mb-3">
    <form method="POST"
        enctype="multipart/form-data"
        action="{{ $editingProduct ? route('products.update', $editingProduct) : route('products.store') }}">
        @csrf

        @if($editingProduct)
        @method('PUT')
        @endif

        <!-- Category and Status Form -->
        <div class="grid-2">
            <div class="form-group">
                <label class="form-label">
                    Category
                </label>

                <select name="category_id" class="form-select">
                    <option value="">
                        Select Category
                    </option>
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}"
                        @selected(old('category_id', $editingProduct->category_id ?? '') == $category->id)>
                        {{ $category->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">
                    Status
                </label>

                <select name="status" class="form-select">
                    <option value="1"
                        @selected(old('status', $editingProduct->status ?? 1) == 1)>
                        Active
                    </option>

                    <option value="0"
                        @selected(old('status', $editingProduct->status ?? 1) == 0)>
                        Inactive
                    </option>
                </select>
            </div>

        </div>

        <!-- Product Name, Description and Image Form -->
        <div class="form-group mb-2">
            <label class="form-label">
                Product Name
            </label>

            <input type="text" name="name" class="form-input" placeholder="Enter product name"
                value="{{ old('name', $editingProduct->name ?? '') }}">
        </div>

        <div class="form-group mb-2">
            <label class="form-label">
                Description
            </label>

            <textarea name="description" class="form-input"
                placeholder="Optional description">{{ old('description', $editingProduct->description ?? '') }}</textarea>
        </div>

        @if($editingProduct && $editingProduct->image)
        <div class="mb-2">
            <img src="{{ asset($editingProduct->image) }}" class="image-preview">
        </div>
        @endif

        <div class="form-group mb-2">
            <label class="form-label">
                Product Image
            </label>

            <input type="file" name="image" class="form-input">
        </div>

        <!-- Create, Update and Cancel Buttons  -->
        <div class="table-actions">
            <button class="btn btn-primary">
                {{ $editingProduct ? 'Update Product' : 'Create Product' }}
            </button>

            @if($editingProduct)
            <a href="{{ route('products.index') }}" class="btn btn-primary">
                Cancel
            </a>
            @endif
        </div>

    </form>
</div>

<!-- Product Table -->
<div class="card">
    <table class="dashboard-table">
        <thead>
            <tr>
                <th>Image</th>
                <th>Category</th>
                <th>Product</th>
                <th>Status</th>
                <th>Variants</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>
            @forelse($products as $product)
            <tr>
                <td>
                    @if($product->image)
                    <img src="{{ asset($product->image) }}" class="image-preview">
                    @else
                    -
                    @endif
                </td>

                <td>
                    {{ $product->category->name }}
                </td>

                <td>
                    {{ $product->name }}
                </td>

                <td>
                    <span class="{{ $product->status ? 'status-active' : 'status-inactive' }}">
                        {{ $product->status ? 'Active' : 'Inactive' }}
                    </span>
                </td>

                <td>
                    {{ $product->variants_count ?? 0 }}
                </td>

                <td>
                    <div class="table-actions">
                        <a href="{{ route('products.edit', $product) }}" class="btn btn-primary">
                            Edit
                        </a>

                        <form action="{{ route('products.destroy', $product) }}" method="POST">
                            @csrf
                            @method('DELETE')

                            <button class="btn btn-primary" onclick="return confirm('Delete this product?')">
                                Delete
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6">
                    No products found.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection