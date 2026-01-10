# تحليلات + (Analytics Plus)

[![GitHub](https://img.shields.io/badge/GitHub-Repository-blue)](https://github.com/peter-tharwat/analytics-plus)
[![Laravel](https://img.shields.io/badge/Laravel-12.x-red)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-blue)](https://php.net)

نظام تحليلات متقدم وشامل مبني على Laravel لتتبع وإدارة المواقع الإلكترونية والإعلانات.

**المستودع:** [https://github.com/peter-tharwat/analytics-plus.git](https://github.com/peter-tharwat/analytics-plus.git)

## 📸 لقطات الشاشة

### لوحة التحكم الرئيسية
![Dashboard](https://raw.githubusercontent.com/peter-tharwat/analytics-plus/main/public/images/screenshots2/1.png)

### إحصائيات الموقع التفصيلية
![Site Statistics](https://raw.githubusercontent.com/peter-tharwat/analytics-plus/main/public/images/screenshots2/2.png)

### إدارة المواقع
![Sites Management](https://raw.githubusercontent.com/peter-tharwat/analytics-plus/main/public/images/screenshots2/3.png)

### كود التتبع
![Tracking Code](https://raw.githubusercontent.com/peter-tharwat/analytics-plus/main/public/images/screenshots2/4.png)

### إحصائيات الإعلانات
![Advertisement Statistics](https://raw.githubusercontent.com/peter-tharwat/analytics-plus/main/public/images/screenshots2/5.png)

### إدارة الإعلانات
![Advertisement Management](https://raw.githubusercontent.com/peter-tharwat/analytics-plus/main/public/images/screenshots2/6.png)

### تفاصيل الزيارة
![Visit Details](https://raw.githubusercontent.com/peter-tharwat/analytics-plus/main/public/images/screenshots2/7.png)

### رفع الملفات
![File Upload](https://raw.githubusercontent.com/peter-tharwat/analytics-plus/main/public/images/screenshots2/8.png)

### تعديل الإعلان
![Edit Advertisement](https://raw.githubusercontent.com/peter-tharwat/analytics-plus/main/public/images/screenshots2/9.png)

## 📋 نظرة عامة

تحليلات + هو نظام متكامل يوفر:
- **تحليلات المواقع**: تتبع شامل لزوار المواقع مع إحصائيات مفصلة
- **إدارة الإعلانات**: نظام متقدم لإدارة وتتبع الإعلانات مع إحصائيات مفصلة
- **لوحة تحكم احترافية**: واجهة عربية كاملة مع تصميم عصري
- **إدارة المحتوى**: مقالات، صفحات، قوائم، وأكثر
- **نظام المستخدمين**: إدارة المستخدمين والصلاحيات

## ✨ المميزات الرئيسية

### 📊 تحليلات المواقع
- تتبع الزوار في الوقت الفعلي
- إحصائيات مفصلة (الصفحات، الدول، الأجهزة، المتصفحات)
- رسوم بيانية تفاعلية باستخدام Chart.js
- تتبع الجلسات والزيارات
- تحليل مصادر الزيارات
- تتبع URL Patterns مخصصة

### 📢 نظام الإعلانات
- أنواع إعلانات متعددة:
  - داخل المحتوى (In Content)
  - منبثق من الأسفل/الأعلى (Pop from Bottom/Top)
  - شاشة كاملة (Interstitial)
- استهداف متقدم:
  - حسب الموقع
  - حسب نوع الجهاز
  - حسب الدولة
  - حسب URL Patterns
  - حسب CSS Selectors
- إحصائيات مفصلة لكل إعلان:
  - عدد العروض والضغطات
  - نسبة الضغط (CTR)
  - أعلى الصفحات والمواقع
  - أعلى الدول والأجهزة
  - رسوم بيانية تفاعلية

### 🎨 لوحة التحكم
- تصميم عصري ومتجاوب بالكامل
- دعم اللغة العربية بالكامل
- خط Baloo Bhaijaan 2
- أيقونات Font Awesome Pro
- إشعارات في الوقت الفعلي
- نظام تنبيهات ذكي

### 📝 إدارة المحتوى
- نظام مقالات متكامل مع أقسام
- صفحات مخصصة مع محرر متقدم
- نظام قوائم قابل للتخصيص
- نظام تحويل الروابط (Redirections)
- إدارة الملفات والصور

### 👥 إدارة المستخدمين
- نظام صلاحيات متقدم (Spatie Permissions)
- إدارة الأدوار
- ملفات شخصية مع صور
- نظام إشعارات متكامل

### 🔒 الأمان والحماية
- نظام Rate Limiting
- حماية من الهجمات
- تتبع الأخطاء والتنبيهات
- دعم Cloudflare
- نظام تسجيل دخول آمن

## 🛠️ المتطلبات

- PHP >= 8.2.0
- Composer
- Node.js & NPM
- MySQL/MariaDB
- Redis (اختياري للأداء)
- ImageMagick PHP Extension

## 📦 التثبيت

### 1. تثبيت المتطلبات

```bash
# تثبيت ImageMagick
sudo apt-get install php-imagick

# تثبيت Composer dependencies
composer install

# تثبيت NPM dependencies
npm install
```

### 2. إعداد البيئة

```bash
# نسخ ملف البيئة
cp .env.example .env

# توليد مفتاح التطبيق
php artisan key:generate

# ربط مجلد التخزين
php artisan storage:link
```

### 3. إعداد قاعدة البيانات

قم بتعديل ملف `.env` وإضافة بيانات الاتصال بقاعدة البيانات:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=analytics
DB_USERNAME=root
DB_PASSWORD=
```

ثم قم بتشغيل Migrations:

```bash
php artisan migrate:fresh
php artisan db:seed
```

### 4. بناء الأصول

```bash
# للتطوير
npm run dev

# للإنتاج
npm run build
```

### 5. تشغيل المهام المجدولة والطوابير

```bash
# تشغيل الطوابير
php artisan queue:work

# تشغيل المهام المجدولة
php artisan schedule:run
```

أو استخدام Supervisor لإدارة هذه العمليات تلقائياً.

## 🔑 بيانات الدخول الافتراضية

```
البريد الإلكتروني: admin@admin.com
كلمة المرور: password
```

**⚠️ مهم**: قم بتغيير كلمة المرور فوراً بعد التثبيت!

## 📚 الاستخدام

### إضافة موقع جديد

1. انتقل إلى `/admin/analytics`
2. انقر على "إضافة موقع جديد"
3. أدخل بيانات الموقع
4. انسخ كود التتبع
5. أضف الكود إلى موقعك

### كود التتبع

أضف الكود التالي قبل إغلاق `</body>` في موقعك:

```html
<script>
(function() {
    var script = document.createElement('script');
    script.src = 'https://your-domain.com/js/analytics.js';
    script.setAttribute('data-site-key', 'YOUR_SITE_KEY');
    script.async = true;
    document.body.appendChild(script);
})();
</script>
```

### إضافة إعلان

1. انتقل إلى `/admin/advertisements`
2. انقر على "إضافة إعلان جديد"
3. اختر نوع الإعلان
4. حدد المواقع المستهدفة
5. أضف المحتوى
6. حدد شروط العرض (الدولة، الجهاز، URL Patterns)
7. احفظ الإعلان

## 🏗️ البنية التقنية

### التقنيات المستخدمة

- **Backend**: Laravel 12
- **Frontend**: Bootstrap 5, jQuery, Chart.js
- **Database**: MySQL/MariaDB
- **Caching**: Redis (اختياري)
- **Queue**: Laravel Queue
- **Real-time**: Livewire 3
- **Permissions**: Spatie Laravel Permission
- **Media**: Spatie Media Library

### البنية الأساسية

```
analytics/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/          # API Controllers
│   │   │   └── Backend/      # Admin Controllers
│   │   └── Middleware/      # Custom Middleware
│   ├── Models/               # Eloquent Models
│   ├── Jobs/                 # Queue Jobs
│   ├── Livewire/             # Livewire Components
│   └── Helpers/              # Helper Classes
├── database/
│   ├── migrations/           # Database Migrations
│   └── seeders/              # Database Seeders
├── resources/
│   ├── views/                # Blade Templates
│   └── js/                   # JavaScript Files
├── public/
│   └── js/
│       └── analytics.js       # Analytics Tracking Script
└── routes/
    ├── web.php               # Web Routes
    └── api.php               # API Routes
```

## 🔌 API Endpoints

### Analytics Tracking

```
POST /api/analytics/track
```

### Advertisement Management

```
POST /api/ads/get              # الحصول على الإعلانات المطابقة
POST /api/ads/impression       # تتبع عرض الإعلان
POST /api/ads/click            # تتبع ضغطة الإعلان
```

## 🎨 التخصيص

### الألوان الرئيسية

يمكن تخصيص الألوان من خلال ملفات CSS أو متغيرات CSS:

```css
:root {
    --primary: #0194fe;
    --primary-dark: #0178cc;
    --color-2: #7b60fb;
}
```

### الخطوط

النظام يستخدم خط **Baloo Bhaijaan 2** كخط رئيسي.

### الإشعارات

```php
// إشعار في لوحة التحكم
notify()->success('تم الحفظ بنجاح', 'نجاح');

// إشعار للمستخدم
(new \MainHelper)->notify_user([
    'user_id' => 1,
    'message' => 'رسالة الإشعار',
    'url' => '/admin/dashboard',
    'methods' => ['database', 'mail']
]);
```

## 📊 الإحصائيات المتاحة

### إحصائيات المواقع
- إجمالي الزيارات
- الزوار الفريدين
- أعلى الصفحات
- أعلى الدول
- أعلى الأجهزة
- أعلى المتصفحات
- أعلى أنظمة التشغيل
- مصادر الزيارات
- الزوار الحاليين

### إحصائيات الإعلانات
- إجمالي العروض
- إجمالي الضغطات
- نسبة الضغط (CTR)
- الضغطات آخر 30 دقيقة
- العروض والضغطات آخر 30 يوم
- أعلى الصفحات حسب الضغطات
- أعلى المواقع حسب الضغطات/العروض
- أعلى الدول حسب الضغطات/العروض
- أعلى الأجهزة
- أعلى المتصفحات
- أعلى أنظمة التشغيل

## 🔧 الصيانة

### تنظيف البيانات القديمة

```bash
# تنظيف بيانات التتبع القديمة (أكثر من 90 يوم)
php artisan analytics:cleanup
```

### تحسين الأداء

- استخدم Redis للتخزين المؤقت
- قم بتفعيل Queue للمهام الثقيلة
- استخدم CDN للملفات الثابتة
- قم بضغط الصور قبل الرفع

## 🐛 استكشاف الأخطاء

### المشاكل الشائعة

1. **لا تظهر الإحصائيات**
   - تأكد من تشغيل `queue:work`
   - تحقق من إعدادات قاعدة البيانات

2. **الإعلانات لا تظهر**
   - تحقق من `is_active` للإعلان
   - تأكد من تطابق شروط الاستهداف
   - تحقق من كود التتبع في الموقع

3. **مشاكل في الصور**
   - تأكد من تثبيت ImageMagick
   - تحقق من صلاحيات مجلد `storage`

## 📝 التطوير

### إضافة ميزة جديدة

1. أنشئ Migration للجداول الجديدة
2. أنشئ Model
3. أنشئ Controller
4. أضف Routes
5. أنشئ Views
6. أضف Tests

### الاختبار

```bash
php artisan test
```

## 📄 الترخيص

هذا المشروع مرخص تحت رخصة MIT.

## 🤝 المساهمة

نرحب بمساهماتكم! يرجى فتح Issue أو Pull Request.

## 📞 الدعم

للحصول على الدعم، يرجى فتح Issue في المستودع.

## 🔄 التحديثات

### الإصدار الحالي: 1.0.0

- نظام تحليلات متكامل
- نظام إعلانات متقدم
- لوحة تحكم عربية كاملة
- إحصائيات مفصلة ورسوم بيانية

---

**صُنع بـ ❤️ باستخدام Laravel**
