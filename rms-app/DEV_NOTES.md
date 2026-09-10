---
noteId: "2b7dd4a05cec11f1bc30b1685060d607"
tags: []
---

# DEV NOTES - SSLCommerz demo (minimal)

- Purpose: quick developer guide to the SSLCommerz sandbox integration and where to find core code.

- Key files:
    - app/Http/Controllers/SslCommerzPaymentController.php : main demo flow (create demo order, redirect, callbacks, sync)
    - resources/views/sslcommerz/forward.blade.php : auto-submit form to gateway (hosted flow)
    - resources/views/success.blade.php : local test page to simulate/confirm payments
    - routes/web.php : demo routes start with `/sslcommerz/*` and `/success` (test UI)
    - database/migrations/_ssl_example_orders_.php : demo table structure

- Quick test steps:
    1. Ensure `.env` has `APP_URL=http://127.0.0.1:8000` and session domain set to `127.0.0.1`.
    2. Visit `/sslcommerz/example1` and follow the flow; after gateway returns, use the `/success` page.
    3. For local-only marking, use the "Simulate Success" button on `/success` (OTP not required for simulate).

- Notes: Comments in code are intentionally brief. Ask if you need a longer explanation for any specific function.
