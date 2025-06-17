@extends('layouts.app')

@section('title', 'تفاصيل العنصر: ' . $item->name . ' - نظام إدارة المذاخر')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
    <li class="breadcrumb-item"><a href="{{ route('items.index') }}">العناصر</a></li>
    <li class="breadcrumb-item active">{{ $item->name }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>
                <i class="fas fa-pills me-2"></i>
                {{ $item->name }}
            </h2>
            <div>
                <a href="{{ route('items.index') }}" class="btn btn-outline-secondary me-2">
                    <i class="fas fa-arrow-left me-2"></i>
                    العودة للقائمة
                </a>
                @can('edit_items')
                    <a href="{{ route('items.edit', $item->id) }}" class="btn btn-warning me-2">
                        <i class="fas fa-edit me-2"></i>
                        تعديل
                    </a>
                @endcan
                @can('delete_items')
                    <form method="POST" action="{{ route('items.destroy', $item->id) }}" 
                          class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا العنصر؟')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash me-2"></i>
                            حذف
                        </button>
                    </form>
                @endcan
            </div>
        </div>
    </div>
</div>

<!-- معلومات العنصر الأساسية -->
<div class="row mb-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    معلومات العنصر
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>الكود:</strong></td>
                                <td>{{ $item->code }}</td>
                            </tr>
                            <tr>
                                <td><strong>اسم العنصر:</strong></td>
                                <td>{{ $item->name }}</td>
                            </tr>
                            <tr>
                                <td><strong>الفئة:</strong></td>
                                <td>{{ $item->category ?? 'غير محدد' }}</td>
                            </tr>
                            <tr>
                                <td><strong>الوحدة:</strong></td>
                                <td>{{ $item->unit }}</td>
                            </tr>
                            <tr>
                                <td><strong>الباركود:</strong></td>
                                <td>{{ $item->barcode ?? 'غير محدد' }}</td>
                            </tr>
                            <tr>
                                <td><strong>المورد:</strong></td>
                                <td>{{ $item->supplier->name ?? 'غير محدد' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>السعر:</strong></td>
                                <td><span class="text-success fs-5">{{ number_format($item->price, 2) }} دينار</span></td>
                            </tr>
                            <tr>
                                <td><strong>التكلفة:</strong></td>
                                <td>{{ $item->cost ? number_format($item->cost, 2) . ' دينار' : 'غير محدد' }}</td>
                            </tr>
                            <tr>
                                <td><strong>المخزون الحالي:</strong></td>
                                <td>
                                    <span class="badge {{ $item->stock_quantity <= $item->min_stock_level ? 'bg-danger' : 'bg-success' }} fs-6">
                                        {{ $item->stock_quantity }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>الحد الأدنى للمخزون:</strong></td>
                                <td>{{ $item->min_stock_level }}</td>
                            </tr>
                            <tr>
                                <td><strong>تاريخ الانتهاء:</strong></td>
                                <td>
                                    @if($item->expiry_date)
                                        {{ $item->expiry_date->format('Y-m-d') }}
                                        @if($item->expiry_date->isPast())
                                            <span class="badge bg-danger ms-2">منتهي الصلاحية</span>
                                        @elseif($item->expiry_date->diffInDays() <= 30)
                                            <span class="badge bg-warning ms-2">ينتهي قريباً</span>
                                        @endif
                                    @else
                                        غير محدد
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>رقم الدفعة:</strong></td>
                                <td>{{ $item->batch_number ?? 'غير محدد' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                @if($item->description)
                    <hr>
                    <div class="row">
                        <div class="col-12">
                            <h6><strong>الوصف:</strong></h6>
                            <p class="text-muted">{{ $item->description }}</p>
                        </div>
                    </div>
                @endif
                
                @if($item->notes)
                    <hr>
                    <div class="row">
                        <div class="col-12">
                            <h6><strong>ملاحظات:</strong></h6>
                            <p class="text-muted">{{ $item->notes }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <!-- حالة المخزون -->
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-warehouse me-2"></i>
                    حالة المخزون
                </h5>
            </div>
            <div class="card-body text-center">
                @if($item->stock_quantity <= 0)
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                        <h6>نفد المخزون</h6>
                        <p class="mb-0">هذا العنصر غير متوفر حالياً</p>
                    </div>
                @elseif($item->stock_quantity <= $item->min_stock_level)
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                        <h6>مخزون منخفض</h6>
                        <p class="mb-0">يحتاج إعادة تموين</p>
                    </div>
                @else
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle fa-2x mb-2"></i>
                        <h6>مخزون جيد</h6>
                        <p class="mb-0">الكمية متوفرة</p>
                    </div>
                @endif
                
                <div class="row text-center mt-3">
                    <div class="col-6">
                        <h4 class="text-primary">{{ $item->stock_quantity }}</h4>
                        <small class="text-muted">الكمية الحالية</small>
                    </div>
                    <div class="col-6">
                        <h4 class="text-warning">{{ $item->min_stock_level }}</h4>
                        <small class="text-muted">الحد الأدنى</small>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- حالة العنصر -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-toggle-on me-2"></i>
                    حالة العنصر
                </h5>
            </div>
            <div class="card-body text-center">
                @if($item->status === 'active')
                    <span class="badge bg-success fs-6 mb-3">نشط</span>
                    <p class="text-muted">هذا العنصر متاح للطلب</p>
                @else
                    <span class="badge bg-secondary fs-6 mb-3">غير نشط</span>
                    <p class="text-muted">هذا العنصر غير متاح للطلب</p>
                @endif
                
                <hr>
                
                <div class="row text-center">
                    <div class="col-12">
                        <small class="text-muted">تاريخ الإنشاء</small>
                        <br>
                        <strong>{{ $item->created_at->format('Y-m-d') }}</strong>
                    </div>
                </div>
                
                @if($item->updated_at != $item->created_at)
                    <div class="row text-center mt-2">
                        <div class="col-12">
                            <small class="text-muted">آخر تحديث</small>
                            <br>
                            <strong>{{ $item->updated_at->format('Y-m-d H:i') }}</strong>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- إحصائيات العنصر -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-chart-bar me-2"></i>
                    إحصائيات العنصر
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="stat-card">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h4 class="mb-1">{{ $item->orderItems->sum('quantity') }}</h4>
                                    <p class="mb-0">إجمالي المبيعات</p>
                                </div>
                                <div class="fs-1 opacity-75">
                                    <i class="fas fa-shopping-cart"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="stat-card success">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h4 class="mb-1">{{ number_format($item->orderItems->sum('total_price'), 2) }}</h4>
                                    <p class="mb-0">إجمالي الإيرادات (دينار)</p>
                                </div>
                                <div class="fs-1 opacity-75">
                                    <i class="fas fa-dollar-sign"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="stat-card warning">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h4 class="mb-1">{{ $item->orderItems->groupBy('order_id')->count() }}</h4>
                                    <p class="mb-0">عدد الطلبات</p>
                                </div>
                                <div class="fs-1 opacity-75">
                                    <i class="fas fa-list"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="stat-card info">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h4 class="mb-1">{{ $item->orderItems->avg('quantity') ? number_format($item->orderItems->avg('quantity'), 1) : '0' }}</h4>
                                    <p class="mb-0">متوسط الكمية</p>
                                </div>
                                <div class="fs-1 opacity-75">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .stat-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1.5rem;
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        transition: transform 0.3s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
    }
    
    .stat-card.success {
        background: linear-gradient(135deg, #56ab2f 0%, #a8e6cf 100%);
    }
    
    .stat-card.warning {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }
    
    .stat-card.info {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }
</style>
@endpush
