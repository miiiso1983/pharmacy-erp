@extends('layouts.app')

@section('title', 'أفضل المنتجات - نظام إدارة المذاخر')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
    <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">التقارير</a></li>
    <li class="breadcrumb-item active">أفضل المنتجات</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>
                <i class="fas fa-star me-2"></i>
                أفضل المنتجات
            </h2>
            <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                العودة للتقارير
            </a>
        </div>
    </div>
</div>

<!-- فلاتر التاريخ -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('reports.topItems') }}">
                    <div class="row align-items-end">
                        <div class="col-md-4">
                            <label class="form-label">من تاريخ</label>
                            <input type="date" class="form-control" name="from_date" 
                                   value="{{ $fromDate->format('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">إلى تاريخ</label>
                            <input type="date" class="form-control" name="to_date" 
                                   value="{{ $toDate->format('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-2"></i>
                                تحديث التقرير
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- إحصائيات المنتجات -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stat-card primary">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">{{ $topItems->count() }}</h4>
                    <p class="mb-0">منتجات مباعة</p>
                </div>
                <div class="fs-1 opacity-75">
                    <i class="fas fa-pills"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stat-card success">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">{{ number_format($topItems->sum('total_revenue'), 2) }}</h4>
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
                    <h4 class="mb-1">{{ $topItems->sum('total_quantity') }}</h4>
                    <p class="mb-0">إجمالي الكمية المباعة</p>
                </div>
                <div class="fs-1 opacity-75">
                    <i class="fas fa-boxes"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stat-card info">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">{{ $topItems->count() > 0 ? number_format($topItems->sum('total_revenue') / $topItems->count(), 2) : 0 }}</h4>
                    <p class="mb-0">متوسط الإيراد (دينار)</p>
                </div>
                <div class="fs-1 opacity-75">
                    <i class="fas fa-chart-line"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- جدول أفضل المنتجات -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-trophy me-2"></i>
                    أفضل 20 منتج من {{ $fromDate->format('Y-m-d') }} إلى {{ $toDate->format('Y-m-d') }}
                </h5>
            </div>
            <div class="card-body">
                @if($topItems->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>الترتيب</th>
                                    <th>الكود</th>
                                    <th>اسم المنتج</th>
                                    <th>الفئة</th>
                                    <th>المورد</th>
                                    <th>الكمية المباعة</th>
                                    <th>إجمالي الإيرادات</th>
                                    <th>متوسط السعر</th>
                                    <th>المخزون الحالي</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topItems as $index => $item)
                                    <tr>
                                        <td>
                                            <div class="rank-badge-{{ $index < 3 ? ['gold', 'silver', 'bronze'][$index] : 'default' }}">
                                                {{ $index + 1 }}
                                            </div>
                                        </td>
                                        <td>
                                            <a href="{{ route('items.show', $item->id) }}" class="text-decoration-none">
                                                <strong>{{ $item->code }}</strong>
                                            </a>
                                            @if($item->barcode)
                                                <br><small class="text-muted">{{ $item->barcode }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <div>
                                                <strong>{{ $item->name }}</strong>
                                                @if($item->description)
                                                    <br><small class="text-muted">{{ Str::limit($item->description, 30) }}</small>
                                                @endif
                                            </div>
                                        </td>
                                        <td>{{ $item->category ?? 'غير محدد' }}</td>
                                        <td>{{ $item->supplier->name ?? 'غير محدد' }}</td>
                                        <td>
                                            <span class="badge bg-primary">{{ $item->total_quantity }} {{ $item->unit }}</span>
                                        </td>
                                        <td>
                                            <strong class="text-success">{{ number_format($item->total_revenue, 2) }} دينار</strong>
                                        </td>
                                        <td>
                                            {{ $item->total_quantity > 0 ? number_format($item->total_revenue / $item->total_quantity, 2) : 0 }} دينار
                                        </td>
                                        <td>
                                            <span class="badge {{ $item->stock_quantity <= $item->min_stock_level ? 'bg-danger' : 'bg-success' }}">
                                                {{ $item->stock_quantity }} {{ $item->unit }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="table-success">
                                    <td colspan="5"><strong>الإجمالي:</strong></td>
                                    <td><strong>{{ $topItems->sum('total_quantity') }}</strong></td>
                                    <td><strong>{{ number_format($topItems->sum('total_revenue'), 2) }} دينار</strong></td>
                                    <td colspan="2"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-star fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">لا توجد مبيعات</h5>
                        <p class="text-muted">لم يتم العثور على أي مبيعات في الفترة المحددة</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- تحليل المنتجات -->
@if($topItems->count() > 0)
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-medal me-2"></i>
                        أفضل 5 منتجات
                    </h5>
                </div>
                <div class="card-body">
                    @foreach($topItems->take(5) as $index => $item)
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="d-flex align-items-center">
                                <div class="rank-badge-{{ $index < 3 ? ['gold', 'silver', 'bronze'][$index] : 'default' }} me-3">
                                    {{ $index + 1 }}
                                </div>
                                <div>
                                    <strong>{{ $item->name }}</strong>
                                    <br><small class="text-muted">{{ $item->total_quantity }} {{ $item->unit }} مباع</small>
                                </div>
                            </div>
                            <div class="text-end">
                                <strong class="text-success">{{ number_format($item->total_revenue, 2) }} دينار</strong>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-pie me-2"></i>
                        توزيع المبيعات حسب الفئة
                    </h5>
                </div>
                <div class="card-body">
                    @php
                        $categoryGroups = $topItems->groupBy('category');
                        $totalRevenue = $topItems->sum('total_revenue');
                    @endphp
                    
                    @foreach($categoryGroups as $category => $categoryItems)
                        @php
                            $categoryRevenue = $categoryItems->sum('total_revenue');
                            $percentage = $totalRevenue > 0 ? ($categoryRevenue / $totalRevenue) * 100 : 0;
                        @endphp
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>{{ $category ?? 'غير محدد' }}</span>
                            <div>
                                <span class="badge bg-primary me-2">{{ number_format($categoryRevenue, 2) }} دينار</span>
                                <span class="text-muted">{{ number_format($percentage, 1) }}%</span>
                            </div>
                        </div>
                        <div class="progress mb-3" style="height: 8px;">
                            <div class="progress-bar" style="width: {{ $percentage }}%"></div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    
    <!-- تحليل إضافي -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-bar me-2"></i>
                        تحليل الأداء
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="text-center">
                                <h4 class="text-primary">{{ $topItems->first()->name ?? 'لا يوجد' }}</h4>
                                <p class="text-muted">أفضل منتج مبيعاً</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h4 class="text-success">{{ number_format($topItems->max('total_revenue'), 2) }} دينار</h4>
                                <p class="text-muted">أعلى إيراد لمنتج واحد</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h4 class="text-warning">{{ $topItems->max('total_quantity') }}</h4>
                                <p class="text-muted">أعلى كمية مباعة</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h4 class="text-info">{{ $topItems->where('stock_quantity', '<=', $topItems->pluck('min_stock_level'))->count() }}</h4>
                                <p class="text-muted">منتجات تحتاج تموين</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
@endsection

@push('styles')
<style>
    .stat-card {
        color: white;
        padding: 1.5rem;
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        transition: transform 0.3s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
    }
    
    .stat-card.primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
    
    .rank-badge-gold {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 16px;
        box-shadow: 0 2px 10px rgba(255, 215, 0, 0.3);
    }
    
    .rank-badge-silver {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        background: linear-gradient(135deg, #C0C0C0 0%, #A8A8A8 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 16px;
        box-shadow: 0 2px 10px rgba(192, 192, 192, 0.3);
    }
    
    .rank-badge-bronze {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        background: linear-gradient(135deg, #CD7F32 0%, #B87333 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 16px;
        box-shadow: 0 2px 10px rgba(205, 127, 50, 0.3);
    }
    
    .rank-badge-default {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 14px;
    }
</style>
@endpush
