---
noteId: "c830ad305c4811f1bc30b1685060d607"
tags: []
---

# Restaurant Management System — Complete Project Documentation

Last updated: 2026-05-30

## 1) What this project is

This is a Laravel restaurant management system for:

- public menu browsing
- customer registration/login and online ordering
- staff order creation and payment recording
- kitchen queue and order status updates
- inventory tracking and recipe-based stock deduction
- table management
- dashboard/reporting
- site branding and settings

## 2) Tech stack

- Backend: PHP 8.2+, Laravel 11
- Frontend: Blade templates + Vite
- Database: MySQL/MariaDB
- Auth: session-based web auth
- Roles/permissions: Spatie Laravel Permission
- PDF/API/gateway: removed in the simplified web-only version

## 3) Full project structure

```text
app/
  Console/
  Exceptions/
  Helpers/
  Http/
    Controllers/
      Admin/
      Website/
    Middleware/
    Resources/
  Models/
  Providers/
bootstrap/
config/
database/
  factories/
  migrations/
  seeders/
docs/
public/
resources/
  css/
  js/
  views/
routes/
storage/
tests/
vendor/
```

### What each folder means

- `app/Http/Controllers/Admin/` — admin order, dashboard, notification, table, staff, settings logic
- `app/Http/Controllers/Website/` — customer-facing pages and ordering flow
- `app/Models/` — database models like `Order`, `Menu`, `Inventory`, `Table`
- `resources/views/` — all Blade screens for admin and website
- `routes/web.php` — main route list
- `database/migrations/` — table structure changes
- `database/seeders/` — sample data and default setup
- `resources/css/` and `resources/js/` — frontend assets
- `public/` — public entry files and built assets

## 4) Main features

- Public restaurant website
- Customer account system
- Staff order management
- Customer order approval flow
- Kitchen display system
- Inventory checks and recipe deduction
- Table status updates
- Menu CRUD
- Staff/admin dashboard
- Reports and settings

### সহজ বাংলা অর্থ

- **Order** — অর্ডার / খাবারের আবেদন
- **Menu** — মেনু / খাবারের তালিকা
- **Inventory** — স্টক / মালামাল
- **Report** — রিপোর্ট / হিসাবের সারাংশ
- **Dashboard** — মূল প্যানেল / প্রধান স্ক্রিন
- **Approval** — অনুমোদন
- **Status** — অবস্থা
- **Search** — খোঁজা / সার্চ
- **Top selling** — সবচেয়ে বেশি বিক্রি হওয়া আইটেম
- **Route** — কোন পেজে যাবে তার ঠিকানা
- **Controller** — লজিক চালানোর ফাইল
- **View** — স্ক্রিনে যা দেখা যায়

## 5) Important data tables

- `users` — staff, admin, and customer accounts
- `menus` — menu items
- `inventories` — stock items
- `menu_ingredients` — recipe mapping between menu and inventory
- `tables` — table records and occupancy state
- `orders` — order header, source, status, payment info
- `order_items` — ordered menu rows
- `payment_transactions` — payment audit log
- `site_settings` — branding and site config

## 6) How the order flow works

### Staff order flow

1. Staff creates the order.
2. Menu items are validated.
3. Inventory is checked.
4. Order items are saved.
5. Total is calculated.
6. Payment is recorded if needed.
7. Table status updates if the order is linked to a table.

### Customer order flow

1. Customer places an order.
2. Order starts as pending/unapproved.
3. Admin approves it.
4. Kitchen receives it.
5. Status moves through preparing and ready.
6. Final state becomes served/completed.

### Kitchen flow

1. Chef opens the kitchen queue.
2. Only approved orders are processed.
3. Moving to `preparing` deducts inventory.
4. Duplicate deduction is prevented.
5. Order can then become `ready`, `served`, or `completed`.

## 7) What was changed in this project

### Order workflow

- Added `approved` status
- Kept the flow: `pending → approved → preparing → ready → served/completed`
- Restricted admin status dropdown to valid transitions
- Updated customer tracking to include the new status

### Receipt and customer info

- Added customer address and phone to receipts where available
- Kept receipts browser-friendly and simple

### Notification UI

- New notifications can be highlighted in the admin UI
- Session-based last-seen tracking is shared to views

### Menu and list behavior

- Menu listing is sorted ascending
- Some heavy admin styles were simplified

### Style cleanup

- Removed many inline styles
- Added small utility classes
- Made badge styles neutral where possible

### Signature removal

- Removed the signature section from the website output

## 8) If you want to change something, edit here

This is the most useful section for beginners.

### Change order status

- File: `app/Http/Controllers/Admin/OrderController.php`
- Also check: `app/Http/Controllers/Kitchen/KitchenController.php`
- Also check: `app/Models/Order.php`

If you want to add, remove, or rename a status:

1. Update the allowed status list in the controller.
2. Update dropdown options in the Blade views.
3. Update any badge or label helpers in the `Order` model.
4. Update kitchen/dashboard counts if they use the old status.

### Change menu behavior

- File: `app/Http/Controllers/Menu/MenuController.php`
- View files: `resources/views/menu/*.blade.php`

If you want to rename a field, change sort order, or show new columns:

1. Update controller query/validation.
2. Update the Blade table or form.
3. Check the model if the field is computed.

### Change receipt layout

- File: `resources/views/orders/receipt.blade.php`

If you want to show more customer or payment information:

1. Add the data in the controller.
2. Print it in the receipt view.
3. Check if the print CSS needs adjustment.

### Change notification behavior

- File: `app/Http/Controllers/Admin/NotificationController.php`
- File: `app/Providers/AppServiceProvider.php`

If you want a new notification type:

1. Add the logic in the controller.
2. Share the value to views if needed.
3. Update the icon or badge in the Blade file.

### Change styling

- File: `resources/views/layouts/partials/styles.blade.php`
- File: `resources/views/admin/_utils.blade.php`

If you want to make the UI simpler:

1. Add a small utility class.
2. Replace repeated inline styles with that class.
3. Avoid changing every view manually when one shared class is enough.

## 9) Beginner questions and answers

### “Where do I change page text?”

Check the Blade view in `resources/views/`.

### “Where do I change database fields?”

Check the migration in `database/migrations/`.

### “Where do I change business logic?”

Check the controller in `app/Http/Controllers/`.

### “Where do I change what is shown on screen?”

Check the Blade file in `resources/views/`.

### “Where do I change a model’s computed value?”

Check the model in `app/Models/`.

### “Why did a feature stop working?”

Usually one of these changed:

- route name
- controller method
- model attribute
- Blade variable name
- CSS class or utility class

## 10) Common change examples

### Example: add a button

If you want to add a button like `Add`, `Edit`, or `Save`:

1. Find the Blade file in `resources/views/`.
2. Put the button inside the right `<div>` or `<td>`.
3. Reuse existing classes if possible.
4. If the button should do something, connect it to a route, form, or JavaScript action.

### Example: move the cart section higher

If you want the cart to appear above another section:

1. Find the page Blade file.
2. Move the cart block above the other block in the HTML.
3. Check spacing classes like `mt-*`, `mb-*`, `row`, `col-*`.
4. If needed, adjust the CSS in the shared style file.

### Example: add another row

If you want one more row in a table or layout:

1. Find the table or grid in the Blade file.
2. Copy an existing row block.
3. Change the values or variables inside it.
4. If data comes from the controller, make sure the controller sends it.

### Example: add a new block in a page

If you want a new section on a page:

1. Decide where it should appear.
2. Add a new `div`, `section`, or `card`.
3. Keep the class names simple.
4. Test the page in the browser.

### Example: make order statuses simpler

If you want fewer statuses:

1. Remove the status from controller logic.
2. Remove it from dropdowns.
3. Remove its badge rule.
4. Check reports and filters that use it.

### Example: add a new order field

If you want a new field like `delivery_note`:

1. Add a migration column.
2. Add validation in the controller.
3. Save the field during create/update.
4. Show it in the relevant Blade view.

### Example: change how the dashboard looks

1. Edit `resources/views/admin/dashboard/index.blade.php`.
2. Reuse the shared style partial if possible.
3. Avoid repeating the same inline styles.

## 11) How the logic works

এই section-এ app কীভাবে কাজ করে তা একদম সহজ ভাষায় বলা হয়েছে।

### 1. Basic logic flow

প্রায় সব feature এই নিয়মে চলে:

1. User button ক্লিক করে বা form submit করে।
2. Request যায় `routes/web.php`-এর route এ।
3. Route controller method চালায়।
4. Controller data check করে এবং logic চালায়।
5. Controller model থেকে data save/read করে।
6. শেষে Blade view বা JSON response পাঠায়।

### 2. How search works

Search সাধারণত controller-এর ভিতরে করা হয়।

Example:

- `app/Http/Controllers/Admin/MenuController.php` searches menu names with `where('name', 'like', '%...%')`
- `app/Http/Controllers/Admin/InventoryController.php` searches inventory items with `where('item_name', 'like', '%...%')`

সহজ ধারণা:

1. User search box-এ keyword লেখে।
2. Controller দেখে `search` ভরা আছে কি না।
3. ভরা থাকলে query filter করে।
4. তারপর filtered result table-এ দেখায়।

### 3. How top-selling items are generated

Top-selling item order item data থেকে বানানো হয়।

In `app/Http/Controllers/Admin/ReportController.php`:

1. `order_items` এর সাথে `menus` join করা হয়।
2. cancelled order বাদ দেওয়া হয়।
3. item কতবার বিক্রি হয়েছে তা `SUM(order_items.quantity)` দিয়ে যোগ করা হয়।
4. আয় `SUM(order_items.subtotal)` দিয়ে যোগ করা হয়।
5. সবচেয়ে বেশি quantity অনুযায়ী result sort হয়।
6. প্রথম result-টাই সবচেয়ে popular dish হয়।

সহজ কথায়:

- বেশি বিক্রি = top item
- বেশি quantity = বেশি rank
- cancelled order count হয় না

### 4. How reports are generated

Report তৈরি হয় `app/Http/Controllers/Admin/ReportController.php`-এ।

The controller:

1. Request থেকে date range নেয়।
2. শুরু ও শেষ date-কে full day timestamp বানায়।
3. ওই range-এর order বের করে।
4. revenue, order count, completed count, cancelled count হিসাব করে।
5. দিন অনুযায়ী sales group করে daily revenue বানায়।
6. top item আর category revenue বের করে।
7. তারপর report page বা JSON endpoint-এ data পাঠায়।

### 5. কেন এটা দরকার

এই logic-এর মাধ্যমে app দেখাতে পারে:

- মোট বিক্রি
- সবচেয়ে বেশি বিক্রি হওয়া খাবার
- category অনুযায়ী আয়
- daily chart data
- recent order

### 6. পরে আমাকে যেসব প্রশ্ন করতে পারো

- “Search ta kibhabe kaj kore?”
- “Top selling dish kibhabe ber hoy?”
- “Report generation logic dekhaw.”
- “Revenue kibhabe calculate hoy?”
- “Order status flow kibhabe kaj kore?”
- “Menu search kothay ache?”
- “Inventory low stock kibhabe ber kore?”
- “Customer order approval kibhabe kaj kore?”
- “Kitchen e order kibhabe jay?”
- “Dashboard er data kothay theke ashe?”

### 7. Logic change korte chaile

Tumi jodi logic change korte bolo, ami usually ei gula check korbo:

1. the controller
2. the model
3. the Blade view
4. the route
5. the database field or migration

## 12) MVC sequence and code map

### MVC sequence

This project follows the normal Laravel MVC flow:

1. Browser request আসে।
2. `routes/web.php` দেখে কোন controller যাবে তা ঠিক হয়।
3. Middleware auth, role, CSRF, session check করে।
4. Controller business logic চালায়।
5. Model database থেকে data আনে বা save করে।
6. Blade view screen-এ output দেখায়।

### কোন file-এ কী change করা যায়

- `routes/web.php` — নতুন page, নতুন action, route name change
- `app/Http/Kernel.php` — middleware add/remove, alias change
- `app/Http/Middleware/Authenticate.php` — login না থাকলে কোথায় redirect হবে
- `app/Http/Middleware/RoleMiddleware.php` — কোন role কোন page পাবে
- `app/Exceptions/Handler.php` — exception ধরার জায়গা
- `resources/views/errors/common.blade.php` — error page design
- `app/Http/Controllers/Admin/` — admin logic
- `app/Http/Controllers/Website/` — customer logic
- `app/Models/` — data relation, computed value, helper method
- `resources/views/` — UI, table, form, button, layout

### Normal request path

Example: order page open করলে

1. User `/orders` যায়।
2. Route `OrderController@index` চালায়।
3. Middleware দেখে user permission আছে কি না।
4. Controller order list আনে।
5. Model দিয়ে query হয়।
6. Blade table-এ result দেখায়।

## 13) Exception handling

এই app-এ exception handle করা হয় `app/Exceptions/Handler.php`-এ।

### কোন exception কোথায় যায়

- `TokenMismatchException` — session expire বা CSRF mismatch; 419 হয়
- `AuthenticationException` — login না থাকলে login page-এ পাঠায়
- `AuthorizationException` — permission না থাকলে 403 হয়
- `ModelNotFoundException` — data না পেলে 404 হয়
- `NotFoundHttpException` — invalid URL হলে 404 হয়
- `MethodNotAllowedHttpException` — wrong HTTP method হলে 405 হয়
- `ThrottleRequestsException` — বেশি request দিলে 429 হয়
- `ValidationException` — form ভুল হলে 422 হয়

### Special exception logic

- `orders.store`-এ `table_id` missing হলে custom message দেখায়
- JSON request হলে JSON response পাঠায়
- normal web request হলে redirect back বা login page-এ পাঠায়
- `resources/views/errors/common.blade.php` সাধারণ error screen দেখায়

### Role/permission error কোথায় হয়

- `app/Http/Middleware/RoleMiddleware.php` invalid access হলে `abort(403)` করে
- তাই wrong role হলে সাধারণত access denied দেখায়

### Auth redirect কোথায় হয়

- `app/Http/Middleware/Authenticate.php` guest user-কে `login` বা `customer.login`-এ পাঠায়

## 15) Simplification notes

This project was intentionally simplified to keep maintenance easier:

- API v1 endpoints removed
- PDF invoice generation removed
- online gateway integration removed
- heavy admin CSS reduced
- repeated inline styles replaced with shared utility classes

## 16) Useful commands

```bash
composer install
npm install
php artisan key:generate
php artisan migrate --seed
npm run build
php artisan test
php artisan view:clear
```

## 17) Where to look first as a beginner

If you are new to the codebase, start here:

1. `routes/web.php` — see all pages and actions
2. `app/Http/Controllers/` — see what each page does
3. `resources/views/` — see the actual UI
4. `app/Models/` — see what data each feature uses
5. `database/migrations/` — see the table structure

## 18) Final note

This file is now the single project summary for the docs folder.

If you want, I can next make it even more beginner-friendly by adding:

- a controller-by-controller list
- a view-by-view list
- a “how to edit step by step” section
- a simple architecture diagram

## 19) Controller and Blade structure map

This section is the simple “what file was created for what” guide.

### Main controller folders

#### `app/Http/Controllers/Admin/`

These files power the admin panel. They are for staff and admin only.

- `AuthController.php` — admin login/logout flow
- `CustomerController.php` — manage customer accounts
- `DashboardController.php` — admin dashboard data
- `InventoryController.php` — stock items and stock search
- `KitchenController.php` — kitchen order queue and cooking flow
- `MenuController.php` — menu create/edit/delete/search
- `NotificationController.php` — notification list and unread tracking
- `OrderController.php` — admin order list, edit, approve, payment, receipt
- `ReportController.php` — sales and item reports
- `SettingsController.php` — general setting pages
- `SiteSettingsController.php` — website branding, logo, hero, and contact info
- `StaffController.php` — staff list and staff management
- `TableController.php` — table list and table status

#### `app/Http/Controllers/Website/`

These files power the customer-facing website.

- `HomeController.php` — homepage and public pages
- `CustomerAuthController.php` — customer login/register/logout
- `CustomerCartController.php` — cart add/remove/update
- `CustomerOrderController.php` — customer order submit and order history

#### Other controller areas

- `app/Http/Controllers/Orders/OrderController.php` — order-related flow in the current code split
- `app/Http/Controllers/Dashboard/DashboardController.php` — dashboard-related flow in the current code split
- `app/Http/Controllers/Controller.php` — base controller class used by other controllers

### Main Blade view folders

#### `resources/views/admin/`

These are the admin panel screens.

- `admin/dashboard/index.blade.php` — dashboard screen
- `admin/auth/login.blade.php` — admin login page
- `admin/customers/index.blade.php` — customer list
- `admin/inventory/index.blade.php` — stock list
- `admin/kitchen/index.blade.php` — kitchen queue screen
- `admin/menu/create.blade.php` — create menu form
- `admin/menu/edit.blade.php` — edit menu form
- `admin/menu/index.blade.php` — menu list page
- `admin/notifications/index.blade.php` — notification page
- `admin/orders/create.blade.php` — create order screen
- `admin/orders/edit.blade.php` — edit order screen
- `admin/orders/index.blade.php` — order list screen
- `admin/orders/receipt.blade.php` — receipt print view
- `admin/reports/index.blade.php` — report page
- `admin/settings/index.blade.php` — settings screen
- `admin/site-settings/index.blade.php` — site branding screen
- `admin/staff/index.blade.php` — staff list page
- `admin/tables/index.blade.php` — table management page

#### `resources/views/website/`

These are public website screens.

- `website/index.blade.php` — home page
- `website/pages/about.blade.php` — about page
- `website/pages/contact.blade.php` — contact page
- `website/pages/menu.blade.php` — public menu page
- `website/customer/cart.blade.php` — customer cart
- `website/customer/orders.blade.php` — customer order history
- `website/customer/account.blade.php` — customer account page
- `website/customer/auth/login.blade.php` — customer login page
- `website/customer/auth/register.blade.php` — customer register page
- `website/layouts/app.blade.php` — customer layout
- `website/partials/nav.blade.php` — navigation bar
- `website/partials/header.blade.php` — header section
- `website/partials/footer.blade.php` — footer section

### Shared layout and helper views

- `resources/views/layouts/app.blade.php` — main app shell for admin pages
- `resources/views/layouts/partials/sidebar.blade.php` — admin sidebar menu
- `resources/views/layouts/partials/top-navbar.blade.php` — admin top navbar
- `resources/views/layouts/partials/alerts.blade.php` — success/error messages
- `resources/views/layouts/partials/styles.blade.php` — shared CSS styles
- `resources/views/admin/_utils.blade.php` — small utility classes for repeated styling
- `resources/views/errors/common.blade.php` — friendly error page

### What each Blade file usually contains

1. `index.blade.php` — list page or summary page
2. `create.blade.php` — form for new record
3. `edit.blade.php` — form for updating existing record
4. `receipt.blade.php` — print-friendly output
5. `_partials.blade.php` — reusable chunk used in other files

### Simple beginner rule

If you want to change a feature, use this order:

1. Find the route in `routes/web.php`.
2. Open the controller that route points to.
3. Check the Blade file that controller returns.
4. If data is missing, check the model and migration.
5. If access fails, check middleware and exceptions.
