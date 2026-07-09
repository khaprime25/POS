<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductModifier;

class ProductModifierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::with('category')
            ->where('status', 1)
            ->orderBy('name')
            ->get();

        $modifiers = ProductModifier::with(['product.category'])->latest()->get();

        return view('modifier.index', [
            'products' => $products,
            'modifiers' => $modifiers,
            'editingModifier' => null,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'title' => 'required|max:100',
            'option' => 'required|max:100',
            'extra_charge' => 'required|numeric|min:0',
            'status' => 'required|boolean',
        ]);

        ProductModifier::create([
            'product_id' => $request->product_id,
            'title' => $request->title,
            'option' => $request->option,
            'extra_charge' => $request->extra_charge,
            'status' => $request->status,
        ]);

        return redirect()
            ->route('modifiers.index')
            ->with('success', 'Modifier created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductModifier $modifier)
    {
        $products = Product::with('category')
            ->where('status', 1)
            ->orderBy('name')
            ->get();

        $modifiers = ProductModifier::with(['product.category'])->latest()->get();

        return view('modifier.index', [
            'products' => $products,
            'modifiers' => $modifiers,
            'editingModifier' => $modifier,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProductModifier $modifier)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'title' => 'required|max:100',
            'option' => 'required|max:100',
            'extra_charge' => 'required|numeric|min:0',
            'status' => 'required|boolean',
        ]);

        $modifier->update([
            'product_id' => $request->product_id,
            'title' => $request->title,
            'option' => $request->option,
            'extra_charge' => $request->extra_charge,
            'status' => $request->status,
        ]);

        return redirect()
            ->route('modifiers.index')
            ->with('success', 'Modifier updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductModifier $modifier)
    {
        $modifier->delete();

        return back()
            ->with('success', 'Modifier deleted successfully.');
    }
}
