# RestaurantOS — Visual UI Component Mapping (দৃশ্যমান গাইড)

Laravel Blade স্ক্রিন → ফাইল ম্যাপ।  
ডিফল্ট লোকাল URL: **http://127.0.0.1:8010** (পোর্ট বদলাতে পারে)।

---

## Public Website

### Home — `/`
```
View: resources/views/website/index.blade.php
Layout: website/layouts/app.blade.php
Partials:
├── header.blade.php
├── sections/hero.blade.php
├── sections/features.blade.php
├── featured dishes (index-এ লুপ)
├── sections/cta.blade.php
└── footer.blade.php
Controller: Website\HomeController@index
```

**UI elements:**
- Brand / hero title (`config/restaurant.php` + site_settings)
- Feature cards
- Featured menu images → `Menu::image_url` → `public/images/dishes/`
- CTA → Order / Admin Panel (স্টাফ লগইন থাকলে)

---

### Menu — `/our-menu`
```
View: resources/views/website/pages/menu.blade.php
Controller: HomeController@menu
Scripts: website/partials/home-scripts.blade.php (lightbox, search, nav)
```

**UI elements:**
- Search + category dropdown
- Dish cards: photo, name, price, stock badge, Order button
- Out of stock state
- Dish preview / lightbox (`#imgLightbox`)

---

### About — `/about`
```
View: website/pages/about.blade.php
Controller: HomeController@about
```

---

### Contact + Reservation — `/contact#reserve`
```
View: website/pages/contact.blade.php
Controller: HomeController@contact
Store: Website\ReservationController@store
Available JSON: GET /reservations/available
```

**UI elements:**
- Address / hours cards
- Reserve form:
  - Name, Phone (digits, BD format), Email
  - Date, Time slot, Party size
  - **Available table** dropdown (number · seats · location)
  - Notes + Reserve button
- Upcoming bookings list (logged-in customer)

---

### Customer Login — `/customer/login`
```
View: website/customer/auth/login.blade.php
Controller: CustomerAuthController
Label: "Login" (not Guest Login)
Link to Admin Panel: /login
```

### Customer Register — `/customer/register`
```
View: website/customer/auth/register.blade.php
```

### Cart — `/customer/cart`
```
View: website/customer/cart.blade.php
Controller: CustomerCartController
Checkout: CustomerOrderController@store
```

**UI elements:**
- Line items + qty − / +
- Payment: Cash on Delivery | Pay online (SSLCommerz)
- Checkout / Clear Cart (rectangular buttons)
- Refund policy box → `partials/refund-policy.blade.php`

### My Orders — `/customer/orders`
```
View: website/customer/orders.blade.php
```

**UI elements:**
- Order number, badge, approval text
- **Horizontal status stepper** (Pending → … → Delivered) — one row
- Cancel button (if allowed) + policy hint
- Payment / refund status text

### Account — `/customer/account`
```
View: website/customer/account.blade.php
```

---

## Admin Panel

### Staff Login — `/login`
```
View: admin/auth/login.blade.php
Controller: Admin\AuthController
Title: "Admin Panel"
```

### Dashboard — `/dashboard`
```
View: admin/dashboard/index.blade.php
Controller: Admin\DashboardController
Layout: layouts/app.blade.php
Sidebar: admin/partials/sidebar.blade.php
Top bar: admin/partials/top-navbar.blade.php
Styles: admin/layouts/styles.blade.php
```

**UI elements:**
- Command center / ops banner
- Stat cards: revenue, orders, tables, low stock
- Active / recent orders tables
- Chef variant: pending / preparing / ready counts

### Orders list — `/orders`
```
View: admin/orders/index.blade.php
Controller: OrderController@index
```

**UI:** filters (status, payment, date), table, New Order, actions

### Create Order — `/orders/create`
```
View: admin/orders/create.blade.php
JS: resources/js/admin/orders/create.js
```

**UI:**
- Table select
- Menu cards with image + qty (max 20 / stock)
- Live total, cash paid amount, payment method
- Stock estimation panel

### Order edit / payment / receipt
```
edit.blade.php
receipt.blade.php
```

### Kitchen Display — `/kitchen`
```
View: admin/kitchen/index.blade.php
Tickets: admin/kitchen/partials/tickets.blade.php
Controller: KitchenController
```

**UI:** ticket cards, Start Cooking, Order Ready (AJAX refresh)

### Menu (admin) — `/menu`
```
admin/menu/index.blade.php   # cards + image
admin/menu/create.blade.php
admin/menu/edit.blade.php    # image preview + ingredients
```

### Inventory — `/inventory`
```
admin/inventory/index.blade.php
```

### Tables — `/tables`
```
admin/tables/index.blade.php
```

**UI:** table cards by status (available / occupied / reserved / maintenance)

### Reservations (admin) — `/reservations`
```
admin/reservations/index.blade.php
Controller: Admin\ReservationController
```

**UI:** guest, phone, slot, party, assigned table, status update

### Reports — `/reports`
```
admin/reports/index.blade.php + Chart.js
```

### Customers / Staff / Notifications / Settings / Site settings
```
admin/customers/index.blade.php
admin/staff/index.blade.php
admin/notifications/index.blade.php
admin/settings/index.blade.php
admin/site-settings/index.blade.php
```

---

## Layout chrome (সব Admin পেজে)

### Sidebar
```
File: resources/views/admin/partials/sidebar.blade.php
(also layouts/partials/sidebar.blade.php include chain)

UI:
├── Brand + "Admin panel"
├── Nav links (role-filtered)
└── User avatar / role / logout
```

### Top navbar
```
File: admin/partials/top-navbar.blade.php
UI: page title, View site, notifications, settings, mobile menu toggle
```

### Website header
```
File: website/partials/header.blade.php
UI: logo, Home/About/Menu/Reserve/Contact, Cart, Sign in, Admin Panel
```

---

## CSS / Theme কোথায় বদলাবেন

| কি বদলাবেন | ফাইল |
|------------|------|
| ওয়েবসাইট রং/টাইপো | `website/partials/theme.blade.php` |
| ওয়েবসাইট বড় লেআউট CSS | `website/layouts/app.blade.php` `<style>` |
| মোবাইল ব্রেকপয়েন্ট | `website/partials/responsive.blade.php` |
| Order stepper এক লাইন | `layouts/app` + `theme` + `responsive` + `stacking` (`.status-stepper`) |
| Admin card / sidebar | `admin/layouts/styles.blade.php` |
| ব্র্যান্ড ডিফল্ট টেক্সট | `config/restaurant.php` |

---

## ছবি কোথায়

```
public/images/dishes/*.jpg     # মেনু ফটো (slug = নাম)
storage/app/public/menu-images # আপলোড কপি
Menu::image_url                # URL রেজলভার
```

---

## কিভাবে Specific Element খুঁজবেন

### Method 1 — Browser Inspect
1. F12 → Elements  
2. Pointer দিয়ে UI সিলেক্ট  
3. Visible text / class কপি  
4. VS Code `Ctrl+Shift+F` → `.blade.php`  

### Method 2 — Route থেকে
1. URL জানেন (যেমন `/kitchen`)  
2. `routes/web.php` এ খুঁজুন → Controller  
3. Controller `return view('...')` → Blade ফাইল  

### Method 3 — DEFENSE ট্যাগ
কোডবেসে সার্চ: `DEFENSE:` — viva সেকশন মার্ক করা আছে।

---

## Quick Edit Examples

### ওয়েবসাইট ন্যাভে "Admin Panel" টেক্সট
```
File: resources/views/website/partials/header.blade.php
→ Admin Panel লিংক
```

### রিজার্ভেশন ফোনে শুধু সংখ্যা
```
File: website/pages/contact.blade.php
→ #reservePhone + JS replace(/[^\d+]/g,'')
Server: Reservation::PHONE_REGEX
```

### স্ট্যাটাস স্টেপার এক লাইনে রাখা
```
CSS: .status-stepper { grid-template-columns: repeat(5, minmax(0,1fr)) !important; }
Files: website/layouts/app.blade.php, partials/theme, responsive, stacking
```

### প্রতি আইটেমে max 20
```
Config: config/restaurant.php → max_item_quantity
Logic: Menu::maxOrderableQuantity()
Enforce: CustomerCartController, CustomerOrderController, Admin OrderController
```

---

## Demo credentials (seed পর)

| Role | URL | Email (typical seed) |
|------|-----|----------------------|
| Admin | `/login` | admin@restaurant.com / password |
| Customer | `/customer/login` | register নতুন অথবা seed guest |

---

## Twenty Defense Questions — স্ক্রিন থেকে কোডে যাবেন যেভাবে

| # | প্রশ্ন | দেখান / বলুন |
|---|--------|----------------|
| 1 | প্রজেক্ট কী? | Home + Admin Dashboard খুলে দেখান |
| 2 | Laravel কেন? | `routes/web.php` + Controller + Blade চেইন |
| 3 | Route কোথায়? | `routes/web.php` |
| 4 | MVC? | `/our-menu` → HomeController → Menu → menu.blade.php |
| 5 | Role? | Customer দিয়ে `/kitchen` → 403 |
| 6 | Cart DB? | Session; `CustomerCartController` |
| 7 | Oversell? | Checkout transaction + lock |
| 8 | Stock cut? | Kitchen Start Cooking |
| 9 | Recipe? | Menu Edit → ingredients |
| 10 | Double book? | `Reservation::bookAtomically` |
| 11 | Table for 6? | Contact → available dropdown |
| 12 | Tracker UI? | My Orders horizontal stepper |
| 13 | SSLCommerz? | Cart payment select + forward.blade.php |
| 14 | Refund? | refund-policy + Order::refundBreakdown |
| 15 | Courier API? | নেই; `config/restaurant.php` delivery |
| 16 | Timezone? | Asia/Dhaka timestamps Admin এ |
| 17 | Phone? | Reserve form validation |
| 18 | Admin vs Web? | `views/admin` vs `views/website` |
| 19 | New feature? | migration→model→controller→route→blade |
| 20 | Strongest? | Stock integrity + locked reservation + RBAC |

পূর্ণ উত্তরের টেক্সট: **`SIMPLIFIED_WORKFLOW.md` → “Twenty Important Defense Board Questions”**
