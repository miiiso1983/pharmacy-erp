@extends('layouts.app')

@section('title', 'إنشاء حساب جديد - نظام إدارة المذاخر')

@section('content')
<div class="container">
    <div class="row justify-content-center align-items-center min-vh-100 py-4">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-lg">
                <div class="card-header text-center">
                    <h3 class="mb-0">
                        <i class="fas fa-pills me-2"></i>
                        نظام إدارة المذاخر
                    </h3>
                    <p class="mb-0 mt-2">إنشاء حساب جديد</p>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('register') }}">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">
                                    <i class="fas fa-user me-2"></i>
                                    الاسم الكامل *
                                </label>
                                <input type="text" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name') }}" 
                                       required 
                                       autofocus
                                       placeholder="أدخل الاسم الكامل">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope me-2"></i>
                                    البريد الإلكتروني *
                                </label>
                                <input type="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email') }}" 
                                       required
                                       placeholder="أدخل البريد الإلكتروني">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock me-2"></i>
                                    كلمة المرور *
                                </label>
                                <input type="password" 
                                       class="form-control @error('password') is-invalid @enderror" 
                                       id="password" 
                                       name="password" 
                                       required
                                       placeholder="أدخل كلمة المرور">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="password_confirmation" class="form-label">
                                    <i class="fas fa-lock me-2"></i>
                                    تأكيد كلمة المرور *
                                </label>
                                <input type="password" 
                                       class="form-control" 
                                       id="password_confirmation" 
                                       name="password_confirmation" 
                                       required
                                       placeholder="أعد إدخال كلمة المرور">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">
                                    <i class="fas fa-phone me-2"></i>
                                    رقم الهاتف
                                </label>
                                <input type="text" 
                                       class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" 
                                       name="phone" 
                                       value="{{ old('phone') }}"
                                       placeholder="أدخل رقم الهاتف">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="company_name" class="form-label">
                                    <i class="fas fa-building me-2"></i>
                                    اسم الشركة
                                </label>
                                <input type="text" 
                                       class="form-control @error('company_name') is-invalid @enderror" 
                                       id="company_name" 
                                       name="company_name" 
                                       value="{{ old('company_name') }}"
                                       placeholder="أدخل اسم الشركة">
                                @error('company_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="tax_number" class="form-label">
                                    <i class="fas fa-receipt me-2"></i>
                                    الرقم الضريبي
                                </label>
                                <input type="text" 
                                       class="form-control @error('tax_number') is-invalid @enderror" 
                                       id="tax_number" 
                                       name="tax_number" 
                                       value="{{ old('tax_number') }}"
                                       placeholder="أدخل الرقم الضريبي">
                                @error('tax_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="address" class="form-label">
                                    <i class="fas fa-map-marker-alt me-2"></i>
                                    العنوان
                                </label>
                                <input type="text" 
                                       class="form-control @error('address') is-invalid @enderror" 
                                       id="address" 
                                       name="address" 
                                       value="{{ old('address') }}"
                                       placeholder="أدخل العنوان">
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-user-plus me-2"></i>
                                إنشاء الحساب
                            </button>
                        </div>
                    </form>
                    
                    <hr class="my-4">
                    
                    <div class="text-center">
                        <p class="mb-2">لديك حساب بالفعل؟</p>
                        <a href="{{ route('login') }}" class="btn btn-outline-primary">
                            <i class="fas fa-sign-in-alt me-2"></i>
                            تسجيل الدخول
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    body {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
    }
    
    .card {
        border: none;
        border-radius: 20px;
        backdrop-filter: blur(10px);
        background: rgba(255, 255, 255, 0.95);
    }
    
    .card-header {
        background: transparent;
        border-bottom: 1px solid rgba(0,0,0,0.1);
        border-radius: 20px 20px 0 0 !important;
    }
    
    .form-control {
        border-radius: 10px;
        border: 2px solid #e9ecef;
        padding: 12px 15px;
        transition: all 0.3s ease;
    }
    
    .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }
    
    .btn-primary {
        border-radius: 10px;
        padding: 12px;
        font-weight: 600;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        transition: all 0.3s ease;
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
    }
    
    .btn-outline-primary {
        border-radius: 10px;
        border: 2px solid #667eea;
        color: #667eea;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-outline-primary:hover {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-color: #667eea;
        transform: translateY(-2px);
    }
</style>
@endsection
