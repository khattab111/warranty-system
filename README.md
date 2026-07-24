# نظام إدارة ضمان الهواتف

نظام متكامل لإدارة ضمان الهواتف باستخدام Laravel و Filament. يتيح إنشاء رموز QR فريدة وإدارة ضمانات الهواتف وعرض معلومات الضمان للعملاء.

## المتطلبات

- PHP 8.3+
- Composer 2.x
- Node.js 18+
- MySQL 8.0+
- Extensions: BCMath, Ctype, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML, GD, Zip

## التقنيات المستخدمة

- **Laravel 13** - إطار العمل الرئيسي
- **Filament 5** - لوحة الإدارة
- **endroid/qr-code** - توليد رموز QR
- **html5-qrcode** - مسح رموز QR من الكاميرا
- **barryvdh/laravel-dompdf** - تصدير PDF
- **Tailwind CSS** - تصميم الواجهات
- **Vite** - إدارة ملفات JavaScript و CSS

## التثبيت

```bash
# 1. نسخ المشروع
git clone <repository-url> warranty-system
cd warranty-system

# 2. تثبيت مكتبات PHP
composer install

# 3. تثبيت مكتبات JavaScript
npm install

# 4. نسخ ملف البيئة
cp .env.example .env

# 5. توليد مفتاح التطبيق
php artisan key:generate

# 6. إعداد قاعدة البيانات
# قم بتعديل ملف .env وأضف معلومات اتصال MySQL:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=warranty_system
# DB_USERNAME=root
# DB_PASSWORD=

# 7. إنشاء قاعدة البيانات
mysql -u root -p -e "CREATE DATABASE warranty_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 8. تشغيل الترحيلات
php artisan migrate

# 9. تشغيل البذار التجريبي
php artisan db:seed

# 10. بناء ملفات الواجهة
npm run build

# 11. تشغيل الخادم
php artisan serve
```

## بيانات الدخول التجريبية

| البريد الإلكتروني | كلمة المرور | الصلاحية |
|---|---|---|
| admin@example.com | password | مدير |

**يرجى تغيير كلمة المرور فوراً في بيئة الإنتاج.**

## هيكل قاعدة البيانات

### جدول `warranties`

| الحقل | النوع | الوصف |
|---|---|---|
| id | bigint | المفتاح الأساسي |
| public_token | uuid | رمز فريد للرابط العام (فريد) |
| device_type | varchar(150) | نوع الجهاز |
| imei | varchar(15) | رقم IMEI (فريد، يمكن أن يكون فارغاً) |
| warranty_expires_at | timestamp | تاريخ انتهاء الضمان |
| activated_at | timestamp | تاريخ التفعيل |
| created_at | timestamp | تاريخ الإنشاء |
| updated_at | timestamp | تاريخ التحديث |

## الصفحات

### لوحة الإدارة (`/admin`)

- **لوحة التحكم** - الصفحة الرئيسية
- **الضمانات** - إدارة وعرض جميع الضمانات مع إمكانية التصفية والبحث
- **توليد رموز QR** - إنشاء رموز QR جديدة (1-500 في المرة)
- **مسح QR Code** - مسح رموز QR باستخدام الكاميرا لتفعيل الضمانات

### الصفحة العامة (`/warranty/{public_token}`)

صفحة متجاوبة باللغة العربية تعرض معلومات ضمان الجهاز مع عداد تنازلي.

## الميزات

- توليد رموز QR فريدة وغير قابلة للتخمين (UUID)
- مسح QR Code من كاميرا الهاتف داخل لوحة الإدارة
- تفعيل الضمانات وإدارة معلومات الأجهزة
- عرض العداد التنازلي المباشر لانتهاء الضمان
- إخفاء رقم IMEI جزئياً في الصفحة العامة
- تصدير رموز QR بصيغ ZIP و PDF
- طباعة معلومات الضمان
- نظام تصفية متقدم للضمانات
- دعم كامل للغة العربية (RTL)
- صفحات متجاوبة مع جميع الأجهزة

## الأمان

- جميع صفحات الإدارة محمية بتسجيل الدخول
- استخدام UUID بدلاً من ID التسلسلي في الروابط العامة
- إخفاء معظم رقم IMEI في الصفحة العامة
- منع إدخال HTML و JavaScript في الحقول النصية
- استخدام CSRF Protection
- Rate Limiting للصفحة العامة
- Database Transactions لعمليات الإنشاء المجمعة
- تسجيل جميع عمليات التفعيل والتعديل في السجلات

## الاختبارات

```bash
php artisan test
```

## التوسع المستقبلي

- إضافة `spatie/laravel-permission` لإدارة الصلاحيات المتقدمة
- إضافة أدوار: Super Admin, Admin, Employee
- إضافة إشعارات البريد الإلكتروني
- إضافة API للخدمات الخارجية

## الترخيص

هذا المشروع مرخص تحت [MIT license](https://opensource.org/licenses/MIT).
