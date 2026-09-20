# RestaurantOS (Spice Garden) — Simplified Workflow Documentation

## System Overview

**RestaurantOS** is a **Laravel 10 + Blade** Restaurant Management System (not React SPA).  
One codebase serves:

| Side | Who | What |
|------|-----|------|
| **Public website** | Guests / customers | Menu, cart, online order, table reservation, order tracking |
| **Admin Panel** | Admin, Manager, Chef, Cashier | Dashboard, orders, kitchen, inventory, reports, settings |

**Stack:** PHP 8.2 · Laravel 10 · Blade · MySQL · Vite · Spatie Permission · SSLCommerz (sandbox)

**Timezone:** `Asia/Dhaka` (Bangladesh Time)

---

## Complete System Workflow

### 1. Authentication Flow

```
Public guest
  → /customer/login  → role:customer → Cart / Orders / Account

Staff
  → /login (Admin Panel) → role:admin|manager|chef|cashier → /dashboard
```

**Roles (5):**

| Role | Login | After login |
|------|-------|-------------|
| `customer` | `/customer/login` | Account / Menu |
| `cashier` | `/login` | Dashboard + create dine-in orders |
| `chef` | `/login` | Kitchen Display (KDS) |
| `manager` | `/login` | Orders, menu, inventory, reports, reservations |
| `admin` | `/login` | Everything + staff + site settings |

**Key files:**
- Staff login → `app/Http/Controllers/Admin/AuthController.php`
- Customer login → `app/Http/Controllers/Website/CustomerAuthController.php`
- Role guard → `app/Http/Middleware/RoleMiddleware.php`
- Routes → `routes/web.php`

---

### 2. Admin Panel — Central Control

```
Staff Login → /dashboard
├── Today's revenue / orders
├── Available tables
├── Low stock alerts
├── Active / recent orders
└── Role-based sidebar links
```

**Sidebar modules (typical):**
- Dashboard
- Orders (create / list / payment / receipt)
- Kitchen (chef)
- Menu
- Tables
- Reservations
- Inventory
- Customers / Staff (admin)
- Reports
- Notifications
- Settings / Site settings

**Key files:**
- View → `resources/views/admin/dashboard/index.blade.php`
- Controller → `app/Http/Controllers/Admin/DashboardController.php`
- Layout → `resources/views/layouts/app.blade.php`
- Sidebar → `resources/views/admin/partials/sidebar.blade.php`

---

### 3. Menu Management Workflow

```
Menu Management
├── List items (cards + photo + servings left)
├── Add / Edit item
│   ├── Name, category, price, description
│   ├── Image upload (public disk → menu-images/)
│   ├── Recipe: inventory ingredients × qty per dish
│   └── Availability toggle
├── Chef: toggle available / unavailable
└── Stock sync: if servings = 0 → is_available = false
```

**Business rule:**  
`available_servings` = floor(stock / qty_per_dish) across all recipe ingredients.  
Orderable only if `is_available` **and** servings > 0.  
Per-order max = `min(20, available_servings)`.

**Key files:**
- Model → `app/Models/Menu.php` (`available_servings`, `maxOrderableQuantity`)
- Recipe → `app/Models/MenuIngredient.php`
- Controller → `app/Http/Controllers/Admin/MenuController.php`
- Views → `resources/views/admin/menu/*.blade.php`
- Public menu → `resources/views/website/pages/menu.blade.php`

---

### 4. Order Processing Workflow

#### A) Customer online order

```
Browse /our-menu → Add to cart (session)
  → Checkout (COD or SSLCommerz)
  → Order status: pending
  → Manager/Admin approves
  → Chef: preparing (stock deducted) → ready → completed (Delivered)
```

#### B) Staff dine-in order

```
Admin Panel → New Order
  → Select table + menu items + qty
  → Payment (cash / bKash / rocket / card)
  → Kitchen ticket
  → Preparing → Ready → Served / Completed
```

**Status track (customer UI):**  
`Pending → Approved → Preparing → Ready → Delivered`

**Key files:**
- Customer cart → `CustomerCartController.php` (session `cart.items`)
- Customer checkout → `CustomerOrderController.php`
- Staff orders → `Admin/OrderController.php`
- Kitchen → `Admin/KitchenController.php`
- Order model → `app/Models/Order.php`

---

### 5. Billing & Payment Workflow

```
Staff / Customer payment
├── Cash on Delivery (customer online)
├── SSLCommerz hosted checkout (sandbox) → marks order paid
├── Staff: cash / bKash / rocket / card + receipt
└── Cancellation refund (tiered fees on paid orders)
    ├── Pending paid → 0% fee (full refund)
    ├── Approved paid → 20% fee
    └── Preparing (if still cancellable) → 30% fee
```

**Key files:**
- SSLCommerz → `SslCommerzPaymentController.php` + `app/Library/SslCommerz/`
- Forward form → `resources/views/sslcommerz/forward.blade.php`
- Config → `config/sslcommerz.php`, `config/restaurant.php` (`refund`)
- Receipt → `resources/views/admin/orders/receipt.blade.php`
- Audit → `app/Models/PaymentTransaction.php`

---

### 6. Inventory Management Workflow

```
Inventory
├── Item name, qty, unit, min qty, cost, supplier
├── Low stock / out of stock filters
├── Menu recipe links ingredients → dishes
└── When chef starts cooking → deduct once (inventory_deducted_at)
```

**Key files:**
- Model → `app/Models/Inventory.php`
- Controller → `Admin/InventoryController.php`
- View → `resources/views/admin/inventory/index.blade.php`
- Deduction → `Admin/KitchenController.php` (preparing/ready transition)

---

### 7. Table Reservation Workflow

```
Contact page #reserve
  → Date + time slot + party size
  → GET /reservations/available → free tables (number + seats)
  → Select table + BD phone
  → POST /reservations (DB transaction + lockForUpdate)
  → Admin Panel confirms / cancels / assigns table
```

**Rules:**
- Slot length = 60 minutes (overlap blocked)
- Same table cannot be double-booked for overlapping slots
- Phone: Bangladesh mobile only (`01XXXXXXXXX`)

**Key files:**
- Model → `app/Models/Reservation.php`
- Public → `Website/ReservationController.php`
- Admin → `Admin/ReservationController.php`
- Form → `resources/views/website/pages/contact.blade.php`

---

### 8. Delivery Model (important for viva)

```
Delivery = in-house only
├── Customer online orders use synthetic table "ONLINE"
├── No Pathao / Steadfast / RedX / Foodpanda API
└── Config: config/restaurant.php → delivery.provider = in_house
```

---

### 9. Reports & Settings

```
Reports → date range, revenue, completed/cancelled, charts
Site settings → logo, hero, about, hours, branding text
User settings → profile / password
```

**Key files:**
- `Admin/ReportController.php` + `admin/reports/index.blade.php`
- `Admin/SiteSettingsController.php` + `admin/site-settings/index.blade.php`
- Branding defaults → `config/restaurant.php`

---

## Technical Architecture (Laravel — not React)

### Request path

```
Browser
  → public/index.php
  → routes/web.php
  → Middleware (session, CSRF, auth, role)
  → Controller
  → Eloquent Model / DB::transaction
  → Blade view  OR  redirect  OR  JSON
```

### Frontend (this project)

```
Blade views + CSS in layouts/partials
├── Website layout → resources/views/website/layouts/app.blade.php
├── Admin layout   → resources/views/layouts/app.blade.php
├── Theme / responsive → website/partials/theme.blade.php, responsive.blade.php
└── Small JS → resources/js/admin/orders/create.js , home-scripts
```

**There is no separate React frontend.** UI is server-rendered Blade.

### Data flow example (add to cart)

```
Menu page form POST → CustomerCartController@add
  → validate qty ≤ min(20, stock)
  → session('cart.items')[menu_id] = qty
  → redirect / JSON
```

---

## Security Features

- CSRF on all forms (SSLCommerz callbacks excepted)
- Password hashing (Laravel Hash)
- Role middleware on staff routes
- Customer cannot open `/kitchen` or `/dashboard`
- Eloquent / query builder (SQL injection mitigation)
- Input validation on controllers

---

## How to Run

```bash
cd d:\WebProject\rms-app
composer install
copy .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
php artisan serve --host=127.0.0.1 --port=8010
```

Demo logins (after seed):  
- Admin: `admin@restaurant.com` / `password`  
- Customer: create via `/customer/register` or seeded guest if present  

---

## Use Case Scenarios

### Scenario 1 — Daily ops
1. Manager logs into Admin Panel  
2. Checks dashboard revenue + low stock  
3. Approves pending customer orders  
4. Chef works Kitchen tickets  

### Scenario 2 — Online guest order
1. Guest registers / logs in  
2. Adds dishes (max 20 / stock cap)  
3. Pays COD or SSLCommerz  
4. Tracks Pending → … → Delivered  
5. Can cancel before kitchen deducts stock (tiered refund if paid)

### Scenario 3 — Table booking
1. Guest opens Contact → Reserve  
2. Picks date/slot/party of 6 → sees T-04 (6 seats), etc.  
3. Submits; concurrent second user cannot take same table/slot  

---

## Twenty Important Defense Board Questions

> Board e “logic kothay?” asle **file path + line range** bolo. Code e `DEFENSE Q…` comment o same number follow kore.

### Quick map (logic → file → lines)

| Topic | File | Lines |
|--------|------|-------|
| All routes | `routes/web.php` | 1–154 (header 1–8; kitchen 98–100; SSL 134–149) |
| RBAC | `app/Http/Middleware/RoleMiddleware.php` | `handle` **23–44** |
| Session cart | `app/Http/Controllers/Website/CustomerCartController.php` | add **31–79**; get/put **151–165** |
| Qty cap min(20, stock) | `app/Models/Menu.php` | `maxOrderableQuantity` **102–108** |
| Recipe servings | `app/Models/Menu.php` | `getAvailableServingsAttribute` **66–90** |
| Checkout + lock | `app/Http/Controllers/Website/CustomerOrderController.php` | `store` **32–206** (`lockForUpdate` ~118) |
| Stock cut once | `app/Http/Controllers/Admin/KitchenController.php` | `updateStatus` **38–177** |
| Cancel / refund | `app/Models/Order.php` + CustomerOrderController | `canBeCancelled…` **132–149**; `refundBreakdown` **159–188**; `cancel` **238–290** |
| Book table atomic | `app/Models/Reservation.php` | `bookAtomically` **178–218** |
| Free tables for party | `app/Models/Reservation.php` | `availableTables` **124–148** |
| BD phone | `app/Models/Reservation.php` | `PHONE_REGEX` **line 25** |
| Timezone | `config/app.php` | **67–73** (`Asia/Dhaka`) |
| Delivery / ONLINE / refund % | `config/restaurant.php` | max qty **109**; delivery **125–134**; refund **140–145** |

---

### Q1. What is this project?
**A:** Laravel 10 Restaurant Management System — public website (menu, session cart, reservation) + role-based **Admin Panel** (orders, kitchen, inventory, reports). Blade + MySQL.  
**Code:** project root `rms-app/`; entry routes `routes/web.php` **1–154**.

### Q2. Why Laravel instead of only PHP / React?
**A:** MVC, Eloquent, middleware RBAC, migrations/seeders, Blade — full-stack practicum without a separate SPA.  
**Code:** Controllers under `app/Http/Controllers/{Website,Admin}/`; models `app/Models/`; views `resources/views/`.

### Q3. Where are all URLs defined?
**A:** Only `routes/web.php`. Blade uses `route('name')`.  
**Code:** `routes/web.php` **1–8** (DEFENSE header), public site **32–35**, reservations **39–41**, customer cart/orders **57–71**, staff panel **73–128**, SSLCommerz **134–149**.

### Q4. Explain MVC with one example (menu page).
**A:**  
1. **Route** `GET /our-menu` → `routes/web.php` **35**  
2. **Controller** `HomeController@menu` → `app/Http/Controllers/Website/HomeController.php` **28–37**  
3. **Model** `Menu::with('menuIngredients.inventory')` loads DB  
4. **View** `resources/views/website/pages/menu.blade.php`

### Q5. How do roles work? Can a customer open the kitchen?
**A:** `RoleMiddleware` allows listed roles **or** `users.role === 'admin'`. Kitchen is `role:chef` only → customer gets **403**.  
**Code:**  
- Middleware: `app/Http/Middleware/RoleMiddleware.php` **23–44** (admin bypass **35**)  
- Kitchen routes: `routes/web.php` **98–100**  
- Staff login rejects customers: `app/Http/Controllers/Admin/AuthController.php` **27–69** (customer check **42–46**)

### Q6. Where is the cart stored? Is there a cart table?
**A:** **No cart table.** Session key `cart.items` = `[menu_id => qty]`.  
**Code:** `CustomerCartController.php` — add **31–79**; `getCartItems` **151–159**; `storeCartItems` **161–165**. Routes: `routes/web.php` cart group ~**57–65**.

### Q7. How do you stop overselling stock?
**A:** Three layers: (1) qty ≤ `min(20, servings)` (2) checkout `DB::transaction` + `Inventory::lockForUpdate()` (3) kitchen deducts once with the same lock pattern.  
**Code:**  
- Cap: `Menu::maxOrderableQuantity()` **102–108**; config `config/restaurant.php` **109**  
- Cart enforce: `CustomerCartController@add` **51–60**  
- Checkout lock: `CustomerOrderController@store` **99–120** (`lockForUpdate` **118**)

### Q8. When is inventory actually reduced?
**A:** When chef sets status **preparing/ready** and `inventory_deducted_at` is still `null` — **not** at pending checkout. Stamp `inventory_deducted_at = now()` so it never cuts twice.  
**Code:** `app/Http/Controllers/Admin/KitchenController.php` `updateStatus` **38–177** (gate **56–62**; decrement ~**121–130**; stamp **133–136**).

### Q9. What is recipe-based inventory?
**A:** `menu_ingredients.quantity_per_dish` × stock → servings = **min** over ingredients of `floor(stock / qty_per_dish)`. Dish orderable only if `is_available && servings > 0`.  
**Code:** `Menu::getAvailableServingsAttribute` **66–90**; `isOrderable` **93–96**; image resolve `getImageUrlAttribute` **41–59**.

### Q10. How does table reservation avoid double-booking?
**A:** `Reservation::bookAtomically()` → `DB::transaction` → `Table::lockForUpdate()` → `isTableFree()` (also `lockForUpdate` on booking rows + `slotsOverlap`) → `create`. Concurrent requests wait on the row lock.  
**Code:**  
- Model: `bookAtomically` **178–218**; `isTableFree` **154–171**; `slotsOverlap` **92–100**  
- HTTP: `ReservationController@store` **64–116** (calls `bookAtomically` ~**96**)  
- Route: `POST /reservations` → `routes/web.php` **41**

### Q11. How do guests find a table for 6 people?
**A:** AJAX `GET /reservations/available` → `availableTables(date, slot, party_size)` keeps tables with `capacity >= party` and no overlapping pending/confirmed booking; excludes `ONLINE` / maintenance.  
**Code:** Controller `available` **19–58**; model `availableTables` **124–148**; route `routes/web.php` **40**.

### Q12. Explain order status for the customer tracker.
**A:** Steps: `pending → approved → preparing → ready → completed` (UI label **Delivered**). Index helper maps `served`→`ready`. CSS keeps stepper **5 columns** on mobile (horizontal).  
**Code:**  
- Index: `Order::getCustomerTrackIndexAttribute` **114–125**  
- Labels: `CustomerOrderController@index` **212–231**  
- Blade: `resources/views/website/customer/orders.blade.php` ~**44**  
- Horizontal CSS: `resources/views/website/partials/stacking.blade.php` **102–107**; `responsive.blade.php` **201–205**

### Q13. What payment options exist?
**A:** Customer: **COD** (`cash`) or **SSLCommerz** sandbox. Staff: cash / bKash / rocket / card. Success path updates `orders.payment_status` + `payment_transactions`. CSRF excepted for gateway callbacks.  
**Code:**  
- Checkout branch: `CustomerOrderController@store` **193–205**  
- SSL routes: `routes/web.php` **134–149**  
- Success sync: `SslCommerzPaymentController` `success` ~**81**; `syncMainOrderPayment` ~**343–378**  
- CSRF except: `VerifyCsrfToken.php` **14–22**

### Q14. Explain cancellation / refund policy.
**A:** Cancel only if `canBeCancelledByCustomer()` (own customer order, before stock cut, not ready/served/completed). Unpaid COD → fee 0. Paid fees from config: pending **0%**, approved **20%**, preparing **30%**. Written to `cancellation_fee_percent` / `refund_amount`.  
**Code:**  
- Rules: `Order::canBeCancelledByCustomer` **132–149**; `refundBreakdown` **159–188**  
- Action: `CustomerOrderController@cancel` **238–290**  
- Percents: `config/restaurant.php` **140–145**

### Q15. Is Pathao / Foodpanda integrated?
**A:** **No.** Delivery is **in-house**. Online tickets use synthetic table number `ONLINE` (`Table::firstOrCreate`).  
**Code:** `config/restaurant.php` delivery block **125–134**; create ONLINE table in `CustomerOrderController@store` **146–149**.

### Q16. Where is timezone handled?
**A:** App timezone `Asia/Dhaka` (GMT+6) — Admin Panel `now()` / Carbon timestamps follow this.  
**Code:** `config/app.php` **67–73**. Slot parsing also uses `config('app.timezone')` in `Reservation::slotStart` **71–74**.

### Q17. How is phone validated on reservation?
**A:** Server regex Bangladesh mobile: optional `+88`/`88` + `01[3-9]` + 8 digits. Controller normalizes `+88`/`88` prefix before save.  
**Code:** Constant `Reservation::PHONE_REGEX` **line 25**; validate in `ReservationController@store` **68**; normalize **86–91**.

### Q18. Difference between Admin Panel and public site?
**A:** Public = `resources/views/website/*` + `Website\*` controllers (guest/customer). Admin Panel = `resources/views/admin/*` + `Admin\*` controllers behind `auth` + `role:manager,chef,cashier` (`routes/web.php` **73–128**). Staff login: `Admin\AuthController` **27–69**.

### Q19. How would you add a new similar feature (e.g. feedback)?
**A:** Same Laravel pattern as reservations: migration → Model → Controller → `routes/web.php` + middleware → Blade → optional seeder. Mirror `Reservation` / `ReservationController` / routes **39–41**.

### Q20. What is the strongest technical point?
**A:** End-to-end stock + booking integrity: recipe servings (`Menu` **66–108**) → session cart caps (`CustomerCartController` **31–79**) → locked checkout (`CustomerOrderController` **99–120**) → one-time kitchen deduct (`KitchenController` **38–177**) + atomic table booking (`Reservation::bookAtomically` **178–218**).

---

**Related docs:**  
- Folder map → `PROJECT_STRUCTURE_GUIDE_BANGLA.md`  
- Screen → file map → `VISUAL_UI_MAPPING_BANGLA.md`  
- Extra viva notes → `DEFENSE.md`
