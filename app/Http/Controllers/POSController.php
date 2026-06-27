<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\ProductVariant;
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

        $cart = session()->get('cart', []);

        return view('pos.index', [
            'categories' => $categories,
            'products' => $products,
            'cart' => $cart
        ]);
    }

    public function addToCart(Request $request)
    {
        $request->validate([
            'variant_id' => 'required|exists:product_variants,id',
            'quantity'   => 'required|integer|min:1',
        ]);

        $variant = ProductVariant::with('product')
            ->findOrFail($request->variant_id);

        $cart = session()->get('cart', []);

        $variantId = $variant->id;

        if (isset($cart[$variantId])) {

            $cart[$variantId]['quantity'] += $request->quantity;

            $cart[$variantId]['subtotal'] =
                $cart[$variantId]['price']
                * $cart[$variantId]['quantity'];
        } else {

            $cart[$variantId] = [

                'product_id'   => $variant->product_id,

                'variant_id'   => $variant->id,

                'product_name' => $variant->product->name,

                'variant_name' => $variant->name,

                'price'        => $variant->price,

                'quantity'     => $request->quantity,

                'subtotal'     => $variant->price * $request->quantity,

            ];
        }

        session()->put('cart', $cart);

        return redirect()->route('pos.index');
    }

    public function increaseQty(Int $variantId)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$variantId])) {

            $cart[$variantId]['quantity']++;

            $cart[$variantId]['subtotal'] =
                $cart[$variantId]['price']
                * $cart[$variantId]['quantity'];

            session()->put('cart', $cart);
        }

        return back();
    }

    public function decreaseQty(Int $variantId)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$variantId])) {

            $cart[$variantId]['quantity']--;

            if ($cart[$variantId]['quantity'] <= 0) {

                unset($cart[$variantId]);
            } else {

                $cart[$variantId]['subtotal'] =
                    $cart[$variantId]['price']
                    * $cart[$variantId]['quantity'];
            }

            session()->put('cart', $cart);
        }

        return back();
    }

    public function removeItem(Int $variantId)
    {
        $cart = session()->get('cart', []);

        unset($cart[$variantId]);

        session()->put('cart', $cart);

        return back();
    }
}
