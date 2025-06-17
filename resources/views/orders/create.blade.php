@extends('layouts.app')

@section('title', 'إنشاء طلب جديد')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
    <li class="breadcrumb-item"><a href="{{ route('orders.index') }}">الطلبات</a></li>
    <li class="breadcrumb-item active">إنشاء طلب جديد</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-shopping-cart me-2"></i>
                        إنشاء طلب جديد
                    </h5>
                </div>
                
                <div class="card-body">
                    <form method="POST" action="{{ route('orders.store') }}" id="orderForm">
                        @csrf
                        
                        <div class="row">
                            <!-- معلومات العميل -->
                            @if(Auth::user()->user_type !== 'customer')
                            <div class="col-md-6 mb-3">
                                <label for="customer_id" class="form-label">العميل <span class="text-danger">*</span></label>
                                <select class="form-select @error('customer_id') is-invalid @enderror" 
                                        id="customer_id" 
                                        name="customer_id" 
                                        required>
                                    <option value="">اختر العميل</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                            {{ $customer->name }} - {{ $customer->email }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('customer_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            @endif
                            
                            <!-- تاريخ التسليم -->
                            <div class="col-md-6 mb-3">
                                <label for="delivery_date" class="form-label">تاريخ التسليم</label>
                                <input type="date" 
                                       class="form-control @error('delivery_date') is-invalid @enderror" 
                                       id="delivery_date" 
                                       name="delivery_date" 
                                       value="{{ old('delivery_date') }}"
                                       min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                                @error('delivery_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- عنوان التسليم -->
                        <div class="mb-3">
                            <label for="delivery_address" class="form-label">عنوان التسليم</label>
                            <textarea class="form-control @error('delivery_address') is-invalid @enderror" 
                                      id="delivery_address" 
                                      name="delivery_address" 
                                      rows="3" 
                                      placeholder="أدخل عنوان التسليم...">{{ old('delivery_address') }}</textarea>
                            @error('delivery_address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- العناصر -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0">عناصر الطلب</h6>
                                <button type="button" class="btn btn-sm btn-primary" onclick="addItem()">
                                    <i class="fas fa-plus me-1"></i>
                                    إضافة عنصر
                                </button>
                            </div>
                            
                            <div id="itemsContainer">
                                <!-- سيتم إضافة العناصر هنا بواسطة JavaScript -->
                            </div>
                            
                            @error('items')
                                <div class="text-danger mt-2">{{ $message }}</div>
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
                        
                        <!-- ملخص الطلب -->
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title">ملخص الطلب</h6>
                                <div class="row">
                                    <div class="col-md-4">
                                        <strong>المجموع الفرعي: </strong>
                                        <span id="subtotal">0.00 د.ع</span>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>الضريبة (15%): </strong>
                                        <span id="tax">0.00 د.ع</span>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>المجموع الكلي: </strong>
                                        <span id="total" class="text-primary fs-5">0.00 د.ع</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- أزرار التحكم -->
                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('orders.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-right me-2"></i>
                                العودة
                            </a>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="fas fa-save me-2"></i>
                                حفظ الطلب
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- قالب عنصر الطلب -->
<template id="itemTemplate">
    <div class="item-row border rounded p-3 mb-3">
        <div class="row align-items-end">
            <div class="col-md-4">
                <label class="form-label">العنصر</label>
                <select class="form-select item-select" name="items[INDEX][item_id]" required>
                    <option value="">اختر العنصر</option>
                    @foreach($items as $item)
                        <option value="{{ $item->id }}"
                                data-price="{{ $item->selling_price }}"
                                data-stock="{{ $item->quantity_in_stock }}">
                            {{ $item->name }} - {{ number_format($item->selling_price, 2) }} د.ع
                            (متوفر: {{ $item->quantity_in_stock }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">الكمية</label>
                <input type="number" 
                       class="form-control quantity-input" 
                       name="items[INDEX][quantity]" 
                       min="1" 
                       value="1" 
                       required>
            </div>
            <div class="col-md-2">
                <label class="form-label">سعر الوحدة</label>
                <input type="text" 
                       class="form-control unit-price" 
                       readonly>
            </div>
            <div class="col-md-2">
                <label class="form-label">المجموع</label>
                <input type="text" 
                       class="form-control item-total" 
                       readonly>
            </div>
            <div class="col-md-2">
                <button type="button" 
                        class="btn btn-danger btn-sm w-100" 
                        onclick="removeItem(this)">
                    <i class="fas fa-trash"></i>
                    حذف
                </button>
            </div>
        </div>
    </div>
</template>
@endsection

@push('scripts')
<script>
let itemIndex = 0;

function addItem() {
    const template = document.getElementById('itemTemplate');
    const container = document.getElementById('itemsContainer');
    
    const clone = template.content.cloneNode(true);
    
    // تحديث الفهارس
    clone.querySelectorAll('[name*="INDEX"]').forEach(element => {
        element.name = element.name.replace('INDEX', itemIndex);
    });
    
    // إضافة مستمعي الأحداث
    const itemRow = clone.querySelector('.item-row');
    const select = clone.querySelector('.item-select');
    const quantityInput = clone.querySelector('.quantity-input');
    
    select.addEventListener('change', updateItemPrice);
    quantityInput.addEventListener('input', updateItemTotal);
    
    container.appendChild(clone);
    itemIndex++;
    
    updateOrderTotal();
}

function removeItem(button) {
    button.closest('.item-row').remove();
    updateOrderTotal();
}

function updateItemPrice() {
    const row = this.closest('.item-row');
    const selectedOption = this.options[this.selectedIndex];
    const price = selectedOption.dataset.price || 0;
    const stock = selectedOption.dataset.stock || 0;
    
    const unitPriceInput = row.querySelector('.unit-price');
    const quantityInput = row.querySelector('.quantity-input');
    
    unitPriceInput.value = parseFloat(price).toFixed(2) + ' د.ع';
    quantityInput.max = stock;
    
    updateItemTotal.call(quantityInput);
}

function updateItemTotal() {
    const row = this.closest('.item-row');
    const select = row.querySelector('.item-select');
    const selectedOption = select.options[select.selectedIndex];
    const price = parseFloat(selectedOption.dataset.price || 0);
    const quantity = parseInt(this.value || 0);
    
    const total = price * quantity;
    const totalInput = row.querySelector('.item-total');
    totalInput.value = total.toFixed(2) + ' د.ع';
    
    updateOrderTotal();
}

function updateOrderTotal() {
    let subtotal = 0;
    
    document.querySelectorAll('.item-row').forEach(row => {
        const select = row.querySelector('.item-select');
        const quantityInput = row.querySelector('.quantity-input');
        
        if (select.value && quantityInput.value) {
            const selectedOption = select.options[select.selectedIndex];
            const price = parseFloat(selectedOption.dataset.price || 0);
            const quantity = parseInt(quantityInput.value || 0);
            subtotal += price * quantity;
        }
    });
    
    const tax = subtotal * 0.15;
    const total = subtotal + tax;
    
    document.getElementById('subtotal').textContent = subtotal.toFixed(2) + ' د.ع';
    document.getElementById('tax').textContent = tax.toFixed(2) + ' د.ع';
    document.getElementById('total').textContent = total.toFixed(2) + ' د.ع';
}

// إضافة عنصر واحد عند تحميل الصفحة
document.addEventListener('DOMContentLoaded', function() {
    addItem();
});

// التحقق من صحة النموذج قبل الإرسال
document.getElementById('orderForm').addEventListener('submit', function(e) {
    const items = document.querySelectorAll('.item-row');
    
    if (items.length === 0) {
        e.preventDefault();
        alert('يجب إضافة عنصر واحد على الأقل');
        return;
    }
    
    let hasValidItem = false;
    items.forEach(row => {
        const select = row.querySelector('.item-select');
        const quantity = row.querySelector('.quantity-input');
        
        if (select.value && quantity.value && parseInt(quantity.value) > 0) {
            hasValidItem = true;
        }
    });
    
    if (!hasValidItem) {
        e.preventDefault();
        alert('يجب إضافة عنصر صحيح واحد على الأقل');
        return;
    }
});
</script>
@endpush
