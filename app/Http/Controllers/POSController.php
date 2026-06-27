<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\ProductVariant;
use App\Models\Sales;
use App\Models\SaleItem;
use Illuminate\Support\Facades\DB;
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

    public function storeSale(Request $request)
    {
        $request->validate([
            'service_type'   => 'required|in:take_away,dine_in',
            'payment_method' => 'required|in:cash,kpay,wave',
            'table_name'     => 'required_if:service_type,dine_in',
            'cash_received'  => 'required_if:payment_method,cash|nullable|numeric|min:0',
        ]);

        if (
            $request->service_type === 'dine_in'
            && empty($request->table_name)
        ) {

            return back()->withErrors([
                'table_name' => 'Table name is required.'
            ]);
        }

        $cart = session('cart', []);

        if (empty($cart)) {

            return redirect()
                ->route('pos.index')
                ->with('error', 'Cart is empty.');
        }

        /*
    |--------------------------------------------------------------------------
    | Recalculate Totals
    |--------------------------------------------------------------------------
    */

        $subtotal = collect($cart)
            ->sum('subtotal');

        $discount = 0;

        $tax = 0;

        $grandTotal =
            $subtotal
            - $discount
            + $tax;

        /*
    |--------------------------------------------------------------------------
    | Payment Validation
    |--------------------------------------------------------------------------
    */

        if (
            $request->payment_method === 'cash'
            && $request->cash_received < $grandTotal
        ) {

            return back()->withErrors([
                'cash_received' => 'Cash received is not enough.'
            ]);
        }

        $changeGiven =
            max(
                0,
                $request->cash_received - $grandTotal
            );

        /*
    |--------------------------------------------------------------------------
    | Create Sale + SaleItems
    |--------------------------------------------------------------------------
    */

        DB::transaction(function () use (
            $request,
            $cart,
            $subtotal,
            $discount,
            $tax,
            $grandTotal,
            $changeGiven
        ) {

            $nextId =
                (Sales::max('id') ?? 0) + 1;

            $invoiceNumber =
                'KC-' .
                str_pad(
                    $nextId,
                    6,
                    '0',
                    STR_PAD_LEFT
                );
            $user_id = auth()->id();
            $sale = Sales::create([

                'invoice_number' => $invoiceNumber,

                'user_id' => $user_id,

                'service_type' =>
                $request->service_type,

                'table_name' =>
                $request->service_type === 'dine_in'
                    ? $request->table_name
                    : null,

                'subtotal' => $subtotal,

                'discount_amount' => $discount,

                'tax_amount' => $tax,

                'grand_total' => $grandTotal,

                'payment_method' =>
                $request->payment_method,
                'cash_received' => $request->payment_method === 'cash'
                    ? $request->cash_received
                    : null,

                'change_given' => $request->payment_method === 'cash'
                    ? max(0, $request->cash_received - $grandTotal)
                    : null,

                'order_status' =>
                'sent_to_kitchen',

                'sale_date' => now(),
            ]);

            foreach ($cart as $item) {

                SaleItem::create([

                    'sale_id' =>
                    $sale->id,

                    'product_id' =>
                    $item['product_id'],

                    'product_variant_id' =>
                    $item['variant_id'],

                    'product_name' =>
                    $item['product_name'],

                    'variant_name' =>
                    $item['variant_name'],

                    'price' =>
                    $item['price'],

                    'quantity' =>
                    $item['quantity'],

                    'subtotal' =>
                    $item['subtotal'],

                    'modifiers' =>
                    null,
                ]);
            }
        });

        /*
    |--------------------------------------------------------------------------
    | Clear Cart
    |--------------------------------------------------------------------------
    */

        session()->forget('cart');

        return redirect()
            ->route('pos.index')
            ->with(
                'success',
                'Sale completed successfully.'
            );
    }
}
