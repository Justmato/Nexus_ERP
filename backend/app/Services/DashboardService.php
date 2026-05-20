<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function getMetrics(): array
    {
        $today = Carbon::today();
        $monthStart = Carbon::now()->startOfMonth();

        $salesToday = Sale::where('status', '!=', 'cancelled')
            ->whereDate('sale_date', $today)->sum('total');

        $salesMonth = Sale::where('status', '!=', 'cancelled')
            ->where('sale_date', '>=', $monthStart)->sum('total');

        $purchasesMonth = Purchase::where('status', '!=', 'cancelled')
            ->where('purchase_date', '>=', $monthStart)->sum('total');

        $lowStockCount = Product::where('track_stock', true)
            ->whereColumn('stock', '<=', 'min_stock')
            ->where('is_active', true)
            ->count();

        return [
            'sales_today' => (float) $salesToday,
            'sales_month' => (float) $salesMonth,
            'purchases_month' => (float) $purchasesMonth,
            'profit_month' => (float) ($salesMonth - $purchasesMonth),
            'products_count' => Product::where('is_active', true)->count(),
            'customers_count' => Customer::where('is_active', true)->count(),
            'suppliers_count' => Supplier::where('is_active', true)->count(),
            'low_stock_count' => $lowStockCount,
            'pending_sales' => Sale::where('status', 'draft')->count(),
            'pending_purchases' => Purchase::where('status', 'draft')->count(),
        ];
    }

    public function getSalesChart(int $days = 30): array
    {
        $start = Carbon::now()->subDays($days - 1)->startOfDay();

        $sales = Sale::where('status', '!=', 'cancelled')
            ->where('sale_date', '>=', $start)
            ->select(DB::raw('DATE(sale_date) as date'), DB::raw('SUM(total) as total'))
            ->groupBy(DB::raw('DATE(sale_date)'))
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $labels = [];
        $data = [];

        for ($i = 0; $i < $days; $i++) {
            $date = $start->copy()->addDays($i)->format('Y-m-d');
            $labels[] = Carbon::parse($date)->format('d M');
            $data[] = (float) ($sales[$date]->total ?? 0);
        }

        return ['labels' => $labels, 'data' => $data];
    }

    public function getTopProducts(int $limit = 5): array
    {
        return DB::table('sale_items')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->where('sales.status', '!=', 'cancelled')
            ->where('sales.sale_date', '>=', Carbon::now()->startOfMonth())
            ->select('products.name', 'products.sku', DB::raw('SUM(sale_items.quantity) as qty'), DB::raw('SUM(sale_items.total) as revenue'))
            ->groupBy('products.id', 'products.name', 'products.sku')
            ->orderByDesc('revenue')
            ->limit($limit)
            ->get()
            ->toArray();
    }
}
