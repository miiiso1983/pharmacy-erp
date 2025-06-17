@extends('layouts.app')

@section('title', 'المستخدمون - نظام إدارة المذاخر')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
    <li class="breadcrumb-item active">المستخدمون</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>
                <i class="fas fa-users me-2"></i>
                المستخدمون
            </h2>
            @can('create_users')
                <a href="{{ route('users.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>
                    مستخدم جديد
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
                    <h4 class="mb-1">{{ $users->total() }}</h4>
                    <p class="mb-0">إجمالي المستخدمين</p>
                </div>
                <div class="fs-1 opacity-75">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stat-card success">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">{{ $users->where('user_type', 'admin')->count() }}</h4>
                    <p class="mb-0">مديرين</p>
                </div>
                <div class="fs-1 opacity-75">
                    <i class="fas fa-user-shield"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stat-card warning">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">{{ $users->where('user_type', 'employee')->count() }}</h4>
                    <p class="mb-0">موظفين</p>
                </div>
                <div class="fs-1 opacity-75">
                    <i class="fas fa-user-tie"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stat-card info">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">{{ $users->where('user_type', 'customer')->count() }}</h4>
                    <p class="mb-0">عملاء</p>
                </div>
                <div class="fs-1 opacity-75">
                    <i class="fas fa-user"></i>
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
                <form method="GET" action="{{ route('users.index') }}">
                    <div class="row">
                        <div class="col-md-3">
                            <label class="form-label">الاسم</label>
                            <input type="text" class="form-control" name="name" 
                                   value="{{ request('name') }}" placeholder="ابحث بالاسم">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">البريد الإلكتروني</label>
                            <input type="email" class="form-control" name="email" 
                                   value="{{ request('email') }}" placeholder="ابحث بالبريد الإلكتروني">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">نوع المستخدم</label>
                            <select class="form-select" name="user_type">
                                <option value="">جميع الأنواع</option>
                                <option value="admin" {{ request('user_type') == 'admin' ? 'selected' : '' }}>مدير</option>
                                <option value="employee" {{ request('user_type') == 'employee' ? 'selected' : '' }}>موظف</option>
                                <option value="customer" {{ request('user_type') == 'customer' ? 'selected' : '' }}>عميل</option>
                            </select>
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
                            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>إلغاء
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- جدول المستخدمين -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2"></i>
                    قائمة المستخدمين ({{ $users->total() }})
                </h5>
            </div>
            <div class="card-body">
                @if($users->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>الاسم</th>
                                    <th>البريد الإلكتروني</th>
                                    <th>نوع المستخدم</th>
                                    <th>الهاتف</th>
                                    <th>الأدوار</th>
                                    <th>الحالة</th>
                                    <th>تاريخ التسجيل</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-circle me-3">
                                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                                </div>
                                                <div>
                                                    <strong>{{ $user->name }}</strong>
                                                    @if($user->company_name)
                                                        <br><small class="text-muted">{{ $user->company_name }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            @switch($user->user_type)
                                                @case('admin')
                                                    <span class="badge bg-danger">مدير</span>
                                                    @break
                                                @case('employee')
                                                    <span class="badge bg-warning">موظف</span>
                                                    @break
                                                @case('customer')
                                                    <span class="badge bg-info">عميل</span>
                                                    @break
                                            @endswitch
                                        </td>
                                        <td>{{ $user->phone ?? 'غير محدد' }}</td>
                                        <td>
                                            @if($user->roles->count() > 0)
                                                @foreach($user->roles as $role)
                                                    <span class="badge bg-secondary me-1">{{ $role->name }}</span>
                                                @endforeach
                                            @else
                                                <span class="text-muted">لا توجد أدوار</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($user->status === 'active')
                                                <span class="badge bg-success">نشط</span>
                                            @else
                                                <span class="badge bg-secondary">غير نشط</span>
                                            @endif
                                        </td>
                                        <td>{{ $user->created_at->format('Y-m-d') }}</td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('users.show', $user->id) }}" 
                                                   class="btn btn-outline-primary" title="عرض التفاصيل">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @can('edit_users')
                                                    <a href="{{ route('users.edit', $user->id) }}" 
                                                       class="btn btn-outline-warning" title="تعديل">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endcan
                                                @can('delete_users')
                                                    @if($user->id !== auth()->id())
                                                        <form method="POST" action="{{ route('users.destroy', $user->id) }}" 
                                                              class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا المستخدم؟')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-outline-danger" title="حذف">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    @endif
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
                        {{ $users->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">لا توجد مستخدمين</h5>
                        <p class="text-muted">لم يتم العثور على أي مستخدمين تطابق معايير البحث</p>
                        @can('create_users')
                            <a href="{{ route('users.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>
                                إضافة مستخدم جديد
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
    
    .avatar-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 14px;
    }
</style>
@endpush
