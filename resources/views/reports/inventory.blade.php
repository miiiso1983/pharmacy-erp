@extends('layouts.app')

@section('title', 'تقرير المخزون - نظام إدارة المذاخر')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
    <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">التقارير</a></li>
    <li class="breadcrumb-item active">تقرير المخزون</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>
                <i class="fas fa-boxes me-2"></i>
                تقرير المخزون
            </h2>
            <div>
                <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary me-2">
                    <i class="fas fa-arrow-left me-2"></i>
                    العودة للتقارير
                </a>
                <form method="POST" action="{{ route('reports.exportInventory') }}" class="d-inline">
                    @csrf
                    <input type="hidden" name="category" value="{{ request('category') }}">
                    <input type="hidden" name="low_stock" value="{{ request('low_stock') }}">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-download me-2"></i>
                        تصدير Excel
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- فلاتر المخزون -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('reports.inventory') }}">
                    <div class="row align-items-end">
                        <div class="col-md-4">
                            <label class="form-label">الفئة</label>
                            <select class="form-select" name="category">
                                <option value="">جميع الفئات</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                                        {{ $category }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check mt-4">
                                <input class="form-check-input" type="checkbox" name="low_stock" value="1" 
                                       {{ request('low_stock') ? 'checked' : '' }} id="lowStockFilter">
                                <label class="form-check-label" for="lowStockFilter">
                                    عرض المخزون المنخفض فقط
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter me-2"></i>
                                تطبيق الفلاتر
                            </button>
                            <a href="{{ route('reports.inventory') }}" class="btn btn-outline-secondary ms-2">
                                <i class="fas fa-times me-2"></i>
                                إلغاء
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- إحصائيات المخزون -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stat-card primary">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">{{ $totalItems }}</h4>
                    <p class="mb-0">إجمالي العناصر</p>
                </div>
                <div class="fs-1 opacity-75">
                    <i class="fas fa-pills"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stat-card danger">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">{{ $lowStockItems }}</h4>
                    <p class="mb-0">مخزون منخفض</p>
                </div>
                <div class="fs-1 opacity-75">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stat-card success">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">{{ number_format($totalValue, 2) }}</h4>
                    <p class="mb-0">قيمة المخزون (دينار)</p>
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
                    <h4 class="mb-1">{{ $categories->count() }}</h4>
                    <p class="mb-0">عدد الفئات</p>
                </div>
                <div class="fs-1 opacity-75">
                    <i class="fas fa-tags"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- جدول المخزون -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2"></i>
                    تفاصيل المخزون
                    @if(request('category'))
                        - فئة: {{ request('category') }}
                    @endif
                    @if(request('low_stock'))
                        - مخزون منخفض فقط
                    @endif
                </h5>
            </div>
            <div class="card-body">
                @if($items->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>الكود</th>
                                    <th>اسم العنصر</th>
                                    <th>الفئة</th>
                                    <th>المورد</th>
                                    <th>المخزون الحالي</th>
                                    <th>الحد الأدنى</th>
                                    <th>سعر التكلفة</th>
                                    <th>سعر البيع</th>
                                    <th>قيمة المخزون</th>
                                    <th>الحالة</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($items as $item)
                                    <tr class="{{ $item->stock_quantity <= $item->min_stock_level ? 'table-warning' : '' }}">
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
                                            <span class="badge {{ $item->stock_quantity <= $item->min_stock_level ? 'bg-danger' : 'bg-success' }}">
                                                {{ $item->stock_quantity }} {{ $item->unit }}
                                            </span>
                                        </td>
                                        <td>{{ $item->min_stock_level }} {{ $item->unit }}</td>
                                        <td>{{ $item->cost ? number_format($item->cost, 2) . ' دينار' : 'غير محدد' }}</td>
                                        <td>{{ number_format($item->price, 2) }} دينار</td>
                                        <td>
                                            <strong>{{ number_format($item->stock_quantity * ($item->cost ?? 0), 2) }} دينار</strong>
                                        </td>
                                        <td>
                                            @if($item->stock_quantity <= 0)
                                                <span class="badge bg-danger">نفد المخزون</span>
                                            @elseif($item->stock_quantity <= $item->min_stock_level)
                                                <span class="badge bg-warning">مخزون منخفض</span>
                                            @else
                                                <span class="badge bg-success">متوفر</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="table-primary">
                                    <td colspan="8"><strong>الإجمالي:</strong></td>
                                    <td><strong>{{ number_format($items->sum(function($item) { return $item->stock_quantity * ($item->cost ?? 0); }), 2) }} دينار</strong></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-boxes fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">لا توجد عناصر</h5>
                        <p class="text-muted">لم يتم العثور على أي عناصر تطابق معايير البحث</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- تحليل المخزون -->
@if($items->count() > 0)
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-pie me-2"></i>
                        توزيع المخزون حسب الفئة
                    </h5>
                </div>
                <div class="card-body">
                    @php
                        $categoryGroups = $items->groupBy('category');
                    @endphp
                    
                    @foreach($categoryGroups as $category => $categoryItems)
                        @php
                            $categoryValue = $categoryItems->sum(function($item) { 
                                return $item->stock_quantity * ($item->cost ?? 0); 
                            });
                            $percentage = $totalValue > 0 ? ($categoryValue / $totalValue) * 100 : 0;
                        @endphp
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>{{ $category ?? 'غير محدد' }}</span>
                            <div>
                                <span class="badge bg-primary me-2">{{ $categoryItems->count() }} عنصر</span>
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
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        تنبيهات المخزون
                    </h5>
                </div>
                <div class="card-body">
                    @php
                        $outOfStock = $items->where('stock_quantity', '<=', 0);
                        $lowStock = $items->filter(function($item) {
                            return $item->stock_quantity > 0 && $item->stock_quantity <= $item->min_stock_level;
                        });
                        $goodStock = $items->filter(function($item) {
                            return $item->stock_quantity > $item->min_stock_level;
                        });
                    @endphp
                    
                    <div class="alert alert-danger d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-times-circle me-2"></i>
                            نفد المخزون
                        </div>
                        <span class="badge bg-danger">{{ $outOfStock->count() }} عنصر</span>
                    </div>
                    
                    <div class="alert alert-warning d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            مخزون منخفض
                        </div>
                        <span class="badge bg-warning">{{ $lowStock->count() }} عنصر</span>
                    </div>
                    
                    <div class="alert alert-success d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-check-circle me-2"></i>
                            مخزون جيد
                        </div>
                        <span class="badge bg-success">{{ $goodStock->count() }} عنصر</span>
                    </div>
                    
                    @if($lowStock->count() > 0)
                        <hr>
                        <h6>العناصر التي تحتاج إعادة تموين:</h6>
                        <ul class="list-unstyled">
                            @foreach($lowStock->take(5) as $item)
                                <li class="mb-1">
                                    <small>
                                        <strong>{{ $item->name }}</strong> 
                                        - متبقي: {{ $item->stock_quantity }} {{ $item->unit }}
                                    </small>
                                </li>
                            @endforeach
                            @if($lowStock->count() > 5)
                                <li><small class="text-muted">و {{ $lowStock->count() - 5 }} عنصر آخر...</small></li>
                            @endif
                        </ul>
                    @endif
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
    
    .stat-card.danger {
        background: linear-gradient(135deg, #ff416c 0%, #ff4b2b 100%);
    }
    
    .stat-card.success {
        background: linear-gradient(135deg, #56ab2f 0%, #a8e6cf 100%);
    }
    
    .stat-card.warning {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }
    
    .table-warning {
        background-color: rgba(255, 193, 7, 0.1) !important;
    }
</style>
@endpush
