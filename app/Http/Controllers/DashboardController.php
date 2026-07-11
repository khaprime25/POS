<?php

namespace App\Http\Controllers;

use App\Models\ProductVariant;
use App\Models\Report;
use App\Models\Sales;
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

        // =========================
        // Today's Revenue
        // =========================
        $todayRevenue = Sales::whereDate('sale_date', $today)
            ->where('order_status', 'completed')
            ->sum('grand_total');

        $weeklyRevenue = Sales::whereBetween('sale_date', [
            $startOfWeek,
            $endOfWeek
        ])
            ->where('order_status', 'completed')
            ->sum('grand_total');

        $monthlyRevenue = Sales::whereBetween('sale_date', [
            $startOfMonth,
            $endOfMonth
        ])
            ->where('order_status', 'completed')
            ->sum('grand_total');

        // =========================
        // Low Stock Items
        // =========================
        $lowStockItems = ProductVariant::where('stock', '<', 10)->count();

        // =========================
        // Kitchen Overview
        // =========================
        $pendingOrders = Sales::where('order_status', 'sent_to_kitchen')->count();

        $preparingOrders = Sales::where('order_status', 'preparing')->count();

        $readyOrders = Sales::where('order_status', 'ready')->count();

        $completedOrders = Sales::where('order_status', 'completed')->count();

        // =========================
        // Staff Reports
        // =========================
        $reports = Report::with('user')
            ->where('status', 'open')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.index', compact(
            'todayRevenue',
            'weeklyRevenue',
            'monthlyRevenue',
            'lowStockItems',
            'pendingOrders',
            'preparingOrders',
            'readyOrders',
            'completedOrders',
            'reports'
        ));
    }
}
