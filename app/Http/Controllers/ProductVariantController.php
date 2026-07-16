<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\SaleItem;

class ProductVariantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $variants = ProductVariant::with([
            'product.category'
        ])->when($request->category, function ($query) use ($request) {
            $query->whereHas('product', function ($productQuery) use ($request) {
                $productQuery->where('category_id', $request->category);
            });
        })->when($request->search, function ($query) use ($request) {
            $query->where('name', 'like', '%' . $request->search . '%');
        })->latest()->get();

        return view('variants.index', [
            'variants' => $variants,
            'products' => Product::where('status', 1)->orderBy('name')->get(),
            'categories' => Category::where('status', 1)->orderBy('name')->get(),
            'editingVariant' => null,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'name' => 'required|max:255',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'status' => 'required|boolean',
        ]);

        $exists = ProductVariant::where('product_id', $request->product_id)->where('name', $request->name)->exists();

        if ($exists) {
            return back()->withErrors(['name' => 'This variant already exists for the selected product.'])->withInput();
        }

        ProductVariant::create([
            'product_id' => $request->product_id,
            'name' => trim($request->name),
            'price' => $request->price,
            'cost_price' => $request->cost_price,
            'stock' => $request->stock,
            'status' => $request->status,
        ]);

        return redirect()->route('variants.index')->with('success', 'Variant created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductVariant $variant)
    {
        $variants = ProductVariant::with([
            'product.category'
        ])->latest()->get();

        return view('variants.index', [
            'variants' => $variants,
            'products' => Product::where('status', 1)->orderBy('name')->get(),
            'categories' => Category::where('status', 1)->orderBy('name')->get(),
            'editingVariant' => $variant,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProductVariant $variant)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'name' => 'required|max:255',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'status' => 'required|boolean',
        ]);

        $exists = ProductVariant::where('product_id', $request->product_id)
            ->where('name', $request->name)
            ->where('id', '!=', $variant->id)
            ->exists();

        if ($exists) {
            return back()->withErrors([
                'name' => 'This variant already exists for the selected product.'
            ])->withInput();
        }

        $variant->update([
            'product_id' => $request->product_id,
            'name' => trim($request->name),
            'price' => $request->price,
            'cost_price' => $request->cost_price,
            'stock' => $request->stock,
            'status' => $request->status,
        ]);

        return redirect()->route('variants.index')->with('success', 'Variant updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductVariant $variant)
    {
        $variant->delete();

        return redirect()->route('variants.index')->with('success', 'Variant deleted successfully.');
    }
}
