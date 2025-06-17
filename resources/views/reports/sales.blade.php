@extends('layouts.app')

@section('title', 'تقرير المبيعات - نظام إدارة المذاخر')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
    <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">التقارير</a></li>
    <li class="breadcrumb-item active">تقرير المبيعات</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>
                <i class="fas fa-chart-line me-2"></i>
                تقرير المبيعات
            </h2>
            <div>
                <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary me-2">
                    <i class="fas fa-arrow-left me-2"></i>
                    العودة للتقارير
                </a>
                <form method="POST" action="{{ route('reports.exportSales') }}" class="d-inline">
                    @csrf
                    <input type="hidden" name="from_date" value="{{ $fromDate->format('Y-m-d') }}">
                    <input type="hidden" name="to_date" value="{{ $toDate->format('Y-m-d') }}">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-download me-2"></i>
                        تصدير Excel
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- فلاتر التاريخ -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('reports.sales') }}">
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

<!-- إحصائيات المبيعات -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stat-card success">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">{{ number_format($totalSales, 2) }}</h4>
                    <p class="mb-0">إجمالي المبيعات (دينار)</p>
                </div>
                <div class="fs-1 opacity-75">
                    <i class="fas fa-dollar-sign"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stat-card primary">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">{{ $totalOrders }}</h4>
                    <p class="mb-0">عدد الطلبات</p>
                </div>
                <div class="fs-1 opacity-75">
                    <i class="fas fa-shopping-cart"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stat-card warning">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">{{ number_format($avgOrderValue, 2) }}</h4>
                    <p class="mb-0">متوسط قيمة الطلب (دينار)</p>
                </div>
                <div class="fs-1 opacity-75">
                    <i class="fas fa-chart-bar"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stat-card info">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">{{ $orders->sum(function($order) { return $order->orderItems->sum('quantity'); }) }}</h4>
                    <p class="mb-0">إجمالي الكمية المباعة</p>
                </div>
                <div class="fs-1 opacity-75">
                    <i class="fas fa-boxes"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- جدول تفاصيل المبيعات -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2"></i>
                    تفاصيل المبيعات من {{ $fromDate->format('Y-m-d') }} إلى {{ $toDate->format('Y-m-d') }}
                </h5>
            </div>
            <div class="card-body">
                @if($orders->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>رقم الطلب</th>
                                    <th>العميل</th>
                                    <th>التاريخ</th>
                                    <th>عدد العناصر</th>
                                    <th>المجموع الفرعي</th>
                                    <th>الضريبة</th>
                                    <th>المجموع الإجمالي</th>
                                    <th>الحالة</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $order)
                                    <tr>
                                        <td>
                                            <a href="{{ route('orders.show', $order->id) }}" class="text-decoration-none">
                                                {{ $order->order_number }}
                                            </a>
                                        </td>
                                        <td>
                                            <div>
                                                <strong>{{ $order->customer->name }}</strong>
                                                @if($order->customer->company_name)
                                                    <br><small class="text-muted">{{ $order->customer->company_name }}</small>
                                                @endif
                                            </div>
                                        </td>
                                        <td>{{ $order->created_at->format('Y-m-d H:i') }}</td>
                                        <td>
                                            <span class="badge bg-primary">{{ $order->orderItems->count() }}</span>
                                        </td>
                                        <td>{{ number_format($order->subtotal, 2) }} دينار</td>
                                        <td>{{ number_format($order->tax_amount, 2) }} دينار</td>
                                        <td><strong>{{ number_format($order->total_amount, 2) }} دينار</strong></td>
                                        <td>
                                            @switch($order->status)
                                                @case('pending')
                                                    <span class="badge bg-warning">في الانتظار</span>
                                                    @break
                                                @case('confirmed')
                                                    <span class="badge bg-info">مؤكد</span>
                                                    @break
                                                @case('processing')
                                                    <span class="badge bg-primary">قيد المعالجة</span>
                                                    @break
                                                @case('shipped')
                                                    <span class="badge bg-secondary">تم الشحن</span>
                                                    @break
                                                @case('delivered')
                                                    <span class="badge bg-success">تم التسليم</span>
                                                    @break
                                                @case('cancelled')
                                                    <span class="badge bg-danger">ملغي</span>
                                                    @break
                                            @endswitch
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="table-success">
                                    <td colspan="4"><strong>الإجمالي:</strong></td>
                                    <td><strong>{{ number_format($orders->sum('subtotal'), 2) }} دينار</strong></td>
                                    <td><strong>{{ number_format($orders->sum('tax_amount'), 2) }} دينار</strong></td>
                                    <td><strong>{{ number_format($orders->sum('total_amount'), 2) }} دينار</strong></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">لا توجد مبيعات</h5>
                        <p class="text-muted">لم يتم العثور على أي مبيعات في الفترة المحددة</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- تحليل المبيعات حسب الحالة -->
@if($orders->count() > 0)
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-pie-chart me-2"></i>
                        المبيعات حسب الحالة
                    </h5>
                </div>
                <div class="card-body">
                    @php
                        $statusCounts = $orders->groupBy('status')->map->count();
                        $statusLabels = [
                            'pending' => 'في الانتظار',
                            'confirmed' => 'مؤكد',
                            'processing' => 'قيد المعالجة',
                            'shipped' => 'تم الشحن',
                            'delivered' => 'تم التسليم',
                            'cancelled' => 'ملغي'
                        ];
                    @endphp
                    
                    @foreach($statusCounts as $status => $count)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>{{ $statusLabels[$status] ?? $status }}</span>
                            <div>
                                <span class="badge bg-primary me-2">{{ $count }}</span>
                                <span class="text-muted">{{ number_format(($count / $totalOrders) * 100, 1) }}%</span>
                            </div>
                        </div>
                        <div class="progress mb-3" style="height: 8px;">
                            <div class="progress-bar" style="width: {{ ($count / $totalOrders) * 100 }}%"></div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-users me-2"></i>
                        أفضل العملاء
                    </h5>
                </div>
                <div class="card-body">
                    @php
                        $topCustomers = $orders->groupBy('customer_id')
                            ->map(function($customerOrders) {
                                return [
                                    'customer' => $customerOrders->first()->customer,
                                    'total' => $customerOrders->sum('total_amount'),
                                    'count' => $customerOrders->count()
                                ];
                            })
                            ->sortByDesc('total')
                            ->take(5);
                    @endphp
                    
                    @foreach($topCustomers as $customerData)
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <strong>{{ $customerData['customer']->name }}</strong>
                                <br><small class="text-muted">{{ $customerData['count'] }} طلب</small>
                            </div>
                            <div class="text-end">
                                <strong class="text-success">{{ number_format($customerData['total'], 2) }} دينار</strong>
                            </div>
                        </div>
                    @endforeach
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
    
    .stat-card.success {
        background: linear-gradient(135deg, #56ab2f 0%, #a8e6cf 100%);
    }
    
    .stat-card.primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    
    .stat-card.warning {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }
    
    .stat-card.info {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }
</style>
@endpush
