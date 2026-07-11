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

    <a href="{{route('sales.index', ['period'=>'week'])}}">
        <div class="card stat-card">
            <div class="stat-icon">
                <i class="fa-solid fa-receipt"></i>
            </div>

            <div>
                <h3>{{ number_format($weeklyRevenue) }} Ks</h3>
                <p>Weekly Revenue</p>
            </div>
        </div>
    </a>

    <a href="{{route('sales.index', ['period'=>'month'])}}">
        <div class="card stat-card">
            <div class="stat-icon">
                <i class="fa-solid fa-kitchen-set"></i>
            </div>

            <div>
                <h3>{{ number_format($monthlyRevenue) }} Ks</h3>
                <p>Mothly Revenue</p>
            </div>
        </div>
    </a>

    <a href="{{route('variants.index')}}">
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
                <th>Order No</th>
                <th>Customer</th>
                <th>Total</th>
                <th>Status</th>
            </tr>
        </thead>

        <tbody>
            <tr>
                <td>#1001</td>
                <td>Walk In</td>
                <td>8,000 Ks</td>
                <td>
                    <span class="role-badge">Paid</span>
                </td>
            </tr>
        </tbody>
    </table>
</div>

@endsection