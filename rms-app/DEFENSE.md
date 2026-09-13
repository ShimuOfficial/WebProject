# Spice Garden RMS — Practicum / Defense Guide

Use this file during viva. Search the codebase for **`DEFENSE:`** to jump to the exact section a teacher asks about.

- Board asks **“ei logic koi?”** → [Section 5](#5-feature-map--logic-koi)
- Board asks **“notun similar feature kivabe add korbo?”** → [Section 6](#6-how-to-implement-a-similar-new-feature)
- Board asks **20 common questions** → [Section 11](#11-twenty-important-defense-questions)

---

## 1. One-minute project pitch

**Spice Garden RMS** is a role-based **Restaurant Management System** for a Bangladeshi kitchen (Dhanmondi demo). It has two sides:

| Actor | What they do |
|---|---|
| **Guest / Customer** | Browse menu, register, add to cart, place online order (COD or SSLCommerz demo), track status, cancel before kitchen starts, book a table |
| **Cashier** | Create dine-in orders, take payment, print receipt |
| **Chef** | Kitchen display (KDS), start cooking (this **deducts inventory**), mark ready, toggle menu availability |
| **Manager** | Orders, menu, tables, reservations, inventory, reports, approve customer orders |
| **Admin** | Everything a manager can do, plus staff accounts and website branding (`site_settings`) |

**Highlight feature (likely the main viva topic):**  
**Recipe-based inventory + two-step stock control.**

1. When a staff order is created, ingredients are **reserved** (`reserved_requirements`) — stock is not yet reduced.  
2. When the chef marks the ticket **preparing/ready**, stock is **deducted once** (`inventory_deducted_at`).  
3. Customer online orders stay **pending** until a manager/admin **approves** them. Only then they appear on the kitchen screen.  
4. If stock hits zero, related dishes are auto-set `is_available = false`.

**Stack:** Laravel 10, PHP 8.2, Blade, Vite 5, vanilla JS, MySQL, Spatie Laravel Permission, SSLCommerz sandbox demo.

**Viva sentence:**

> Controllers handle HTTP. Routes live in `routes/web.php`. Data lives in `app/Models`. The hard business logic is order status, inventory reservation/deduction, and role middleware — not in the Blade files.

---

## 2. How a request travels (say this in viva)

```
Browser
  → public/index.php
  → bootstrap/app.php + app/Http/Kernel.php
  → routes/web.php
  → Middleware (web group: session, CSRF, then auth / role)
  → Controller
  → Model / DB transaction
  → Blade view  OR  JSON  OR  redirect
```

**Important sentence:**

> A customer cannot open `/kitchen` because that route uses `auth` + `role:chef`. Even if they guess the URL, `RoleMiddleware` returns 403. Admin is a super-role and is allowed through.

CSRF: every staff/customer form is protected except SSLCommerz **callback URLs** (the bank posts back; they cannot send our CSRF token). See `app/Http/Middleware/VerifyCsrfToken.php`.

---

## 3. Project file structure (what lives where)

```
rms-app/
├── app/
│   ├── Console/Kernel.php
│   ├── Exceptions/               # Handler + OutOfStockException
│   ├── Helpers/SiteHelper.php    # logo, site name for Blade
│   ├── Http/
│   │   ├── Kernel.php            # ★ middleware aliases (role, auth, guest)
│   │   ├── Controllers/
│   │   │   ├── Admin/            # staff panel (dashboard, orders, kitchen…)
│   │   │   ├── Website/          # public site + customer account
│   │   │   └── SslCommerzPaymentController.php
│   │   ├── Middleware/
│   │   │   ├── RoleMiddleware.php      # ★ RBAC
│   │   │   ├── Authenticate.php        # guest → /login or /customer/login
│   │   │   └── VerifyCsrfToken.php     # SSL callbacks excepted
│   │   └── Resources/            # API JSON transformers (Sanctum-ready)
│   ├── Library/SslCommerz/       # gateway SDK wrapper
│   ├── Models/                   # Eloquent tables
│   └── Providers/AppServiceProvider.php  # shared $site data + cart badge
│
├── bootstrap/                    # Laravel boot
├── config/                       # auth, database, permission, restaurant.php
├── database/
│   ├── migrations/               # table schemas
│   └── seeders/                  # demo users, menu, inventory, reservations
├── public/                       # web root (index.php, images/dishes)
├── resources/
│   ├── css/app.css
│   ├── js/app.js
│   └── views/
│       ├── website/              # public + customer pages
│       ├── admin/                # staff panel
│       ├── layouts/              # admin shell
│       └── sslcommerz/           # payment forward form
├── routes/web.php                # ★ ALL web routes
├── tests/Feature/                # kitchen access, auth session, API
├── START_GUIDE.md                # how to run the project
└── DEFENSE.md                    # this file
```

### Viva: why this folder layout?

| Question | Answer |
|---|---|
| Where is business logic? | Mostly **controllers** (this project is not module/service-split like a big HR app). Inventory math is also on `Menu` model. |
| Where is the UI? | `resources/views/` — Blade. Staff uses `admin/`, customers use `website/`. |
| Where are URLs? | Only `routes/web.php`. |
| Where is the database shape? | `database/migrations/`. Demo rows: `database/seeders/DatabaseSeeder.php`. |

---

## 4. Roles, middleware, permissions

### 4.1 Five roles

Stored as string column `users.role` **and** synced to Spatie (`syncLegacyRole()` on `User`).

| Role | Login page | After login | Typical screens |
|---|---|---|---|
| `admin` | `/login` | `/dashboard` | Staff + site settings + staff CRUD |
| `manager` | `/login` | `/dashboard` | Orders, menu, tables, reports, reservations |
| `chef` | `/login` | `/dashboard` | Kitchen + menu availability |
| `cashier` | `/login` | `/dashboard` | Create/pay orders, receipt |
| `customer` | `/customer/login` | `/customer/account` | Cart, my orders, profile |

Staff login **rejects** `role === customer` so a guest cannot enter the kitchen via `/login`.  
Customer login uses `Auth::attempt(..., 'role' => 'customer')`.

### 4.2 Middleware

Registered in `app/Http/Kernel.php` as alias `'role' => RoleMiddleware`.

| Alias | File | Job |
|---|---|---|
| `auth` | `Authenticate.php` | Must be logged in. Customer URLs → customer login |
| `guest` | `RedirectIfAuthenticated.php` | Already logged in cannot open register/login |
| `role:chef` | `RoleMiddleware.php` | Role must match **or** user is `admin` |
| `web` | Kernel group | Session + CSRF |

**Viva line:** “Chef route is `role:chef`. Cashier cannot open `/kitchen`. Admin can, because RoleMiddleware treats `admin` as superuser.”

**Viva line:** “Customer routes sit in `auth` + `role:customer`. Staff routes sit in `auth` + `role:manager,chef,cashier` with extra `role:` on sensitive actions.”

### 4.3 Extra gates (say if asked)

| Rule | Where |
|---|---|
| Staff login blocks customers | `AuthController::login` |
| Customer cannot cancel after kitchen starts | `Order::canBeCancelledByCustomer()` |
| Customer order hidden from KDS until approved | `KitchenController::index` (`is_customer_approved`) |
| Invalid status jump blocked | `OrderController::update` `$allowedTransitions` |
| Past reservation slot rejected | `ReservationController::store` |
| Seat capacity per slot | `Reservation::remainingSeats()` |
| SSL callbacks skip CSRF | `VerifyCsrfToken::$except` |

---

## 5. Feature map — “logic koi?”

For each feature: **entry → controller → logic → data → view**.  
In code, search `DEFENSE:` plus the section number.

### 5.1 Public website

| Layer | File |
|---|---|
| Routes | `routes/web.php` — `/`, `/about`, `/our-menu`, `/contact` |
| Controller | `app/Http/Controllers/Website/HomeController.php` |
| Data | `Menu` (available items), `SiteSettings`, `Reservation` slots |
| Shared branding | `AppServiceProvider::composeSharedViewData()` |
| Views | `resources/views/website/` |

### 5.2 Staff login / logout

| Layer | File |
|---|---|
| UI | `resources/views/admin/auth/login.blade.php` |
| Logic | `Admin\AuthController` — email exists, not customer, `is_active`, password hash, `session()->regenerate()` |
| Redirect | `/dashboard` |

### 5.3 Customer register / login / account

| Layer | File |
|---|---|
| UI | `website/customer/auth/register.blade.php`, `login.blade.php`, `account.blade.php` |
| Logic | `CustomerAuthController` — BD phone regex `01[3-9]…`, role `customer`, Spatie sync |
| Gate | Must add **delivery address** before checkout (`CustomerOrderController::store`) |

### 5.4 Cart (session, not a `carts` table)

| Layer | File |
|---|---|
| Storage | `session('cart.items')` = `[menu_id => qty]` |
| Logic | `CustomerCartController` — `Menu::isOrderable()`, `available_servings` |
| View | `website/customer/cart.blade.php` |
| Badge | `AppServiceProvider::buildCartSummary()` |

**Viva:** Cart is per-session so it stays simple. After checkout the session key is forgotten.

### 5.5 Customer online order (highlight)

| Layer | File |
|---|---|
| Route | `POST /customer/orders` |
| Logic | `CustomerOrderController::store` |
| Checks | Unavailable menu, missing recipe, `lockForUpdate()` on inventory, address required |
| Insert | `orders` + `order_items`, `order_source = customer`, `is_customer_approved = false`, `status = pending` |
| Table | Virtual table `ONLINE` |
| Pay | COD → my-orders page; `sslcommerz` → `sslcommerz/forward.blade.php` |

### 5.6 Staff dine-in order + payment

| Layer | File |
|---|---|
| UI | `admin/orders/create.blade.php` + `resources/js/admin/orders/create.js` |
| Logic | `OrderController::store` |
| Payment | Cash must be ≥ total; bKash/Rocket/card need `payment_reference` |
| After pay | Table → `occupied`; ingredients **reserved** (not deducted) |
| Receipt | `OrderController::receipt` → `admin/orders/receipt.blade.php` |

### 5.7 Customer order approval

| Layer | File |
|---|---|
| Route | `PATCH /orders/{order}/approve-customer` (`role:manager,admin`) |
| Logic | `OrderController::approveCustomerOrder` sets `is_customer_approved = true`, `status = approved` |
| Effect | Kitchen query now includes this ticket |

### 5.8 Kitchen / inventory deduction (highlight)

| Layer | File |
|---|---|
| UI | `admin/kitchen/index.blade.php` |
| List | `KitchenController::index` — pending/approved/preparing; customer only if approved |
| Cook | `KitchenController::updateStatus` |
| Deduct | Recipe × qty, `lockForUpdate()`, ignore stock reserved by **other** orders, decrement once, set `inventory_deducted_at` |
| Side effect | Empty ingredient → disable menus; `Menu::syncAvailabilityFromInventory()` |

### 5.9 Order status machine

Allowed jumps in `OrderController::update`:

```
pending  → approved / cancelled
approved → preparing / cancelled
preparing → ready / cancelled
ready    → served / completed / cancelled
served   → completed
completed / cancelled → stay
```

Customer path shown on the website: `pending → approved → preparing → ready → completed (Delivered)`.

### 5.10 Tables

| Layer | File |
|---|---|
| CRUD / status | `TableController` |
| Occupied | Set on staff order create |
| Freed | When last active order is completed/cancelled |

`ONLINE` is a fake table for delivery orders (excluded from reservation seat math).

### 5.11 Reservations

| Layer | File |
|---|---|
| Public form | Contact page → `ReservationController::store` |
| Capacity | `Reservation::remainingSeats()` = sum of table seats − booked party sizes |
| Staff | `Admin\ReservationController` confirm / cancel |

### 5.12 Menu + recipes

| Layer | File |
|---|---|
| CRUD | `MenuController` (manager); chef can only toggle availability |
| Recipe | `menu_ingredients.quantity_per_dish` |
| Servings | `Menu::getAvailableServingsAttribute()` = floor(stock / qty per dish), min across ingredients |
| Images | `public/images/dishes/` via `Menu::getImageUrlAttribute()` |

### 5.13 Inventory

| Layer | File |
|---|---|
| CRUD | `InventoryController` (manager/admin); chef view-only |
| Low stock | `quantity <= min_quantity` (notifications + reports) |

### 5.14 Dashboard & reports

| Layer | File |
|---|---|
| Dashboard | `Admin\DashboardController` — role-aware counts / kitchen queue for chef |
| Reports | `ReportController` — revenue, top dishes, daily chart (`/reports/data` JSON) |

### 5.15 Notifications

| Layer | File |
|---|---|
| Page | `NotificationController` |
| Badge | `AppServiceProvider` compares last-seen session time vs pending orders / unpaid / low stock |

### 5.16 Site settings (branding)

| Layer | File |
|---|---|
| Admin | `SiteSettingsController` (`role:admin`) |
| Singleton row | `SiteSettings::getInstance()` id = 1 |
| Used on | Website nav, colors, about text |

### 5.17 SSLCommerz (sandbox demo)

| Layer | File |
|---|---|
| Routes | `/sslcommerz/*`, `/success` |
| Controller | `SslCommerzPaymentController` |
| Library | `app/Library/SslCommerz/` |
| CSRF | callbacks listed in `VerifyCsrfToken::$except` |
| Local test | `/success` “Simulate Success” (`DEV_NOTES.md`) |

---

## 6. How to implement a similar new feature

Example: add **“order rating”**.

1. **Migration** — `ratings` table (`order_id`, `user_id`, `stars`, `comment`).  
2. **Model** — `Rating` + `Order::ratings()`.  
3. **Controller** — validate 1–5, only completed orders, only the owner.  
4. **Route** — inside `auth` + `role:customer`.  
5. **View** — form on `website/customer/orders.blade.php`.  
6. **Optional** — show average on menu page.

Same pattern as reservations or cart: route → middleware → controller → model → Blade.

---

## 7. Database (tables you must remember)

| Table | Purpose |
|---|---|
| `users` | Staff + customers (`role`, `phone`, `address`, `is_active`) |
| `roles` / `model_has_roles` | Spatie Permission |
| `menus` | Dishes, price, `is_available`, image |
| `inventories` | Stock qty, unit, min qty, supplier |
| `menu_ingredients` | Recipe: menu ↔ inventory + `quantity_per_dish` |
| `tables` | Dine-in + `ONLINE` |
| `orders` | Header: status, payment, source, reserved/deducted flags |
| `order_items` | Line items (price snapshot) |
| `payment_transactions` | Payment audit |
| `reservations` | Table booking |
| `site_settings` | One branding row |
| `ssl_example_orders` | Gateway demo only |

**Order columns to mention:**  
`order_source` (`staff` / `customer`), `is_customer_approved`, `reserved_requirements` (JSON), `reserved_at`, `inventory_deducted_at`, `payment_status`, `payment_method`.

---

## 8. Controllers (quick index)

| Area | Controller | Job |
|---|---|---|
| Website | `HomeController` | Home, about, menu, contact |
| Website | `CustomerAuthController` | Register / login / profile |
| Website | `CustomerCartController` | Session cart |
| Website | `CustomerOrderController` | Place / list / cancel |
| Website | `ReservationController` | Public booking |
| Staff | `AuthController` | Staff login |
| Staff | `DashboardController` | KPIs |
| Staff | `OrderController` | ★ Dine-in CRUD, pay, approve, transitions |
| Staff | `KitchenController` | ★ KDS + deduct stock |
| Staff | `MenuController` | Menu + recipes |
| Staff | `InventoryController` | Stock CRUD |
| Staff | `TableController` | Tables |
| Staff | `ReservationController` (Admin) | Confirm bookings |
| Staff | `ReportController` | Sales reports |
| Staff | `StaffController` | Users (admin) |
| Staff | `CustomerController` | Ban/list customers |
| Staff | `SiteSettingsController` | Branding |
| Staff | `NotificationController` | Alerts |
| Pay | `SslCommerzPaymentController` | Sandbox gateway |

---

## 9. Views / UI map

| Path | Screens |
|---|---|
| `website/layouts/app.blade.php` | Public + customer chrome |
| `website/index.blade.php` | Home |
| `website/pages/menu.blade.php` | Public menu + add to cart |
| `website/customer/*` | Cart, orders, account, auth |
| `layouts/app.blade.php` + `admin/partials/sidebar` | Staff shell (role-based links) |
| `admin/dashboard` | Manager/cashier/chef home |
| `admin/orders` | List / create / edit / receipt |
| `admin/kitchen` | KDS tickets |
| `admin/menu`, `inventory`, `tables`, `reservations`, `reports` | Ops |
| `sslcommerz/forward.blade.php` | Auto-POST to gateway |

CSS: `resources/css/app.css` (Vite)  
JS: `resources/js/app.js`, `resources/js/admin/orders/create.js`

---

## 10. Security points (short)

- Passwords hashed (`'password' => 'hashed'` on User).  
- Session regenerate on login (session-fixation).  
- CSRF on forms; gateway callbacks excepted.  
- Role middleware + extra role on each sensitive route.  
- Inactive staff cannot login.  
- Customer cancel only while `pending`/`approved` and stock not deducted.  
- Inventory updates use DB transactions + `lockForUpdate()` to reduce double-sell.

---

## 11. Twenty important defense questions

**Q1. What is your project?**  
A restaurant management system: public website + customer online orders + staff POS + kitchen display + recipe inventory + table reservations + reports.

**Q2. Why Laravel?**  
MVC, routing, Eloquent, migrations, middleware, validation, sessions, and auth are built in. Fast and safe for a multi-role web app with MySQL.

**Q3. Explain MVC in this project with one example.**  
Customer clicks Place Order → route `customer.orders.store` → `CustomerOrderController` (C) → `Order` / `Menu` models (M) → redirect to `website.customer.orders` Blade (V).

**Q4. How do you stop a cashier from opening the kitchen?**  
`/kitchen` uses `middleware('role:chef')`. `RoleMiddleware` aborts 403 unless role is chef or admin.

**Q5. Why two login pages?**  
Staff (`/login`) must not be a customer. Customer (`/customer/login`) attempts auth with `role = customer`. Unauthenticated `/customer/*` redirects to customer login.

**Q6. What is the order lifecycle?**  
`pending → approved → preparing → ready → served/completed` (or `cancelled`). Customer orders need approval before kitchen. Transitions are enforced in `OrderController::update`.

**Q7. Why must a manager approve online orders?**  
Kitchen should not cook unpaid/unreviewed delivery tickets. Approval sets `is_customer_approved` so KDS query includes them.

**Q8. When is inventory actually reduced?**  
Not at checkout. Staff create **reserves** ingredients. Chef **preparing/ready** deducts once and stamps `inventory_deducted_at`. Prevents double deduction.

**Q9. What is a recipe / menu ingredient?**  
`menu_ingredients` maps each dish to stock items and `quantity_per_dish`. 2 biryani × 0.25 kg rice = 0.5 kg reserved/deducted.

**Q10. How do you avoid two chefs deducting the same stock?**  
`DB::transaction` + `Inventory::lockForUpdate()` (row lock). Second request waits, then sees the new quantity.

**Q11. Where is the cart stored? Why not a table?**  
Laravel session key `cart.items`. Enough for one-browser shopping; no extra table; cleared after order.

**Q12. How do you know how many servings are left?**  
`Menu::available_servings` = minimum of `floor(inventory.qty / qty_per_dish)` across ingredients. Cart add is blocked if qty exceeds that.

**Q13. How do table reservations avoid overbooking?**  
`Reservation::remainingSeats(date, slot)` = total seats of non-maintenance tables (except ONLINE) minus pending/confirmed party sizes.

**Q14. What happens to a table after a dine-in order?**  
Create → `occupied`. When no other active orders remain on complete/cancel → `available`.

**Q15. How does SSLCommerz work here?**  
Demo hosted checkout: app POSTs to sandbox, gateway POSTs success/fail/IPN. Those URLs are CSRF-excepted. Locally you can simulate success on `/success`.

**Q16. What is Spatie Permission doing if you already have `users.role`?**  
Legacy column is the source of truth. `syncLegacyRole()` copies it into Spatie. Middleware checks the column first, then Spatie roles. Admin bypasses.

**Q17. How are reports calculated?**  
`ReportController` aggregates paid/completed orders in a date range: revenue, order counts, daily series, top items (`order_items` grouped by menu).

**Q18. How do you handle CSRF vs payment gateway?**  
Normal forms need `@csrf`. SSLCommerz server cannot send our token, so success/fail/cancel/IPN are in `$except`. Other POSTs stay protected.

**Q19. What is your ER idea in one sentence?**  
A User places Orders of OrderItems that point to Menus; Menus need Inventory through MenuIngredients; Orders may sit on a Table; Payments and Reservations are extra facts.

**Q20. Limitations and future work?**  
No real production SSL keys in `.env` yet; cart is session-only (lost if session expires); no queue/jobs for emails; staff cancel from edit is disabled by policy; would add unit tests for inventory math, SMS order alerts, and a proper service layer if the app grows.

---

## 12. Demo & run (live board demo)

See also `START_GUIDE.md`.

```bash
composer install
npm install
copy .env.example .env
php artisan key:generate
# create MySQL database rms_app
php artisan migrate --seed
php artisan serve
npm run dev
```

Open `http://127.0.0.1:8000` (or `:8080` if 8000 is busy).

Password for all demo users: **`password`**

| Role | Email |
|---|---|
| Admin | admin@restaurant.com |
| Manager | sarah@restaurant.com |
| Chef | marco@restaurant.com |
| Cashier | lisa@restaurant.com |
| Customer | rahim@customer.com |

**5-minute demo script:**

1. Customer `rahim@customer.com` → menu → cart → place COD order → show “Pending”.  
2. Manager → Orders → **Approve** customer order.  
3. Chef → Kitchen → mark **Preparing** (stock drops) → **Ready**.  
4. Cashier → New dine-in order → cash ≥ total → receipt → table occupied.  
5. Manager → Reports + Inventory low-stock.  
6. Contact page → reservation (optional).

---

## 13. Bangla viva cheat-sheet

- **Project ta ki?** Restaurant management — customer online order kore, cashier dine-in ney, chef kitchen e ranna kore, manager approve/report kore.  
- **Main contribution?** Recipe-based inventory: reserve on create, deduct once when chef starts, auto-disable menu if stock shesh.  
- **Logic koi?** Order: `OrderController`. Kitchen deduct: `KitchenController::updateStatus`. Online checkout: `CustomerOrderController::store`. Role: `RoleMiddleware`.  
- **Customer kitchen e keno jay na?** Route `role:chef` + customer alada login.  
- **Stock duibar kome?** `inventory_deducted_at` null check + transaction lock.  
- **Cart kothay?** Session, `cart.items` — database table nai.  
- **Payment?** Dine-in manual cash/bKash/card. Online COD or SSLCommerz demo.  
- **Notun feature?** Migration → Model → Controller → `web.php` + role → Blade.

---

## 14. File touch-map (keep these tabs open)

| If they ask about… | Open these files first |
|---|---|
| Request flow | `public/index.php`, `routes/web.php`, `app/Http/Kernel.php` |
| Roles / 403 | `RoleMiddleware.php`, `Kernel.php` `$middlewareAliases` |
| Staff login | `Admin/AuthController.php` |
| Customer login | `Website/CustomerAuthController.php` |
| Cart | `CustomerCartController.php` |
| **Online order** | **`CustomerOrderController.php`** |
| **Dine-in + pay + approve** | **`Admin/OrderController.php`** |
| **Kitchen + stock deduct** | **`Admin/KitchenController.php`** |
| Recipe / servings | `Menu.php`, `MenuIngredient.php` |
| Reservations | `Website/ReservationController.php`, `Reservation.php` |
| Tables | `TableController.php` |
| Reports | `ReportController.php` |
| Branding | `SiteSettings.php`, `AppServiceProvider.php` |
| CSRF / gateway | `VerifyCsrfToken.php`, `SslCommerzPaymentController.php` |
| Demo data | `database/seeders/DatabaseSeeder.php` |
| Run steps | `START_GUIDE.md` |

Search the repo for **`DEFENSE:`** — every major section is tagged in source.

---

*Spice Garden RMS — Laravel 10 · practicum defense reference. Keep `DEFENSE.md` open during viva.*
