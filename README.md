# 📅 BookingApp — نظام حجز المواعيد
**Laravel 12 | Tailwind CSS | MySQL**

---

## 🗂️ هيكل المشروع

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── BookingController.php       ← حجوزات العميل
│   │   ├── ServiceController.php       ← عرض الخدمات (public)
│   │   ├── ReviewController.php        ← تقييمات
│   │   ├── PaymentController.php       ← دفع العميل (رفع إثبات التحويل)
│   │   ├── Provider/
│   │   │   ├── DashboardController.php     ← لوحة مزود الخدمة
│   │   │   ├── ServiceController.php       ← إدارة الخدمات
│   │   │   ├── SlotController.php          ← إدارة الأوقات
│   │   │   ├── BookingController.php       ← إدارة الحجوزات
│   │   │   ├── PaymentMethodController.php ← حسابات استلام الدفع
│   │   │   └── PaymentController.php       ← توثيق/رفض الدفعات
│   │   └── Admin/
│   │       └── DashboardController.php ← لوحة الإدارة
│   ├── Middleware/
│   │   └── RoleMiddleware.php          ← حماية المسارات بالأدوار
│   └── Requests/
├── Models/
│   ├── User.php          ← أدوار: admin | provider | customer
│   ├── Service.php       ← الخدمات
│   ├── Slot.php          ← الأوقات المتاحة
│   ├── Booking.php       ← الحجوزات
│   ├── Review.php        ← التقييمات
│   ├── PaymentMethod.php ← حسابات استلام المزود (بنكي/PalPay/محفظة)
│   └── Payment.php       ← سجل الدفع لكل حجز
├── Notifications/
│   ├── BookingCreated.php    ← إشعار عند الحجز
│   ├── BookingConfirmed.php  ← إشعار عند التأكيد
│   ├── BookingCancelled.php  ← إشعار عند الإلغاء
│   ├── PaymentSubmitted.php  ← إشعار المزود برفع إثبات دفع
│   ├── PaymentVerified.php   ← إشعار العميل بتوثيق الدفع
│   └── PaymentRejected.php   ← إشعار العميل برفض الدفع
└── Policies/
    ├── BookingPolicy.php
    └── ServicePolicy.php
```

### الدفع الإلكتروني

لا توجد بوابة دفع إلكترونية حقيقية (API) مربوطة بالمشروع، لذا يعتمد النظام على **دفع يدوي موثّق**:
كل مزود خدمة يضيف من لوحته (`/provider/payment-methods`) حسابات استلامه (تحويل بنكي / PalPay / محفظة جوال)،
والعميل يحوّل المبلغ بنفسه ثم يرفع صورة إثبات + رقم مرجعي من صفحة `/bookings/{id}/payment`،
ومزود الخدمة يراجع الإثبات من `/provider/bookings` ويوثّقه أو يرفضه — ولا يمكن تأكيد الحجز قبل توثيق الدفع (إن كانت الخدمة مدفوعة).

### الهوية البصرية

- العميل: `layouts/app.blade.php` (فاتح، لون أساسي indigo).
- مزود الخدمة: `layouts/provider.blade.php` (Sidebar داكن، لون تمييز emerald).
- الإدارة: `layouts/admin.blade.php` (Sidebar داكن، لون تمييز violet).

---

## 🚀 خطوات التشغيل

### 1. إعداد قاعدة البيانات
في ملف `.env`:
```
DB_DATABASE=booking_app_db
DB_USERNAME=root
DB_PASSWORD=
```

### 2. تشغيل Migrations و Seeder
```bash
php artisan migrate:fresh --seed
```

### 3. إنشاء رابط التخزين
```bash
php artisan storage:link
```

### 4. بناء الواجهة (Tailwind + Font Awesome + خط Tajawal — محليًا بالكامل)
لا يعتمد المشروع على أي CDN خارجي (لا Tailwind CDN، لا Google Fonts، لا jsDelivr) — كل شيء يُبنى محليًا عبر Vite حتى يعمل الستايل بدون إنترنت:
```bash
npm install
npm run build
```
> بعد أي تعديل على Blade/CSS يلزم إعادة `npm run build` (أو تشغيل `npm run dev` أثناء التطوير).

### 5. تشغيل المشروع
```bash
php artisan serve
```

---

## 👥 حسابات تجريبية

تسجيل الدخول يتم برقم الهوية (9 أرقام) وليس بالإيميل.

| الدور    | رقم الهوية | التخصص           | الإيميل                | كلمة المرور |
|----------|------------|-------------------|-------------------------|-------------|
| Admin    | 900000001  | —                 | admin@booking.test      | password    |
| Customer | 900000004  | —                 | customer@booking.test   | password    |
| Provider | 900000005  | أسنان             | dr.halabi@clinic.test   | password    |
| Provider | 900000006  | أطفال             | dr.rana@clinic.test     | password    |
| Provider | 900000007  | طب عام            | dr.khaled@clinic.test   | password    |
| Provider | 900000008  | نساء وتوليد       | dr.heba@clinic.test     | password    |
| Provider | 900000009  | جلدية             | dr.yousef@clinic.test   | password    |
| Provider | 900000010  | عظام              | dr.samer@clinic.test    | password    |

---

## 🗺️ المسارات الرئيسية

| المسار                          | الوصف                    |
|---------------------------------|--------------------------|
| `/services`                     | قائمة الخدمات           |
| `/services/{id}`                | تفاصيل الخدمة + الأوقات |
| `/bookings`                     | حجوزاتي                 |
| `/bookings/slots/{slot}`        | تأكيد الحجز             |
| `/bookings/{id}/payment`        | إتمام الدفع ورفع الإثبات |
| `/provider`                     | لوحة مزود الخدمة (الرئيسية) |
| `/provider/services`            | خدماتي                  |
| `/provider/services/{id}/slots` | إدارة الأوقات           |
| `/provider/bookings`            | حجوزات العملاء ومراجعة الدفعات |
| `/provider/payment-methods`     | إدارة حسابات استلام الدفع |
| `/admin`                        | لوحة الإدارة            |
| `/admin/users`                  | إدارة المستخدمين (إضافة/تعديل/حذف/تغيير دور) |

---

## ✉️ الإشعارات البريدية

لتفعيل البريد الإلكتروني الحقيقي، عدّل `.env`:
```
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_FROM_ADDRESS=noreply@bookingapp.com
MAIL_FROM_NAME="BookingApp"
```

للتطوير، استخدم [Mailtrap](https://mailtrap.io) أو:
```
MAIL_MAILER=log
```

