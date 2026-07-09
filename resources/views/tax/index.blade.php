@extends('layouts.app')
@section('title', 'POS')
@section('page-title', 'Setting')
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

{{-- Tax Header --}}
<div class="mb-1">
    <p class="section-subtitle">
        Manage Tax Percentages Here!
    </p>
</div>

<!-- Tax Section -->
<div class="card" style="width: 75%;">
    <div class="section-header">
        <h5>Current Tax For Sale =
            <span class="link">
                {{ $setting->tax_percentage }}%
            </span>
        </h5>
    </div>

    <form method="POST" action="{{ route('setting.tax.update') }}">
        @csrf
        <div class="form-group">
            <label class="form-label">Tax Percentage</label>
            <input type="number" name="tax_percentage" class="form-input" min="0" max="100" step="0.01" value="{{ $setting->tax_percentage }}">
        </div>

        <button class="btn btn-primary">
            Update Tax
        </button>
    </form>
</div>

{{-- Discount Header --}}
<div class="mb-1 mt-2">
    <p class="section-subtitle">
        Manage Discount Percentages Here!
    </p>
</div>

<!-- Discount Section -->
<div class="card" style="width: 75%;">
    <div class="section-header">
        <h5>Current Discount For Sale =
            <span class="link">
                {{ $setting->discount_percentage }}%
            </span>
        </h5>
    </div>

    <form method="POST" action="{{ route('setting.discount.update') }}">
        @csrf
        <div class="form-group">
            <label class="form-label">Discount Percentage</label>
            <input type="number" name="discount_percentage" class="form-input" min="0" max="100" step="0.01" value="{{ $setting->discount_percentage }}">
        </div>

        <button class="btn btn-primary">
            Update Discount
        </button>
    </form>
</div>

@endsection