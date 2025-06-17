#!/bin/bash

# إنشاء مجلد storage إذا لم يكن موجود
mkdir -p storage/app/public
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs

# تعيين الصلاحيات
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# إنشاء قاعدة البيانات إذا لم تكن موجودة
touch database/database.sqlite

# تشغيل migrations
php artisan migrate --force

# تشغيل seeders
php artisan db:seed --force

# إنشاء symbolic link للتخزين
php artisan storage:link

# تشغيل الخادم
php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
