<?php

namespace App\Http\Controllers;

use App\Library\SslCommerz\SslCommerzNotification;
use App\Models\Order;
use App\Models\PaymentTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * SslCommerzPaymentController
 * Purpose: lightweight demo controller for SSLCommerz sandbox flow.
 * Key actions: create demo order, redirect to gateway, handle callbacks
 * and sync payments to the main `orders` and `payment_transactions` tables.
 * Comments are intentionally concise to aid quick navigation.
 */
class SslCommerzPaymentController extends Controller
{
    public function exampleEasyCheckout()
    {
        $rows = DB::table('ssl_example_orders as seo')
            ->leftJoin('users as u', 'u.id', '=', 'seo.user_id')
            ->leftJoin('orders as o', 'o.id', '=', 'seo.main_order_id')
            ->select('seo.id', 'seo.transaction_id', 'seo.name', 'seo.status', 'seo.amount', 'seo.currency', 'u.name as user_name', 'o.order_number')
            ->orderByDesc('seo.id')
            ->limit(10)
            ->get();

        return view('sslcommerz.exampleEasycheckout', compact('rows'));
    }

    public function exampleHostedCheckout()
    {
        return $this->exampleEasyCheckout();
    }

    public function index(Request $request)
    {
        $postData = $this->buildPostData($request);
        $this->saveDemoOrder($postData);

        $sslc = new SslCommerzNotification();
        $paymentOptions = $sslc->makePayment($postData, 'hosted');

        // If library returned an error message string
        if (is_string($paymentOptions)) {
            return back()->with('error', $paymentOptions);
        }

        // Expecting an array with 'GatewayPageURL' returned from the library
        if (is_array($paymentOptions) && !empty($paymentOptions['GatewayPageURL'])) {
            $gatewayUrl = $paymentOptions['GatewayPageURL'];

            // try to extract SESSIONKEY (or session key) from gateway URL query
            $parts = parse_url($gatewayUrl);
            if (!empty($parts['query'])) {
                parse_str($parts['query'], $qs);
                $sessionKey = $qs['SESSIONKEY'] ?? $qs['sessionkey'] ?? $qs['session_key'] ?? null;
                if ($sessionKey) {
                    DB::table('ssl_example_orders')
                        ->where('transaction_id', $postData['tran_id'])
                        ->update(['session_key' => $sessionKey]);
                }
            }

            // Redirect the user's browser to the gateway page
            return redirect()->away($gatewayUrl);
        }

        return back()->with('error', 'Could not initiate payment session');
    }

    public function payViaAjax(Request $request)
    {
        $postData = $this->buildPostData($request);
        $this->saveDemoOrder($postData);

        $sslc = new SslCommerzNotification();
        $paymentOptions = $sslc->makePayment($postData, 'checkout', 'json');

        return response($paymentOptions, 200)->header('Content-Type', 'application/json');
    }

    public function success(Request $request)
    {
        $tranId = $request->input('tran_id');
        $amount = $request->input('amount');
        $currency = $request->input('currency');

        // Allow gateway to post SESSIONKEY instead of tran_id
        $sessionKey = $request->input('SESSIONKEY') ?: $request->input('sessionkey') ?: $request->input('session_key');

        $order = null;
        if ($tranId) {
            $order = DB::table('ssl_example_orders')->where('transaction_id', $tranId)->first();
        }

        if (!$order && $tranId) {
            // Maybe gateway sent tran_id as SESSIONKEY — try session_key fallback
            $order = DB::table('ssl_example_orders')->where('session_key', $tranId)->first();
            if ($order) {
                $tranId = $order->transaction_id;
            }
        }

        if (!$order && $sessionKey) {
            $order = DB::table('ssl_example_orders')->where('session_key', $sessionKey)->first();
            if ($order) {
                $tranId = $order->transaction_id;
            }
        }

        if (!$order) {
            return response('Invalid Transaction', 404);
        }

        if ($order->status === 'Pending') {
            $sslc = new SslCommerzNotification();
            $validation = $sslc->orderValidate($request->all(), $tranId, $amount, $currency);

            if ($validation) {
                DB::table('ssl_example_orders')->where('transaction_id', $tranId)->update(['status' => 'Complete']);
                $this->syncMainOrderPayment($order, $tranId, $amount, $request->all());
                $message = "Transaction is successfully Completed";
                // Always show local test success page with tran_id (no auto back)
                return redirect('/success?tran_id=' . urlencode($tranId))->with('success', $message);
            }
        }

        if ($order->status === 'Processing' || $order->status === 'Complete') {
            $this->syncMainOrderPayment($order, $tranId, $amount, $request->all());
            $message = "Transaction is successfully Completed";
            return redirect('/success?tran_id=' . urlencode($tranId))->with('success', $message);
        }

        return response('Invalid Transaction', 422);
    }

    public function fail(Request $request)
    {
        $tranId = $request->input('tran_id');
        $order = DB::table('ssl_example_orders')->where('transaction_id', $tranId)->first();

        if (!$order) {
            return response('Invalid Transaction', 404);
        }

        if ($order->status === 'Pending') {
            DB::table('ssl_example_orders')->where('transaction_id', $tranId)->update(['status' => 'Failed']);
            return response('Transaction is Failed');
        }

        if ($order->status === 'Processing' || $order->status === 'Complete') {
            return response('Transaction is already Successful');
        }

        return response('Transaction is Invalid', 422);
    }

    public function cancel(Request $request)
    {
        $tranId = $request->input('tran_id');
        $order = DB::table('ssl_example_orders')->where('transaction_id', $tranId)->first();

        if (!$order) {
            return response('Invalid Transaction', 404);
        }

        if ($order->status === 'Pending') {
            DB::table('ssl_example_orders')->where('transaction_id', $tranId)->update(['status' => 'Canceled']);
            return response('Transaction is Canceled');
        }

        if ($order->status === 'Processing' || $order->status === 'Complete') {
            return response('Transaction is already Successful');
        }

        return response('Transaction is Invalid', 422);
    }

    public function ipn(Request $request)
    {
        if (!$request->input('tran_id')) {
            return response('Invalid Data', 422);
        }

        $tranId = $request->input('tran_id');
        $order = DB::table('ssl_example_orders')->where('transaction_id', $tranId)->first();

        if (!$order) {
            return response('Invalid Transaction', 404);
        }

        if ($order->status === 'Pending') {
            $sslc = new SslCommerzNotification();
            $validation = $sslc->orderValidate($request->all(), $tranId, $order->amount, $order->currency);

            if ($validation === true) {
                // IPN validated — mark complete
                DB::table('ssl_example_orders')->where('transaction_id', $tranId)->update(['status' => 'Complete']);
                $this->syncMainOrderPayment($order, $tranId, $order->amount, $request->all());
                return response('Transaction is successfully Completed');
            }
        }

        if ($order->status === 'Processing' || $order->status === 'Complete') {
            $this->syncMainOrderPayment($order, $tranId, $order->amount, $request->all());
            return response('Transaction is already successfully Completed');
        }

        return response('Invalid Transaction', 422);
    }

    /**
     * Simulate a successful gateway callback for local testing.
     * Accepts: tran_id, amount (optional), currency (optional)
     */
    public function simulateSuccess(Request $request)
    {
        $tranId = $request->input('tran_id');
        $amount = $request->input('amount');
        $currency = $request->input('currency', 'BDT');

        if (!$tranId) {
            return back()->with('error', 'tran_id is required for simulation');
        }

        $order = DB::table('ssl_example_orders')->where('transaction_id', $tranId)->first();
        if (!$order) {
            return back()->with('error', "Transaction not found: {$tranId}");
        }

        if ($order->status === 'Pending') {
            DB::table('ssl_example_orders')->where('transaction_id', $tranId)->update(['status' => 'Complete']);
            $message = "Payment successful: {$tranId}";
            if (auth()->check()) {
                return redirect()->route('customer.orders')->with('success', $message);
            }

            return redirect('/')->with('success', $message);
        }

        $info = "Transaction status is {$order->status}";
        if (auth()->check()) {
            return redirect()->route('customer.orders')->with('info', $info);
        }

        return redirect('/')->with('info', $info);
    }

    /**
     * Show local test success page with recent demo transactions to pick from.
     */
    public function showTestSuccess(Request $request)
    {
        $rows = DB::table('ssl_example_orders')
            ->orderByDesc('id')
            ->limit(20)
            ->get();

        $selectedTran = null;
        $tid = $request->query('tran_id') ?: $request->query('val_id') ?: $request->query('SESSIONKEY') ?: $request->query('sessionkey') ?: $request->query('session_key');

        if ($tid) {
            // Try to find by transaction_id first, then session_key
            $order = DB::table('ssl_example_orders')->where('transaction_id', $tid)->first();
            if (!$order) {
                $order = DB::table('ssl_example_orders')->where('session_key', $tid)->first();
            }

            if ($order) {
                $selectedTran = $order->transaction_id;
                // Only show that order in the dropdown for clarity
                $rows = collect([$order]);
            }
        }

        return view('success', compact('rows', 'selectedTran'));
    }

    private function buildPostData(Request $request): array
    {
        $userId = $request->input('user_id') ?: DB::table('users')->value('id');
        $mainOrderId = $request->input('main_order_id') ?: DB::table('orders')->value('id');

        return [
            'total_amount' => $request->input('total_amount', 1200),
            'currency' => $request->input('currency', 'BDT'),
            'tran_id' => $request->input('tran_id', 'SSLC-' . now()->format('YmdHis') . '-' . random_int(100, 999)),
            'cus_name' => $request->input('cus_name', 'Test Customer'),
            'cus_email' => $request->input('cus_email', 'test@example.com'),
            'cus_add1' => $request->input('cus_add1', 'Dhaka'),
            'cus_add2' => $request->input('cus_add2', ''),
            'cus_city' => $request->input('cus_city', 'Dhaka'),
            'cus_state' => $request->input('cus_state', 'Dhaka'),
            'cus_postcode' => $request->input('cus_postcode', '1000'),
            'cus_country' => $request->input('cus_country', 'Bangladesh'),
            'cus_phone' => $request->input('cus_phone', '01700000000'),
            'cus_fax' => $request->input('cus_fax', ''),
            'ship_name' => $request->input('ship_name', 'Test Store'),
            'ship_add1' => $request->input('ship_add1', 'Dhaka'),
            'ship_add2' => $request->input('ship_add2', 'Dhaka'),
            'ship_city' => $request->input('ship_city', 'Dhaka'),
            'ship_state' => $request->input('ship_state', 'Dhaka'),
            'ship_postcode' => $request->input('ship_postcode', '1000'),
            'ship_country' => $request->input('ship_country', 'Bangladesh'),
            'shipping_method' => $request->input('shipping_method', 'NO'),
            'num_of_item' => $request->input('num_of_item', 1),
            'product_name' => $request->input('product_name', 'Restaurant Order'),
            'product_category' => $request->input('product_category', 'Food'),
            'product_profile' => $request->input('product_profile', 'physical-goods'),
            'value_a' => $request->input('value_a', 'ssl-test-a'),
            'value_b' => $request->input('value_b', 'ssl-test-b'),
            'value_c' => $request->input('value_c', 'ssl-test-c'),
            'value_d' => $request->input('value_d', 'ssl-test-d'),
            'main_order_id' => $mainOrderId,
            'user_id' => $userId,
        ];
    }

    private function saveDemoOrder(array $postData): void
    {
        DB::table('ssl_example_orders')->updateOrInsert(
            ['transaction_id' => $postData['tran_id']],
            [
                'user_id' => $postData['user_id'],
                'main_order_id' => $postData['main_order_id'],
                'name' => $postData['cus_name'],
                'email' => $postData['cus_email'],
                'phone' => $postData['cus_phone'],
                'amount' => $postData['total_amount'],
                'address' => $postData['cus_add1'],
                'status' => 'Pending',
                'currency' => $postData['currency'],
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
    }

    /**
     * Sync demo payment into the main Order and PaymentTransaction records.
     * - Updates `orders` with paid status and amount
     * - Creates or updates a `payment_transactions` record
     */
    private function syncMainOrderPayment(object $demoOrder, string $transactionId, $paidAmount = null, array $gatewayResponse = []): void
    {
        if (empty($demoOrder->main_order_id)) {
            return;
        }

        $order = Order::find($demoOrder->main_order_id);
        if (!$order) {
            return;
        }

        $amount = is_numeric($paidAmount) ? (float) $paidAmount : (float) ($demoOrder->amount ?? $order->total_amount ?? 0);
        if ($amount <= 0) {
            $amount = (float) ($order->total_amount ?? 0);
        }

        $order->forceFill([
            'payment_status' => 'paid',
            'paid_amount' => $amount,
            'paid_at' => now(),
            'payment_method' => 'sslcommerz',
            'payment_reference' => $transactionId,
        ])->save();

        PaymentTransaction::updateOrCreate(
            [
                'order_id' => $order->id,
                'transaction_id' => $transactionId,
            ],
            [
                'gateway' => 'sslcommerz',
                'amount' => $amount,
                'status' => 'success',
                'payment_method' => 'sslcommerz',
                'gateway_response' => $gatewayResponse,
                'paid_at' => now(),
            ]
        );
    }
}
