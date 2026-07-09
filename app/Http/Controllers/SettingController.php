<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;

class SettingController extends Controller
{

    public function index()
    {
        $setting = Setting::first();

        if (!$setting) {
            $setting = Setting::create([
                'tax_percentage' => 0,
                'discount_percentage' => 0,
            ]);
        }

        return view('tax.index', compact('setting'));
    }

    public function updateTax(Request $request)
    {
        $validated = $request->validate([
            'tax_percentage' => ['required', 'numeric', 'min:0', 'max:100']
        ]);

        $setting = Setting::first();
        $setting->update([
            'tax_percentage' => $validated['tax_percentage']
        ]);

        return back()->with('success', 'Tax updated successfully.');
    }

    public function updateDiscount(Request $request)
    {
        $validated = $request->validate([
            'discount_percentage' => ['required', 'numeric', 'min:0', 'max:100']
        ]);

        $setting = Setting::first();
        $setting->update([
            'discount_percentage' => $validated['discount_percentage']
        ]);

        return back()->with('success', 'Discount updated successfully.');
    }
}
