@extends('layouts.app')

@section('title', 'إضافة مخزن جديد')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
    <li class="breadcrumb-item"><a href="{{ route('warehouses.index') }}">المخازن</a></li>
    <li class="breadcrumb-item active">إضافة مخزن جديد</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-warehouse me-2"></i>
                        إضافة مخزن جديد
                    </h5>
                </div>
                
                <div class="card-body">
                    <form method="POST" action="{{ route('warehouses.store') }}">
                        @csrf
                        
                        <div class="row">
                            <!-- اسم المخزن -->
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">اسم المخزن <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name') }}" 
                                       required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- كود المخزن -->
                            <div class="col-md-6 mb-3">
                                <label for="code" class="form-label">كود المخزن <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('code') is-invalid @enderror" 
                                       id="code" 
                                       name="code" 
                                       value="{{ old('code') }}" 
                                       placeholder="مثال: WH001"
                                       required>
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row">
                            <!-- المدينة -->
                            <div class="col-md-6 mb-3">
                                <label for="city" class="form-label">المدينة <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('city') is-invalid @enderror" 
                                       id="city" 
                                       name="city" 
                                       value="{{ old('city') }}" 
                                       required>
                                @error('city')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- المنطقة -->
                            <div class="col-md-6 mb-3">
                                <label for="area" class="form-label">المنطقة <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('area') is-invalid @enderror" 
                                       id="area" 
                                       name="area" 
                                       value="{{ old('area') }}" 
                                       required>
                                @error('area')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- العنوان -->
                        <div class="mb-3">
                            <label for="address" class="form-label">العنوان التفصيلي <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('address') is-invalid @enderror" 
                                      id="address" 
                                      name="address" 
                                      rows="3" 
                                      required>{{ old('address') }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="row">
                            <!-- رقم الهاتف -->
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">رقم الهاتف</label>
                                <input type="tel" 
                                       class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" 
                                       name="phone" 
                                       value="{{ old('phone') }}" 
                                       placeholder="07xxxxxxxxx">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- المدير المسؤول -->
                            <div class="col-md-6 mb-3">
                                <label for="manager" class="form-label">المدير المسؤول</label>
                                <input type="text" 
                                       class="form-control @error('manager') is-invalid @enderror" 
                                       id="manager" 
                                       name="manager" 
                                       value="{{ old('manager') }}">
                                @error('manager')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- نوع المخزن -->
                        <div class="mb-3">
                            <label for="type" class="form-label">نوع المخزن <span class="text-danger">*</span></label>
                            <select class="form-select @error('type') is-invalid @enderror" 
                                    id="type" 
                                    name="type" 
                                    required>
                                <option value="">اختر نوع المخزن</option>
                                <option value="main" {{ old('type') == 'main' ? 'selected' : '' }}>مخزن رئيسي</option>
                                <option value="branch" {{ old('type') == 'branch' ? 'selected' : '' }}>مخزن فرعي</option>
                                <option value="pharmacy" {{ old('type') == 'pharmacy' ? 'selected' : '' }}>صيدلية</option>
                                <option value="distribution" {{ old('type') == 'distribution' ? 'selected' : '' }}>مركز توزيع</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- الوصف -->
                        <div class="mb-3">
                            <label for="description" class="form-label">الوصف</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" 
                                      name="description" 
                                      rows="3" 
                                      placeholder="وصف مختصر عن المخزن...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- ملاحظات -->
                        <div class="mb-4">
                            <label for="notes" class="form-label">ملاحظات</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" 
                                      name="notes" 
                                      rows="3" 
                                      placeholder="أي ملاحظات إضافية...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- أزرار التحكم -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('warehouses.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-right me-2"></i>
                                العودة
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>
                                حفظ المخزن
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // تلقائياً إنشاء كود المخزن من الاسم
    document.getElementById('name').addEventListener('input', function() {
        const name = this.value;
        const codeField = document.getElementById('code');
        
        if (name && !codeField.value) {
            // إنشاء كود بسيط من الاسم
            const words = name.split(' ');
            let code = 'WH';
            
            words.forEach(word => {
                if (word.length > 0) {
                    code += word.charAt(0).toUpperCase();
                }
            });
            
            // إضافة رقم عشوائي
            code += Math.floor(Math.random() * 100).toString().padStart(2, '0');
            
            codeField.value = code;
        }
    });
</script>
@endpush
