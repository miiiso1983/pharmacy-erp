#!/bin/bash

echo "🚀 Starting Pharmacy ERP on Railway..."

# نسخ ملف البيئة
if [ ! -f .env ]; then
    echo "📄 Creating .env file..."
    cp .env.example .env
fi

# إنشاء المجلدات
echo "📁 Creating directories..."
mkdir -p storage/app/public storage/framework/{cache,sessions,views} storage/logs bootstrap/cache

# تعيين الصلاحيات
echo "🔐 Setting permissions..."
chmod -R 775 storage bootstrap/cache

# إنشاء قاعدة البيانات
echo "🗄️ Setting up database..."
touch database/database.sqlite
chmod 664 database/database.sqlite

# تحسين Laravel
echo "⚡ Optimizing Laravel..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# تشغيل migrations
echo "🔄 Running migrations..."
php artisan migrate --force --no-interaction

# تشغيل seeders
echo "🌱 Seeding database..."
php artisan db:seed --force --no-interaction

echo "✅ Setup complete! Starting server on port ${PORT:-8000}..."

# تشغيل الخادم
exec php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
