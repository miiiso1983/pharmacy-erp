@extends('layouts.app')

@section('title', 'لوحة تحكم العميل - نظام إدارة المذاخر')

@section('breadcrumb')
    <li class="breadcrumb-item active">لوحة التحكم</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <h2 class="mb-4">
            <i class="fas fa-tachometer-alt me-2"></i>
            مرحباً {{ Auth::user()->name }}
        </h2>
    </div>
</div>

<!-- إحصائيات العميل -->
<div class="row mb-4">
    <div class="col-lg-4 col-md-6 mb-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="mb-1">{{ number_format($stats['total_orders']) }}</h3>
                    <p class="mb-0">إجمالي طلباتي</p>
                </div>
                <div class="fs-1 opacity-75">
                    <i class="fas fa-shopping-cart"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4 col-md-6 mb-3">
        <div class="stat-card warning">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="mb-1">{{ number_format($stats['pending_orders']) }}</h3>
                    <p class="mb-0">طلبات معلقة</p>
                </div>
                <div class="fs-1 opacity-75">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4 col-md-6 mb-3">
        <div class="stat-card success">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="mb-1">{{ number_format($stats['delivered_orders']) }}</h3>
                    <p class="mb-0">طلبات مكتملة</p>
                </div>
                <div class="fs-1 opacity-75">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- إحصائيات مالية -->
<div class="row mb-4">
    <div class="col-lg-4 col-md-6 mb-3">
        <div class="stat-card info">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="mb-1">{{ number_format($stats['total_invoices_amount'], 2) }}</h3>
                    <p class="mb-0">إجمالي الفواتير (دينار)</p>
                </div>
                <div class="fs-1 opacity-75">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4 col-md-6 mb-3">
        <div class="stat-card success">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="mb-1">{{ number_format($stats['paid_amount'], 2) }}</h3>
                    <p class="mb-0">المبلغ المدفوع (دينار)</p>
                </div>
                <div class="fs-1 opacity-75">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4 col-md-6 mb-3">
        <div class="stat-card danger">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="mb-1">{{ number_format($stats['remaining_amount'], 2) }}</h3>
                    <p class="mb-0">المبلغ المتبقي (دينار)</p>
                </div>
                <div class="fs-1 opacity-75">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- إجراءات سريعة -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-bolt me-2"></i>
                    إجراءات سريعة
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-3 col-md-6 mb-3">
                        <a href="#" class="btn btn-primary w-100 p-3">
                            <i class="fas fa-plus-circle fa-2x mb-2"></i><br>
                            طلب جديد
                        </a>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <a href="#" class="btn btn-info w-100 p-3">
                            <i class="fas fa-file-invoice fa-2x mb-2"></i><br>
                            عرض الفواتير
                        </a>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <a href="#" class="btn btn-success w-100 p-3">
                            <i class="fas fa-redo fa-2x mb-2"></i><br>
                            إعادة طلب
                        </a>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <a href="#" class="btn btn-warning w-100 p-3">
                            <i class="fas fa-undo fa-2x mb-2"></i><br>
                            طلب إرجاع
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- الطلبات الحديثة -->
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-shopping-cart me-2"></i>
                    طلباتي الحديثة
                </h5>
                <a href="#" class="btn btn-sm btn-outline-primary">عرض جميع الطلبات</a>
            </div>
            <div class="card-body">
                @if(count($recent_orders) > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>رقم الطلب</th>
                                    <th>الحالة</th>
                                    <th>المبلغ</th>
                                    <th>التاريخ</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recent_orders as $order)
                                    <tr>
                                        <td>{{ $order->order_number }}</td>
                                        <td>
                                            @switch($order->status)
                                                @case('pending')
                                                    <span class="badge bg-warning">في الانتظار</span>
                                                    @break
                                                @case('confirmed')
                                                    <span class="badge bg-info">مؤكد</span>
                                                    @break
                                                @case('processing')
                                                    <span class="badge bg-primary">قيد المعالجة</span>
                                                    @break
                                                @case('shipped')
                                                    <span class="badge bg-secondary">تم الشحن</span>
                                                    @break
                                                @case('delivered')
                                                    <span class="badge bg-success">تم التسليم</span>
                                                    @break
                                                @case('cancelled')
                                                    <span class="badge bg-danger">ملغي</span>
                                                    @break
                                            @endswitch
                                        </td>
                                        <td>{{ number_format($order->total_amount, 2) }} دينار</td>
                                        <td>{{ $order->created_at->format('Y-m-d') }}</td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="#" class="btn btn-outline-primary" title="عرض التفاصيل">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @if($order->status === 'delivered')
                                                    <a href="#" class="btn btn-outline-success" title="إعادة الطلب">
                                                        <i class="fas fa-redo"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-shopping-cart fa-3x mb-3"></i>
                        <h5>لا توجد طلبات حتى الآن</h5>
                        <p>ابدأ بإنشاء طلبك الأول</p>
                        <a href="#" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>
                            طلب جديد
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- الفواتير الحديثة -->
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-file-invoice me-2"></i>
                    فواتيري
                </h5>
                <a href="#" class="btn btn-sm btn-outline-primary">عرض الكل</a>
            </div>
            <div class="card-body">
                @if(count($recent_invoices) > 0)
                    <div class="list-group list-group-flush">
                        @foreach($recent_invoices as $invoice)
                            <div class="list-group-item px-0">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="mb-0">{{ $invoice->invoice_number }}</h6>
                                    @switch($invoice->status)
                                        @case('pending')
                                            <span class="badge bg-warning">معلقة</span>
                                            @break
                                        @case('paid')
                                            <span class="badge bg-success">مدفوعة</span>
                                            @break
                                        @case('partially_paid')
                                            <span class="badge bg-info">مدفوعة جزئياً</span>
                                            @break
                                        @case('overdue')
                                            <span class="badge bg-danger">متأخرة</span>
                                            @break
                                    @endswitch
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">{{ $invoice->created_at->format('Y-m-d') }}</small>
                                    <strong>{{ number_format($invoice->total_amount, 2) }} دينار</strong>
                                </div>
                                @if($invoice->remaining_amount > 0)
                                    <div class="mt-2">
                                        <small class="text-danger">
                                            متبقي: {{ number_format($invoice->remaining_amount, 2) }} دينار
                                        </small>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center text-muted">
                        <i class="fas fa-file-invoice fa-3x mb-3"></i>
                        <p>لا توجد فواتير</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
