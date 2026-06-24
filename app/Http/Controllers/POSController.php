<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class POSController extends Controller
{
    public function index()
    {
        $categories = Category::where('status', 1)->orderBy('name')->get();

        $products = Product::with([
            'variants' => function ($query) {
                $query->where('status', 1);
            }
        ])->where('status', 1)->get();

        return view('pos.index', [
            'categories' => $categories,
            'products' => $products,
        ]);
    }
}
