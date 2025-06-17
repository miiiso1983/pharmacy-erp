# 🚀 دليل رفع المشروع على الإنترنت

## 📋 الخيارات المتاحة

### 1. 🚄 Railway (الأسهل - مجاني)
### 2. 🌊 Heroku (مجاني محدود)
### 3. 🌐 DigitalOcean (مدفوع)
### 4. ☁️ AWS (مجاني لسنة واحدة)

---

## 🚄 الطريقة الأولى: Railway (الموصى بها)

### الخطوة 1: إعداد GitHub

1. **إنشاء مستودع GitHub جديد**:
   - اذهب إلى [GitHub](https://github.com)
   - اضغط على "New repository"
   - اسم المستودع: `pharmacy-erp`
   - اجعله Public أو Private

2. **رفع الكود إلى GitHub**:
```bash
cd pharmacy-erp
git init
git add .
git commit -m "Initial commit - Pharmacy ERP System"
git branch -M main
git remote add origin https://github.com/YOUR_USERNAME/pharmacy-erp.git
git push -u origin main
```

### الخطوة 2: إعداد Railway

1. **إنشاء حساب**:
   - اذهب إلى [Railway.app](https://railway.app)
   - سجل دخول باستخدام GitHub

2. **إنشاء مشروع جديد**:
   - اضغط "New Project"
   - اختر "Deploy from GitHub repo"
   - اختر مستودع `pharmacy-erp`

3. **إعداد متغيرات البيئة**:
   في لوحة تحكم Railway، اذهب إلى Variables وأضف:
   ```
   APP_NAME=نظام إدارة الصيدلية
   APP_ENV=production
   APP_DEBUG=false
   APP_KEY=base64:QKyZoyATcjBxA0qzfcTUPrsxush+g+1ASMVMxxjXcwk=
   APP_URL=https://YOUR_APP_NAME.up.railway.app
   DB_CONNECTION=sqlite
   APP_LOCALE=ar
   ```

4. **النشر**:
   - Railway سيقوم بالنشر تلقائياً
   - ستحصل على رابط مثل: `https://pharmacy-erp-production.up.railway.app`

---

## 🌊 الطريقة الثانية: Heroku

### الخطوة 1: إعداد Heroku

1. **إنشاء حساب**:
   - اذهب إلى [Heroku](https://heroku.com)
   - إنشاء حساب مجاني

2. **تثبيت Heroku CLI**:
```bash
# على macOS
brew tap heroku/brew && brew install heroku

# على Windows
# حمل من الموقع الرسمي
```

3. **تسجيل الدخول**:
```bash
heroku login
```

### الخطوة 2: إعداد المشروع

1. **إنشاء تطبيق Heroku**:
```bash
cd pharmacy-erp
heroku create pharmacy-erp-YOUR_NAME
```

2. **إعداد متغيرات البيئة**:
```bash
heroku config:set APP_NAME="نظام إدارة الصيدلية"
heroku config:set APP_ENV=production
heroku config:set APP_DEBUG=false
heroku config:set APP_KEY=base64:QKyZoyATcjBxA0qzfcTUPrsxush+g+1ASMVMxxjXcwk=
heroku config:set DB_CONNECTION=sqlite
heroku config:set APP_LOCALE=ar
```

3. **النشر**:
```bash
git add .
git commit -m "Deploy to Heroku"
git push heroku main
```

---

## 🌐 الطريقة الثالثة: DigitalOcean

### الخطوة 1: إنشاء Droplet

1. **إنشاء حساب DigitalOcean**
2. **إنشاء Droplet جديد**:
   - Ubuntu 22.04 LTS
   - Basic plan ($6/month)
   - اختر منطقة قريبة

### الخطوة 2: إعداد الخادم

```bash
# الاتصال بالخادم
ssh root@YOUR_SERVER_IP

# تحديث النظام
apt update && apt upgrade -y

# تثبيت PHP وMySQL
apt install php8.1 php8.1-fpm php8.1-mysql php8.1-xml php8.1-curl php8.1-zip php8.1-mbstring nginx mysql-server -y

# تثبيت Composer
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer

# استنساخ المشروع
cd /var/www
git clone https://github.com/YOUR_USERNAME/pharmacy-erp.git
cd pharmacy-erp

# تثبيت التبعيات
composer install --no-dev --optimize-autoloader

# إعداد الصلاحيات
chown -R www-data:www-data /var/www/pharmacy-erp
chmod -R 755 /var/www/pharmacy-erp/storage
```

### الخطوة 3: إعداد قاعدة البيانات

```bash
# إعداد MySQL
mysql_secure_installation

# إنشاء قاعدة البيانات
mysql -u root -p
CREATE DATABASE pharmacy_erp;
CREATE USER 'pharmacy_user'@'localhost' IDENTIFIED BY 'strong_password';
GRANT ALL PRIVILEGES ON pharmacy_erp.* TO 'pharmacy_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### الخطوة 4: إعداد Nginx

```bash
# إنشاء ملف إعداد Nginx
nano /etc/nginx/sites-available/pharmacy-erp

# إضافة الإعداد التالي:
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/pharmacy-erp/public;
    
    index index.php index.html;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}

# تفعيل الموقع
ln -s /etc/nginx/sites-available/pharmacy-erp /etc/nginx/sites-enabled/
nginx -t
systemctl reload nginx
```

---

## 🔧 نصائح مهمة

### 1. **الأمان**:
- استخدم HTTPS دائماً
- غير APP_KEY في الإنتاج
- استخدم كلمات مرور قوية

### 2. **الأداء**:
- فعل caching في Laravel
- استخدم CDN للملفات الثابتة
- راقب استخدام الموارد

### 3. **النسخ الاحتياطي**:
- اعمل نسخ احتياطية دورية لقاعدة البيانات
- احفظ نسخة من ملفات التطبيق

### 4. **المراقبة**:
- راقب logs الأخطاء
- استخدم أدوات مراقبة الأداء

---

## 📞 الدعم

إذا واجهت أي مشاكل:
1. تحقق من logs الأخطاء
2. راجع documentation Laravel
3. اطلب المساعدة في المجتمعات

**نصيحة**: ابدأ بـ Railway لأنه الأسهل والأسرع! 🚀
