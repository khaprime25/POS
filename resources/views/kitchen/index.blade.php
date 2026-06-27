@extends('layouts.app')
@section('title', 'POS')
@section('page-title', 'Kitchen')
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

{{-- FILTERS --}}
<div class="mb-3">
    <div class="filter-group">
        <a href="{{ route('kitchen.index') }}"
            class="filter-btn {{ !request('status') ? 'active' : '' }}">
            All
        </a>

        <a href="{{ route('kitchen.index', ['status' => 'sent_to_kitchen']) }}"
            class="filter-btn {{ request('status') == 'sent_to_kitchen' ? 'active' : '' }}">
            Sent
        </a>

        <a href="{{ route('kitchen.index', ['status' => 'preparing']) }}"
            class="filter-btn {{ request('status') == 'preparing' ? 'active' : '' }}">
            Preparing
        </a>

        <a href="{{ route('kitchen.index', ['status' => 'ready']) }}"
            class="filter-btn {{ request('status') == 'ready' ? 'active' : '' }}">
            Ready
        </a>
    </div>
</div>

<div class="card">

    <div class="section-header">
        <h4>Kitchen Orders</h4>
        <p class="section-subtitle">
            Latest kitchen orders.
        </p>
    </div>

    <div class="table-responsive">
        <table class="dashboard-table">
            <thead>
                <tr>
                    <th>Invoice</th>
                    <th style="text-align: center;">Service</th>
                    <th style="text-align: center;">Status</th>
                    <th>Time</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse($sales as $sale)
                <tr>
                    <td>{{ $sale->invoice_number }}</td>
                    <td style="text-align: center;">
                        <strong>
                            {{ $sale->service_type == 'dine_in' ? 'Table - ' : 'TA' }}

                            @if($sale->service_type == 'dine_in')
                            {{ $sale->table_name }}
                            @endif
                        </strong>
                    </td>
                    <td style="text-align: center;">
                        <span class="status-badge status-{{ $sale->order_status }}">
                            {{ ucfirst(str_replace('_', ' ', $sale->order_status)) }}
                        </span>
                    </td>
                    <td>
                        {{ $sale->created_at->diffForHumans() }}
                    </td>
                    <td>
                        <button class="table-action-btn">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </td>
                </tr>
                @empty

                <tr>
                    <td colspan="5">
                        <div class="empty-state">
                            <i class="fa-solid fa-utensils"></i>
                            <p>
                                No kitchen orders.
                            </p>
                        </div>
                    </td>
                </tr>

                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection