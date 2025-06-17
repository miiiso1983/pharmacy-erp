@extends('layouts.app')

@section('title', 'تفاصيل الطلب #' . $order->order_number . ' - نظام إدارة المذاخر')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
    <li class="breadcrumb-item"><a href="{{ route('orders.index') }}">الطلبات</a></li>
    <li class="breadcrumb-item active">تفاصيل الطلب #{{ $order->order_number }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>
                <i class="fas fa-shopping-cart me-2"></i>
                تفاصيل الطلب #{{ $order->order_number }}
            </h2>
            <div>
                <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary me-2">
                    <i class="fas fa-arrow-left me-2"></i>
                    العودة للقائمة
                </a>
                @if($order->invoice)
                    <a href="{{ route('invoices.show', $order->invoice->id) }}" class="btn btn-info">
                        <i class="fas fa-file-invoice me-2"></i>
                        عرض الفاتورة
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- معلومات الطلب الأساسية -->
<div class="row mb-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    معلومات الطلب
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>رقم الطلب:</strong></td>
                                <td>{{ $order->order_number }}</td>
                            </tr>
                            <tr>
                                <td><strong>العميل:</strong></td>
                                <td>
                                    {{ $order->customer->name }}
                                    @if($order->customer->company_name)
                                        <br><small class="text-muted">{{ $order->customer->company_name }}</small>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>الحالة:</strong></td>
                                <td>
                                    @switch($order->status)
                                        @case('pending')
                                            <span class="badge bg-warning fs-6">في الانتظار</span>
                                            @break
                                        @case('confirmed')
                                            <span class="badge bg-info fs-6">مؤكد</span>
                                            @break
                                        @case('processing')
                                            <span class="badge bg-primary fs-6">قيد المعالجة</span>
                                            @break
                                        @case('shipped')
                                            <span class="badge bg-secondary fs-6">تم الشحن</span>
                                            @break
                                        @case('delivered')
                                            <span class="badge bg-success fs-6">تم التسليم</span>
                                            @break
                                        @case('cancelled')
                                            <span class="badge bg-danger fs-6">ملغي</span>
                                            @break
                                    @endswitch
                                </td>
                            </tr>
                            <tr>
                                <td><strong>تاريخ الإنشاء:</strong></td>
                                <td>{{ $order->created_at->format('Y-m-d H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>عنوان التسليم:</strong></td>
                                <td>{{ $order->delivery_address ?? 'غير محدد' }}</td>
                            </tr>
                            <tr>
                                <td><strong>تاريخ التسليم:</strong></td>
                                <td>{{ $order->delivery_date ? $order->delivery_date->format('Y-m-d') : 'غير محدد' }}</td>
                            </tr>
                            <tr>
                                <td><strong>ملاحظات:</strong></td>
                                <td>{{ $order->notes ?? 'لا توجد ملاحظات' }}</td>
                            </tr>
                            <tr>
                                <td><strong>أنشئ بواسطة:</strong></td>
                                <td>{{ $order->createdBy->name ?? 'غير محدد' }}</td>
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
                        <td class="text-end"><strong>{{ number_format($order->subtotal, 2) }} دينار</strong></td>
                    </tr>
                    <tr>
                        <td>الضريبة (15%):</td>
                        <td class="text-end"><strong>{{ number_format($order->tax_amount, 2) }} دينار</strong></td>
                    </tr>
                    <tr class="border-top">
                        <td><strong>المجموع الإجمالي:</strong></td>
                        <td class="text-end"><strong class="text-primary fs-5">{{ number_format($order->total_amount, 2) }} دينار</strong></td>
                    </tr>
                </table>
                
                @can('edit_orders')
                    @if($order->status !== 'delivered' && $order->status !== 'cancelled')
                        <hr>
                        <form method="POST" action="{{ route('orders.updateStatus', $order->id) }}">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label class="form-label">تحديث الحالة:</label>
                                <select name="status" class="form-select" required>
                                    <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>في الانتظار</option>
                                    <option value="confirmed" {{ $order->status === 'confirmed' ? 'selected' : '' }}>مؤكد</option>
                                    <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>قيد المعالجة</option>
                                    <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>تم الشحن</option>
                                    <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>تم التسليم</option>
                                    <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>ملغي</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-save me-2"></i>
                                تحديث الحالة
                            </button>
                        </form>
                    @endif
                @endcan
            </div>
        </div>
    </div>
</div>

<!-- عناصر الطلب -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2"></i>
                    عناصر الطلب ({{ $order->orderItems->count() }})
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
                            @foreach($order->orderItems as $orderItem)
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
                                <td><strong>{{ number_format($order->subtotal, 2) }} دينار</strong></td>
                            </tr>
                            <tr class="table-light">
                                <td colspan="5" class="text-end"><strong>الضريبة (15%):</strong></td>
                                <td><strong>{{ number_format($order->tax_amount, 2) }} دينار</strong></td>
                            </tr>
                            <tr class="table-primary">
                                <td colspan="5" class="text-end"><strong>المجموع الإجمالي:</strong></td>
                                <td><strong>{{ number_format($order->total_amount, 2) }} دينار</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@if($order->status === 'delivered' && Auth::user()->user_type === 'customer')
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-success">
                <div class="card-body text-center">
                    <h5 class="text-success">
                        <i class="fas fa-check-circle me-2"></i>
                        تم تسليم الطلب بنجاح
                    </h5>
                    <p class="text-muted">هل تريد إعادة طلب نفس العناصر؟</p>
                    <a href="{{ route('orders.repeat', $order->id) }}" class="btn btn-success">
                        <i class="fas fa-redo me-2"></i>
                        إعادة الطلب
                    </a>
                </div>
            </div>
        </div>
    </div>
@endif
@endsection
