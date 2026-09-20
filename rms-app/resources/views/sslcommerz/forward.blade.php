{{-- DEFENSE: §5.17 auto-submit form to SSLCommerz sandbox --}}
<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Redirecting to Payment...</title>
</head>

<body>
    <!-- Dev: Auto-submit form to initiate SSLCommerz hosted checkout (sandbox). Callbacks set to our app. -->
    <form id="sslForm" method="POST" action="{{ url('/sslcommerz/pay') }}">
        @csrf
        <input type="hidden" name="total_amount" value="{{ $order->total_amount }}">
        <input type="hidden" name="currency" value="BDT">
        <input type="hidden" name="tran_id" value="{{ $order->order_number }}">
        <input type="hidden" name="cus_name" value="{{ optional($order->user)->name ?? 'Guest' }}">
        <input type="hidden" name="cus_email" value="{{ optional($order->user)->email ?? '' }}">
        <input type="hidden" name="cus_phone"
            value="{{ optional($order->user)->phone ?? env('SSLCOMMERZ_TEST_PHONE', '01700000000') }}">
        <input type="hidden" name="cus_add1" value="{{ optional($order->user)->address ?? ($order->notes ?? 'Dhaka') }}">
        <input type="hidden" name="product_name" value="Restaurant Order {{ $order->order_number }}">
        <input type="hidden" name="product_category" value="Food">
        <input type="hidden" name="product_profile" value="physical-goods">
        <input type="hidden" name="user_id" value="{{ $order->user_id }}">
        <input type="hidden" name="main_order_id" value="{{ $order->id }}">
        <input type="hidden" name="success_url" value="{{ url('/sslcommerz/success') }}">
        <input type="hidden" name="fail_url" value="{{ url('/sslcommerz/fail') }}">
        <input type="hidden" name="cancel_url" value="{{ url('/sslcommerz/cancel') }}">
        <input type="hidden" name="ipn_url" value="{{ url('/sslcommerz/ipn') }}">
    </form>
    <script>
        document.getElementById('sslForm').submit();
    </script>
</body>

</html>
