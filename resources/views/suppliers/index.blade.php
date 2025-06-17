@extends('layouts.app')

@section('title', 'الموردين - نظام إدارة المذاخر')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
    <li class="breadcrumb-item active">الموردين</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>
                <i class="fas fa-truck me-2"></i>
                الموردين
            </h2>
            <div class="btn-group">
                @can('create_suppliers')
                    <a href="{{ route('suppliers.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>
                        مورد جديد
                    </a>
                @endcan
                @can('create_suppliers')
                    <a href="{{ route('suppliers.import.form') }}" class="btn btn-success">
                        <i class="fas fa-file-upload me-2"></i>
                        استيراد من Excel
                    </a>
                @endcan
            </div>
        </div>
    </div>
</div>

<!-- إحصائيات سريعة -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">{{ $suppliers->total() }}</h4>
                    <p class="mb-0">إجمالي الموردين</p>
                </div>
                <div class="fs-1 opacity-75">
                    <i class="fas fa-truck"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stat-card success">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">{{ $suppliers->where('status', 'active')->count() }}</h4>
                    <p class="mb-0">موردين نشطين</p>
                </div>
                <div class="fs-1 opacity-75">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stat-card warning">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">{{ $suppliers->sum('items_count') }}</h4>
                    <p class="mb-0">إجمالي العناصر</p>
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
                    <h4 class="mb-1">{{ $suppliers->where('status', 'inactive')->count() }}</h4>
                    <p class="mb-0">موردين غير نشطين</p>
                </div>
                <div class="fs-1 opacity-75">
                    <i class="fas fa-pause-circle"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- فلاتر البحث -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('suppliers.index') }}">
                    <div class="row">
                        <div class="col-md-3">
                            <label class="form-label">اسم المورد</label>
                            <input type="text" class="form-control" name="name" 
                                   value="{{ request('name') }}" placeholder="ابحث باسم المورد">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">الشخص المسؤول</label>
                            <input type="text" class="form-control" name="contact_person" 
                                   value="{{ request('contact_person') }}" placeholder="ابحث بالشخص المسؤول">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">المدينة</label>
                            <input type="text" class="form-control" name="city" 
                                   value="{{ request('city') }}" placeholder="ابحث بالمدينة">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">الحالة</label>
                            <select class="form-select" name="status">
                                <option value="">جميع الحالات</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>غير نشط</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-search me-2"></i>بحث
                            </button>
                            <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>إلغاء
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- جدول الموردين -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2"></i>
                    قائمة الموردين ({{ $suppliers->total() }})
                </h5>
            </div>
            <div class="card-body">
                @if($suppliers->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>اسم المورد</th>
                                    <th>الشخص المسؤول</th>
                                    <th>الهاتف</th>
                                    <th>البريد الإلكتروني</th>
                                    <th>المدينة</th>
                                    <th>عدد العناصر</th>
                                    <th>الحالة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($suppliers as $supplier)
                                    <tr>
                                        <td>
                                            <div>
                                                <strong>{{ $supplier->name }}</strong>
                                                @if($supplier->tax_number)
                                                    <br><small class="text-muted">الرقم الضريبي: {{ $supplier->tax_number }}</small>
                                                @endif
                                            </div>
                                        </td>
                                        <td>{{ $supplier->contact_person ?? 'غير محدد' }}</td>
                                        <td>{{ $supplier->phone ?? 'غير محدد' }}</td>
                                        <td>{{ $supplier->email ?? 'غير محدد' }}</td>
                                        <td>{{ $supplier->city ?? 'غير محدد' }}</td>
                                        <td>
                                            <span class="badge bg-primary">{{ $supplier->items_count }} عنصر</span>
                                        </td>
                                        <td>
                                            @if($supplier->status === 'active')
                                                <span class="badge bg-success">نشط</span>
                                            @else
                                                <span class="badge bg-secondary">غير نشط</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('suppliers.show', $supplier->id) }}" 
                                                   class="btn btn-outline-primary" title="عرض التفاصيل">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @can('edit_suppliers')
                                                    <a href="{{ route('suppliers.edit', $supplier->id) }}" 
                                                       class="btn btn-outline-warning" title="تعديل">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endcan
                                                @can('delete_suppliers')
                                                    <form method="POST" action="{{ route('suppliers.destroy', $supplier->id) }}" 
                                                          class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا المورد؟')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-outline-danger" title="حذف">
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
                        {{ $suppliers->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-truck fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">لا توجد موردين</h5>
                        <p class="text-muted">لم يتم العثور على أي موردين تطابق معايير البحث</p>
                        @can('create_suppliers')
                            <a href="{{ route('suppliers.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>
                                إضافة مورد جديد
                            </a>
                        @endcan
                    </div>
                @endif
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
