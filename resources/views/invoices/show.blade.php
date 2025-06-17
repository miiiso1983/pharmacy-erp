@extends('layouts.app')

@section('title', 'تفاصيل الفاتورة #' . $invoice->invoice_number . ' - نظام إدارة المذاخر')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
    <li class="breadcrumb-item"><a href="{{ route('invoices.index') }}">الفواتير</a></li>
    <li class="breadcrumb-item active">فاتورة #{{ $invoice->invoice_number }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>
                <i class="fas fa-file-invoice me-2"></i>
                فاتورة #{{ $invoice->invoice_number }}
            </h2>
            <div>
                <a href="{{ route('invoices.index') }}" class="btn btn-outline-secondary me-2">
                    <i class="fas fa-arrow-left me-2"></i>
                    العودة للقائمة
                </a>
                <a href="{{ route('invoices.print', $invoice->id) }}" class="btn btn-info me-2" target="_blank">
                    <i class="fas fa-print me-2"></i>
                    طباعة
                </a>
                @can('edit_invoices')
                    @if($invoice->remaining_amount > 0)
                        <form method="POST" action="{{ route('invoices.markAsPaid', $invoice->id) }}" 
                              class="d-inline" onsubmit="return confirm('هل أنت متأكد من وضع علامة مدفوع؟')">
                            @csrf
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-check me-2"></i>
                                وضع علامة مدفوع
                            </button>
                        </form>
                    @endif
                @endcan
            </div>
        </div>
    </div>
</div>

<!-- معلومات الفاتورة الأساسية -->
<div class="row mb-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    معلومات الفاتورة
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>رقم الفاتورة:</strong></td>
                                <td>{{ $invoice->invoice_number }}</td>
                            </tr>
                            <tr>
                                <td><strong>رقم الطلب:</strong></td>
                                <td>
                                    <a href="{{ route('orders.show', $invoice->order->id) }}" class="text-decoration-none">
                                        {{ $invoice->order->order_number }}
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>العميل:</strong></td>
                                <td>
                                    {{ $invoice->customer->name }}
                                    @if($invoice->customer->company_name)
                                        <br><small class="text-muted">{{ $invoice->customer->company_name }}</small>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>الحالة:</strong></td>
                                <td>
                                    @switch($invoice->status)
                                        @case('pending')
                                            <span class="badge bg-warning fs-6">معلقة</span>
                                            @break
                                        @case('paid')
                                            <span class="badge bg-success fs-6">مدفوعة</span>
                                            @break
                                        @case('partially_paid')
                                            <span class="badge bg-info fs-6">مدفوعة جزئياً</span>
                                            @break
                                        @case('overdue')
                                            <span class="badge bg-danger fs-6">متأخرة</span>
                                            @break
                                    @endswitch
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>تاريخ الإنشاء:</strong></td>
                                <td>{{ $invoice->created_at->format('Y-m-d H:i') }}</td>
                            </tr>
                            <tr>
                                <td><strong>تاريخ الاستحقاق:</strong></td>
                                <td>
                                    @if($invoice->due_date)
                                        {{ $invoice->due_date->format('Y-m-d') }}
                                        @if($invoice->due_date->isPast() && $invoice->remaining_amount > 0)
                                            <br><small class="text-danger">متأخرة</small>
                                        @endif
                                    @else
                                        غير محدد
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>ملاحظات:</strong></td>
                                <td>{{ $invoice->notes ?? 'لا توجد ملاحظات' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-calculator me-2"></i>
                    ملخص المبالغ
                </h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <td>المجموع الفرعي:</td>
                        <td class="text-end"><strong>{{ number_format($invoice->subtotal, 2) }} دينار</strong></td>
                    </tr>
                    <tr>
                        <td>الضريبة (15%):</td>
                        <td class="text-end"><strong>{{ number_format($invoice->tax_amount, 2) }} دينار</strong></td>
                    </tr>
                    @if($invoice->discount_amount > 0)
                        <tr>
                            <td>الخصم:</td>
                            <td class="text-end"><strong class="text-success">-{{ number_format($invoice->discount_amount, 2) }} دينار</strong></td>
                        </tr>
                    @endif
                    <tr class="border-top">
                        <td><strong>المجموع الإجمالي:</strong></td>
                        <td class="text-end"><strong class="text-primary fs-5">{{ number_format($invoice->total_amount, 2) }} دينار</strong></td>
                    </tr>
                    <tr>
                        <td><strong>المبلغ المدفوع:</strong></td>
                        <td class="text-end"><strong class="text-success">{{ number_format($invoice->paid_amount, 2) }} دينار</strong></td>
                    </tr>
                    <tr class="border-top">
                        <td><strong>المبلغ المتبقي:</strong></td>
                        <td class="text-end">
                            <strong class="{{ $invoice->remaining_amount > 0 ? 'text-danger' : 'text-success' }} fs-5">
                                {{ number_format($invoice->remaining_amount, 2) }} دينار
                            </strong>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- عناصر الفاتورة -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2"></i>
                    عناصر الفاتورة ({{ $invoice->order->orderItems->count() }})
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>الكود</th>
                                <th>اسم العنصر</th>
                                <th>الوحدة</th>
                                <th>الكمية</th>
                                <th>سعر الوحدة</th>
                                <th>المجموع</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invoice->order->orderItems as $orderItem)
                                <tr>
                                    <td>
                                        <strong>{{ $orderItem->item->code }}</strong>
                                        @if($orderItem->item->barcode)
                                            <br><small class="text-muted">{{ $orderItem->item->barcode }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ $orderItem->item->name }}</strong>
                                            @if($orderItem->item->description)
                                                <br><small class="text-muted">{{ Str::limit($orderItem->item->description, 50) }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>{{ $orderItem->item->unit }}</td>
                                    <td>
                                        <span class="badge bg-primary">{{ $orderItem->quantity }}</span>
                                    </td>
                                    <td>{{ number_format($orderItem->unit_price, 2) }} دينار</td>
                                    <td><strong>{{ number_format($orderItem->total_price, 2) }} دينار</strong></td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="table-light">
                                <td colspan="5" class="text-end"><strong>المجموع الفرعي:</strong></td>
                                <td><strong>{{ number_format($invoice->subtotal, 2) }} دينار</strong></td>
                            </tr>
                            <tr class="table-light">
                                <td colspan="5" class="text-end"><strong>الضريبة (15%):</strong></td>
                                <td><strong>{{ number_format($invoice->tax_amount, 2) }} دينار</strong></td>
                            </tr>
                            @if($invoice->discount_amount > 0)
                                <tr class="table-light">
                                    <td colspan="5" class="text-end"><strong>الخصم:</strong></td>
                                    <td><strong class="text-success">-{{ number_format($invoice->discount_amount, 2) }} دينار</strong></td>
                                </tr>
                            @endif
                            <tr class="table-primary">
                                <td colspan="5" class="text-end"><strong>المجموع الإجمالي:</strong></td>
                                <td><strong>{{ number_format($invoice->total_amount, 2) }} دينار</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- سجل التحصيلات -->
@if($invoice->collections->count() > 0)
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-money-bill-wave me-2"></i>
                        سجل التحصيلات ({{ $invoice->collections->count() }})
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>التاريخ</th>
                                    <th>المبلغ</th>
                                    <th>طريقة الدفع</th>
                                    <th>تم التحصيل بواسطة</th>
                                    <th>ملاحظات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($invoice->collections as $collection)
                                    <tr>
                                        <td>{{ $collection->collection_date->format('Y-m-d H:i') }}</td>
                                        <td><strong class="text-success">{{ number_format($collection->amount, 2) }} دينار</strong></td>
                                        <td>
                                            @switch($collection->payment_method)
                                                @case('cash')
                                                    <span class="badge bg-success">نقداً</span>
                                                    @break
                                                @case('bank_transfer')
                                                    <span class="badge bg-primary">تحويل بنكي</span>
                                                    @break
                                                @case('check')
                                                    <span class="badge bg-warning">شيك</span>
                                                    @break
                                                @case('credit_card')
                                                    <span class="badge bg-info">بطاقة ائتمان</span>
                                                    @break
                                                @default
                                                    <span class="badge bg-secondary">{{ $collection->payment_method }}</span>
                                            @endswitch
                                        </td>
                                        <td>{{ $collection->collectedBy->name ?? 'غير محدد' }}</td>
                                        <td>{{ $collection->notes ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="table-success">
                                    <td><strong>إجمالي المحصل:</strong></td>
                                    <td><strong>{{ number_format($invoice->collections->sum('amount'), 2) }} دينار</strong></td>
                                    <td colspan="3"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
@endsection
