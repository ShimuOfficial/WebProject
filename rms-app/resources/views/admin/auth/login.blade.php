{{-- DEFENSE: §5.2 staff login screen --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Staff Console — {{ $site['name'] }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ $site['favicon_url'] }}">
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700&family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            min-height: 100vh;
            font-family: Outfit, sans-serif;
            background: #14110e;
            color: #1a1510;
        }
        .wrap {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 1.1fr .9fr;
        }
        .pane {
            padding: 56px;
            color: #f6efe4;
            background:
                linear-gradient(160deg, rgba(20,17,14,.92), rgba(196,92,38,.35)),
                url('{{ $site['staff_image_url'] }}') center/cover;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
        }
        .pane img { width: 56px; height: 56px; border-radius: 14px; object-fit: cover; margin-bottom: 18px; }
        .pane h1 {
            font-family: Fraunces, serif;
            font-size: clamp(40px, 5vw, 64px);
            line-height: .95;
            max-width: 520px;
        }
        .pane p { color: rgba(246,239,228,.75); max-width: 460px; }
        .form-side {
            background: #f4efe6;
            display: grid;
            place-items: center;
            padding: 32px;
        }
        .card {
            width: min(420px, 100%);
            background: #fffdf8;
            border: 1px solid #e4d8c8;
            border-radius: 24px;
            padding: 32px;
        }
        .brand-row { display:flex; align-items:center; gap:10px; margin-bottom:16px; font-weight:800; }
        .brand-row img { width:40px; height:40px; border-radius:10px; object-fit:cover; }
        h2 { font-family: Fraunces, serif; font-size: 28px; }
        .subtitle { color: #74685c; margin-bottom: 22px; }
        .btn-login {
            width: 100%;
            border: 0;
            border-radius: 12px;
            padding: 12px;
            background: #c45c26;
            color: #fff;
            font-weight: 700;
        }
        .form-control, .input-group-text {
            border-color: #e4d8c8;
            background: #fffdf8;
        }
        html, body { max-width: 100%; overflow-x: hidden; scrollbar-width: none; }
        * { scrollbar-width: none; }
        @media (max-width: 900px) {
            .wrap { grid-template-columns: 1fr; }
            .pane { min-height: 220px; padding: 24px; }
            .pane h1 { font-size: 32px; }
            .form-side { padding: 16px; }
            .card { padding: 20px; border-radius: 18px; }
            h2 { font-size: 24px; }
        }
    </style>
</head>
<body>
    <div class="wrap">
        <section class="pane">
            <img src="{{ $site['logo_url'] }}" alt="{{ $site['name'] }}">
            <div class="section-label" style="letter-spacing:.18em;text-transform:uppercase;font-size:12px;color:#e8b86d">Staff access</div>
            <h1>{{ $site['content']['staff_login_title'] }}</h1>
            <p>{{ $site['content']['staff_login_subtitle'] }}</p>
        </section>
        <section class="form-side">
            <div class="card">
                <div class="brand-row">
                    <img src="{{ $site['logo_url'] }}" alt="{{ $site['name'] }}">
                    {{ $site['name'] }}
                </div>
                <h2>Console login</h2>
                <p class="subtitle">{{ $site['name'] }} operations</p>
                @if ($errors->any())
                    <div class="alert alert-danger py-2 px-3" style="border-radius:12px;font-size:13px;border:none">
                        {{ $errors->first() }}
                    </div>
                @endif
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="form-check mb-4">
                        <input type="checkbox" name="remember" class="form-check-input" id="remember">
                        <label class="form-check-label" for="remember">Remember me</label>
                    </div>
                    <button type="submit" class="btn-login">Enter console</button>
                </form>
                <div class="d-flex justify-content-between mt-3" style="font-size:13px">
                    <a href="{{ route('website.home') }}" style="color:#c45c26">Back to website</a>
                    <a href="{{ route('customer.login') }}" style="color:#74685c">Guest login</a>
                </div>
            </div>
        </section>
    </div>
</body>
</html>
