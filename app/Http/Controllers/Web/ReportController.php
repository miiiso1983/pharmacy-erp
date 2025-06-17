<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Invoice;
use App\Models\Collection;
use App\Models\Item;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    public function sales(Request $request)
    {
        $fromDate = $request->from_date ? Carbon::parse($request->from_date) : Carbon::now()->startOfMonth();
        $toDate = $request->to_date ? Carbon::parse($request->to_date) : Carbon::now()->endOfMonth();

        $orders = Order::with(['customer', 'orderItems'])
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->orderBy('created_at', 'desc')
            ->get();

        $totalSales = $orders->sum('total_amount');
        $totalOrders = $orders->count();
        $avgOrderValue = $totalOrders > 0 ? $totalSales / $totalOrders : 0;

        return view('reports.sales', compact('orders', 'totalSales', 'totalOrders', 'avgOrderValue', 'fromDate', 'toDate'));
    }

    public function financial(Request $request)
    {
        $fromDate = $request->from_date ? Carbon::parse($request->from_date) : Carbon::now()->startOfMonth();
        $toDate = $request->to_date ? Carbon::parse($request->to_date) : Carbon::now()->endOfMonth();

        $invoices = Invoice::with(['customer'])
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->get();

        $collections = collect(); // مؤقتاً حتى يتم إنشاء جدول التحصيلات

        $totalInvoiced = $invoices->sum('total_amount');
        $totalCollected = 0; // مؤقتاً
        $totalOutstanding = $invoices->sum('remaining_amount');

        return view('reports.financial', compact(
            'invoices', 'collections', 'totalInvoiced', 'totalCollected',
            'totalOutstanding', 'fromDate', 'toDate'
        ));
    }

    public function inventory(Request $request)
    {
        $items = Item::with('supplier')
            ->when($request->category, function($query, $category) {
                return $query->where('category', $category);
            })
            ->when($request->low_stock, function($query) {
                return $query->whereColumn('stock_quantity', '<=', 'min_stock_level');
            })
            ->orderBy('name')
            ->get();

        $categories = Item::distinct()->pluck('category')->filter();
        $totalItems = $items->count();
        $lowStockItems = $items->filter(function($item) {
            return $item->stock_quantity <= $item->min_stock_level;
        })->count();
        $totalValue = $items->sum(function($item) {
            return $item->stock_quantity * ($item->cost ?? 0);
        });

        return view('reports.inventory', compact('items', 'categories', 'totalItems', 'lowStockItems', 'totalValue'));
    }

    public function customers(Request $request)
    {
        $fromDate = $request->from_date ? Carbon::parse($request->from_date) : Carbon::now()->startOfYear();
        $toDate = $request->to_date ? Carbon::parse($request->to_date) : Carbon::now()->endOfYear();

        $customers = User::where('user_type', 'customer')
            ->with(['orders' => function($query) use ($fromDate, $toDate) {
                $query->whereBetween('created_at', [$fromDate, $toDate]);
            }])
            ->get()
            ->map(function($customer) {
                $customer->orders_count = $customer->orders->count();
                $customer->total_invoiced = $customer->orders->sum('total_amount');
                $customer->total_paid = 0; // سيتم حسابها لاحقاً
                return $customer;
            })
            ->sortByDesc('total_invoiced');

        return view('reports.customers', compact('customers', 'fromDate', 'toDate'));
    }

    public function topItems(Request $request)
    {
        $fromDate = $request->from_date ? Carbon::parse($request->from_date) : Carbon::now()->startOfMonth();
        $toDate = $request->to_date ? Carbon::parse($request->to_date) : Carbon::now()->endOfMonth();

        $topItems = Item::with(['supplier', 'orderItems' => function($query) use ($fromDate, $toDate) {
                $query->whereHas('order', function($q) use ($fromDate, $toDate) {
                    $q->whereBetween('created_at', [$fromDate, $toDate]);
                });
            }])
            ->get()
            ->map(function($item) {
                $item->total_quantity = $item->orderItems->sum('quantity');
                $item->total_revenue = $item->orderItems->sum('total_price');
                return $item;
            })
            ->filter(function($item) {
                return $item->total_quantity > 0;
            })
            ->sortByDesc('total_revenue')
            ->take(20);

        return view('reports.top-items', compact('topItems', 'fromDate', 'toDate'));
    }

    public function exportSales(Request $request)
    {
        // سيتم تطوير وظيفة التصدير لاحقاً
        return back()->with('info', 'وظيفة التصدير قيد التطوير');
    }

    public function exportFinancial(Request $request)
    {
        // سيتم تطوير وظيفة التصدير لاحقاً
        return back()->with('info', 'وظيفة التصدير قيد التطوير');
    }

    public function exportInventory(Request $request)
    {
        // سيتم تطوير وظيفة التصدير لاحقاً
        return back()->with('info', 'وظيفة التصدير قيد التطوير');
    }
}
