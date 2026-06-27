<?php

namespace App\Http\Controllers;

use App\Models\Sales;
use App\Models\SaleItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class SaleController extends Controller
{
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
        $discount = 0;
        $tax = 0;
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
            $user_id = auth()->id();

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
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'subtotal' => $item['subtotal'],
                    'modifiers' => null,
                ]);
            }
        });

        // Clear Cart
        session()->forget('cart');

        return redirect()->route('pos.index')->with('success', 'Sale completed successfully.');
    }
}
