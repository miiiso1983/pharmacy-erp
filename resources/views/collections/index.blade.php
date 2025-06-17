@extends('layouts.app')

@section('title', 'التحصيلات - نظام إدارة المذاخر')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
    <li class="breadcrumb-item active">التحصيلات</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>
                <i class="fas fa-money-bill-wave me-2"></i>
                التحصيلات
            </h2>
            @can('create_collections')
                <a href="{{ route('collections.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>
                    تحصيل جديد
                </a>
            @endcan
        </div>
    </div>
</div>

<!-- إحصائيات سريعة -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">{{ $collections->total() }}</h4>
                    <p class="mb-0">إجمالي التحصيلات</p>
                </div>
                <div class="fs-1 opacity-75">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stat-card success">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">{{ number_format($collections->sum('amount'), 2) }}</h4>
                    <p class="mb-0">إجمالي المبلغ (دينار)</p>
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
                    <h4 class="mb-1">{{ $collections->where('payment_method', 'cash')->count() }}</h4>
                    <p class="mb-0">تحصيلات نقدية</p>
                </div>
                <div class="fs-1 opacity-75">
                    <i class="fas fa-coins"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stat-card info">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">{{ $collections->where('payment_method', 'bank_transfer')->count() }}</h4>
                    <p class="mb-0">تحويلات بنكية</p>
                </div>
                <div class="fs-1 opacity-75">
                    <i class="fas fa-university"></i>
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
                <form method="GET" action="{{ route('collections.index') }}">
                    <div class="row">
                        <div class="col-md-3">
                            <label class="form-label">رقم الفاتورة</label>
                            <input type="text" class="form-control" name="invoice_number" 
                                   value="{{ request('invoice_number') }}" placeholder="ابحث برقم الفاتورة">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">طريقة الدفع</label>
                            <select class="form-select" name="payment_method">
                                <option value="">جميع الطرق</option>
                                <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>نقداً</option>
                                <option value="bank_transfer" {{ request('payment_method') == 'bank_transfer' ? 'selected' : '' }}>تحويل بنكي</option>
                                <option value="check" {{ request('payment_method') == 'check' ? 'selected' : '' }}>شيك</option>
                                <option value="credit_card" {{ request('payment_method') == 'credit_card' ? 'selected' : '' }}>بطاقة ائتمان</option>
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
                            <a href="{{ route('collections.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>إلغاء
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- جدول التحصيلات -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2"></i>
                    قائمة التحصيلات ({{ $collections->total() }})
                </h5>
            </div>
            <div class="card-body">
                @if($collections->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>رقم الفاتورة</th>
                                    @if(Auth::user()->user_type !== 'customer')
                                        <th>العميل</th>
                                    @endif
                                    <th>المبلغ</th>
                                    <th>طريقة الدفع</th>
                                    <th>تاريخ التحصيل</th>
                                    <th>تم بواسطة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($collections as $collection)
                                    <tr>
                                        <td>
                                            <a href="{{ route('invoices.show', $collection->invoice->id) }}" class="text-decoration-none">
                                                {{ $collection->invoice->invoice_number }}
                                            </a>
                                        </td>
                                        @if(Auth::user()->user_type !== 'customer')
                                            <td>
                                                <div>
                                                    <strong>{{ $collection->customer->name }}</strong>
                                                    @if($collection->customer->company_name)
                                                        <br><small class="text-muted">{{ $collection->customer->company_name }}</small>
                                                    @endif
                                                </div>
                                            </td>
                                        @endif
                                        <td>
                                            <strong class="text-success">{{ number_format($collection->amount, 2) }} دينار</strong>
                                        </td>
                                        <td>
                                            @switch($collection->payment_method)
                                                @case('cash')
                                                    <span class="badge bg-success">نقداً</span>
                                                    @break
                                                @case('bank_transfer')
                                                    <span class="badge bg-primary">تحويل بنكي</span>
                                                    @break
                                                @case('check')
                                                    <span class="badge bg-warning">شيك</span>
                                                    @break
                                                @case('credit_card')
                                                    <span class="badge bg-info">بطاقة ائتمان</span>
                                                    @break
                                                @default
                                                    <span class="badge bg-secondary">{{ $collection->payment_method }}</span>
                                            @endswitch
                                        </td>
                                        <td>{{ $collection->collection_date->format('Y-m-d H:i') }}</td>
                                        <td>{{ $collection->collectedBy->name ?? 'غير محدد' }}</td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('collections.show', $collection->id) }}" 
                                                   class="btn btn-outline-primary" title="عرض التفاصيل">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @can('edit_collections')
                                                    <a href="{{ route('collections.edit', $collection->id) }}" 
                                                       class="btn btn-outline-warning" title="تعديل">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endcan
                                                @can('delete_collections')
                                                    <form method="POST" action="{{ route('collections.destroy', $collection->id) }}" 
                                                          class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا التحصيل؟')">
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
                        {{ $collections->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-money-bill-wave fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">لا توجد تحصيلات</h5>
                        <p class="text-muted">لم يتم العثور على أي تحصيلات تطابق معايير البحث</p>
                        @can('create_collections')
                            <a href="{{ route('collections.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>
                                إضافة تحصيل جديد
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
