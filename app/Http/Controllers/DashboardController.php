<?php

namespace App\Http\Controllers;

use App\Models\ProductVariant;
use App\Models\Report;
use App\Models\Sales;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

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

        $bestSellerThreshold = 10;
        $bestSellerCount = SaleItem::query()->select('product_variant_id')
            ->selectRaw('SUM(quantity) as total_sold')
            ->groupBy('product_variant_id')
            ->havingRaw('SUM(quantity) >= ?', [$bestSellerThreshold])
            ->count();


        $todayOrders = Sales::where('order_status', 'completed')->whereDate('sale_date', today())->count();

        $recentSales = Sales::orderByDesc('sale_date')->take(3)->get();

        $pendingOrders = Sales::where('order_status', 'sent_to_kitchen')->count();
        $preparingOrders = Sales::where('order_status', 'preparing')->count();
        $readyOrders = Sales::where('order_status', 'ready')->count();
        $completedOrders = Sales::where('order_status', 'completed')->count();

        $reports = Report::with('user')->where('status', 'open')->latest()->take(5)->get();
        $reportCount = Report::with('user')->count();

        $sales = Sales::whereDate('sale_date', '>=', $startOfWeek)->whereDate('sale_date', '<=', $endOfWeek)->get();

        $chartLabels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
        $chartData = [];

        for ($i = 0; $i < 7; $i++) {
            $date = $startOfWeek->copy()->addDays($i);
            $dailySales = $sales->filter(function ($sale) use ($date) {
                return Carbon::parse($sale->sale_date)->isSameDay($date);
            })->sum('grand_total');
            $chartData[] = $dailySales;
        }

        return view('dashboard.index', compact(
            'todayRevenue',
            'monthlyRevenue',
            'todayProfit',
            'monthlyProfit',
            'lowStockItems',
            'bestSellerCount',
            'todayOrders',
            'recentSales',
            'pendingOrders',
            'preparingOrders',
            'readyOrders',
            'completedOrders',
            'reports',
            'reportCount',
            'chartData',
            'chartLabels'
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
        $variants = $query->orderBy('stock')->paginate(12)->withQueryString();

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

    public function top(Request $request)
    {
        $bestSellerThreshold = 10;

        $query = SaleItem::query()->select(
            'product_id',
            'product_variant_id',
            'product_name',
            'variant_name'
        )->selectRaw('SUM(quantity) as total_sold')
            ->groupBy(
                'product_id',
                'product_variant_id',
                'product_name',
                'variant_name'
            );

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('product_name', 'like', "%{$search}%")
                    ->orWhere('variant_name', 'like', "%{$search}%");
            });
        }

        switch ($request->status) {
            case 'best':
                $query->havingRaw('SUM(quantity) >= ?', [$bestSellerThreshold]);
                break;

            case 'moderate':
                $query->havingRaw('SUM(quantity) BETWEEN 5 AND 9');
                break;

            case 'slow':
                $query->havingRaw('SUM(quantity) BETWEEN 1 AND 4');
                break;
        }

        $query->orderByDesc('total_sold');

        $summary = SaleItem::query()->selectRaw('
            product_variant_id, SUM(quantity) as total_sold')->groupBy('product_variant_id')->get();

        $totalVariants = $summary->count();

        $bestSellers = $summary->where('total_sold', '>=', 10)->count();

        $moderateSellers = $summary->filter(function ($item) {
            return $item->total_sold >= 5 && $item->total_sold <= 9;
        })->count();

        $slowSellers = $summary->filter(function ($item) {
            return $item->total_sold >= 1 && $item->total_sold <= 4;
        })->count();

        $variants = $query->paginate(8)->withQueryString();

        return view('stock.top', compact(
            'variants',
            'totalVariants',
            'bestSellers',
            'moderateSellers',
            'slowSellers'
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
