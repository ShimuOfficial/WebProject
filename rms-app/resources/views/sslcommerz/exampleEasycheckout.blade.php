<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SSLCommerz Demo</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>

<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h3 class="mb-3">SSLCommerz Test Checkout</h3>
                        <p class="text-muted mb-4">This page uses the new `ssl_example_orders` table for payment and
                            join testing.</p>

                        @if (session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        <form method="POST" action="{{ url('/sslcommerz/pay') }}" class="row g-3 mb-3">
                            @csrf
                            <div class="col-md-6">
                                <label class="form-label">Name</label>
                                <input type="text" name="cus_name" class="form-control" value="Test Customer One">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" name="cus_email" class="form-control" value="test1@example.com">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone</label>
                                <input type="text" name="cus_phone" class="form-control" value="01700000001">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Amount</label>
                                <input type="number" step="0.01" name="total_amount" class="form-control"
                                    value="1250.50">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Address</label>
                                <input type="text" name="cus_add1" class="form-control" value="Dhaka">
                            </div>
                            <div class="col-12 d-flex gap-2">
                                <button class="btn btn-primary" type="submit">Hosted Checkout</button>
                                <button class="btn btn-outline-primary" id="sslczPayBtn" token="" postdata=""
                                    order="" endpoint="{{ url('/sslcommerz/pay-via-ajax') }}" type="button">
                                    Popup Checkout
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="mb-3">Joined Test Rows</h5>
                        <div class="table-responsive">
                            <table class="table table-sm table-striped align-middle">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Transaction</th>
                                        <th>Name</th>
                                        <th>Status</th>
                                        <th>Amount</th>
                                        <th>User</th>
                                        <th>Order</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($rows as $row)
                                        <tr>
                                            <td>{{ $row->id }}</td>
                                            <td>{{ $row->transaction_id }}</td>
                                            <td>{{ $row->name }}</td>
                                            <td>{{ $row->status }}</td>
                                            <td>{{ $row->amount }}</td>
                                            <td>{{ $row->user_name ?? '-' }}</td>
                                            <td>{{ $row->order_number ?? '-' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted">No rows yet</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        var obj = {};
        obj.cus_name = $('input[name="cus_name"]').val();
        obj.cus_phone = $('input[name="cus_phone"]').val();
        obj.cus_email = $('input[name="cus_email"]').val();
        obj.cus_addr1 = $('input[name="cus_add1"]').val();
        obj.amount = $('input[name="total_amount"]').val();
        $('#sslczPayBtn').prop('postdata', obj);

        (function(window, document) {
            var loader = function() {
                var script = document.createElement('script');
                var tag = document.getElementsByTagName('script')[0];
                script.src =
                    "{{ config('sslcommerz.apiDomain') === 'https://sandbox.sslcommerz.com' ? 'https://sandbox.sslcommerz.com/embed.min.js' : 'https://seamless-epay.sslcommerz.com/embed.min.js' }}?" +
                    Math.random().toString(36).substring(7);
                tag.parentNode.insertBefore(script, tag);
            };

            window.addEventListener ? window.addEventListener('load', loader, false) : window.attachEvent('onload',
                loader);
        })(window, document);
    </script>
</body>

</html>
