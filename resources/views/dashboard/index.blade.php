@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('content')

<!-- Dashboard Header -->
<div class="mb-3">
    <p class="section-subtitle">
        Welcome back, {{ auth()->user()->name }}.
        Here's what's happening in your café today.
    </p>
</div>

<!-- Stats -->
<div class="dashboard-stats mb-3">

    <a href="{{route('sales.index', ['period'=>'week'])}}">
        <div class="card stat-card">
            <div class="stat-icon">
                <i class="fa-solid fa-receipt"></i>
            </div>

            <div>
                <h3>{{ number_format($todayProfit) }} Ks</h3>
                <p>Today's Profit</p>
            </div>
        </div>
    </a>

    <a href="{{route('sales.index', ['period'=>'today'])}}">
        <div class="card stat-card">
            <div class="stat-icon">
                <i class="fa-solid fa-sack-dollar"></i>
            </div>

            <div>
                <h3>{{ number_format($todayRevenue) }} Ks</h3>
                <p>Today's Revenue</p>
            </div>
        </div>
    </a>

    <a href="{{route('sales.index', ['period'=>'month'])}}">
        <div class="card stat-card">
            <div class="stat-icon">
                <i class="fa-solid fa-receipt"></i>
            </div>

            <div>
                <h3>{{ number_format($monthlyProfit) }} Ks</h3>
                <p>Monthly Profit</p>
            </div>
        </div>
    </a>

    <a href="{{route('sales.index', ['period'=>'month'])}}">
        <div class="card stat-card">
            <div class="stat-icon">
                <i class="fa-solid fa-receipt"></i>
            </div>

            <div>
                <h3>{{ number_format($monthlyRevenue) }} Ks</h3>
                <p>Monthly Revenue</p>
            </div>
        </div>
    </a>

    <a href="{{route('dashboard.stock')}}">
        <div class="card stat-card">
            <div class="stat-icon">
                <i class="fa-solid fa-envelope"></i>
            </div>

            <div>
                <h3>{{ $lowStockItems }}</h3>
                <p>Low Stock Items</p>
            </div>
        </div>
    </a>

    <a href="{{route('sales.index', ['period'=>'today'])}}">
        <div class="card stat-card">
            <div class="stat-icon">
                <i class="fa-solid fa-envelope"></i>
            </div>

            <div>
                <h3>{{ $todayOrders }}</h3>
                <p>Today's Sales</p>
            </div>
        </div>
    </a>

</div>

<!-- Today's Activity -->
<div class="dashboard-grid mb-3">
    <!-- Kitchen Overiew -->
    <div class="card">
        <h2 class="section-header mb-2">
            Kitchen Overview
        </h2>

        <div class="kitchen-stats">
            <div>
                <h3>{{ $pendingOrders }}</h3>
                <p>Pending</p>
            </div>

            <div>
                <h3>{{ $preparingOrders }}</h3>
                <p>Preparing</p>
            </div>

            <div>
                <h3>{{ $readyOrders }}</h3>
                <p>Ready</p>
            </div>

            <div>
                <h3>{{ $completedOrders }}</h3>
                <p>Served</p>
            </div>
        </div>
    </div>

    <div class="card">

        <h2 class="section-header mb-2">
            Staff Reports
        </h2>

        <div class="activity-list">

            @forelse($reports as $report)

            <div class="activity-item">

                <div>

                    <strong>
                        {{ $report->title }}
                    </strong>

                    <p class="section-subtitle">

                        Reported by

                        {{ ucfirst($report->user->role) }}

                        -

                        {{ $report->user->name }}

                    </p>

                    <small>

                        {{ \Illuminate\Support\Str::limit($report->message, 60) }}

                    </small>

                </div>

                <span class="role-badge">

                    {{ ucfirst($report->priority) }}

                </span>

            </div>

            @empty

            <div class="empty-state">

                No open reports.

            </div>

            @endforelse

        </div>

    </div>

</div>

<!-- Recent Orders Table -->
<div class="card">

    <h2 class="section-header mb-2">
        Recent Orders
    </h2>

    <table class="dashboard-table">
        <thead>
            <tr>
                <th style="text-align: center;">Order No</th>
                <th style="text-align: center;">Customer</th>
                <th style="text-align: center;">Total</th>
                <th style="text-align: center;">Order Status</th>
                <th style="text-align: center;">Payment Method</th>
                <th style="text-align: center;">Time</th>
            </tr>
        </thead>

        <tbody>
            @foreach($recentSales as $sale)
            <tr>
                <td style="text-align: center;">{{ $sale->invoice_number }}</td>
                <td style="text-align: center;">{{ $sale->service_type == 'dine_in' ? 'Table - ' : 'Take Away' }}</td>
                <td style="text-align: center;">{{ number_format($sale->grand_total) }} Ks</td>
                <td style="text-align: center;">{{ ucfirst($sale->payment_method) }}</td>
                <td style="text-align: center;">
                    <span class="badge">{{ \Illuminate\Support\Str::headline($sale->order_status) }}</span>
                </td>
                <td style="text-align: center;">
                    {{ $sale->created_at->diffForHumans() }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@endsection