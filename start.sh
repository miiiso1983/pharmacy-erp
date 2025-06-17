#!/bin/bash

echo "🚀 Starting Pharmacy ERP System..."

# إنشاء مجلدات مطلوبة
echo "📁 Creating required directories..."
mkdir -p storage/app/public
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs
mkdir -p bootstrap/cache

# تعيين الصلاحيات
echo "🔐 Setting permissions..."
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# إنشاء قاعدة البيانات
echo "🗄️ Creating database..."
touch database/database.sqlite
chmod 664 database/database.sqlite

# تحسين الأداء
echo "⚡ Optimizing performance..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# تشغيل migrations
echo "🔄 Running migrations..."
php artisan migrate --force

# تشغيل seeders
echo "🌱 Seeding database..."
php artisan db:seed --force

# إنشاء symbolic link
echo "🔗 Creating storage link..."
php artisan storage:link

echo "✅ Setup complete! Starting server..."

# تشغيل الخادم
php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
