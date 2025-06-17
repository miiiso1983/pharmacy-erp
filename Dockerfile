# استخدام PHP 8.2 مع Apache
FROM php:8.2-apache

# تثبيت التبعيات المطلوبة
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    sqlite3 \
    libsqlite3-dev \
    default-mysql-client \
    && docker-php-ext-install pdo_mysql pdo_sqlite mbstring exif pcntl bcmath gd zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# تثبيت Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# تعيين مجلد العمل
WORKDIR /var/www/html

# نسخ ملفات المشروع
COPY . .

# تثبيت تبعيات Laravel
RUN composer install --optimize-autoloader --no-dev

# إنشاء المجلدات المطلوبة
RUN mkdir -p storage/app/public \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache

# إنشاء قاعدة البيانات
RUN touch database/database.sqlite

# تعيين الصلاحيات
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache \
    && chmod 664 /var/www/html/database/database.sqlite

# تمكين mod_rewrite
RUN a2enmod rewrite

# نسخ تكوين Apache
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf

# إعداد Laravel
RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache \
    && php artisan migrate --force \
    && php artisan db:seed --force

# فتح المنفذ 80
EXPOSE 80

# تشغيل Apache
CMD ["apache2-foreground"]
