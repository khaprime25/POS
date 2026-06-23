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

    <div class="card stat-card">
        <div class="stat-icon">
            <i class="fa-solid fa-sack-dollar"></i>
        </div>

        <div>
            <h3>250,000 MMK</h3>
            <p>Today's Revenue</p>
        </div>
    </div>

    <div class="card stat-card">
        <div class="stat-icon">
            <i class="fa-solid fa-receipt"></i>
        </div>

        <div>
            <h3>87</h3>
            <p>Orders Today</p>
        </div>
    </div>

    <div class="card stat-card">
        <div class="stat-icon">
            <i class="fa-solid fa-kitchen-set"></i>
        </div>

        <div>
            <h3>5</h3>
            <p>Kitchen Queue</p>
        </div>
    </div>

    <div class="card stat-card">
        <div class="stat-icon">
            <i class="fa-solid fa-envelope"></i>
        </div>

        <div>
            <h3>3</h3>
            <p>Today Reports</p>
        </div>
    </div>

</div>

<!-- Today's Activity -->
<div class="dashboard-grid mb-3">

    <!-- TODAY ACTIVITY -->
    <div class="card">
        <h2 class="section-header mb-2">
            Today's Activity
        </h2>

        <div class="activity-list">
            <div class="activity-item">
                <span>#1001</span>
                <span>Americano Order</span>
                <span>08:00 AM</span>
            </div>

            <div class="activity-item">
                <span>#1002</span>
                <span>Latte Order</span>
                <span>08:15 AM</span>
            </div>

            <div class="activity-item">
                <span>#1003</span>
                <span>Cappuccino Order</span>
                <span>08:22 AM</span>
            </div>
        </div>
    </div>

    <!-- Kitchen Overiew -->
    <div class="card">
        <h2 class="section-header mb-2">
            Kitchen Overview
        </h2>

        <div class="kitchen-stats">
            <div>
                <h3>5</h3>
                <p>Pending</p>
            </div>

            <div>
                <h3>2</h3>
                <p>Preparing</p>
            </div>

            <div>
                <h3>3</h3>
                <p>Ready</p>
            </div>

            <div>
                <h3>80</h3>
                <p>Served</p>
            </div>
        </div>
    </div>

</div>

<!-- Products and Staff Reports -->
<div class="dashboard-grid mb-3">

    <div class="card">
        <h2 class="section-header mb-2">
            Best Sellers
        </h2>

        <div class="activity-list">
            <div class="activity-item">
                <span>Americano</span>
                <span>120 Sold</span>
            </div>

            <div class="activity-item">
                <span>Latte</span>
                <span>98 Sold</span>
            </div>

            <div class="activity-item">
                <span>Mocha</span>
                <span>75 Sold</span>
            </div>
        </div>
    </div>

    <div class="card">
        <h2 class="section-header mb-2">
            Staff Reports
        </h2>

        <div class="activity-list">

            <div class="activity-item">
                <div>
                    <strong>Sugar Running Low</strong>
                    <p class="section-subtitle">
                        Reported by Chef
                    </p>
                </div>

                <span class="role-badge">
                    High
                </span>
            </div>

            <div class="activity-item">
                <div>
                    <strong>Receipt Paper Needed</strong>
                    <p class="section-subtitle">Reported by Cashier</p>
                </div>

                <span class="role-badge">
                    Medium
                </span>
            </div>

            <div class="activity-item">
                <div>
                    <strong>Oven Maintenance</strong>
                    <p class="section-subtitle">Reported by Chef</p>
                </div>

                <span class="role-badge">
                    Urgent
                </span>
            </div>
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
                <td>8,000 MMK</td>
                <td>
                    <span class="role-badge">Paid</span>
                </td>
            </tr>
        </tbody>
    </table>
</div>

@endsection