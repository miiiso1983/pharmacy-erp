#!/bin/bash

# 🚀 تشغيل خادم نظام إدارة الصيدلية - إصدار محسن

echo "🚀 بدء تشغيل خادم نظام إدارة الصيدلية..."

# التحقق من وجود Python
if ! command -v python3 &> /dev/null; then
    echo "❌ خطأ: Python 3 غير مثبت"
    exit 1
fi

echo "✅ Python 3 متاح"

# تحديد مجلد التطبيق
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
APP_DIR="$SCRIPT_DIR/PharmacyApp"

echo "📁 مجلد التطبيق: $APP_DIR"

# التحقق من وجود مجلد التطبيق
if [ ! -d "$APP_DIR" ]; then
    echo "❌ خطأ: مجلد التطبيق غير موجود: $APP_DIR"
    exit 1
fi

# الانتقال إلى مجلد التطبيق
cd "$APP_DIR"

# التحقق من وجود الملفات
if [ ! -f "index.html" ]; then
    echo "❌ خطأ: ملفات التطبيق غير موجودة في $APP_DIR"
    exit 1
fi

echo "✅ ملفات التطبيق موجودة"

# إيقاف أي خادم يعمل على المنافذ المطلوبة
echo "🔄 إيقاف الخوادم السابقة..."
pkill -f "python3 -m http.server" 2>/dev/null || true
lsof -ti:8080 | xargs kill -9 2>/dev/null || true

# انتظار قليل
sleep 2

# الحصول على عنوان IP المحلي
LOCAL_IP=$(ifconfig | grep "inet " | grep -v 127.0.0.1 | head -1 | awk '{print $2}')

# تشغيل الخادم على المنفذ 8080
echo "🌐 تشغيل الخادم على المنفذ 8080..."
echo "📁 من المجلد: $(pwd)"

# تشغيل الخادم في الخلفية
python3 -m http.server 8080 --bind 0.0.0.0 &
SERVER_PID=$!

# انتظار قليل لتشغيل الخادم
sleep 3

# التحقق من أن الخادم يعمل
if curl -s http://localhost:8080 > /dev/null; then
    echo "✅ الخادم يعمل بنجاح!"
else
    echo "❌ فشل في تشغيل الخادم"
    kill $SERVER_PID 2>/dev/null
    exit 1
fi

echo ""
echo "🎉 تم تشغيل خادم نظام إدارة الصيدلية بنجاح!"
echo ""
echo "📋 معلومات الوصول:"
echo "   🌐 الرابط المحلي: http://localhost:8080"
if [ ! -z "$LOCAL_IP" ]; then
    echo "   🌐 الرابط الشبكي: http://$LOCAL_IP:8080"
fi
echo "   📱 للمحاكي: http://127.0.0.1:8080"
echo ""
echo "📱 الصفحات المتاحة:"
echo "   🏠 الصفحة الرئيسية: http://localhost:8080"
echo "   🧪 صفحة الاختبار: http://localhost:8080/test.html"
echo "   🔐 تسجيل الدخول: http://localhost:8080/login.html"
echo "   📊 لوحة التحكم: http://localhost:8080/dashboard.html"
echo "   🏪 إدارة المخازن: http://localhost:8080/warehouses.html"
echo "   💰 مبيعات المخزن: http://localhost:8080/warehouse-sales.html"
echo "   📊 تقارير المخازن: http://localhost:8080/warehouse-reports.html"
echo ""
echo "🔐 بيانات الدخول التجريبية:"
echo "   📧 البريد: admin@pharmacy-erp.com"
echo "   🔑 كلمة المرور: password123"
echo ""

# فتح المتصفح تلقائياً
if command -v open &> /dev/null; then
    echo "🌐 فتح المتصفح..."
    sleep 2
    open http://localhost:8080/test.html
fi

# إبقاء السكريبت يعمل
trap "echo ''; echo '🛑 إيقاف الخادم...'; kill $SERVER_PID 2>/dev/null; echo '✅ تم إيقاف الخادم بنجاح'; exit" INT

# انتظار إشارة الإيقاف
wait $SERVER_PID
