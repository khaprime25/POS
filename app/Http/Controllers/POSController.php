<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Setting;
use App\Models\ProductVariant;
use App\Models\ProductModifier;

use Illuminate\Http\Request;

class POSController extends Controller
{
    public function index()
    {
        $categories = Category::where('status', 1)->orderBy('name')->get();

        $products = Product::with([
            'variants' => function ($query) {
                $query->where('status', 1);
            },

            'modifiers' => function ($query) {
                $query->where('status', 1);
            }
        ])->where('status', 1)->get();

        $cart = session()->get('cart', []);
        $setting = Setting::first();
        // dd($cart);
        return view('pos.index', [
            'categories' => $categories,
            'products' => $products,
            'cart' => $cart,
            'setting' => $setting
        ]);
    }

    public function addToCart(Request $request)
    {
        $request->validate([
            'variant_id' => 'required|exists:product_variants,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $variant = ProductVariant::with('product')->findOrFail($request->variant_id);

        $selectedModifierIds = $request->input('modifiers', []);
        $modifiers = ProductModifier::whereIn('id', $selectedModifierIds)->get();

        $modifierTotal = $modifiers->sum('extra_charge');

        // Generate Cart Key
        $modifierKey = $modifiers->pluck('id')->sort()->implode('_');
        $cartKey = $variant->id;

        if ($modifierKey) {
            $cartKey .= '_' . $modifierKey;
        }

        // Cart
        $cart = session()->get('cart', []);
        $unitPrice = $variant->price + $modifierTotal;

        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['quantity'] += $request->quantity;
            $cart[$cartKey]['subtotal'] = $cart[$cartKey]['unit_price'] * $cart[$cartKey]['quantity'];
        } else {
            $cart[$cartKey] = [
                'cart_key' => $cartKey,
                'product_id' => $variant->product_id,
                'variant_id' => $variant->id,
                'product_name' => $variant->product->name,
                'variant_name' => $variant->name,
                'base_price' => $variant->price,
                'modifier_total' => $modifierTotal,
                'unit_price' => $unitPrice,
                'quantity' => $request->quantity,
                'subtotal' => $unitPrice * $request->quantity,
                'modifiers' => $modifiers->map(function ($modifier) {
                    return [
                        'id' => $modifier->id,
                        'title' => $modifier->title,
                        'option' => $modifier->option,
                        'extra_charge' => $modifier->extra_charge,
                    ];
                })->toArray(),
            ];
        }

        session()->put('cart', $cart);

        return redirect()->route('pos.index');
    }

    public function increaseQty(string $cartKey)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$cartKey])) {

            $cart[$cartKey]['quantity']++;

            $cart[$cartKey]['subtotal'] =
                $cart[$cartKey]['unit_price']
                * $cart[$cartKey]['quantity'];

            session()->put('cart', $cart);
        }

        return back();
    }

    public function decreaseQty(string $cartKey)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$cartKey])) {

            $cart[$cartKey]['quantity']--;

            if ($cart[$cartKey]['quantity'] <= 0) {

                unset($cart[$cartKey]);
            } else {

                $cart[$cartKey]['subtotal'] =
                    $cart[$cartKey]['unit_price']
                    * $cart[$cartKey]['quantity'];
            }

            session()->put('cart', $cart);
        }

        return back();
    }

    public function removeItem(string $cartKey)
    {
        $cart = session()->get('cart', []);

        unset($cart[$cartKey]);

        session()->put('cart', $cart);

        return back();
    }
}
