<?php

namespace App\Exports;

use App\Models\Invoice;
use App\Models\Collection;
use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FinancialReportExport implements WithMultipleSheets
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate = null, $endDate = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function sheets(): array
    {
        return [
            'الملخص المالي' => new FinancialSummarySheet($this->startDate, $this->endDate),
            'تفاصيل الفواتير' => new InvoiceDetailsSheet($this->startDate, $this->endDate),
            'تفاصيل التحصيلات' => new CollectionDetailsSheet($this->startDate, $this->endDate),
        ];
    }
}

class FinancialSummarySheet implements FromArray, WithHeadings, WithStyles, ShouldAutoSize
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function array(): array
    {
        $query = Invoice::query();

        if ($this->startDate) {
            $query->whereDate('created_at', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->whereDate('created_at', '<=', $this->endDate);
        }

        $totalInvoices = $query->sum('total_amount');
        $paidInvoices = $query->sum('paid_amount');
        $pendingInvoices = $query->where('status', 'pending')->sum('total_amount');
        $overdueInvoices = $query->where('status', 'overdue')->sum('total_amount');

        $collectionQuery = Collection::query();

        if ($this->startDate) {
            $collectionQuery->whereDate('collection_date', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $collectionQuery->whereDate('collection_date', '<=', $this->endDate);
        }

        $totalCollections = $collectionQuery->sum('amount');

        return [
            ['إجمالي الفواتير', number_format($totalInvoices, 2) . ' ريال'],
            ['إجمالي المدفوعات', number_format($paidInvoices, 2) . ' ريال'],
            ['الفواتير المعلقة', number_format($pendingInvoices, 2) . ' ريال'],
            ['الفواتير المتأخرة', number_format($overdueInvoices, 2) . ' ريال'],
            ['إجمالي التحصيلات', number_format($totalCollections, 2) . ' ريال'],
            ['المبلغ المتبقي', number_format($totalInvoices - $paidInvoices, 2) . ' ريال'],
        ];
    }

    public function headings(): array
    {
        return [
            'البيان',
            'المبلغ'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
            'A:B' => ['font' => ['size' => 12]],
        ];
    }
}

class InvoiceDetailsSheet implements FromArray, WithHeadings, WithStyles, ShouldAutoSize
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function array(): array
    {
        $query = Invoice::with(['customer', 'order']);

        if ($this->startDate) {
            $query->whereDate('created_at', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->whereDate('created_at', '<=', $this->endDate);
        }

        $invoices = $query->orderBy('created_at', 'desc')->get();

        $data = [];
        foreach ($invoices as $invoice) {
            $data[] = [
                $invoice->invoice_number,
                $invoice->customer->name,
                $invoice->customer->company_name ?? 'غير محدد',
                $this->getStatusInArabic($invoice->status),
                number_format($invoice->total_amount, 2),
                number_format($invoice->paid_amount, 2),
                number_format($invoice->remaining_amount, 2),
                $invoice->created_at->format('Y-m-d'),
            ];
        }

        return $data;
    }

    public function headings(): array
    {
        return [
            'رقم الفاتورة',
            'اسم العميل',
            'الشركة',
            'الحالة',
            'المبلغ الإجمالي',
            'المبلغ المدفوع',
            'المبلغ المتبقي',
            'تاريخ الإنشاء'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }

    private function getStatusInArabic($status)
    {
        $statuses = [
            'pending' => 'في الانتظار',
            'paid' => 'مدفوعة',
            'partially_paid' => 'مدفوعة جزئياً',
            'overdue' => 'متأخرة'
        ];

        return $statuses[$status] ?? $status;
    }
}

class CollectionDetailsSheet implements FromArray, WithHeadings, WithStyles, ShouldAutoSize
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function array(): array
    {
        $query = Collection::with(['customer', 'invoice', 'collectedBy']);

        if ($this->startDate) {
            $query->whereDate('collection_date', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->whereDate('collection_date', '<=', $this->endDate);
        }

        $collections = $query->orderBy('collection_date', 'desc')->get();

        $data = [];
        foreach ($collections as $collection) {
            $data[] = [
                $collection->collection_number,
                $collection->invoice->invoice_number,
                $collection->customer->name,
                $collection->customer->company_name ?? 'غير محدد',
                number_format($collection->amount, 2),
                $this->getPaymentMethodInArabic($collection->payment_method),
                $collection->collectedBy->name,
                $collection->collection_date->format('Y-m-d'),
            ];
        }

        return $data;
    }

    public function headings(): array
    {
        return [
            'رقم التحصيل',
            'رقم الفاتورة',
            'اسم العميل',
            'الشركة',
            'المبلغ',
            'طريقة الدفع',
            'تم التحصيل بواسطة',
            'تاريخ التحصيل'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }

    private function getPaymentMethodInArabic($method)
    {
        $methods = [
            'cash' => 'نقداً',
            'bank_transfer' => 'تحويل بنكي',
            'check' => 'شيك',
            'credit_card' => 'بطاقة ائتمان'
        ];

        return $methods[$method] ?? $method;
    }
}
