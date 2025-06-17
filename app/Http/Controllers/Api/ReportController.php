<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Exports\OrdersExport;
use App\Exports\InvoicesExport;
use App\Exports\CollectionsExport;
use App\Exports\FinancialReportExport;
use App\Models\Order;
use App\Models\Invoice;
use App\Models\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    // تقرير الطلبات
    public function ordersReport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'nullable|in:pending,confirmed,processing,shipped,delivered,cancelled',
            'customer_id' => 'nullable|exists:users,id',
            'format' => 'required|in:excel,pdf',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $status = $request->status;
        $customerId = $request->customer_id;
        $format = $request->format;

        if ($format === 'excel') {
            return Excel::download(
                new OrdersExport($startDate, $endDate, $status, $customerId),
                'orders_report_' . date('Y-m-d') . '.xlsx'
            );
        } else {
            // PDF Export
            $query = Order::with(['customer', 'orderItems.item']);

            if ($startDate) {
                $query->whereDate('created_at', '>=', $startDate);
            }

            if ($endDate) {
                $query->whereDate('created_at', '<=', $endDate);
            }

            if ($status) {
                $query->where('status', $status);
            }

            if ($customerId) {
                $query->where('customer_id', $customerId);
            }

            $orders = $query->orderBy('created_at', 'desc')->get();

            $pdf = Pdf::loadView('reports.orders', compact('orders', 'startDate', 'endDate'));
            return $pdf->download('orders_report_' . date('Y-m-d') . '.pdf');
        }
    }

    // تقرير الفواتير
    public function invoicesReport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'nullable|in:pending,paid,partially_paid,overdue',
            'customer_id' => 'nullable|exists:users,id',
            'format' => 'required|in:excel,pdf',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $status = $request->status;
        $customerId = $request->customer_id;
        $format = $request->format;

        if ($format === 'excel') {
            return Excel::download(
                new InvoicesExport($startDate, $endDate, $status, $customerId),
                'invoices_report_' . date('Y-m-d') . '.xlsx'
            );
        } else {
            // PDF Export
            $query = Invoice::with(['customer', 'order']);

            if ($startDate) {
                $query->whereDate('created_at', '>=', $startDate);
            }

            if ($endDate) {
                $query->whereDate('created_at', '<=', $endDate);
            }

            if ($status) {
                $query->where('status', $status);
            }

            if ($customerId) {
                $query->where('customer_id', $customerId);
            }

            $invoices = $query->orderBy('created_at', 'desc')->get();

            $pdf = Pdf::loadView('reports.invoices', compact('invoices', 'startDate', 'endDate'));
            return $pdf->download('invoices_report_' . date('Y-m-d') . '.pdf');
        }
    }

    // تقرير التحصيلات
    public function collectionsReport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'payment_method' => 'nullable|in:cash,bank_transfer,check,credit_card',
            'customer_id' => 'nullable|exists:users,id',
            'format' => 'required|in:excel,pdf',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $paymentMethod = $request->payment_method;
        $customerId = $request->customer_id;
        $format = $request->format;

        if ($format === 'excel') {
            return Excel::download(
                new CollectionsExport($startDate, $endDate, $paymentMethod, $customerId),
                'collections_report_' . date('Y-m-d') . '.xlsx'
            );
        } else {
            // PDF Export
            $query = Collection::with(['customer', 'invoice', 'collectedBy']);

            if ($startDate) {
                $query->whereDate('collection_date', '>=', $startDate);
            }

            if ($endDate) {
                $query->whereDate('collection_date', '<=', $endDate);
            }

            if ($paymentMethod) {
                $query->where('payment_method', $paymentMethod);
            }

            if ($customerId) {
                $query->where('customer_id', $customerId);
            }

            $collections = $query->orderBy('collection_date', 'desc')->get();

            $pdf = Pdf::loadView('reports.collections', compact('collections', 'startDate', 'endDate'));
            return $pdf->download('collections_report_' . date('Y-m-d') . '.pdf');
        }
    }

    // التقرير المالي الشامل
    public function financialReport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'format' => 'required|in:excel,pdf',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $format = $request->format;

        if ($format === 'excel') {
            return Excel::download(
                new FinancialReportExport($startDate, $endDate),
                'financial_report_' . date('Y-m-d') . '.xlsx'
            );
        } else {
            // PDF Export - التقرير المالي الشامل
            $invoiceQuery = Invoice::query();
            $collectionQuery = Collection::query();

            if ($startDate) {
                $invoiceQuery->whereDate('created_at', '>=', $startDate);
                $collectionQuery->whereDate('collection_date', '>=', $startDate);
            }

            if ($endDate) {
                $invoiceQuery->whereDate('created_at', '<=', $endDate);
                $collectionQuery->whereDate('collection_date', '<=', $endDate);
            }

            $totalInvoices = $invoiceQuery->sum('total_amount');
            $paidInvoices = $invoiceQuery->sum('paid_amount');
            $pendingInvoices = $invoiceQuery->where('status', 'pending')->sum('total_amount');
            $overdueInvoices = $invoiceQuery->where('status', 'overdue')->sum('total_amount');
            $totalCollections = $collectionQuery->sum('amount');

            $summary = [
                'total_invoices' => $totalInvoices,
                'paid_invoices' => $paidInvoices,
                'pending_invoices' => $pendingInvoices,
                'overdue_invoices' => $overdueInvoices,
                'total_collections' => $totalCollections,
                'remaining_amount' => $totalInvoices - $paidInvoices,
            ];

            $pdf = Pdf::loadView('reports.financial', compact('summary', 'startDate', 'endDate'));
            return $pdf->download('financial_report_' . date('Y-m-d') . '.pdf');
        }
    }

    // إحصائيات سريعة للداشبورد
    public function dashboardStats(Request $request)
    {
        $user = $request->user();

        $stats = [];

        if ($user->user_type === 'customer') {
            // إحصائيات العميل
            $stats = [
                'total_orders' => Order::where('customer_id', $user->id)->count(),
                'pending_orders' => Order::where('customer_id', $user->id)->where('status', 'pending')->count(),
                'total_invoices' => Invoice::where('customer_id', $user->id)->sum('total_amount'),
                'paid_amount' => Invoice::where('customer_id', $user->id)->sum('paid_amount'),
                'remaining_amount' => Invoice::where('customer_id', $user->id)->sum('remaining_amount'),
            ];
        } else {
            // إحصائيات المدير/الموظف
            $stats = [
                'total_orders' => Order::count(),
                'pending_orders' => Order::where('status', 'pending')->count(),
                'total_customers' => \App\Models\User::where('user_type', 'customer')->count(),
                'total_invoices' => Invoice::sum('total_amount'),
                'paid_amount' => Invoice::sum('paid_amount'),
                'remaining_amount' => Invoice::sum('remaining_amount'),
                'today_collections' => Collection::whereDate('collection_date', today())->sum('amount'),
                'this_month_revenue' => Invoice::whereMonth('created_at', now()->month)->sum('total_amount'),
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}
