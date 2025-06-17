@extends('layouts.app')

@section('title', 'إدارة المخازن')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
    <li class="breadcrumb-item active">المخازن</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- إحصائيات المخازن -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-0">{{ $stats['total_warehouses'] }}</h3>
                        <p class="mb-0">إجمالي المخازن</p>
                    </div>
                    <i class="fas fa-warehouse fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="stat-card success">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-0">{{ $stats['active_warehouses'] }}</h3>
                        <p class="mb-0">المخازن النشطة</p>
                    </div>
                    <i class="fas fa-check-circle fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="stat-card info">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-0">{{ number_format($stats['total_items']) }}</h3>
                        <p class="mb-0">إجمالي العناصر</p>
                    </div>
                    <i class="fas fa-boxes fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="stat-card warning">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-0">{{ number_format($stats['total_value'], 0) }} د.ع</h3>
                        <p class="mb-0">إجمالي القيمة</p>
                    </div>
                    <i class="fas fa-dollar-sign fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- أدوات التحكم -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-warehouse me-2"></i>
                قائمة المخازن
            </h5>
            <div>
                @can('create_warehouses')
                <a href="{{ route('warehouses.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>
                    إضافة مخزن جديد
                </a>
                @endcan
            </div>
        </div>
        
        <div class="card-body">
            @if($warehouses->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>الاسم</th>
                            <th>الكود</th>
                            <th>الموقع</th>
                            <th>النوع</th>
                            <th>المدير</th>
                            <th>العناصر</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($warehouses as $warehouse)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="warehouse-icon me-3">
                                        <i class="fas fa-warehouse text-primary"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $warehouse->name }}</h6>
                                        @if($warehouse->description)
                                        <small class="text-muted">{{ Str::limit($warehouse->description, 50) }}</small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $warehouse->code }}</span>
                            </td>
                            <td>
                                <div>
                                    <strong>{{ $warehouse->city }}</strong><br>
                                    <small class="text-muted">{{ $warehouse->area }}</small>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $warehouse->type_label }}</span>
                            </td>
                            <td>
                                @if($warehouse->manager)
                                    <div>
                                        <i class="fas fa-user me-1"></i>
                                        {{ $warehouse->manager }}
                                    </div>
                                @else
                                    <span class="text-muted">غير محدد</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-primary">{{ $warehouse->warehouse_items_count ?? 0 }}</span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $warehouse->status_badge }}">
                                    {{ $warehouse->status_label }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    @can('view_warehouses')
                                    <a href="{{ route('warehouses.show', $warehouse->id) }}" 
                                       class="btn btn-sm btn-outline-primary" 
                                       title="عرض التفاصيل">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @endcan
                                    
                                    @can('edit_warehouses')
                                    <a href="{{ route('warehouses.edit', $warehouse->id) }}" 
                                       class="btn btn-sm btn-outline-warning" 
                                       title="تعديل">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @endcan
                                    
                                    @can('view_warehouses')
                                    <a href="{{ route('warehouses.items', $warehouse->id) }}" 
                                       class="btn btn-sm btn-outline-info" 
                                       title="عرض العناصر">
                                        <i class="fas fa-boxes"></i>
                                    </a>
                                    @endcan
                                    
                                    @can('view_warehouses')
                                    <a href="{{ route('warehouses.reports', $warehouse->id) }}" 
                                       class="btn btn-sm btn-outline-success" 
                                       title="التقارير">
                                        <i class="fas fa-chart-bar"></i>
                                    </a>
                                    @endcan
                                    
                                    @can('delete_warehouses')
                                    <form method="POST" action="{{ route('warehouses.destroy', $warehouse->id) }}" 
                                          class="d-inline" 
                                          onsubmit="return confirm('هل أنت متأكد من حذف هذا المخزن؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="btn btn-sm btn-outline-danger" 
                                                title="حذف">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $warehouses->links() }}
            </div>
            @else
            <div class="text-center py-5">
                <i class="fas fa-warehouse fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">لا توجد مخازن مسجلة</h5>
                <p class="text-muted">ابدأ بإضافة مخزن جديد لإدارة المخزون</p>
                @can('create_warehouses')
                <a href="{{ route('warehouses.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>
                    إضافة مخزن جديد
                </a>
                @endcan
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .warehouse-icon {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: rgba(102, 126, 234, 0.1);
        border-radius: 8px;
    }
    
    .stat-card {
        transition: transform 0.3s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-2px);
    }
</style>
@endpush
