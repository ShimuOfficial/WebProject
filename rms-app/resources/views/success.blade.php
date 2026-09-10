<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Payment Success (Test)</title>
    <style>
        body {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial;
            padding: 2rem;
        }

        .card {
            max-width: 680px;
            margin: 2rem auto;
            padding: 1.5rem;
            border: 1px solid #e5e7eb;
            border-radius: 8px
        }

        h1 {
            margin: 0 0 1rem
        }

        .otp {
            font-weight: 700;
            font-size: 1.25rem;
            color: #0b74de
        }

        .hint {
            color: #6b7280;
            margin-top: .5rem
        }

        .actions {
            margin-top: 1.25rem
        }

        a.btn {
            display: inline-block;
            padding: .5rem .75rem;
            border-radius: 6px;
            background: #111827;
            color: #fff;
            text-decoration: none
        }
    </style>
</head>

<body>
    <!-- Dev: Local SSLCommerz test page. OTP=12345. Minimal comments for quick dev navigation. -->
    <div class="card">
        <h1>Payment Success (Test)</h1>
        <p>SSLCommerz</p>

        <p class="hint">If you need the gateway to mark an order paid, use the API callback flow (/sslcommerz/success
            or /sslcommerz/ipn) with test parameters.</p>
        @if (session('success'))
            <div style="padding:.5rem;border:1px solid #10b981;background:#ecfdf5;color:#064e3b;margin-bottom:1rem">
                {{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div style="padding:.5rem;border:1px solid #ef4444;background:#fff1f2;color:#7f1d1d;margin-bottom:1rem">
                {{ session('error') }}</div>
        @endif
        @if (session('info'))
            <div style="padding:.5rem;border:1px solid #6b7280;background:#f3f4f6;color:#111827;margin-bottom:1rem">
                {{ session('info') }}</div>
        @endif

        <form id="simulateForm" method="POST" action="{{ route('sslcommerz.simulate_success') }}">
            @csrf

            <label style="display:block;margin-top:1rem">
                <div style="font-weight:600">Pick a demo transaction</div>
                <select id="demoTran" style="width:100%;padding:.5rem;margin-top:.25rem"
                    @if (!empty($selectedTran)) disabled @endif>
                    @if (empty($selectedTran))
                        <option value="">-- choose recent transaction --</option>
                    @endif
                    @if (!empty($rows) && count($rows))
                        @foreach ($rows as $r)
                            <option value="{{ $r->transaction_id }}" @if (!empty($selectedTran) && $selectedTran === $r->transaction_id) selected @endif>
                                {{ $r->transaction_id }} —
                                {{ $r->name ?? ($r->email ?? 'guest') }} — ৳{{ number_format($r->amount, 2) }} —
                                [{{ $r->status }}]</option>
                        @endforeach
                    @endif
                </select>
            </label>

            <label style="display:block;margin-top:1rem">
                <div style="font-weight:600">Transaction ID (tran_id)</div>
                <input id="tranInput" name="tran_id" placeholder="Paste tran_id or pick above"
                    style="width:100%;padding:.5rem;margin-top:.25rem"
                    @if (!empty($selectedTran)) value="{{ $selectedTran }}" readonly @endif />
            </label>

            <div class="actions" style="margin-top:1rem">
                @if (empty($selectedTran))
                    <button type="submit" class="btn">Simulate Success</button>
                @endif
                <a class="btn" href="http://127.0.0.1:8000/" style="margin-left:.5rem;background:#6b7280">Back to
                    site</a>
            </div>
        </form>

        <script>
            (function() {
                const sel = document.getElementById('demoTran');
                const input = document.getElementById('tranInput');
                const form = document.getElementById('simulateForm');
                if (sel && input) {
                    sel.addEventListener('change', function() {
                        input.value = this.value || '';
                    });
                }

                // Auto-fill and auto-submit when gateway redirects back with tran_id or val_id
                try {
                    const params = new URLSearchParams(window.location.search);
                    const tid = params.get('tran_id') || params.get('tranID') || params.get('val_id') || params.get(
                        'SESSIONKEY');
                    if (tid && input && form) {
                        // If it's a SESSIONKEY, we cannot map it to transaction_id reliably,
                        // but most gateways include tran_id or val_id — use whichever present.
                        input.value = tid;

                        // show countdown and submit after 10 seconds
                        let countdown = 10;
                        const notice = document.createElement('div');
                        notice.id = 'autoNotice';
                        notice.style.marginTop = '.75rem';
                        notice.style.padding = '.5rem';
                        notice.style.border = '1px solid #c7e0d9';
                        notice.style.background = '#ecfdf5';
                        notice.style.color = '#064e3b';
                        notice.style.borderRadius = '4px';
                        notice.textContent = `Redirecting in ${countdown} seconds...`;
                        form.parentNode.insertBefore(notice, form.nextSibling);

                        const tick = setInterval(() => {
                            countdown -= 1;
                            if (countdown <= 0) {
                                clearInterval(tick);
                                form.submit();
                                return;
                            }
                            notice.textContent = `Redirecting in ${countdown} seconds...`;
                        }, 1000);
                    }
                } catch (e) {
                    // ignore
                }
            })();
        </script>
    </div>
</body>

</html>
