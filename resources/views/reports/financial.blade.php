@extends('layouts.app')

@section('title', 'التقرير المالي - نظام إدارة المذاخر')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
    <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">التقارير</a></li>
    <li class="breadcrumb-item active">التقرير المالي</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>
                <i class="fas fa-file-invoice-dollar me-2"></i>
                التقرير المالي
            </h2>
            <div>
                <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary me-2">
                    <i class="fas fa-arrow-left me-2"></i>
                    العودة للتقارير
                </a>
                <form method="POST" action="{{ route('reports.exportFinancial') }}" class="d-inline">
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
                <form method="GET" action="{{ route('reports.financial') }}">
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

<!-- الملخص المالي -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stat-card primary">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">{{ number_format($totalInvoiced, 2) }}</h4>
                    <p class="mb-0">إجمالي الفواتير (دينار)</p>
                </div>
                <div class="fs-1 opacity-75">
                    <i class="fas fa-file-invoice"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stat-card success">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">{{ number_format($totalCollected, 2) }}</h4>
                    <p class="mb-0">إجمالي المحصل (دينار)</p>
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
                    <h4 class="mb-1">{{ number_format($totalOutstanding, 2) }}</h4>
                    <p class="mb-0">المبالغ المستحقة (دينار)</p>
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
                    <h4 class="mb-1">{{ $totalInvoiced > 0 ? number_format(($totalCollected / $totalInvoiced) * 100, 1) : 0 }}%</h4>
                    <p class="mb-0">نسبة التحصيل</p>
                </div>
                <div class="fs-1 opacity-75">
                    <i class="fas fa-percentage"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- جدول الفواتير -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-file-invoice me-2"></i>
                    الفواتير من {{ $fromDate->format('Y-m-d') }} إلى {{ $toDate->format('Y-m-d') }}
                </h5>
            </div>
            <div class="card-body">
                @if($invoices->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>رقم الفاتورة</th>
                                    <th>العميل</th>
                                    <th>التاريخ</th>
                                    <th>المبلغ الإجمالي</th>
                                    <th>المبلغ المدفوع</th>
                                    <th>المبلغ المتبقي</th>
                                    <th>الحالة</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($invoices as $invoice)
                                    <tr>
                                        <td>
                                            <a href="{{ route('invoices.show', $invoice->id) }}" class="text-decoration-none">
                                                {{ $invoice->invoice_number }}
                                            </a>
                                        </td>
                                        <td>
                                            <div>
                                                <strong>{{ $invoice->customer->name }}</strong>
                                                @if($invoice->customer->company_name)
                                                    <br><small class="text-muted">{{ $invoice->customer->company_name }}</small>
                                                @endif
                                            </div>
                                        </td>
                                        <td>{{ $invoice->created_at->format('Y-m-d') }}</td>
                                        <td>{{ number_format($invoice->total_amount, 2) }} دينار</td>
                                        <td>{{ number_format($invoice->paid_amount, 2) }} دينار</td>
                                        <td>
                                            <span class="badge {{ $invoice->remaining_amount > 0 ? 'bg-danger' : 'bg-success' }}">
                                                {{ number_format($invoice->remaining_amount, 2) }} دينار
                                            </span>
                                        </td>
                                        <td>
                                            @switch($invoice->status)
                                                @case('pending')
                                                    <span class="badge bg-warning">معلقة</span>
                                                    @break
                                                @case('paid')
                                                    <span class="badge bg-success">مدفوعة</span>
                                                    @break
                                                @case('partially_paid')
                                                    <span class="badge bg-info">مدفوعة جزئياً</span>
                                                    @break
                                                @case('overdue')
                                                    <span class="badge bg-danger">متأخرة</span>
                                                    @break
                                            @endswitch
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="table-primary">
                                    <td colspan="3"><strong>الإجمالي:</strong></td>
                                    <td><strong>{{ number_format($invoices->sum('total_amount'), 2) }} دينار</strong></td>
                                    <td><strong>{{ number_format($invoices->sum('paid_amount'), 2) }} دينار</strong></td>
                                    <td><strong>{{ number_format($invoices->sum('remaining_amount'), 2) }} دينار</strong></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-file-invoice fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">لا توجد فواتير</h5>
                        <p class="text-muted">لم يتم العثور على أي فواتير في الفترة المحددة</p>
                    </div>
                @endif
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
                    <i class="fas fa-money-bill-wave me-2"></i>
                    التحصيلات من {{ $fromDate->format('Y-m-d') }} إلى {{ $toDate->format('Y-m-d') }}
                </h5>
            </div>
            <div class="card-body">
                @if($collections->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>رقم الفاتورة</th>
                                    <th>العميل</th>
                                    <th>تاريخ التحصيل</th>
                                    <th>المبلغ</th>
                                    <th>طريقة الدفع</th>
                                    <th>تم بواسطة</th>
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
                                        <td>
                                            <div>
                                                <strong>{{ $collection->customer->name }}</strong>
                                                @if($collection->customer->company_name)
                                                    <br><small class="text-muted">{{ $collection->customer->company_name }}</small>
                                                @endif
                                            </div>
                                        </td>
                                        <td>{{ $collection->collection_date->format('Y-m-d H:i') }}</td>
                                        <td><strong class="text-success">{{ number_format($collection->amount, 2) }} دينار</strong></td>
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
                                            @endswitch
                                        </td>
                                        <td>{{ $collection->collectedBy->name ?? 'غير محدد' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="table-success">
                                    <td colspan="3"><strong>إجمالي التحصيلات:</strong></td>
                                    <td><strong>{{ number_format($collections->sum('amount'), 2) }} دينار</strong></td>
                                    <td colspan="2"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-money-bill-wave fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">لا توجد تحصيلات</h5>
                        <p class="text-muted">لم يتم العثور على أي تحصيلات في الفترة المحددة</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- تحليل طرق الدفع -->
@if($collections->count() > 0)
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-credit-card me-2"></i>
                        التحصيلات حسب طريقة الدفع
                    </h5>
                </div>
                <div class="card-body">
                    @php
                        $paymentMethods = $collections->groupBy('payment_method');
                        $paymentLabels = [
                            'cash' => 'نقداً',
                            'bank_transfer' => 'تحويل بنكي',
                            'check' => 'شيك',
                            'credit_card' => 'بطاقة ائتمان'
                        ];
                    @endphp
                    
                    @foreach($paymentMethods as $method => $methodCollections)
                        @php
                            $amount = $methodCollections->sum('amount');
                            $percentage = ($amount / $totalCollected) * 100;
                        @endphp
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>{{ $paymentLabels[$method] ?? $method }}</span>
                            <div>
                                <span class="badge bg-primary me-2">{{ number_format($amount, 2) }} دينار</span>
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
                        <i class="fas fa-chart-pie me-2"></i>
                        حالة الفواتير
                    </h5>
                </div>
                <div class="card-body">
                    @php
                        $invoiceStatuses = $invoices->groupBy('status');
                        $statusLabels = [
                            'pending' => 'معلقة',
                            'paid' => 'مدفوعة',
                            'partially_paid' => 'مدفوعة جزئياً',
                            'overdue' => 'متأخرة'
                        ];
                    @endphp
                    
                    @foreach($invoiceStatuses as $status => $statusInvoices)
                        @php
                            $amount = $statusInvoices->sum('total_amount');
                            $percentage = $totalInvoiced > 0 ? ($amount / $totalInvoiced) * 100 : 0;
                        @endphp
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>{{ $statusLabels[$status] ?? $status }}</span>
                            <div>
                                <span class="badge bg-secondary me-2">{{ $statusInvoices->count() }}</span>
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
    
    .stat-card.danger {
        background: linear-gradient(135deg, #ff416c 0%, #ff4b2b 100%);
    }
    
    .stat-card.warning {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }
</style>
@endpush
