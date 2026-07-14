<?php

namespace App\Http\Controllers;

use App\Models\ProductVariant;
use App\Models\Report;
use App\Models\Sales;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $todayRevenue = Sales::whereDate('sale_date', $today)
            ->where('order_status', 'completed')
            ->sum('grand_total');

        $monthlyRevenue = Sales::whereBetween('sale_date', [
            $startOfMonth,
            $endOfMonth
        ])->where('order_status', 'completed')->sum('grand_total');

        $todaySales = Sales::with('items.variant')
            ->where('order_status', 'completed')
            ->whereDate('created_at', today())
            ->get();

        $todayProfit = $this->calculateProfit($todaySales);

        $monthlySales = Sales::with('items.variant')
            ->where('order_status', 'completed')
            ->whereBetween('created_at', [
                now()->startOfMonth(),
                now()->endOfMonth(),
            ])
            ->get();

        $monthlyProfit = $this->calculateProfit($monthlySales);

        $lowStockItems = ProductVariant::where('stock', '<', 10)->count();
        $todayOrders = Sales::where('order_status', 'completed')->whereDate('sale_date', today())->count();

        $recentSales = Sales::orderByDesc('sale_date')->take(3)->get();

        $pendingOrders = Sales::where('order_status', 'sent_to_kitchen')->count();
        $preparingOrders = Sales::where('order_status', 'preparing')->count();
        $readyOrders = Sales::where('order_status', 'ready')->count();
        $completedOrders = Sales::where('order_status', 'completed')->count();

        $reports = Report::with('user')
            ->where('status', 'open')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.index', compact(
            'todayRevenue',
            'monthlyRevenue',
            'todayProfit',
            'monthlyProfit',
            'lowStockItems',
            'todayOrders',
            'recentSales',
            'pendingOrders',
            'preparingOrders',
            'readyOrders',
            'completedOrders',
            'reports'
        ));
    }

    public function stock(Request $request)
    {
        $query = ProductVariant::with('product');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhereHas('product', function ($product) use ($search) {
                        $product->where('name', 'like', "%{$search}%");
                    });
            });
        }

        switch ($request->status) {
            case 'good':
                $query->where('stock', '>=', 10);
                break;

            case 'low':
                $query->whereBetween('stock', [1, 9]);
                break;

            case 'out':
                $query->where('stock', 0);
                break;
        }
        $variants = $query->orderBy('stock')->paginate(16)->withQueryString();

        $totalVariants = ProductVariant::count();
        $healthyStock = ProductVariant::where('stock', '>=', 10)->count();
        $lowStock = ProductVariant::whereBetween('stock', [1, 9])->count();
        $outOfStock = ProductVariant::where('stock', 0)->count();

        return view('stock.index', compact(
            'variants',
            'totalVariants',
            'healthyStock',
            'lowStock',
            'outOfStock'
        ));
    }

    private function calculateProfit($sales)
    {
        $profit = 0;
        foreach ($sales as $sale) {
            $saleCost = 0;
            foreach ($sale->items as $item) {
                if ($item->variant) {
                    $saleCost += $item->variant->cost_price * $item->quantity;
                }
            }

            $profit += ($sale->grand_total - $saleCost);
        }

        return $profit;
    }
}
