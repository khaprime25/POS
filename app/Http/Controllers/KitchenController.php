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
}
