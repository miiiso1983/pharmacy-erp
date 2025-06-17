<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تقرير الطلبات</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            direction: rtl;
            text-align: right;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 10px;
        }
        .report-title {
            font-size: 18px;
            color: #34495e;
            margin-bottom: 5px;
        }
        .report-date {
            font-size: 12px;
            color: #7f8c8d;
        }
        .filters {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .filters h3 {
            margin-top: 0;
            color: #2c3e50;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 12px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: right;
        }
        th {
            background-color: #3498db;
            color: white;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .status-pending { color: #f39c12; }
        .status-confirmed { color: #27ae60; }
        .status-processing { color: #3498db; }
        .status-shipped { color: #9b59b6; }
        .status-delivered { color: #2ecc71; }
        .status-cancelled { color: #e74c3c; }
        .total-row {
            background-color: #ecf0f1;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #7f8c8d;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">نظام إدارة المذاخر</div>
        <div class="report-title">تقرير الطلبات</div>
        <div class="report-date">تاريخ التقرير: {{ date('Y-m-d H:i') }}</div>
    </div>

    @if($startDate || $endDate)
    <div class="filters">
        <h3>فلاتر التقرير:</h3>
        @if($startDate)
            <p><strong>من تاريخ:</strong> {{ $startDate }}</p>
        @endif
        @if($endDate)
            <p><strong>إلى تاريخ:</strong> {{ $endDate }}</p>
        @endif
    </div>
    @endif

    <table>
        <thead>
            <tr>
                <th>رقم الطلب</th>
                <th>اسم العميل</th>
                <th>الشركة</th>
                <th>الحالة</th>
                <th>المبلغ الفرعي</th>
                <th>الضريبة</th>
                <th>المبلغ الإجمالي</th>
                <th>تاريخ الإنشاء</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalSubtotal = 0;
                $totalTax = 0;
                $totalAmount = 0;
            @endphp
            @foreach($orders as $order)
                @php
                    $totalSubtotal += $order->subtotal;
                    $totalTax += $order->tax_amount;
                    $totalAmount += $order->total_amount;
                @endphp
                <tr>
                    <td>{{ $order->order_number }}</td>
                    <td>{{ $order->customer->name }}</td>
                    <td>{{ $order->customer->company_name ?? 'غير محدد' }}</td>
                    <td class="status-{{ $order->status }}">
                        @switch($order->status)
                            @case('pending') في الانتظار @break
                            @case('confirmed') مؤكد @break
                            @case('processing') قيد المعالجة @break
                            @case('shipped') تم الشحن @break
                            @case('delivered') تم التسليم @break
                            @case('cancelled') ملغي @break
                            @default {{ $order->status }}
                        @endswitch
                    </td>
                    <td>{{ number_format($order->subtotal, 2) }} دينار</td>
                    <td>{{ number_format($order->tax_amount, 2) }} دينار</td>
                    <td>{{ number_format($order->total_amount, 2) }} دينار</td>
                    <td>{{ $order->created_at->format('Y-m-d') }}</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="4"><strong>الإجمالي</strong></td>
                <td><strong>{{ number_format($totalSubtotal, 2) }} دينار</strong></td>
                <td><strong>{{ number_format($totalTax, 2) }} دينار</strong></td>
                <td><strong>{{ number_format($totalAmount, 2) }} دينار</strong></td>
                <td></td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p>تم إنشاء هذا التقرير بواسطة نظام إدارة المذاخر - {{ date('Y-m-d H:i:s') }}</p>
        <p>عدد الطلبات: {{ count($orders) }}</p>
    </div>
</body>
</html>
