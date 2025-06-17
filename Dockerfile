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
    default-mysql-client \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# تثبيت Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# تعيين مجلد العمل
WORKDIR /var/www/html

# نسخ ملفات المشروع
COPY . .

# تثبيت تبعيات Laravel
RUN composer install --optimize-autoloader --no-dev

# تعيين الصلاحيات
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# تمكين mod_rewrite
RUN a2enmod rewrite

# نسخ تكوين Apache
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf

# فتح المنفذ 80
EXPOSE 80

# تشغيل Apache
CMD ["apache2-foreground"]
