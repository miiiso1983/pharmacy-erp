@extends('layouts.app')

@section('title', 'لوحة تحكم المدير - نظام إدارة المذاخر')

@section('breadcrumb')
    <li class="breadcrumb-item active">لوحة التحكم</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <h2 class="mb-4">
            <i class="fas fa-tachometer-alt me-2"></i>
            لوحة تحكم المدير
        </h2>
    </div>
</div>

<!-- إحصائيات سريعة -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="mb-1">{{ number_format($stats['total_orders']) }}</h3>
                    <p class="mb-0">إجمالي الطلبات</p>
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
                    <h3 class="mb-1">{{ number_format($stats['pending_orders']) }}</h3>
                    <p class="mb-0">طلبات معلقة</p>
                </div>
                <div class="fs-1 opacity-75">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stat-card success">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="mb-1">{{ number_format($stats['total_customers']) }}</h3>
                    <p class="mb-0">العملاء</p>
                </div>
                <div class="fs-1 opacity-75">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stat-card info">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="mb-1">{{ number_format($stats['total_items']) }}</h3>
                    <p class="mb-0">العناصر</p>
                </div>
                <div class="fs-1 opacity-75">
                    <i class="fas fa-pills"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- إحصائيات مالية -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stat-card success">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="mb-1">{{ number_format($stats['total_invoices_amount'], 2) }}</h3>
                    <p class="mb-0">إجمالي الفواتير (دينار)</p>
                </div>
                <div class="fs-1 opacity-75">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stat-card info">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="mb-1">{{ number_format($stats['paid_amount'], 2) }}</h3>
                    <p class="mb-0">المبلغ المدفوع (دينار)</p>
                </div>
                <div class="fs-1 opacity-75">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stat-card danger">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="mb-1">{{ number_format($stats['remaining_amount'], 2) }}</h3>
                    <p class="mb-0">المبلغ المتبقي (دينار)</p>
                </div>
                <div class="fs-1 opacity-75">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stat-card warning">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="mb-1">{{ number_format($stats['today_collections'], 2) }}</h3>
                    <p class="mb-0">تحصيلات اليوم (دينار)</p>
                </div>
                <div class="fs-1 opacity-75">
                    <i class="fas fa-calendar-day"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- الرسم البياني للإيرادات الشهرية -->
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-chart-line me-2"></i>
                    الإيرادات الشهرية
                </h5>
            </div>
            <div class="card-body">
                <canvas id="revenueChart" height="100"></canvas>
            </div>
        </div>
    </div>
    
    <!-- تنبيهات المخزون المنخفض -->
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    تنبيهات المخزون
                    <span class="badge bg-danger">{{ count($low_stock_items) }}</span>
                </h5>
            </div>
            <div class="card-body">
                @if(count($low_stock_items) > 0)
                    <div class="list-group list-group-flush">
                        @foreach($low_stock_items as $item)
                            <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <div>
                                    <h6 class="mb-1">{{ $item->name }}</h6>
                                    <small class="text-muted">كود: {{ $item->code }}</small>
                                </div>
                                <span class="badge bg-danger rounded-pill">{{ $item->stock_quantity }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center text-muted">
                        <i class="fas fa-check-circle fa-3x mb-3"></i>
                        <p>جميع العناصر متوفرة في المخزون</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- الطلبات الحديثة -->
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-shopping-cart me-2"></i>
                    الطلبات الحديثة
                </h5>
                <a href="#" class="btn btn-sm btn-outline-primary">عرض الكل</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>رقم الطلب</th>
                                <th>العميل</th>
                                <th>الحالة</th>
                                <th>المبلغ</th>
                                <th>التاريخ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recent_orders as $order)
                                <tr>
                                    <td>{{ $order->order_number }}</td>
                                    <td>{{ $order->customer->name }}</td>
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
                                    <td>{{ number_format($order->total_amount, 2) }} دينار</td>
                                    <td>{{ $order->created_at->format('Y-m-d') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- التحصيلات الحديثة -->
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-money-bill-wave me-2"></i>
                    التحصيلات الحديثة
                </h5>
            </div>
            <div class="card-body">
                @if(count($recent_collections) > 0)
                    <div class="list-group list-group-flush">
                        @foreach($recent_collections as $collection)
                            <div class="list-group-item px-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">{{ $collection->customer->name }}</h6>
                                        <small class="text-muted">{{ $collection->collection_date->format('Y-m-d') }}</small>
                                    </div>
                                    <span class="badge bg-success">{{ number_format($collection->amount, 2) }} دينار</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center text-muted">
                        <i class="fas fa-money-bill-wave fa-3x mb-3"></i>
                        <p>لا توجد تحصيلات حديثة</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // رسم بياني للإيرادات الشهرية
    const ctx = document.getElementById('revenueChart').getContext('2d');
    const monthlyRevenue = @json($monthly_revenue);
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: monthlyRevenue.map(item => item.month),
            datasets: [{
                label: 'الإيرادات (دينار)',
                data: monthlyRevenue.map(item => item.revenue),
                borderColor: 'rgb(102, 126, 234)',
                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString() + ' دينار';
                        }
                    }
                }
            }
        }
    });
</script>
@endpush
