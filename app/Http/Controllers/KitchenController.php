<?php

namespace App\Http\Controllers;

use App\Models\Sales;
use Illuminate\Http\Request;

class KitchenController extends Controller
{
    public function index(Request $request)
    {
        $query = Sales::with('items')->whereNotIn('order_status', ['completed', 'cancelled']);

        if ($request->filled('status')) {
            $query->where('order_status', $request->status);
        }

        $sales = $query->latest()->get();
        return view('kitchen.index', compact('sales'));
    }

    public function updateStatus(Request $request, Sales $sale)
    {
        $request->validate([
            'status' => 'required|in:sent_to_kitchen,preparing,ready,completed,cancelled'
        ]);

        $allowedTransitions = [
            'sent_to_kitchen' => [
                'preparing',
                'cancelled'
            ],

            'preparing' => [
                'ready'
            ],

            'ready' => [
                'completed'
            ],

            'completed' => [],
            'cancelled' => []
        ];

        if (!in_array($request->status, $allowedTransitions[$sale->order_status])) {
            return back()->with('error', 'Invalid order status transition.');
        }

        $sale->update([
            'order_status' => $request->status
        ]);

        return redirect()->route('kitchen.index')->with(
            'success',
            'Order updated successfully.'
        );
    }
}
