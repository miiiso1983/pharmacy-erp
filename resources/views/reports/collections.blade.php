<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تقرير التحصيلات</title>
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
            background-color: #27ae60;
            color: white;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .payment-cash { color: #27ae60; }
        .payment-bank_transfer { color: #3498db; }
        .payment-check { color: #f39c12; }
        .payment-credit_card { color: #9b59b6; }
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
        <div class="report-title">تقرير التحصيلات</div>
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
                <th>رقم التحصيل</th>
                <th>رقم الفاتورة</th>
                <th>اسم العميل</th>
                <th>الشركة</th>
                <th>المبلغ</th>
                <th>طريقة الدفع</th>
                <th>رقم المرجع</th>
                <th>تم التحصيل بواسطة</th>
                <th>تاريخ التحصيل</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalAmount = 0;
            @endphp
            @foreach($collections as $collection)
                @php
                    $totalAmount += $collection->amount;
                @endphp
                <tr>
                    <td>{{ $collection->collection_number }}</td>
                    <td>{{ $collection->invoice->invoice_number }}</td>
                    <td>{{ $collection->customer->name }}</td>
                    <td>{{ $collection->customer->company_name ?? 'غير محدد' }}</td>
                    <td>{{ number_format($collection->amount, 2) }} دينار</td>
                    <td class="payment-{{ $collection->payment_method }}">
                        @switch($collection->payment_method)
                            @case('cash') نقداً @break
                            @case('bank_transfer') تحويل بنكي @break
                            @case('check') شيك @break
                            @case('credit_card') بطاقة ائتمان @break
                            @default {{ $collection->payment_method }}
                        @endswitch
                    </td>
                    <td>{{ $collection->reference_number ?? 'غير محدد' }}</td>
                    <td>{{ $collection->collectedBy->name }}</td>
                    <td>{{ $collection->collection_date->format('Y-m-d') }}</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="4"><strong>إجمالي التحصيلات</strong></td>
                <td><strong>{{ number_format($totalAmount, 2) }} دينار</strong></td>
                <td colspan="4"></td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p>تم إنشاء هذا التقرير بواسطة نظام إدارة المذاخر - {{ date('Y-m-d H:i:s') }}</p>
        <p>عدد التحصيلات: {{ count($collections) }}</p>
    </div>
</body>
</html>
