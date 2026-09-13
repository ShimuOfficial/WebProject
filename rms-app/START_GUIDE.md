# Spice Garden — start the project yourself

This is a Laravel 10 + Vite restaurant app. Follow these steps on a new machine (Windows + XAMPP is the usual setup).

## What you need

- PHP 8.1+ (`php -v`) — XAMPP PHP is fine
- Composer (`composer -V`)
- Node.js 18+ and npm (`node -v`, `npm -v`)
- MySQL (start Apache + MySQL in XAMPP)

## 1. Open the project folder

```bash
cd f:\WebProject\rms-app
```

## 2. Create your `.env`

```bash
copy .env.example .env
```

On Mac/Linux: `cp .env.example .env`

Edit `.env` if needed:

- `APP_URL=http://127.0.0.1:8000`
- `DB_DATABASE=rms_app`
- `DB_USERNAME=root`
- `DB_PASSWORD=` (empty for default XAMPP)

## 3. Install dependencies

```bash
composer install
npm install
php artisan key:generate
```

If `storage/framework/sessions` is missing, create the folders Laravel needs:

```bash
mkdir storage\framework\sessions
mkdir storage\framework\views
mkdir storage\framework\cache\data
mkdir storage\logs
mkdir bootstrap\cache
```

## 4. Create the database and load sample data

In phpMyAdmin, create a database named `rms_app`, **or** run:

```bash
C:\xampp\mysql\bin\mysql.exe -u root -e "CREATE DATABASE IF NOT EXISTS rms_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

Then migrate and seed:

```bash
php artisan migrate --seed
```

To wipe and rebuild demo data later:

```bash
php artisan migrate:fresh --seed
```

## 5. Start the app (two terminals)

Terminal 1 — Laravel:

```bash
php artisan serve
```

Terminal 2 — Vite (CSS/JS):

```bash
npm run dev
```

Open **http://127.0.0.1:8000**

If port 8000 is already used, pick another port:

```bash
php artisan serve --host=127.0.0.1 --port=8080
```

Then set `APP_URL=http://127.0.0.1:8080` in `.env`.

## Demo logins

Password for every account: `password`

| Role     | Email                    | Use for                          |
|----------|--------------------------|----------------------------------|
| Admin    | admin@restaurant.com     | `/login` — full admin            |
| Manager  | sarah@restaurant.com     | `/login` — floor + reports       |
| Chef     | marco@restaurant.com     | `/login` — kitchen               |
| Cashier  | lisa@restaurant.com      | `/login` — orders / payments     |
| Customer | rahim@customer.com       | `/customer/login` — order online |
| Customer | fatima@customer.com      | `/customer/login`                |
| Customer | tanvir@customer.com      | `/customer/login`                |

Staff login: `/login`  
Customer login: `/customer/login`

## What the seed puts in the database

- 22 Bangladeshi menu items (biryani, curry, snacks, desserts, drinks)
- Kitchen inventory and recipes
- 10 dine-in tables + 1 `ONLINE` delivery table
- Staff dine-in orders and 3 customer online orders
- 3 upcoming reservations
- Site settings for **Spice Garden** (Dhanmondi)

## Useful pages

- Website home: `/`
- Menu: `/our-menu`
- Reservations: from the public site
- Staff dashboard: `/dashboard` (after staff login)

## If something breaks

- **Unknown database `rms_app`** — create the database (step 4), then migrate again
- **Port 8000 already in use** — serve on 8080 and update `APP_URL`
- **CSS looks broken** — `npm run dev` is not running
- **Session / storage errors** — create the `storage/framework/*` folders (step 3)
- **OpenSSL already loaded** — harmless PHP warning from `php.ini`; ignore it
