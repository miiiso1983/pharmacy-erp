<?php

namespace App\Exports;

use App\Models\Invoice;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InvoicesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $startDate;
    protected $endDate;
    protected $status;
    protected $customerId;

    public function __construct($startDate = null, $endDate = null, $status = null, $customerId = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->status = $status;
        $this->customerId = $customerId;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $query = Invoice::with(['customer', 'order']);

        if ($this->startDate) {
            $query->whereDate('created_at', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->whereDate('created_at', '<=', $this->endDate);
        }

        if ($this->status) {
            $query->where('status', $this->status);
        }

        if ($this->customerId) {
            $query->where('customer_id', $this->customerId);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'رقم الفاتورة',
            'رقم الطلب',
            'اسم العميل',
            'شركة العميل',
            'الحالة',
            'المبلغ الفرعي',
            'الضريبة',
            'الخصم',
            'المبلغ الإجمالي',
            'المبلغ المدفوع',
            'المبلغ المتبقي',
            'تاريخ الاستحقاق',
            'تاريخ الإنشاء',
            'ملاحظات'
        ];
    }

    public function map($invoice): array
    {
        return [
            $invoice->invoice_number,
            $invoice->order->order_number,
            $invoice->customer->name,
            $invoice->customer->company_name ?? 'غير محدد',
            $this->getStatusInArabic($invoice->status),
            number_format($invoice->subtotal, 2),
            number_format($invoice->tax_amount, 2),
            number_format($invoice->discount_amount, 2),
            number_format($invoice->total_amount, 2),
            number_format($invoice->paid_amount, 2),
            number_format($invoice->remaining_amount, 2),
            $invoice->due_date ? $invoice->due_date->format('Y-m-d') : 'غير محدد',
            $invoice->created_at->format('Y-m-d H:i'),
            $invoice->notes ?? ''
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
