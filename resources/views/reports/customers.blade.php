@extends('layouts.app')

@section('title', 'تقرير العملاء - نظام إدارة المذاخر')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
    <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">التقارير</a></li>
    <li class="breadcrumb-item active">تقرير العملاء</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>
                <i class="fas fa-users me-2"></i>
                تقرير العملاء
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
                <form method="GET" action="{{ route('reports.customers') }}">
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

<!-- إحصائيات العملاء -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stat-card primary">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">{{ $customers->count() }}</h4>
                    <p class="mb-0">إجمالي العملاء</p>
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
                    <h4 class="mb-1">{{ $customers->where('orders_count', '>', 0)->count() }}</h4>
                    <p class="mb-0">عملاء نشطين</p>
                </div>
                <div class="fs-1 opacity-75">
                    <i class="fas fa-user-check"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stat-card warning">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">{{ number_format($customers->sum('total_invoiced'), 2) }}</h4>
                    <p class="mb-0">إجمالي المبيعات (دينار)</p>
                </div>
                <div class="fs-1 opacity-75">
                    <i class="fas fa-dollar-sign"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stat-card info">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">{{ $customers->count() > 0 ? number_format($customers->sum('total_invoiced') / $customers->where('orders_count', '>', 0)->count(), 2) : 0 }}</h4>
                    <p class="mb-0">متوسط قيمة العميل (دينار)</p>
                </div>
                <div class="fs-1 opacity-75">
                    <i class="fas fa-chart-line"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- جدول العملاء -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2"></i>
                    تفاصيل العملاء من {{ $fromDate->format('Y-m-d') }} إلى {{ $toDate->format('Y-m-d') }}
                </h5>
            </div>
            <div class="card-body">
                @if($customers->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>اسم العميل</th>
                                    <th>البريد الإلكتروني</th>
                                    <th>الهاتف</th>
                                    <th>عدد الطلبات</th>
                                    <th>إجمالي الفواتير</th>
                                    <th>إجمالي المدفوع</th>
                                    <th>المتبقي</th>
                                    <th>متوسط الطلب</th>
                                    <th>آخر طلب</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($customers as $customer)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-circle me-3">
                                                    {{ strtoupper(substr($customer->name, 0, 2)) }}
                                                </div>
                                                <div>
                                                    <strong>{{ $customer->name }}</strong>
                                                    @if($customer->company_name)
                                                        <br><small class="text-muted">{{ $customer->company_name }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $customer->email }}</td>
                                        <td>{{ $customer->phone ?? 'غير محدد' }}</td>
                                        <td>
                                            <span class="badge bg-primary">{{ $customer->orders_count }}</span>
                                        </td>
                                        <td>{{ number_format($customer->total_invoiced ?? 0, 2) }} دينار</td>
                                        <td>{{ number_format($customer->total_paid ?? 0, 2) }} دينار</td>
                                        <td>
                                            @php
                                                $remaining = ($customer->total_invoiced ?? 0) - ($customer->total_paid ?? 0);
                                            @endphp
                                            <span class="badge {{ $remaining > 0 ? 'bg-danger' : 'bg-success' }}">
                                                {{ number_format($remaining, 2) }} دينار
                                            </span>
                                        </td>
                                        <td>
                                            {{ $customer->orders_count > 0 ? number_format(($customer->total_invoiced ?? 0) / $customer->orders_count, 2) : 0 }} دينار
                                        </td>
                                        <td>
                                            @if($customer->orders->count() > 0)
                                                {{ $customer->orders->first()->created_at->format('Y-m-d') }}
                                            @else
                                                لا توجد طلبات
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="table-primary">
                                    <td colspan="4"><strong>الإجمالي:</strong></td>
                                    <td><strong>{{ number_format($customers->sum('total_invoiced'), 2) }} دينار</strong></td>
                                    <td><strong>{{ number_format($customers->sum('total_paid'), 2) }} دينار</strong></td>
                                    <td><strong>{{ number_format($customers->sum('total_invoiced') - $customers->sum('total_paid'), 2) }} دينار</strong></td>
                                    <td colspan="2"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">لا توجد بيانات عملاء</h5>
                        <p class="text-muted">لم يتم العثور على أي عملاء في الفترة المحددة</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- تحليل العملاء -->
@if($customers->count() > 0)
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-crown me-2"></i>
                        أفضل 5 عملاء
                    </h5>
                </div>
                <div class="card-body">
                    @foreach($customers->take(5) as $index => $customer)
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="d-flex align-items-center">
                                <div class="rank-badge me-3">{{ $index + 1 }}</div>
                                <div>
                                    <strong>{{ $customer->name }}</strong>
                                    <br><small class="text-muted">{{ $customer->orders_count }} طلب</small>
                                </div>
                            </div>
                            <div class="text-end">
                                <strong class="text-success">{{ number_format($customer->total_invoiced ?? 0, 2) }} دينار</strong>
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
                        توزيع العملاء
                    </h5>
                </div>
                <div class="card-body">
                    @php
                        $activeCustomers = $customers->where('orders_count', '>', 0)->count();
                        $inactiveCustomers = $customers->where('orders_count', 0)->count();
                        $highValueCustomers = $customers->where('total_invoiced', '>', 1000)->count();
                        $regularCustomers = $customers->where('total_invoiced', '>', 0)->where('total_invoiced', '<=', 1000)->count();
                    @endphp
                    
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>عملاء نشطين</span>
                        <div>
                            <span class="badge bg-success me-2">{{ $activeCustomers }}</span>
                            <span class="text-muted">{{ $customers->count() > 0 ? number_format(($activeCustomers / $customers->count()) * 100, 1) : 0 }}%</span>
                        </div>
                    </div>
                    <div class="progress mb-3" style="height: 8px;">
                        <div class="progress-bar bg-success" style="width: {{ $customers->count() > 0 ? ($activeCustomers / $customers->count()) * 100 : 0 }}%"></div>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>عملاء غير نشطين</span>
                        <div>
                            <span class="badge bg-secondary me-2">{{ $inactiveCustomers }}</span>
                            <span class="text-muted">{{ $customers->count() > 0 ? number_format(($inactiveCustomers / $customers->count()) * 100, 1) : 0 }}%</span>
                        </div>
                    </div>
                    <div class="progress mb-3" style="height: 8px;">
                        <div class="progress-bar bg-secondary" style="width: {{ $customers->count() > 0 ? ($inactiveCustomers / $customers->count()) * 100 : 0 }}%"></div>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>عملاء عالي القيمة (>1000 دينار)</span>
                        <div>
                            <span class="badge bg-warning me-2">{{ $highValueCustomers }}</span>
                            <span class="text-muted">{{ $customers->count() > 0 ? number_format(($highValueCustomers / $customers->count()) * 100, 1) : 0 }}%</span>
                        </div>
                    </div>
                    <div class="progress mb-3" style="height: 8px;">
                        <div class="progress-bar bg-warning" style="width: {{ $customers->count() > 0 ? ($highValueCustomers / $customers->count()) * 100 : 0 }}%"></div>
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
    
    .rank-badge {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 14px;
    }
</style>
@endpush
