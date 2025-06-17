<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فاتورة #{{ $invoice->invoice_number }} - نظام إدارة المذاخر</title>
    
    <!-- Bootstrap RTL CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <!-- Google Fonts - Cairo -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Cairo', sans-serif;
            background-color: white;
            color: #333;
        }
        
        .invoice-header {
            border-bottom: 3px solid #007bff;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .company-info {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .company-name {
            font-size: 2rem;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 10px;
        }
        
        .invoice-details {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
        }
        
        .table th {
            background-color: #007bff;
            color: white;
            border: none;
        }
        
        .table td {
            border-bottom: 1px solid #dee2e6;
        }
        
        .total-section {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            border: 2px solid #007bff;
        }
        
        .footer-note {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            color: #6c757d;
        }
        
        @media print {
            .no-print {
                display: none !important;
            }
            
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .table th {
                background-color: #007bff !important;
                color: white !important;
            }
        }
        
        .status-badge {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-weight: bold;
            text-align: center;
        }
        
        .status-paid {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        
        .status-overdue {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <!-- أزرار الطباعة -->
        <div class="no-print mb-4">
            <div class="d-flex justify-content-between">
                <button onclick="window.print()" class="btn btn-primary">
                    <i class="fas fa-print me-2"></i>
                    طباعة الفاتورة
                </button>
                <button onclick="window.close()" class="btn btn-secondary">
                    <i class="fas fa-times me-2"></i>
                    إغلاق
                </button>
            </div>
        </div>

        <!-- رأس الشركة -->
        <div class="company-info">
            <div class="company-name">نظام إدارة المذاخر</div>
            <div class="h5 text-muted">إدارة شاملة للصيدليات والمذاخر الطبية</div>
            <div class="text-muted">
                <div>الرياض، المملكة العربية السعودية</div>
                <div>هاتف: 0112345678 | البريد الإلكتروني: info@pharmacy-erp.com</div>
                <div>الرقم الضريبي: 123456789012345</div>
            </div>
        </div>

        <!-- رأس الفاتورة -->
        <div class="invoice-header">
            <div class="row">
                <div class="col-md-6">
                    <h2 class="text-primary">فاتورة ضريبية</h2>
                    <h4>رقم الفاتورة: {{ $invoice->invoice_number }}</h4>
                </div>
                <div class="col-md-6 text-end">
                    <div class="status-badge status-{{ $invoice->status === 'paid' ? 'paid' : ($invoice->status === 'overdue' ? 'overdue' : 'pending') }}">
                        @switch($invoice->status)
                            @case('paid')
                                مدفوعة
                                @break
                            @case('pending')
                                معلقة
                                @break
                            @case('partially_paid')
                                مدفوعة جزئياً
                                @break
                            @case('overdue')
                                متأخرة
                                @break
                        @endswitch
                    </div>
                </div>
            </div>
        </div>

        <!-- تفاصيل الفاتورة -->
        <div class="invoice-details">
            <div class="row">
                <div class="col-md-6">
                    <h5 class="text-primary mb-3">بيانات العميل:</h5>
                    <div><strong>الاسم:</strong> {{ $invoice->customer->name }}</div>
                    @if($invoice->customer->company_name)
                        <div><strong>الشركة:</strong> {{ $invoice->customer->company_name }}</div>
                    @endif
                    <div><strong>البريد الإلكتروني:</strong> {{ $invoice->customer->email }}</div>
                    @if($invoice->customer->phone)
                        <div><strong>الهاتف:</strong> {{ $invoice->customer->phone }}</div>
                    @endif
                    @if($invoice->customer->address)
                        <div><strong>العنوان:</strong> {{ $invoice->customer->address }}</div>
                    @endif
                </div>
                <div class="col-md-6">
                    <h5 class="text-primary mb-3">تفاصيل الفاتورة:</h5>
                    <div><strong>رقم الطلب:</strong> {{ $invoice->order->order_number }}</div>
                    <div><strong>تاريخ الإنشاء:</strong> {{ $invoice->created_at->format('Y-m-d') }}</div>
                    @if($invoice->due_date)
                        <div><strong>تاريخ الاستحقاق:</strong> {{ $invoice->due_date->format('Y-m-d') }}</div>
                    @endif
                    <div><strong>طريقة الدفع:</strong> نقداً / تحويل بنكي</div>
                </div>
            </div>
        </div>

        <!-- جدول العناصر -->
        <div class="table-responsive mb-4">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th style="width: 5%">#</th>
                        <th style="width: 15%">الكود</th>
                        <th style="width: 35%">اسم العنصر</th>
                        <th style="width: 10%">الوحدة</th>
                        <th style="width: 10%">الكمية</th>
                        <th style="width: 12.5%">سعر الوحدة</th>
                        <th style="width: 12.5%">المجموع</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->order->orderItems as $index => $orderItem)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $orderItem->item->code }}</td>
                            <td>
                                <strong>{{ $orderItem->item->name }}</strong>
                                @if($orderItem->item->description)
                                    <br><small class="text-muted">{{ $orderItem->item->description }}</small>
                                @endif
                            </td>
                            <td>{{ $orderItem->item->unit }}</td>
                            <td class="text-center">{{ $orderItem->quantity }}</td>
                            <td class="text-end">{{ number_format($orderItem->unit_price, 2) }}</td>
                            <td class="text-end">{{ number_format($orderItem->total_price, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- ملخص المبالغ -->
        <div class="row">
            <div class="col-md-6">
                @if($invoice->notes)
                    <div class="mb-3">
                        <h6 class="text-primary">ملاحظات:</h6>
                        <p class="text-muted">{{ $invoice->notes }}</p>
                    </div>
                @endif
            </div>
            <div class="col-md-6">
                <div class="total-section">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <td><strong>المجموع الفرعي:</strong></td>
                            <td class="text-end"><strong>{{ number_format($invoice->subtotal, 2) }} دينار</strong></td>
                        </tr>
                        <tr>
                            <td><strong>ضريبة القيمة المضافة (15%):</strong></td>
                            <td class="text-end"><strong>{{ number_format($invoice->tax_amount, 2) }} دينار</strong></td>
                        </tr>
                        @if($invoice->discount_amount > 0)
                            <tr>
                                <td><strong>الخصم:</strong></td>
                                <td class="text-end"><strong class="text-success">-{{ number_format($invoice->discount_amount, 2) }} دينار</strong></td>
                            </tr>
                        @endif
                        <tr class="border-top">
                            <td><strong class="fs-5">المجموع الإجمالي:</strong></td>
                            <td class="text-end"><strong class="fs-5 text-primary">{{ number_format($invoice->total_amount, 2) }} دينار</strong></td>
                        </tr>
                        @if($invoice->paid_amount > 0)
                            <tr>
                                <td><strong>المبلغ المدفوع:</strong></td>
                                <td class="text-end"><strong class="text-success">{{ number_format($invoice->paid_amount, 2) }} دينار</strong></td>
                            </tr>
                        @endif
                        @if($invoice->remaining_amount > 0)
                            <tr>
                                <td><strong>المبلغ المتبقي:</strong></td>
                                <td class="text-end"><strong class="text-danger">{{ number_format($invoice->remaining_amount, 2) }} دينار</strong></td>
                            </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        <!-- المبلغ بالأحرف -->
        <div class="mt-4 p-3 bg-light rounded">
            <strong>المبلغ بالأحرف:</strong>
            <span class="text-muted">
                {{ $invoice->total_amount }} دينار سعودي فقط لا غير
            </span>
        </div>

        <!-- تذييل الفاتورة -->
        <div class="footer-note">
            <div class="row">
                <div class="col-md-4">
                    <div class="text-center">
                        <div class="border-top border-dark d-inline-block" style="width: 150px;"></div>
                        <div class="mt-2"><strong>توقيع العميل</strong></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center">
                        <div class="border-top border-dark d-inline-block" style="width: 150px;"></div>
                        <div class="mt-2"><strong>توقيع المحاسب</strong></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center">
                        <div class="border-top border-dark d-inline-block" style="width: 150px;"></div>
                        <div class="mt-2"><strong>ختم الشركة</strong></div>
                    </div>
                </div>
            </div>
            
            <div class="mt-4">
                <p class="mb-1"><strong>شروط وأحكام:</strong></p>
                <ul class="list-unstyled small text-muted">
                    <li>• يرجى مراجعة الفاتورة والتأكد من صحة البيانات</li>
                    <li>• في حالة وجود أي خطأ يرجى التواصل معنا خلال 24 ساعة</li>
                    <li>• جميع المبالغ بالدينار السعودي شاملة ضريبة القيمة المضافة</li>
                    <li>• هذه فاتورة ضريبية معتمدة وفقاً لأنظمة هيئة الزكاة والضريبة والجمارك</li>
                </ul>
            </div>
            
            <div class="mt-3">
                <small class="text-muted">
                    تم إنشاء هذه الفاتورة إلكترونياً بواسطة نظام إدارة المذاخر - {{ now()->format('Y-m-d H:i:s') }}
                </small>
            </div>
        </div>
    </div>

    <!-- Font Awesome -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    
    <script>
        // طباعة تلقائية عند فتح الصفحة (اختياري)
        // window.onload = function() { window.print(); }
        
        // إغلاق النافذة بعد الطباعة
        window.onafterprint = function() {
            // window.close();
        }
    </script>
</body>
</html>
