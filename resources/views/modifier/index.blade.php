@extends('layouts.app')
@section('title', 'POS')
@section('page-title', 'Product Modifier')
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

<div class="card" style="margin-bottom: 4rem;">

    <div class="section-header">

        <div>
            <p>
                {{ $editingModifier ? 'Edit' : 'Create' }} modifier options for your products.
            </p>
        </div>

    </div>

    <form
        method="POST"
        action="{{ $editingModifier ? route('modifiers.update',$editingModifier) : route('modifiers.store') }}">

        @csrf

        @if($editingModifier)
        @method('PUT')
        @endif

        <div class="form-grid">

            {{-- Product --}}
            <div class="form-col-full">

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

            </div>

            {{-- Title --}}
            <div class="form-col-2">

                <label class="form-label">
                    Title
                </label>

                <input
                    type="text"
                    name="title"
                    class="form-input"
                    placeholder="Sugar, Ice, Topping..."
                    value="{{ old('title',$editingModifier->title ?? '') }}">

            </div>

            {{-- Option --}}
            <div class="form-col-2">

                <label class="form-label">
                    Option
                </label>

                <input
                    type="text"
                    name="option"
                    class="form-input"
                    placeholder="50%, Cheese, Strawberry..."
                    value="{{ old('option',$editingModifier->option ?? '') }}">

            </div>

            {{-- Extra Charge --}}
            <div class="form-col-2">

                <label class="form-label">
                    Extra Charge
                </label>

                <input
                    type="number"
                    name="extra_charge"
                    class="form-input"
                    value="{{ old('extra_charge',$editingModifier->extra_charge ?? 0) }}">

            </div>

            {{-- Status --}}
            <div class="form-col-2">

                <label class="form-label">
                    Status
                </label>

                <select
                    name="status"
                    class="form-select">

                    <option
                        value="1"
                        @selected(old('status',$editingModifier->status ?? 1)==1)>

                        Active

                    </option>

                    <option
                        value="0"
                        @selected(old('status',$editingModifier->status ?? 1)==0)>

                        Inactive

                    </option>

                </select>

            </div>

        </div>

        <div class="table-actions mt-3">

            <button
                class="btn btn-primary">

                {{ $editingModifier ? 'Update Modifier' : 'Create Modifier' }}

            </button>

            @if($editingModifier)

            <a
                href="{{ route('modifiers.index') }}"
                class="btn btn-primary">

                Cancel

            </a>

            @endif

        </div>

    </form>

</div>

<div class="card mt-4">

    <div class="section-header">

        <div>

            <p>
                Manage all modifier options.
            </p>

        </div>

    </div>

    <table class="dashboard-table">

        <thead>

            <tr>

                <th>Category</th>

                <th>Product</th>

                <th>Title</th>

                <th>Option</th>

                <th>Extra Charge</th>

                <th>Status</th>

                <th>Actions</th>

            </tr>

        </thead>

        <tbody>

            @forelse($modifiers as $modifier)

            <tr>

                <td>

                    {{ $modifier->product->category->name }}

                </td>

                <td>

                    {{ $modifier->product->name }}

                </td>

                <td>

                    {{ $modifier->title }}

                </td>

                <td>

                    {{ $modifier->option }}

                </td>

                <td>

                    {{ number_format($modifier->extra_charge) }} Ks

                </td>

                <td>

                    <span class="{{ $modifier->status ? 'status-active' : 'status-inactive' }}">

                        {{ $modifier->status ? 'Active' : 'Inactive' }}

                    </span>

                </td>

                <td>

                    <div class="table-actions">

                        <a
                            href="{{ route('modifiers.edit',$modifier) }}"
                            class="btn btn-primary">

                            Edit

                        </a>

                        <form
                            action="{{ route('modifiers.destroy',$modifier) }}"
                            method="POST">

                            @csrf
                            @method('DELETE')

                            <button
                                class="btn btn-danger">

                                Delete

                            </button>

                        </form>

                    </div>

                </td>

            </tr>

            @empty

            <tr>

                <td colspan="7">

                    No modifier options found.

                </td>

            </tr>

            @endforelse

        </tbody>

    </table>

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