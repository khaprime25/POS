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

{{-- Filters --}}

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
        <h4>Latest Kitchen Orders</h4>
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
                            {{ $sale->service_type == 'dine_in' ? 'Table - ' : 'Take Away' }}

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
        {{ $sale->items->count() }}

        <!-- Storing Sale and Sale Items Data -->
        @foreach($sales as $sale)

        <div id="sale-{{ $sale->id }}" class="d-none"

            data-id="{{ $sale->id }}"
            data-invoice="{{ $sale->invoice_number }}"
            data-status="{{ $sale->order_status }}"
            data-service="{{ $sale->service_type }}"
            data-table="{{ $sale->table_name }}"
            data-time="{{ \Carbon\Carbon::parse($sale->sale_date)->diffForHumans() }}">

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

        <div id="kitchenActions" class="kitchen-actions">
        </div>

    </div>

    <div class="kitchen-modal-footer">
    </div>
</div>

<script>
    function buildStatusButton(id, nextStatus, text, buttonClass) {

        return `
        <form method="POST" action="/kitchen/${id}/status">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="status" value="${nextStatus}">
            <button type="submit" class="btn ${buttonClass}">
                ${text}
            </button>
        </form>
    `;

    }
    const kitchenStatusActions = {

        sent_to_kitchen: [{
                status: "preparing",
                text: "Start Preparing",
                class: "btn-primary"
            },

            {
                status: "cancelled",
                text: "Cancel Order",
                class: "btn-cancel"
            }
        ],

        preparing: [{
            status: "ready",
            text: "Mark Ready",
            class: "btn-primary"
        }],

        ready: [{
            status: "completed",
            text: "Complete Order",
            class: "btn-primary"
        }],

        completed: [],

        cancelled: []

    };

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

                        ${modifier.title} :
                        ${modifier.option}

                        ${
                            Number(modifier.extra_charge) > 0
                            ? `<small>(+${Number(modifier.extra_charge).toLocaleString()} Ks)</small>`
                            : ""
                        }

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
                                    (${item.dataset.variant})
                                </small>

                            </h5>

                            ${modifiersHtml}

                        </div>

                        <strong>

                            ${item.dataset.subtotal} Ks

                        </strong>

                    </div>

                    <div class="sale-item-bottom">

                        ${item.dataset.price} Ks × ${item.dataset.qty}

                    </div>

                </div>
                `;
        });

        document.getElementById("kitchenItems").innerHTML = items;

        // Action Buttons
        let actionButtons = '';

        const actions = kitchenStatusActions[status] || [];

        actions.forEach(action => {
            actionButtons += buildStatusButton(
                id,
                action.status,
                action.text,
                action.class
            );
        });

        document.getElementById("kitchenActions").innerHTML = actionButtons;

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