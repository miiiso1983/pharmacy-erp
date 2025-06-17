<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Invoice;
use App\Models\Collection;
use App\Models\User;
use App\Models\Item;
use App\Models\ReturnOrder;

use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct()
    {
        // Middleware will be handled in routes
    }

    public function index()
    {
        $user = Auth::user();

        if ($user->user_type === 'customer') {
            return $this->customerDashboard();
        } else {
            return $this->adminDashboard();
        }
    }

    private function customerDashboard()
    {
        $user = Auth::user();

        $stats = [
            'total_orders' => Order::where('customer_id', $user->id)->count(),
            'pending_orders' => Order::where('customer_id', $user->id)->where('status', 'pending')->count(),
            'delivered_orders' => Order::where('customer_id', $user->id)->where('status', 'delivered')->count(),
            'total_invoices_amount' => Invoice::where('customer_id', $user->id)->sum('total_amount'),
            'paid_amount' => Invoice::where('customer_id', $user->id)->sum('paid_amount'),
            'remaining_amount' => Invoice::where('customer_id', $user->id)->sum('remaining_amount'),
        ];

        $recent_orders = Order::with(['orderItems.item'])
            ->where('customer_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $recent_invoices = Invoice::with('order')
            ->where('customer_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard.customer', compact('stats', 'recent_orders', 'recent_invoices'));
    }

    private function adminDashboard()
    {
        $stats = [
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'total_customers' => User::where('user_type', 'customer')->count(),
            'total_items' => Item::count(),
            'low_stock_items' => Item::whereColumn('stock_quantity', '<=', 'min_stock_level')->count(),
            'total_invoices_amount' => Invoice::sum('total_amount'),
            'paid_amount' => Invoice::sum('paid_amount'),
            'remaining_amount' => Invoice::sum('remaining_amount'),
            'today_collections' => Collection::whereDate('collection_date', today())->sum('amount'),
            'this_month_revenue' => Invoice::whereMonth('created_at', now()->month)->sum('total_amount'),
            'pending_returns' => ReturnOrder::where('status', 'pending')->count(),
        ];

        $recent_orders = Order::with(['customer', 'orderItems.item'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $recent_collections = Collection::with(['customer', 'invoice', 'collectedBy'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $low_stock_items = Item::whereColumn('stock_quantity', '<=', 'min_stock_level')
            ->orderBy('stock_quantity', 'asc')
            ->limit(10)
            ->get();

        $monthly_revenue = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthly_revenue[] = [
                'month' => $date->format('M Y'),
                'revenue' => Invoice::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->sum('total_amount')
            ];
        }

        return view('dashboard.admin', compact(
            'stats',
            'recent_orders',
            'recent_collections',
            'low_stock_items',
            'monthly_revenue'
        ));
    }
}
