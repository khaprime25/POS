@extends('layouts.app')

@section('title', 'Categories')
@section('page-title', 'Categories')
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
    <p class="section-subtitle">Manage product categories</p>
</div>

<!-- Category Form -->
<div class="card input-card mb-3">
    <form method="POST"
        action="{{ $editingCategory ? route('categories.update', $editingCategory) : route('categories.store') }}">
        @csrf

        @if($editingCategory)
        @method('PUT')
        @endif

        <div class="d-flex gap-2">

            <div class="form-group">
                <label class="form-label">
                    Category Name
                </label>
                <input type="text" name="name" class="form-input" placeholder="Enter category name"
                    value="{{ $editingCategory->name ?? '' }}">
            </div>

            <button class="btn btn-primary">
                {{ $editingCategory ? 'Update' : 'Create' }}
            </button>
        </div>
    </form>
</div>

<!-- Category Table -->
<div class="card">
    <table class="dashboard-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Status</th>
                <th width="200" style="text-align: center;">Actions</th>
            </tr>
        </thead>

        <tbody>
            @foreach($categories as $category)
            <tr>
                <td>{{ $category->name }}</td>
                <td>
                    <span class="badge">
                        {{ $category->status ? 'Active' : 'Inactive' }}
                    </span>
                </td>
                <td>
                    <a href="{{ route('categories.edit', $category) }}" class="btn btn-primary btn-sm me-2">
                        Edit
                    </a>

                    <form method="POST" action="{{ route('categories.destroy', $category) }}" style="display:inline;">
                        @csrf
                        @method('DELETE')

                        <button class="btn btn-primary btn-sm">
                            Delete
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@endsection