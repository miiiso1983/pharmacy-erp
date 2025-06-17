@extends('layouts.app')

@section('title', 'العناصر - نظام إدارة المذاخر')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
    <li class="breadcrumb-item active">العناصر</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>
                <i class="fas fa-pills me-2"></i>
                العناصر والأدوية
            </h2>
            <div>
                @can('view_items')
                    <a href="{{ route('items.lowStock') }}" class="btn btn-warning me-2">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        مخزون منخفض
                    </a>
                @endcan
                @can('create_items')
                    <a href="{{ route('items.create') }}" class="btn btn-primary me-2">
                        <i class="fas fa-plus me-2"></i>
                        عنصر جديد
                    </a>
                @endcan
                @can('create_items')
                    <a href="{{ route('items.import.form') }}" class="btn btn-success">
                        <i class="fas fa-file-upload me-2"></i>
                        استيراد من Excel
                    </a>
                @endcan
            </div>
        </div>
    </div>
</div>

<!-- فلاتر البحث -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('items.index') }}">
                    <div class="row">
                        <div class="col-md-3">
                            <label class="form-label">اسم العنصر</label>
                            <input type="text" class="form-control" name="name" 
                                   value="{{ request('name') }}" placeholder="ابحث باسم العنصر">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">الكود</label>
                            <input type="text" class="form-control" name="code" 
                                   value="{{ request('code') }}" placeholder="ابحث بالكود">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">الفئة</label>
                            <input type="text" class="form-control" name="category" 
                                   value="{{ request('category') }}" placeholder="ابحث بالفئة">
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
                            <a href="{{ route('items.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>إلغاء
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- جدول العناصر -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2"></i>
                    قائمة العناصر ({{ $items->total() }})
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
                                    <th>الوحدة</th>
                                    <th>السعر</th>
                                    <th>المخزون</th>
                                    <th>الحد الأدنى</th>
                                    <th>المورد</th>
                                    <th>الحالة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($items as $item)
                                    <tr class="{{ $item->stock_quantity <= $item->min_stock_level ? 'table-warning' : '' }}">
                                        <td>
                                            <strong>{{ $item->code }}</strong>
                                            @if($item->barcode)
                                                <br><small class="text-muted">{{ $item->barcode }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <div>
                                                <strong>{{ $item->name }}</strong>
                                                @if($item->description)
                                                    <br><small class="text-muted">{{ Str::limit($item->description, 50) }}</small>
                                                @endif
                                            </div>
                                        </td>
                                        <td>{{ $item->category ?? 'غير محدد' }}</td>
                                        <td>{{ $item->unit }}</td>
                                        <td>{{ number_format($item->price, 2) }} دينار</td>
                                        <td>
                                            <span class="badge {{ $item->stock_quantity <= $item->min_stock_level ? 'bg-danger' : 'bg-success' }}">
                                                {{ $item->stock_quantity }}
                                            </span>
                                        </td>
                                        <td>{{ $item->min_stock_level }}</td>
                                        <td>{{ $item->supplier->name ?? 'غير محدد' }}</td>
                                        <td>
                                            @if($item->status === 'active')
                                                <span class="badge bg-success">نشط</span>
                                            @else
                                                <span class="badge bg-secondary">غير نشط</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('items.show', $item->id) }}" 
                                                   class="btn btn-outline-primary" title="عرض التفاصيل">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @can('edit_items')
                                                    <a href="{{ route('items.edit', $item->id) }}" 
                                                       class="btn btn-outline-warning" title="تعديل">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endcan
                                                @can('delete_items')
                                                    <form method="POST" action="{{ route('items.destroy', $item->id) }}" 
                                                          class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا العنصر؟')">
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
                        {{ $items->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-pills fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">لا توجد عناصر</h5>
                        <p class="text-muted">لم يتم العثور على أي عناصر تطابق معايير البحث</p>
                        @can('create_items')
                            <a href="{{ route('items.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>
                                إضافة عنصر جديد
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
    .table-warning {
        background-color: rgba(255, 193, 7, 0.1) !important;
    }
</style>
@endpush
