@extends('layouts.app')

@section('title', 'استيراد الموردين - نظام إدارة المذاخر')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
    <li class="breadcrumb-item"><a href="{{ route('suppliers.index') }}">الموردين</a></li>
    <li class="breadcrumb-item active">استيراد الموردين</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>
                <i class="fas fa-file-upload me-2"></i>
                استيراد الموردين من Excel
            </h2>
            <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                العودة للموردين
            </a>
        </div>
    </div>
</div>

<!-- تعليمات الاستيراد -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-info">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    تعليمات الاستيراد
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>متطلبات الملف:</h6>
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                يجب أن يكون الملف من نوع Excel (.xlsx, .xls) أو CSV
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                الحد الأقصى لحجم الملف: 2 ميجابايت
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                يجب أن يحتوي الصف الأول على أسماء الأعمدة
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                عمود "اسم_المورد" مطلوب
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6>الأعمدة المدعومة:</h6>
                        <ul class="list-unstyled">
                            <li class="mb-1"><strong>اسم_المورد</strong> (مطلوب)</li>
                            <li class="mb-1"><strong>الشخص_المسؤول</strong></li>
                            <li class="mb-1"><strong>البريد_الالكتروني</strong></li>
                            <li class="mb-1"><strong>الهاتف</strong></li>
                            <li class="mb-1"><strong>العنوان</strong></li>
                            <li class="mb-1"><strong>المدينة</strong></li>
                            <li class="mb-1"><strong>البلد</strong></li>
                            <li class="mb-1"><strong>الرقم_الضريبي</strong></li>
                            <li class="mb-1"><strong>الحالة</strong> (نشط/غير نشط)</li>
                            <li class="mb-1"><strong>ملاحظات</strong></li>
                        </ul>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="{{ route('suppliers.sample') }}" class="btn btn-outline-primary">
                        <i class="fas fa-download me-2"></i>
                        تحميل ملف نموذجي
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- نموذج رفع الملف -->
<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-upload me-2"></i>
                    رفع ملف الموردين
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('suppliers.import') }}" method="POST" enctype="multipart/form-data" id="importForm">
                    @csrf
                    
                    <div class="mb-4">
                        <label for="file" class="form-label">اختر ملف Excel أو CSV</label>
                        <div class="upload-area" id="uploadArea">
                            <div class="upload-content">
                                <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">اسحب الملف هنا أو انقر للاختيار</h5>
                                <p class="text-muted mb-0">Excel, CSV - حتى 2 ميجابايت</p>
                            </div>
                            <input type="file" class="form-control d-none" id="file" name="file" 
                                   accept=".xlsx,.xls,.csv" required>
                        </div>
                        @error('file')
                            <div class="text-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="file-info d-none" id="fileInfo">
                        <div class="alert alert-info">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-file-excel fa-2x me-3"></i>
                                <div>
                                    <strong id="fileName"></strong>
                                    <br><small id="fileSize" class="text-muted"></small>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-danger ms-auto" id="removeFile">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg" id="submitBtn" disabled>
                            <i class="fas fa-upload me-2"></i>
                            استيراد الموردين
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- نصائح إضافية -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card border-warning">
            <div class="card-header bg-warning text-dark">
                <h6 class="mb-0">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    نصائح مهمة
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <i class="fas fa-lightbulb text-warning me-2"></i>
                                تأكد من صحة البيانات قبل الرفع
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-lightbulb text-warning me-2"></i>
                                البريد الإلكتروني يجب أن يكون فريد لكل مورد
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <i class="fas fa-lightbulb text-warning me-2"></i>
                                سيتم تجاهل الصفوف التي تحتوي على أخطاء
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-lightbulb text-warning me-2"></i>
                                يمكنك تحميل الملف النموذجي كمرجع
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .upload-area {
        border: 2px dashed #dee2e6;
        border-radius: 10px;
        padding: 3rem 2rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        background: #f8f9fa;
    }
    
    .upload-area:hover {
        border-color: #007bff;
        background: #e3f2fd;
    }
    
    .upload-area.dragover {
        border-color: #28a745;
        background: #d4edda;
    }
    
    .upload-content {
        pointer-events: none;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const uploadArea = document.getElementById('uploadArea');
    const fileInput = document.getElementById('file');
    const fileInfo = document.getElementById('fileInfo');
    const fileName = document.getElementById('fileName');
    const fileSize = document.getElementById('fileSize');
    const removeFile = document.getElementById('removeFile');
    const submitBtn = document.getElementById('submitBtn');

    // Click to select file
    uploadArea.addEventListener('click', () => fileInput.click());

    // Drag and drop
    uploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadArea.classList.add('dragover');
    });

    uploadArea.addEventListener('dragleave', () => {
        uploadArea.classList.remove('dragover');
    });

    uploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadArea.classList.remove('dragover');
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            fileInput.files = files;
            handleFileSelect();
        }
    });

    // File selection
    fileInput.addEventListener('change', handleFileSelect);

    function handleFileSelect() {
        const file = fileInput.files[0];
        if (file) {
            fileName.textContent = file.name;
            fileSize.textContent = formatFileSize(file.size);
            fileInfo.classList.remove('d-none');
            uploadArea.style.display = 'none';
            submitBtn.disabled = false;
        }
    }

    // Remove file
    removeFile.addEventListener('click', () => {
        fileInput.value = '';
        fileInfo.classList.add('d-none');
        uploadArea.style.display = 'block';
        submitBtn.disabled = true;
    });

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // Form submission
    document.getElementById('importForm').addEventListener('submit', function() {
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>جاري الاستيراد...';
        submitBtn.disabled = true;
    });
});
</script>
@endpush
