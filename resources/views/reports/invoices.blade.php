<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تقرير الفواتير</title>
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
            font-size: 11px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: right;
        }
        th {
            background-color: #e74c3c;
            color: white;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .status-pending { color: #f39c12; }
        .status-paid { color: #27ae60; }
        .status-partially_paid { color: #3498db; }
        .status-overdue { color: #e74c3c; }
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
        <div class="report-title">تقرير الفواتير</div>
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
                <th>رقم الفاتورة</th>
                <th>رقم الطلب</th>
                <th>اسم العميل</th>
                <th>الشركة</th>
                <th>الحالة</th>
                <th>المبلغ الإجمالي</th>
                <th>المبلغ المدفوع</th>
                <th>المبلغ المتبقي</th>
                <th>تاريخ الاستحقاق</th>
                <th>تاريخ الإنشاء</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalAmount = 0;
                $totalPaid = 0;
                $totalRemaining = 0;
            @endphp
            @foreach($invoices as $invoice)
                @php
                    $totalAmount += $invoice->total_amount;
                    $totalPaid += $invoice->paid_amount;
                    $totalRemaining += $invoice->remaining_amount;
                @endphp
                <tr>
                    <td>{{ $invoice->invoice_number }}</td>
                    <td>{{ $invoice->order->order_number }}</td>
                    <td>{{ $invoice->customer->name }}</td>
                    <td>{{ $invoice->customer->company_name ?? 'غير محدد' }}</td>
                    <td class="status-{{ $invoice->status }}">
                        @switch($invoice->status)
                            @case('pending') في الانتظار @break
                            @case('paid') مدفوعة @break
                            @case('partially_paid') مدفوعة جزئياً @break
                            @case('overdue') متأخرة @break
                            @default {{ $invoice->status }}
                        @endswitch
                    </td>
                    <td>{{ number_format($invoice->total_amount, 2) }} دينار</td>
                    <td>{{ number_format($invoice->paid_amount, 2) }} دينار</td>
                    <td>{{ number_format($invoice->remaining_amount, 2) }} دينار</td>
                    <td>{{ $invoice->due_date ? $invoice->due_date->format('Y-m-d') : 'غير محدد' }}</td>
                    <td>{{ $invoice->created_at->format('Y-m-d') }}</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="5"><strong>الإجمالي</strong></td>
                <td><strong>{{ number_format($totalAmount, 2) }} دينار</strong></td>
                <td><strong>{{ number_format($totalPaid, 2) }} دينار</strong></td>
                <td><strong>{{ number_format($totalRemaining, 2) }} دينار</strong></td>
                <td colspan="2"></td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p>تم إنشاء هذا التقرير بواسطة نظام إدارة المذاخر - {{ date('Y-m-d H:i:s') }}</p>
        <p>عدد الفواتير: {{ count($invoices) }}</p>
    </div>
</body>
</html>
