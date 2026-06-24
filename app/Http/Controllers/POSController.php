<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class POSController extends Controller
{
    public function index()
    {
        return view('pos.index', [
            'categories' => Category::where('status', 1)
                ->orderBy('name')
                ->get(),

            'products' => Product::where('status', 1)
                ->orderBy('name')
                ->get(),
        ]);
    }
}
