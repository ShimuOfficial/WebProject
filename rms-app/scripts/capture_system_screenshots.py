"""
Capture full-page screenshots of every public, customer, and staff screen.
Usage:
    python scripts/capture_system_screenshots.py
"""

from __future__ import annotations

from pathlib import Path

from playwright.sync_api import TimeoutError as PlaywrightTimeout
from playwright.sync_api import sync_playwright

BASE_URL = "http://127.0.0.1:8080"
OUT_DIR = Path(__file__).resolve().parents[1] / "screenshots"

PUBLIC_PAGES = [
    ("01-home", "/"),
    ("02-about", "/about"),
    ("03-menu", "/our-menu"),
    ("04-contact", "/contact"),
    ("05-staff-login", "/login"),
    ("06-customer-login", "/customer/login"),
    ("07-customer-register", "/customer/register"),
]

CUSTOMER_PAGES = [
    ("10-customer-account", "/customer/account"),
    ("11-customer-cart", "/customer/cart"),
    ("12-customer-orders", "/customer/orders"),
]

ADMIN_PAGES = [
    ("20-dashboard", "/dashboard"),
    ("21-notifications", "/notifications"),
    ("22-orders", "/orders"),
    ("23-orders-create", "/orders/create"),
    ("24-menu-admin", "/menu"),
    ("25-menu-create", "/menu/create"),
    ("26-kitchen", "/kitchen"),
    ("27-tables", "/tables"),
    ("28-reservations", "/reservations"),
    ("29-inventory", "/inventory"),
    ("30-customers", "/customers"),
    ("31-reports", "/reports"),
    ("32-staff", "/staff"),
    ("33-settings", "/settings"),
    ("34-site-settings", "/admin/site-settings"),
]

ACCOUNTS = {
    "admin": {"email": "admin@restaurant.com", "password": "password", "login": "/login"},
    "customer": {"email": "rahim@customer.com", "password": "password", "login": "/customer/login"},
}


def snap(page, name: str) -> Path:
    OUT_DIR.mkdir(parents=True, exist_ok=True)
    path = OUT_DIR / f"{name}.png"
    page.wait_for_timeout(400)
    page.screenshot(path=str(path), full_page=True, animations="disabled")
    print(f"saved {path.name}")
    return path


def goto(page, path: str) -> None:
    page.goto(f"{BASE_URL}{path}", wait_until="domcontentloaded", timeout=45000)
    page.wait_for_timeout(600)


def login(page, account: str) -> None:
    creds = ACCOUNTS[account]
    goto(page, creds["login"])
    page.fill('input[name="email"]', creds["email"])
    page.fill('input[name="password"]', creds["password"])
    page.click('button[type="submit"]')
    page.wait_for_load_state("domcontentloaded")
    page.wait_for_timeout(800)


def first_href(page, selector: str) -> str | None:
    loc = page.locator(selector).first
    if loc.count() == 0:
        return None
    href = loc.get_attribute("href")
    if not href:
        return None
    return href if href.startswith("http") else href


def capture_public(page) -> None:
    for name, path in PUBLIC_PAGES:
        goto(page, path)
        snap(page, name)

    goto(page, "/our-menu")
    trigger = page.locator(".js-open-dish-modal, .js-dish-preview").first
    if trigger.count():
        trigger.click()
        page.wait_for_timeout(700)
        snap(page, "03b-menu-dish-preview")
        page.keyboard.press("Escape")


def capture_customer(context) -> None:
    page = context.new_page()
    login(page, "customer")
    for name, path in CUSTOMER_PAGES:
        goto(page, path)
        snap(page, name)
    page.close()


def capture_admin(context) -> None:
    page = context.new_page()
    login(page, "admin")
    for name, path in ADMIN_PAGES:
        try:
            goto(page, path)
            snap(page, name)
        except PlaywrightTimeout:
            print(f"timeout {path}")
            snap(page, f"{name}-timeout")

    edit_menu = first_href(page, 'a[href*="/menu/"][href*="/edit"]')
    if edit_menu:
        page.goto(edit_menu if edit_menu.startswith("http") else f"{BASE_URL}{edit_menu}", wait_until="domcontentloaded")
        snap(page, "25b-menu-edit")

    receipt = first_href(page, 'a[href*="/orders/"][href*="/receipt"]')
    if receipt:
        page.goto(receipt if receipt.startswith("http") else f"{BASE_URL}{receipt}", wait_until="domcontentloaded")
        snap(page, "23b-order-receipt")

    goto(page, "/orders")
    edit_order = first_href(page, 'a[href*="/orders/"][href*="/edit"]')
    if edit_order:
        page.goto(edit_order if edit_order.startswith("http") else f"{BASE_URL}{edit_order}", wait_until="domcontentloaded")
        snap(page, "23c-order-edit")

    page.close()


def main() -> None:
    OUT_DIR.mkdir(parents=True, exist_ok=True)
    with sync_playwright() as playwright:
        browser = playwright.chromium.launch(headless=True)
        context = browser.new_context(
            viewport={"width": 1440, "height": 900},
            device_scale_factor=1,
        )
        page = context.new_page()
        capture_public(page)
        page.close()
        capture_customer(context)
        capture_admin(context)
        browser.close()
    print(f"Done. Screenshots are in {OUT_DIR}")


if __name__ == "__main__":
    main()
