@extends('layouts.app')
@section('title', 'POS')
@section('page-title', 'Sales')
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

{{-- Filters --}}
<div class="mb-3">
    <div class="filter-group">
        <a href="{{ route('sales.index',['period'=>'today']) }}"
            class="filter-btn {{ request('period')=='today' ? 'active' : '' }}">
            Today
        </a>
        <a href="{{ route('sales.index',['period'=>'week']) }}"
            class="filter-btn {{ request('period')=='week' ? 'active' : '' }}">
            This Week
        </a>
        <a href="{{ route('sales.index',['period'=>'month']) }}"
            class="filter-btn {{ request('period')=='month' ? 'active' : '' }}">
            This Month
        </a>
        <a href="{{ route('sales.index',['period'=>'year']) }}"
            class="filter-btn {{ request('period')=='year' ? 'active' : '' }}">
            This Year
        </a>
        <a href="{{ route('sales.index',['period'=>'all']) }}"
            class="filter-btn {{ request('period')=='all' ? 'active' : '' }}">
            All Time
        </a>
    </div>
</div>

<div class="dashboard-grid mb-3">

    <div class="card">
        <div>
            <h3>
                {{ number_format($revenue) }} MMK
            </h3>

            <p>
                Revenue
            </p>
        </div>
    </div>

    <div class="card">
        <div>
            <h3>
                {{ number_format($orders) }}
            </h3>

            <p>
                Total Orders
            </p>
        </div>
    </div>

    <div class="card">
        <div>
            <h3>
                {{ number_format($averageOrder) }} MMK
            </h3>

            <p>
                Average Order
            </p>
        </div>
    </div>

    <div class="card">
        <div>
            <h3>
                {{ number_format($taxCollected) }} MMK
            </h3>

            <p>
                Tax Collected
            </p>
        </div>
    </div>
</div>

<div class="card">
    <div class="section-header">
        <h4>Latest Kitchen Orders</h4>
    </div>

    <div class="table-responsive">
        <table class="dashboard-table">
            <thead>
                <tr>
                    <th>Invoice</th>
                    <th style="text-align: center;">Service</th>
                    <th style="text-align: center;">Total</th>
                    <th style="text-align: center;">Payment</th>
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
                            {{ $sale->service_type == 'dine_in' ? 'Table - ' : 'Take Away' }}

                            @if($sale->service_type == 'dine_in')
                            {{ $sale->table_name }}
                            @endif
                        </strong>
                    </td>
                    <td style="text-align: center;">
                        {{ number_format($sale->grand_total) }} Ks
                    </td>
                    <td style="text-align: center;">
                        {{ ucfirst($sale->payment_method) }}
                    </td>
                    <td>
                        {{ $sale->created_at->diffForHumans() }}
                    </td>
                    <td>
                        <button type="button" class="table-action-btn"
                            onclick="openKitchenOrder({{ $sale->id }})">
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

        <!-- Storing Sale and Sale Items Data -->
        @foreach($sales as $sale)

        <div id="sale-{{ $sale->id }}" class="d-none"

            data-id="{{ $sale->id }}"
            data-invoice="{{ $sale->invoice_number }}"
            data-status="{{ $sale->order_status }}"
            data-service="{{ $sale->service_type }}"
            data-table="{{ $sale->table_name }}"
            data-time="{{ $sale->created_at->diffForHumans() }}"
            data-subtotal="{{ number_format($sale->subtotal) }}"
            data-discount="{{ number_format($sale->discount_amount) }}"
            data-tax="{{ number_format($sale->tax_amount) }}"
            data-total="{{ number_format($sale->grand_total) }}"
            data-payment="{{ strtoupper($sale->payment_method) }}">

            @foreach($sale->items as $item)
            <div class="sale-item"

                data-product="{{ $item->product_name }}"
                data-variant="{{ $item->variant_name }}"
                data-price="{{ number_format($item->price) }}"
                data-qty="{{ $item->quantity }}"
                data-subtotal="{{ number_format($item->subtotal) }}"
                data-modifiers='@json($item->modifiers)'>
            </div>
            @endforeach
        </div>
        @endforeach

    </div>
</div>

<!-- Order Modal -->
<div id="kitchenOrderModal" class="custom-modal">
    <div class="custom-modal-content kitchen-modal">

        <button type="button" class="modal-close"
            onclick="closeKitchenOrder()">
            <i class="fa-solid fa-xmark"></i>
        </button>

        <div class="kitchen-header">
            <h3 id="kitchenInvoice"></h3>
            <span class="status-badge" id="kitchenStatus"></span>
        </div>

        <div class="kitchen-info">
            <div>
                <strong>Service</strong>
                <span id="kitchenService"></span>
            </div>

            <div>
                <strong>Table</strong>
                <span id="kitchenTable"></span>
            </div>

            <div>
                <strong>Ordered</strong>
                <span id="kitchenTime"></span>
            </div>
        </div>

        <div id="kitchenItems" class="kitchen-items">
        </div>

        <div class="sale-summary">
            <div class="summary-row">
                <span>Subtotal</span>
                <strong id="saleSubtotal"></strong>
            </div>

            <div class="summary-row">
                <span>Discount</span>
                <strong id="saleDiscount"></strong>
            </div>

            <div class="summary-row">
                <span>Tax</span>
                <strong id="saleTax"></strong>
            </div>

            <div class="summary-row">
                <span>Payment</span>
                <strong id="salePayment"></strong>
            </div>

            <div class="summary-row total">
                <span>Grand Total</span>
                <strong id="saleGrandTotal"></strong>
            </div>
        </div>

    </div>

    <div class="kitchen-modal-footer">
    </div>
</div>

<script>
    function openKitchenOrder(id) {

        // Get the hidden sale data
        const sale = document.getElementById(`sale-${id}`);

        if (!sale) return;

        // Store current status in a variable
        const status = sale.dataset.status;

        // Fill the modal header
        document.getElementById("kitchenInvoice").textContent = sale.dataset.invoice;

        const statusText = formatText(status);
        const statusBadge = document.getElementById("kitchenStatus");
        statusBadge.className = `status-badge status-${status}`;
        statusBadge.textContent = statusText;

        document.getElementById("kitchenService").textContent = formatText(sale.dataset.service);
        document.getElementById("kitchenTable").textContent = sale.dataset.table || "-";
        document.getElementById("kitchenTime").textContent = sale.dataset.time;

        // Order Items
        let items = "";

        sale.querySelectorAll(".sale-item").forEach(item => {

            const modifiers = JSON.parse(item.dataset.modifiers || "[]");

            let modifiersHtml = "";

            modifiers.forEach(modifier => {

                modifiersHtml += `
                    <div class="sale-item-modifier">
                        <span>
                            ${modifier.title}
                        </span>

                        <strong>
                            ${modifier.option}
                        </strong>

                        ${Number(modifier.extra_charge) > 0 ? `<small>( ${Number(modifier.extra_charge).toLocaleString()} Ks )</small>` : ""}
                    </div>
                `;
            });

            items += `
                <div class="sale-item-card">
                    <div class="sale-item-top">
                        <div>
                            <h5>
                                ${item.dataset.product}
                                <small>
                                    ( ${item.dataset.variant} )
                                </small>
                            </h5>

                            ${modifiersHtml}

                            <div class="sale-item-bottom">
                                ${item.dataset.price} Ks × ${item.dataset.qty}
                            </div>
                        </div>

                        <strong>
                            ${item.dataset.subtotal} Ks
                        </strong>
                    </div>
                </div>
            `;

        });

        document.getElementById("kitchenItems").innerHTML = items;

        document.getElementById("saleSubtotal").textContent = sale.dataset.subtotal + " Ks";
        document.getElementById("saleDiscount").textContent = sale.dataset.discount + " Ks";
        document.getElementById("saleTax").textContent = sale.dataset.tax + " Ks";
        document.getElementById("saleGrandTotal").textContent = sale.dataset.total + " Ks";
        document.getElementById("salePayment").textContent = sale.dataset.payment;

        // Show Modal 
        document.getElementById("kitchenOrderModal").classList.add("active");
    }

    function formatText(text) {
        return text.replaceAll("_", " ").replace(/\b\w/g, char => char.toUpperCase());
    }

    function closeKitchenOrder() {
        document.getElementById("kitchenOrderModal").classList.remove("active");
    }
</script>
@endsection