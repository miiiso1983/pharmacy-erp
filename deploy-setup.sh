#!/bin/bash

echo "🚀 إعداد مشروع نظام إدارة الصيدلية للنشر"
echo "=============================================="

# التحقق من وجود Git
if ! command -v git &> /dev/null; then
    echo "❌ Git غير مثبت. يرجى تثبيت Git أولاً"
    exit 1
fi

# التحقق من وجود Composer
if ! command -v composer &> /dev/null; then
    echo "❌ Composer غير مثبت. يرجى تثبيت Composer أولاً"
    exit 1
fi

echo "✅ التحقق من المتطلبات مكتمل"

# إنشاء مجلدات مطلوبة
echo "📁 إنشاء المجلدات المطلوبة..."
mkdir -p storage/app/public
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions  
mkdir -p storage/framework/views
mkdir -p storage/logs

# تعيين الصلاحيات
echo "🔐 تعيين الصلاحيات..."
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# إنشاء قاعدة البيانات
echo "🗄️ إنشاء قاعدة البيانات..."
touch database/database.sqlite

# تحديث التبعيات
echo "📦 تحديث التبعيات..."
composer install --optimize-autoloader

# تشغيل migrations
echo "🔄 تشغيل migrations..."
php artisan migrate --force

# تشغيل seeders
echo "🌱 إضافة البيانات التجريبية..."
php artisan db:seed --force

# إنشاء symbolic link
echo "🔗 إنشاء symbolic link..."
php artisan storage:link

# تحسين الأداء
echo "⚡ تحسين الأداء..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# إعداد Git إذا لم يكن موجود
if [ ! -d ".git" ]; then
    echo "🔧 إعداد Git repository..."
    git init
    git add .
    git commit -m "Initial commit - Pharmacy ERP System"
    
    echo ""
    echo "📋 الخطوات التالية:"
    echo "1. إنشاء repository على GitHub"
    echo "2. تشغيل الأوامر التالية:"
    echo "   git remote add origin https://github.com/YOUR_USERNAME/pharmacy-erp.git"
    echo "   git branch -M main"
    echo "   git push -u origin main"
else
    echo "📝 إضافة التغييرات إلى Git..."
    git add .
    git commit -m "Prepare for deployment - $(date)"
    echo "✅ يمكنك الآن تشغيل: git push"
fi

echo ""
echo "🎉 تم إعداد المشروع للنشر بنجاح!"
echo ""
echo "🌐 خيارات النشر:"
echo "1. Railway (مجاني): https://railway.app"
echo "2. Heroku (مجاني محدود): https://heroku.com"
echo "3. DigitalOcean (مدفوع): https://digitalocean.com"
echo ""
echo "📖 راجع ملف DEPLOYMENT_GUIDE.md للتفاصيل"
echo ""
echo "🔑 بيانات الدخول التجريبية:"
echo "   البريد: admin@pharmacy-erp.com"
echo "   كلمة المرور: password123"
