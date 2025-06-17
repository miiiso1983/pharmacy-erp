<?php

namespace App\Exports;

use App\Models\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CollectionsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $startDate;
    protected $endDate;
    protected $paymentMethod;
    protected $customerId;

    public function __construct($startDate = null, $endDate = null, $paymentMethod = null, $customerId = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->paymentMethod = $paymentMethod;
        $this->customerId = $customerId;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $query = Collection::with(['customer', 'invoice', 'collectedBy']);

        if ($this->startDate) {
            $query->whereDate('collection_date', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->whereDate('collection_date', '<=', $this->endDate);
        }

        if ($this->paymentMethod) {
            $query->where('payment_method', $this->paymentMethod);
        }

        if ($this->customerId) {
            $query->where('customer_id', $this->customerId);
        }

        return $query->orderBy('collection_date', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'رقم التحصيل',
            'رقم الفاتورة',
            'اسم العميل',
            'شركة العميل',
            'المبلغ',
            'طريقة الدفع',
            'رقم المرجع',
            'تاريخ التحصيل',
            'تم التحصيل بواسطة',
            'تاريخ الإنشاء',
            'ملاحظات'
        ];
    }

    public function map($collection): array
    {
        return [
            $collection->collection_number,
            $collection->invoice->invoice_number,
            $collection->customer->name,
            $collection->customer->company_name ?? 'غير محدد',
            number_format($collection->amount, 2),
            $this->getPaymentMethodInArabic($collection->payment_method),
            $collection->reference_number ?? 'غير محدد',
            $collection->collection_date->format('Y-m-d'),
            $collection->collectedBy->name,
            $collection->created_at->format('Y-m-d H:i'),
            $collection->notes ?? ''
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
