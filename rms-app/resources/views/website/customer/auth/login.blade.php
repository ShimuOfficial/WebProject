{{-- DEFENSE: §5.3 customer login --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Customer Login | {{ $site['name'] }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ $site['favicon_url'] }}">
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700&family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            grid-template-columns: 1.05fr .95fr;
            font-family: Outfit, sans-serif;
            color: #1a1510;
            background: #14110e;
        }
        .intro {
            padding: 56px;
            color: #f6efe4;
            background:
                linear-gradient(160deg, rgba(20,17,14,.9), rgba(31,61,52,.45)),
                url('{{ $site['login_image_url'] }}') center/cover;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
        }
        .intro img { width: 56px; height: 56px; border-radius: 14px; margin-bottom: 18px; object-fit: cover; }
        .intro h2 {
            font-family: Fraunces, serif;
            font-size: clamp(40px, 6vw, 68px);
            line-height: .95;
            margin: 0 0 12px;
        }
        .intro p { color: rgba(246,239,228,.76); max-width: 480px; line-height: 1.6; }
        .panel {
            background: #f4efe6;
            display: grid;
            place-items: center;
            padding: 28px;
        }
        .card {
            width: min(440px, 100%);
            background: #fffdf8;
            border: 1px solid #e4d8c8;
            border-radius: 24px;
            padding: 32px;
        }
        .brand-row { display:flex; align-items:center; gap:10px; margin-bottom:18px; font-weight:800; }
        .brand-row img { width:40px; height:40px; border-radius:10px; object-fit:cover; }
        h1 { margin: 0; font-family: Fraunces, serif; font-size: 32px; }
        p { color: #74685c; }
        label { display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px; }
        input {
            width: 100%;
            border: 1px solid #e4d8c8;
            border-radius: 12px;
            padding: 11px 12px;
            margin-bottom: 14px;
            box-sizing: border-box;
            font: inherit;
        }
        button {
            width: 100%;
            border: 0;
            border-radius: 12px;
            padding: 12px;
            background: #c45c26;
            color: #fff;
            font-weight: 700;
            cursor: pointer;
        }
        .password-wrap { position: relative; }
        .password-wrap input { padding-right: 76px; }
        .toggle-password {
            position: absolute; right: 8px; top: 7px; width: auto;
            padding: 5px 8px; border: 1px solid #ead3b8; background: #fff7ed;
            color: #7b4326; border-radius: 8px; font-size: 12px;
        }
        .alert { margin-bottom: 12px; background: #fee2e2; border: 1px solid #fecaca; color: #991b1b; padding: 10px; border-radius: 12px; font-size: 13px; }
        .links { margin-top: 16px; display: flex; justify-content: space-between; gap: 10px; flex-wrap: wrap; }
        a { color: #c45c26; text-decoration: none; font-weight: 700; }
        @media (max-width: 820px) {
            body { grid-template-columns: 1fr; overflow-x: hidden; }
            .intro { min-height: 220px; padding: 24px; }
            .intro h2 { font-size: 32px; }
            .panel { padding: 16px; }
            .card { padding: 20px; border-radius: 18px; }
            h1 { font-size: 26px; }
        }
    </style>
</head>
<body>
    <section class="intro">
        <img src="{{ $site['logo_url'] }}" alt="{{ $site['name'] }}">
        <h2>{{ $site['content']['login_title'] }}</h2>
        <p>{{ $site['content']['login_subtitle'] }}</p>
    </section>
    <section class="panel">
        <div class="card">
            <div class="brand-row">
                <img src="{{ $site['logo_url'] }}" alt="{{ $site['name'] }}">
                {{ $site['name'] }}
            </div>
            <h1>Login</h1>
            <p>Use your customer account to continue.</p>
            @if (session('success'))
                <div class="alert" style="background:#dcfce7;border-color:#bbf7d0;color:#166534">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert">{{ $errors->first() }}</div>
            @endif
            <form method="POST" action="{{ route('customer.login.store') }}">
                @csrf
                <label for="email">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="email" required autofocus>
                <label for="password">Password</label>
                <div class="password-wrap">
                    <input id="password" type="password" name="password" autocomplete="current-password" required>
                    <button class="toggle-password" type="button" onclick="togglePassword()">Show</button>
                </div>
                <label style="margin:0 0 16px;font-weight:600"><input type="checkbox" name="remember" style="width:auto;margin-right:6px"> Remember me</label>
                <button type="submit">Sign in</button>
            </form>
            <div class="links">
                <a href="{{ route('customer.register') }}">Create account</a>
                <a href="{{ route('login') }}">Admin panel</a>
                <a href="{{ route('website.home') }}">Website</a>
            </div>
        </div>
    </section>
    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const button = document.querySelector('.toggle-password');
            const isHidden = input.type === 'password';
            input.type = isHidden ? 'text' : 'password';
            button.textContent = isHidden ? 'Hide' : 'Show';
        }
    </script>
</body>
</html>
