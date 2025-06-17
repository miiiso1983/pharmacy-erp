@extends('layouts.app')

@section('title', 'التقارير - نظام إدارة المذاخر')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
    <li class="breadcrumb-item active">التقارير</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>
                <i class="fas fa-chart-bar me-2"></i>
                التقارير والإحصائيات
            </h2>
        </div>
    </div>
</div>

<!-- مقدمة التقارير -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h4 class="mb-2">مركز التقارير والتحليلات</h4>
                        <p class="mb-0">احصل على رؤى شاملة حول أداء عملك من خلال التقارير التفصيلية والإحصائيات المتقدمة</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <i class="fas fa-chart-line fa-4x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- تقارير المبيعات والمالية -->
<div class="row mb-4">
    <div class="col-12">
        <h4 class="mb-3">
            <i class="fas fa-dollar-sign me-2"></i>
            التقارير المالية والمبيعات
        </h4>
    </div>
    
    <div class="col-lg-4 col-md-6 mb-3">
        <div class="card report-card h-100">
            <div class="card-body text-center">
                <div class="report-icon mb-3">
                    <i class="fas fa-chart-line fa-3x text-success"></i>
                </div>
                <h5 class="card-title">تقرير المبيعات</h5>
                <p class="card-text text-muted">تحليل شامل للمبيعات والطلبات خلال فترة محددة</p>
                <a href="{{ route('reports.sales') }}" class="btn btn-success">
                    <i class="fas fa-eye me-2"></i>
                    عرض التقرير
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4 col-md-6 mb-3">
        <div class="card report-card h-100">
            <div class="card-body text-center">
                <div class="report-icon mb-3">
                    <i class="fas fa-file-invoice-dollar fa-3x text-primary"></i>
                </div>
                <h5 class="card-title">التقرير المالي</h5>
                <p class="card-text text-muted">تقرير الفواتير والتحصيلات والمبالغ المستحقة</p>
                <a href="{{ route('reports.financial') }}" class="btn btn-primary">
                    <i class="fas fa-eye me-2"></i>
                    عرض التقرير
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4 col-md-6 mb-3">
        <div class="card report-card h-100">
            <div class="card-body text-center">
                <div class="report-icon mb-3">
                    <i class="fas fa-star fa-3x text-warning"></i>
                </div>
                <h5 class="card-title">أفضل المنتجات</h5>
                <p class="card-text text-muted">تقرير المنتجات الأكثر مبيعاً وربحية</p>
                <a href="{{ route('reports.topItems') }}" class="btn btn-warning">
                    <i class="fas fa-eye me-2"></i>
                    عرض التقرير
                </a>
            </div>
        </div>
    </div>
</div>

<!-- تقارير المخزون والعملاء -->
<div class="row mb-4">
    <div class="col-12">
        <h4 class="mb-3">
            <i class="fas fa-warehouse me-2"></i>
            تقارير المخزون والعملاء
        </h4>
    </div>
    
    <div class="col-lg-4 col-md-6 mb-3">
        <div class="card report-card h-100">
            <div class="card-body text-center">
                <div class="report-icon mb-3">
                    <i class="fas fa-boxes fa-3x text-info"></i>
                </div>
                <h5 class="card-title">تقرير المخزون</h5>
                <p class="card-text text-muted">حالة المخزون والمنتجات منخفضة المخزون</p>
                <a href="{{ route('reports.inventory') }}" class="btn btn-info">
                    <i class="fas fa-eye me-2"></i>
                    عرض التقرير
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4 col-md-6 mb-3">
        <div class="card report-card h-100">
            <div class="card-body text-center">
                <div class="report-icon mb-3">
                    <i class="fas fa-users fa-3x text-secondary"></i>
                </div>
                <h5 class="card-title">تقرير العملاء</h5>
                <p class="card-text text-muted">تحليل أداء العملاء ومشترياتهم</p>
                <a href="{{ route('reports.customers') }}" class="btn btn-secondary">
                    <i class="fas fa-eye me-2"></i>
                    عرض التقرير
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4 col-md-6 mb-3">
        <div class="card report-card h-100">
            <div class="card-body text-center">
                <div class="report-icon mb-3">
                    <i class="fas fa-chart-pie fa-3x text-danger"></i>
                </div>
                <h5 class="card-title">تقارير مخصصة</h5>
                <p class="card-text text-muted">إنشاء تقارير مخصصة حسب احتياجاتك</p>
                <button class="btn btn-danger" disabled>
                    <i class="fas fa-cog me-2"></i>
                    قريباً
                </button>
            </div>
        </div>
    </div>
</div>

<!-- إحصائيات سريعة -->
<div class="row">
    <div class="col-12">
        <h4 class="mb-3">
            <i class="fas fa-tachometer-alt me-2"></i>
            إحصائيات سريعة
        </h4>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card bg-gradient-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1">{{ \App\Models\Order::count() }}</h4>
                        <p class="mb-0">إجمالي الطلبات</p>
                    </div>
                    <div class="fs-1 opacity-75">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card bg-gradient-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1">{{ number_format(\App\Models\Invoice::sum('total_amount'), 0) }}</h4>
                        <p class="mb-0">إجمالي المبيعات (دينار)</p>
                    </div>
                    <div class="fs-1 opacity-75">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card bg-gradient-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1">{{ \App\Models\Item::count() }}</h4>
                        <p class="mb-0">إجمالي المنتجات</p>
                    </div>
                    <div class="fs-1 opacity-75">
                        <i class="fas fa-pills"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card bg-gradient-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1">{{ \App\Models\User::where('user_type', 'customer')->count() }}</h4>
                        <p class="mb-0">إجمالي العملاء</p>
                    </div>
                    <div class="fs-1 opacity-75">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- نصائح وإرشادات -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card border-info">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">
                    <i class="fas fa-lightbulb me-2"></i>
                    نصائح لاستخدام التقارير
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                استخدم فلاتر التاريخ للحصول على بيانات دقيقة
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                راجع التقارير بانتظام لمتابعة الأداء
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                استخدم تقرير المخزون لتجنب نفاد المنتجات
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                قارن الأداء بين فترات مختلفة
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                اطبع التقارير للاجتماعات والعروض
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                تابع أفضل المنتجات لزيادة المبيعات
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .report-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: none;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .report-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 20px rgba(0,0,0,0.15);
    }
    
    .report-icon {
        height: 80px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .bg-gradient-success {
        background: linear-gradient(135deg, #56ab2f 0%, #a8e6cf 100%);
    }
    
    .bg-gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    
    .bg-gradient-warning {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }
    
    .bg-gradient-info {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }
</style>
@endpush
