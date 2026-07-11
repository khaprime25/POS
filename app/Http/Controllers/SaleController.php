<?php

namespace App\Http\Controllers;

use App\Models\Sales;
use App\Models\SaleItem;
use App\Models\Setting;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;


class SaleController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', 'today');

        $query = Sales::with([
            'items',
            'user'
        ])->where('order_status', 'completed');

        switch ($period) {

            case 'week':

                $query->whereBetween('sale_date', [
                    Carbon::now()->startOfWeek(),
                    Carbon::now()->endOfWeek()
                ]);

                break;

            case 'month':

                $query->whereBetween('sale_date', [
                    Carbon::now()->startOfMonth(),
                    Carbon::now()->endOfMonth()
                ]);

                break;

            case 'year':

                $query->whereBetween('sale_date', [
                    Carbon::now()->startOfYear(),
                    Carbon::now()->endOfYear()
                ]);

                break;

            case 'all':

                // No filter

                break;

            default:

                $query->whereDate(
                    'sale_date',
                    Carbon::today()
                );

                break;
        }

        $summary = (clone $query);

        $revenue = $summary->sum('grand_total');

        $orders = $summary->count();

        $taxCollected = $summary->sum('tax_amount');

        $averageOrder = $orders > 0
            ? $revenue / $orders
            : 0;

        $sales = $query
            ->latest('sale_date')
            ->paginate(10)
            ->withQueryString();

        return view('sales.index', compact(
            'sales',
            'revenue',
            'orders',
            'averageOrder',
            'taxCollected'
        ));
    }

    public function storeSale(Request $request)
    {
        $request->validate([
            'service_type'   => 'required|in:take_away,dine_in',
            'payment_method' => 'required|in:cash,kpay,wave',
            'table_name'     => 'required_if:service_type,dine_in',
            'cash_received'  => 'required_if:payment_method,cash|nullable|numeric|min:0',
        ]);

        if ($request->service_type === 'dine_in' && empty($request->table_name)) {
            return back()->withErrors([
                'table_name' => 'Table name is required.'
            ]);
        }

        $cart = session('cart', []);

        if (empty($cart)) {
            return redirect()->route('pos.index')->with('error', 'Cart is empty.');
        }

        $subtotal = collect($cart)->sum('subtotal');

        $setting = Setting::first();
        $discount = $subtotal * (($setting->discount_percentage ?? 0) / 100);
        $tax = $subtotal * (($setting->tax_percentage ?? 0) / 100);

        $grandTotal = $subtotal - $discount + $tax;

        // Payment Validation 
        if ($request->payment_method === 'cash' && $request->cash_received < $grandTotal) {
            return back()->withErrors([
                'cash_received' => 'Cash received is not enough.'
            ]);
        }

        $changeGiven = max(0, $request->cash_received - $grandTotal);

        // Create Sale and SaleItem Section
        DB::transaction(function () use (
            $request,
            $cart,
            $subtotal,
            $discount,
            $tax,
            $grandTotal,
            $changeGiven
        ) {

            $nextId = (Sales::max('id') ?? 0) + 1;
            $invoiceNumber = 'KC-' . str_pad($nextId, 6, '0', STR_PAD_LEFT);
            $user_id = Auth::id();

            foreach ($cart as $item) {
                $variant = ProductVariant::findOrFail($item['variant_id']);

                if ($variant->stock < $item['quantity']) {
                    return back()->withErrors([
                        'stock' => "{$variant->name} does not have enough stock."
                    ]);
                }
            }

            $sale = Sales::create([
                'invoice_number' => $invoiceNumber,
                'user_id' => $user_id,
                'service_type' => $request->service_type,
                'table_name' => $request->service_type === 'dine_in' ? $request->table_name : null,
                'subtotal' => $subtotal,
                'discount_amount' => $discount,
                'tax_amount' => $tax,
                'grand_total' => $grandTotal,
                'payment_method' => $request->payment_method,
                'cash_received' => $request->payment_method === 'cash' ? $request->cash_received : null,
                'change_given' => $request->payment_method === 'cash' ? max(0, $request->cash_received - $grandTotal) : null,
                'order_status' => 'sent_to_kitchen',
                'sale_date' => now(),
            ]);

            foreach ($cart as $item) {

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'product_variant_id' => $item['variant_id'],
                    'product_name' => $item['product_name'],
                    'variant_name' => $item['variant_name'],
                    'price' => $item['unit_price'],
                    'quantity' => $item['quantity'],
                    'subtotal' => $item['subtotal'],
                    'modifiers' => $item['modifiers'],
                ]);

                ProductVariant::where('id', $item['variant_id'])->decrement('stock', $item['quantity']);
            }
        });

        // Clear Cart
        session()->forget('cart');

        return redirect()->route('pos.index')->with('success', 'Sale completed successfully.');
    }
}
