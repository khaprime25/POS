<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('products.index', [

            'products' => Product::with('category')
                ->withCount('variants')
                ->latest()
                ->get(),

            'categories' => Category::where('status', 1)
                ->orderBy('name')
                ->get(),

            'editingProduct' => null,

        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $request->validate([

            'category_id' => 'required|exists:categories,id',

            'name' => 'required|max:255',

            'description' => 'nullable',

            'status' => 'required|boolean',

            'image' => 'nullable|image|max:2048',

        ]);

        $imagePath = null;

        if ($request->hasFile('image')) {

            $image = $request->file('image');

            $imageName = time() . '_' . $image->getClientOriginalName();

            $image->move(
                public_path('uploads/products'),
                $imageName
            );

            $imagePath = 'uploads/products/' . $imageName;
        }

        Product::create([

            'category_id' => $request->category_id,

            'name' => $request->name,

            'description' => $request->description,

            'status' => $request->status,

            'image' => $imagePath,

        ]);

        return redirect()
            ->route('products.index')
            ->with('success', 'Product created successfully.');
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
    public function edit(Product $product)
    {
        return view('products.index', [

            'products' => Product::with('category')
                ->withCount('variants')
                ->latest()
                ->get(),

            'categories' => Category::where('status', 1)
                ->orderBy('name')
                ->get(),

            'editingProduct' => $product,

        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([

            'category_id' => 'required|exists:categories,id',

            'name' => 'required|max:255',

            'description' => 'nullable',

            'status' => 'required|boolean',

            'image' => 'nullable|image|max:2048',

        ]);

        $imagePath = $product->image;

        if ($request->hasFile('image')) {

            if ($product->image && file_exists(public_path($product->image))) {

                unlink(public_path($product->image));
            }

            $image = $request->file('image');

            $imageName = time() . '_' . $image->getClientOriginalName();

            $image->move(public_path('products'), $imageName);

            $imagePath = 'products/' . $imageName;
        } else {

            $imagePath = $product->image;
        }

        $product->update([

            'category_id' => $request->category_id,

            'name' => $request->name,

            'description' => $request->description,

            'status' => $request->status,

            'image' => $imagePath,

        ]);

        return redirect()
            ->route('products.index')
            ->with('success', 'Product updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        if ($product->image && file_exists(public_path($product->image))) {

            unlink(public_path($product->image));
        }

        $product->delete();

        return redirect()
            ->route('products.index')
            ->with('success', 'Product deleted successfully.');
    }
}
