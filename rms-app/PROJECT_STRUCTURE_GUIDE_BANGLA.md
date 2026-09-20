# RestaurantOS — Complete Project Structure Guide (বাংলায়)

## Laravel প্রজেক্ট (React নয়)

এই প্রজেক্ট **Laravel 10 + Blade**। আলাদা `src/` React অ্যাপ নেই।  
UI = `resources/views/**/*.blade.php`  
Business logic = `app/Http/Controllers` + `app/Models`  
URL = `routes/web.php`

---

## Root Level (মূল ফোল্ডার)

```
rms-app/
├── app/                 # PHP application code (MVC)
├── bootstrap/           # Laravel boot
├── config/              # app, database, restaurant, sslcommerz, permission
├── database/
│   ├── migrations/      # টেবিল স্কিমা
│   └── seeders/         # ডেমো ইউজার, মেনু, স্টক
├── public/              # ওয়েব রুট (index.php, images/dishes)
├── resources/
│   ├── css/             # Vite CSS
│   ├── js/              # ছোট JS (order create ইত্যাদি)
│   └── views/           # ★ সব Blade UI
├── routes/web.php       # ★ সব ওয়েব রাউট
├── storage/             # uploads, logs, cache
├── tests/               # Feature tests
├── artisan              # CLI
├── composer.json        # PHP packages
├── package.json         # Vite/npm
├── .env                 # লোকাল কনফিগ (গিটে দেবেন না)
├── START_GUIDE.md       # কীভাবে চালাবেন
├── DEFENSE.md           # ডিফেন্স নোট
├── SIMPLIFIED_WORKFLOW.md
├── PROJECT_STRUCTURE_GUIDE_BANGLA.md   # এই ফাইল
└── VISUAL_UI_MAPPING_BANGLA.md
```

---

## app/ — কোথায় কী আছে

### Controllers

```
app/Http/Controllers/
├── Admin/
│   ├── AuthController.php          # স্টাফ লগইন / লগআউট
│   ├── DashboardController.php     # অ্যাডমিন ড্যাশবোর্ড
│   ├── OrderController.php         # ডাইন-ইন অর্ডার, পেমেন্ট, অ্যাপ্রুভ
│   ├── KitchenController.php       # KDS + স্টক ডিডাক্ট
│   ├── MenuController.php          # মেনু CRUD + availability
│   ├── InventoryController.php     # স্টক
│   ├── TableController.php         # টেবিল
│   ├── ReservationController.php   # রিজার্ভেশন কনফার্ম
│   ├── ReportController.php        # রিপোর্ট
│   ├── CustomerController.php
│   ├── StaffController.php         # অ্যাডমিন: স্টাফ
│   ├── SiteSettingsController.php  # ব্র্যান্ডিং
│   ├── SettingsController.php      # প্রোফাইল/পাসওয়ার্ড
│   └── NotificationController.php
├── Website/
│   ├── HomeController.php          # Home, About, Menu, Contact
│   ├── CustomerAuthController.php  # কাস্টমার লগইন/রেজিস্টার
│   ├── CustomerCartController.php  # সেশন কার্ট
│   ├── CustomerOrderController.php # অনলাইন অর্ডার + ক্যান্সেল/রিফান্ড
│   └── ReservationController.php   # পাবলিক বুকিং + available API
└── SslCommerzPaymentController.php # অনলাইন পেমেন্ট স্যান্ডবক্স
```

**এডিট গাইড:** নতুন পেজ = নতুন method কন্ট্রোলারে + `routes/web.php` + Blade।

### Middleware (গুরুত্বপূর্ণ)

```
app/Http/Middleware/
├── RoleMiddleware.php       # ★ role:chef, role:customer ইত্যাদি
├── Authenticate.php         # লগইন ছাড়া ব্লক
├── VerifyCsrfToken.php      # SSLCommerz callback except
└── RedirectIfAuthenticated.php
```

Alias রেজিস্টার: `app/Http/Kernel.php`

### Models (ডাটাবেস টেবিল)

```
app/Models/
├── User.php
├── Menu.php              # available_servings, maxOrderableQuantity, image_url
├── MenuIngredient.php    # রেসিপি
├── Inventory.php
├── Order.php             # status, cancel/refund helpers
├── OrderItem.php
├── Table.php
├── Reservation.php       # availableTables, bookAtomically, phone regex
├── PaymentTransaction.php
└── SiteSettings.php
```

### অন্যান্য

```
app/Helpers/SiteHelper.php     # সাইট নাম/লোগো
app/Library/SslCommerz/        # গেটওয়ে লাইব্রেরি
app/Providers/AppServiceProvider.php  # সব ভিউতে $site শেয়ার
```

---

## resources/views/ — UI কোথায়

### পাবলিক ওয়েবসাইট

```
resources/views/website/
├── layouts/app.blade.php          # ওয়েবসাইট লেআউট + CSS
├── index.blade.php                # হোম
├── pages/
│   ├── menu.blade.php             # লাইভ মেনু
│   ├── about.blade.php
│   └── contact.blade.php          # ★ রিজার্ভেশন ফর্ম
├── sections/ hero, features, cta
├── partials/
│   ├── header.blade.php           # ন্যাভ (Admin Panel লিংক)
│   ├── footer.blade.php
│   ├── theme.blade.php
│   ├── responsive.blade.php
│   ├── refund-policy.blade.php
│   └── home-scripts.blade.php
└── customer/
    ├── auth/login.blade.php       # Login (কাস্টমার)
    ├── auth/register.blade.php
    ├── cart.blade.php
    ├── orders.blade.php           # স্ট্যাটাস স্টেপার
    └── account.blade.php
```

### Admin Panel

```
resources/views/admin/
├── auth/login.blade.php           # Admin Panel লগইন
├── dashboard/index.blade.php
├── orders/ index, create, edit, receipt
├── kitchen/index.blade.php + partials/tickets
├── menu/ index, create, edit
├── inventory/index.blade.php
├── tables/index.blade.php
├── reservations/index.blade.php
├── reports/index.blade.php
├── customers/, staff/, notifications/
├── settings/, site-settings/
├── layouts/styles.blade.php       # অ্যাডমিন CSS টোকেন
└── partials/
    ├── sidebar.blade.php
    └── top-navbar.blade.php
```

### শেয়ার্ড অ্যাডমিন শেল

```
resources/views/layouts/
├── app.blade.php                  # Admin Panel HTML shell
└── partials/ sidebar, styles, alerts, top-navbar
```

### পেমেন্ট

```
resources/views/sslcommerz/
├── forward.blade.php              # অর্ডার → গেটওয়ে অটো-সাবমিট
└── exampleEasycheckout.blade.php
```

---

## database/

```
database/migrations/     # users, menus, orders, inventories, reservations…
database/seeders/
├── DatabaseSeeder.php   # মেনু + ইউজার + ইনভেন্টরি
├── BangladeshMenuSeeder.php
└── MenuRecipeSeeder.php
```

নতুন কলাম = নতুন migration → `php artisan migrate`

---

## config/ (মনে রাখার মতো)

| ফাইল | কাজ |
|------|-----|
| `app.php` | `timezone` = Asia/Dhaka |
| `restaurant.php` | ব্র্যান্ড টেক্সট, max qty 20, refund %, delivery=in_house |
| `sslcommerz.php` | স্যান্ডবক্স স্টোর |
| `permission.php` | Spatie |
| `database.php` | MySQL |

---

## routes/web.php — রাউট ম্যাপ (সংক্ষেপ)

| URL | কে | Controller |
|-----|----|------------|
| `/`, `/our-menu`, `/about`, `/contact` | পাবলিক | HomeController |
| `/reservations/available` | পাবলিক JSON | ReservationController@available |
| `POST /reservations` | পাবলিক | ReservationController@store |
| `/login` | স্টাফ | AuthController |
| `/customer/login`, `/customer/register` | গেস্ট | CustomerAuthController |
| `/customer/cart`, `/customer/orders` | customer | Cart / Order controllers |
| `/dashboard` | স্টাফ | DashboardController |
| `/orders/*` | manager,cashier | OrderController |
| `/kitchen` | chef | KitchenController |
| `/menu` | manager,chef | MenuController |
| `/inventory` | manager,chef | InventoryController |
| `/reservations` (admin list) | manager | Admin\ReservationController |
| `/sslcommerz/*` | গেটওয়ে | SslCommerzPaymentController |

---

## UI এডিট করার ধাপ (Laravel Blade)

1. ব্রাউজারে পেজ খুলুন (যেমন `/our-menu`)  
2. F12 → Elements → যে টেক্সট/বাটন বদলাবেন সিলেক্ট করুন  
3. VS Code এ **Ctrl+Shift+F** → সেই টেক্সট খুঁজুন → `.blade.php` ফাইল পাবেন  
4. লজিক বদলাতে হলে একই নামের **Controller** খুলুন  
5. URL বদলাতে হলে **`routes/web.php`**  

### দ্রুত ম্যাপ

| স্ক্রিন | Blade | Controller |
|---------|-------|------------|
| হোম | `website/index.blade.php` | HomeController@index |
| মেনু | `website/pages/menu.blade.php` | HomeController@menu |
| রিজার্ভ | `website/pages/contact.blade.php` | ReservationController |
| কার্ট | `website/customer/cart.blade.php` | CustomerCartController |
| মাই অর্ডার | `website/customer/orders.blade.php` | CustomerOrderController |
| অ্যাডমিন লগইন | `admin/auth/login.blade.php` | AuthController |
| ড্যাশবোর্ড | `admin/dashboard/index.blade.php` | DashboardController |
| অর্ডার তৈরি | `admin/orders/create.blade.php` | OrderController + `js/admin/orders/create.js` |
| কিচেন | `admin/kitchen/index.blade.php` | KitchenController |
| মেনু অ্যাডমিন | `admin/menu/index.blade.php` | MenuController |
| সাইডবার | `admin/partials/sidebar.blade.php` | — |

---

## চালানোর কমান্ড

```bash
cd d:\WebProject\rms-app
composer install
copy .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
php artisan serve --host=127.0.0.1 --port=8010
```

(ঐচ্ছিক) Vite: `npm install` && `npm run build` অথবা `npm run dev`

---

## Summary — মনে রাখুন

- **URL** → `routes/web.php`  
- **Logic** → `app/Http/Controllers/...`  
- **DB shape** → `database/migrations` + `app/Models`  
- **UI** → `resources/views/...`  
- **রোল** → `RoleMiddleware`  
- **কনফিগ ব্যবসায়িক রুল** → `config/restaurant.php`  

React/Redux এই প্রজেক্টে নেই — ডিফেন্সে বলবেন: **“Server-rendered Blade with Laravel MVC.”**

---

## Twenty Important Defense Questions (সংক্ষিপ্ত উত্তর)

**Line-number সহ পূর্ণ উত্তর:** `SIMPLIFIED_WORKFLOW.md` → “Twenty Important Defense Board Questions” (quick map table + Q1–Q20)।  
কোডে খুঁজতে: VS Code Search → `DEFENSE Q`

1. প্রজেক্ট কী? → Laravel RMS — `routes/web.php` **1–154**  
2. কেন Laravel? → MVC / middleware / Eloquent / Blade  
3. Route কোথায়? → `routes/web.php` only  
4. MVC উদাহরণ? → `HomeController@menu` **28–37** + `Menu` + `menu.blade.php`  
5. Customer কিচেন? → না — `RoleMiddleware` **23–44**; kitchen route **98–100**  
6. Cart টেবিল? → না — session `CustomerCartController` **151–165**  
7. Oversell? → `maxOrderableQuantity` **102–108** + checkout `lockForUpdate` ~**118**  
8. Stock কখন? → `KitchenController@updateStatus` **38–177** (`inventory_deducted_at`)  
9. Recipe? → `Menu` servings **66–90**  
10. Double-booking? → `Reservation::bookAtomically` **178–218**  
11. ৬ জনের টেবিল? → `availableTables` **124–148** + route **40**  
12. Order status? → `Order` track index **114–125**; CSS 5-col stepper  
13. Payment? → COD/SSL — store **193–205**; SSL routes **134–149**  
14. Refund? → `refundBreakdown` **159–188**; config **140–145**  
15. Pathao? → নেই — `config/restaurant.php` delivery **125–134**  
16. Timezone? → `config/app.php` **67–73** Asia/Dhaka  
17. Phone? → `PHONE_REGEX` line **25**; store validate **69**  
18. Admin vs Website? → `views/admin` vs `views/website` + role middleware  
19. নতুন ফিচার? → migration→model→controller→`web.php`→blade  
20. Strongest? → recipe stock + locked checkout + one-time deduct + atomic booking  

ডিফেন্সে কোড দেখাতে: Search `DEFENSE Q7` / `DEFENSE Q10` ইত্যাদি।
