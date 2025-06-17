# 🚀 النشر السريع - 5 دقائق

## 🎯 الطريقة الأسرع: Railway

### 1. إعداد GitHub (دقيقتان)

```bash
# في مجلد المشروع
git init
git add .
git commit -m "Pharmacy ERP System - Ready for deployment"
```

**ثم:**
1. اذهب إلى [GitHub.com](https://github.com)
2. إنشاء repository جديد باسم `pharmacy-erp`
3. انسخ الأوامر من GitHub وشغلها

### 2. النشر على Railway (3 دقائق)

1. **اذهب إلى [Railway.app](https://railway.app)**
2. **سجل دخول بـ GitHub**
3. **اضغط "New Project"**
4. **اختر "Deploy from GitHub repo"**
5. **اختر `pharmacy-erp`**

### 3. إعداد متغيرات البيئة

في Railway، اذهب إلى **Variables** وأضف:

```
APP_NAME=نظام إدارة الصيدلية
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:QKyZoyATcjBxA0qzfcTUPrsxush+g+1ASMVMxxjXcwk=
DB_CONNECTION=sqlite
APP_LOCALE=ar
```

### 4. انتظر النشر

- Railway سيقوم بالنشر تلقائياً
- ستحصل على رابط مثل: `https://pharmacy-erp-production.up.railway.app`

---

## 🎉 تم! موقعك جاهز

### 🔑 بيانات الدخول:
- **البريد**: admin@pharmacy-erp.com  
- **كلمة المرور**: password123

### 🌟 المميزات المتاحة:
- ✅ إدارة المخازن
- ✅ إدارة العناصر والأدوية
- ✅ إدارة الطلبات
- ✅ إدارة الفواتير
- ✅ إدارة التحصيلات
- ✅ إدارة الموردين
- ✅ التقارير والإحصائيات

---

## 🔧 إعدادات إضافية (اختيارية)

### تخصيص الدومين:
1. في Railway، اذهب إلى **Settings**
2. **Custom Domain**
3. أضف دومينك الخاص

### إعداد قاعدة بيانات MySQL:
1. في Railway، أضف **MySQL service**
2. غير `DB_CONNECTION` إلى `mysql`
3. أضف متغيرات قاعدة البيانات

---

## 📞 المساعدة

إذا واجهت مشاكل:
1. تحقق من **Logs** في Railway
2. راجع ملف `DEPLOYMENT_GUIDE.md`
3. تأكد من أن جميع الملفات موجودة في GitHub

**نصيحة**: احفظ رابط موقعك في المفضلة! 🔖
