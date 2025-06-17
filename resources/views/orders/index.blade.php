@extends('layouts.app')

@section('title', 'الطلبات - نظام إدارة المذاخر')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
    <li class="breadcrumb-item active">الطلبات</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>
                <i class="fas fa-shopping-cart me-2"></i>
                الطلبات
            </h2>
            @can('create_orders')
                <a href="{{ route('orders.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>
                    طلب جديد
                </a>
            @endcan
        </div>
    </div>
</div>

<!-- فلاتر البحث -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('orders.index') }}">
                    <div class="row">
                        <div class="col-md-3">
                            <label class="form-label">رقم الطلب</label>
                            <input type="text" class="form-control" name="order_number" 
                                   value="{{ request('order_number') }}" placeholder="ابحث برقم الطلب">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">الحالة</label>
                            <select class="form-select" name="status">
                                <option value="">جميع الحالات</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>في الانتظار</option>
                                <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>مؤكد</option>
                                <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>قيد المعالجة</option>
                                <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>تم الشحن</option>
                                <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>تم التسليم</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ملغي</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">من تاريخ</label>
                            <input type="date" class="form-control" name="from_date" value="{{ request('from_date') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">إلى تاريخ</label>
                            <input type="date" class="form-control" name="to_date" value="{{ request('to_date') }}">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-search me-2"></i>بحث
                            </button>
                            <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>إلغاء
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- جدول الطلبات -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2"></i>
                    قائمة الطلبات ({{ $orders->total() }})
                </h5>
            </div>
            <div class="card-body">
                @if($orders->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>رقم الطلب</th>
                                    @if(Auth::user()->user_type !== 'customer')
                                        <th>العميل</th>
                                    @endif
                                    <th>الحالة</th>
                                    <th>عدد العناصر</th>
                                    <th>المبلغ الإجمالي</th>
                                    <th>تاريخ الإنشاء</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $order)
                                    <tr>
                                        <td>
                                            <strong>{{ $order->order_number }}</strong>
                                        </td>
                                        @if(Auth::user()->user_type !== 'customer')
                                            <td>
                                                <div>
                                                    <strong>{{ $order->customer->name }}</strong>
                                                    @if($order->customer->company_name)
                                                        <br><small class="text-muted">{{ $order->customer->company_name }}</small>
                                                    @endif
                                                </div>
                                            </td>
                                        @endif
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
                                        <td>{{ $order->orderItems->count() }} عنصر</td>
                                        <td>{{ number_format($order->total_amount, 2) }} دينار</td>
                                        <td>{{ $order->created_at->format('Y-m-d H:i') }}</td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('orders.show', $order->id) }}" 
                                                   class="btn btn-outline-primary" title="عرض التفاصيل">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @if($order->status === 'delivered' && Auth::user()->user_type === 'customer')
                                                    <a href="{{ route('orders.repeat', $order->id) }}" 
                                                       class="btn btn-outline-success" title="إعادة الطلب">
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
                    
                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $orders->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">لا توجد طلبات</h5>
                        <p class="text-muted">لم يتم العثور على أي طلبات تطابق معايير البحث</p>
                        @can('create_orders')
                            <a href="{{ route('orders.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>
                                إنشاء طلب جديد
                            </a>
                        @endcan
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
