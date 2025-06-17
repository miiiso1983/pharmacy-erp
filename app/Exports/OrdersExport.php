<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class OrdersExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
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
        $query = Order::with(['customer', 'orderItems.item']);

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
            'رقم الطلب',
            'اسم العميل',
            'شركة العميل',
            'الحالة',
            'المبلغ الفرعي',
            'الضريبة',
            'الخصم',
            'المبلغ الإجمالي',
            'عنوان التسليم',
            'تاريخ التسليم',
            'تاريخ الإنشاء',
            'ملاحظات'
        ];
    }

    public function map($order): array
    {
        return [
            $order->order_number,
            $order->customer->name,
            $order->customer->company_name ?? 'غير محدد',
            $this->getStatusInArabic($order->status),
            number_format($order->subtotal, 2),
            number_format($order->tax_amount, 2),
            number_format($order->discount_amount, 2),
            number_format($order->total_amount, 2),
            $order->delivery_address ?? 'غير محدد',
            $order->delivery_date ? $order->delivery_date->format('Y-m-d') : 'غير محدد',
            $order->created_at->format('Y-m-d H:i'),
            $order->notes ?? ''
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
            'confirmed' => 'مؤكد',
            'processing' => 'قيد المعالجة',
            'shipped' => 'تم الشحن',
            'delivered' => 'تم التسليم',
            'cancelled' => 'ملغي'
        ];

        return $statuses[$status] ?? $status;
    }
}
