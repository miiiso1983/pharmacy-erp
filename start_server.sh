#!/bin/bash

# 🚀 تشغيل خادم نظام إدارة الصيدلية

echo "🚀 بدء تشغيل خادم نظام إدارة الصيدلية..."

# التحقق من وجود Python
if ! command -v python3 &> /dev/null; then
    echo "❌ خطأ: Python 3 غير مثبت"
    exit 1
fi

echo "✅ Python 3 متاح"

# الانتقال إلى مجلد التطبيق
cd "$(dirname "$0")/PharmacyApp"

# التحقق من وجود الملفات
if [ ! -f "index.html" ]; then
    echo "❌ خطأ: ملفات التطبيق غير موجودة"
    exit 1
fi

echo "✅ ملفات التطبيق موجودة"

# إيقاف أي خادم يعمل على المنافذ المطلوبة
echo "🔄 إيقاف الخوادم السابقة..."
pkill -f "python3 -m http.server" 2>/dev/null || true

# انتظار قليل
sleep 1

# تشغيل الخادم على المنفذ 8080
echo "🌐 تشغيل الخادم على المنفذ 8080..."
python3 -m http.server 8080 --bind 0.0.0.0 &
SERVER_PID=$!

# انتظار قليل لتشغيل الخادم
sleep 2

# التحقق من أن الخادم يعمل
if curl -s http://localhost:8080 > /dev/null; then
    echo "✅ الخادم يعمل بنجاح!"
else
    echo "❌ فشل في تشغيل الخادم"
    exit 1
fi

echo ""
echo "🎉 تم تشغيل خادم نظام إدارة الصيدلية بنجاح!"
echo ""
echo "📋 معلومات الوصول:"
echo "   🌐 الرابط المحلي: http://localhost:8080"
echo "   🌐 الرابط الشبكي: http://$(hostname -I | awk '{print $1}'):8080"
echo "   📱 للمحاكي: http://127.0.0.1:8080"
echo ""
echo "📱 الصفحات المتاحة:"
echo "   🏠 الصفحة الرئيسية: http://localhost:8080"
echo "   🔐 تسجيل الدخول: http://localhost:8080/login.html"
echo "   🏪 إدارة المخازن: http://localhost:8080/warehouses.html"
echo "   💰 مبيعات المخزن: http://localhost:8080/warehouse-sales.html"
echo "   📊 تقارير المخازن: http://localhost:8080/warehouse-reports.html"
echo ""
echo "🔐 بيانات الدخول التجريبية:"
echo "   📧 البريد: admin@pharmacy-erp.com"
echo "   🔑 كلمة المرور: password123"
echo ""
echo "⚠️  لإيقاف الخادم، اضغط Ctrl+C"
echo ""

# إبقاء السكريبت يعمل
trap "echo ''; echo '🛑 إيقاف الخادم...'; kill $SERVER_PID 2>/dev/null; echo '✅ تم إيقاف الخادم بنجاح'; exit" INT

# انتظار إشارة الإيقاف
wait $SERVER_PID
