<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Registration | RestaurantOS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700&family=Outfit:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <style>
        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            background: #14110e;
            font-family: Outfit, sans-serif;
            color: #1a1510;
            padding: 20px;
        }

        .card {
            width: min(520px, 100%);
            background: #fffdf8;
            border: 1px solid #e4d8c8;
            border-radius: 24px;
            padding: 32px;
        }

        h1 {
            margin: 0;
            font-family: Fraunces, serif;
            font-size: 34px;
        }

        p {
            margin: 8px 0 20px;
            color: #74685c;
        }

        label {
            display: block;
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 6px;
        }

        input {
            width: 100%;
            border: 1px solid #e4d8c8;
            border-radius: 12px;
            padding: 11px 12px;
            margin-bottom: 14px;
            font-size: 14px;
            box-sizing: border-box;
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

        .alert {
            margin-bottom: 12px;
            background: #fee2e2;
            border: 1px solid #fecaca;
            color: #991b1b;
            padding: 10px;
            border-radius: 10px;
            font-size: 13px;
        }

        .links {
            margin-top: 16px;
            display: flex;
            justify-content: space-between;
            gap: 10px;
            flex-wrap: wrap;
        }

        a {
            color: #c45c26;
            text-decoration: none;
            font-weight: 700;
        }

        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        @media (max-width: 680px) {
            .grid {
                grid-template-columns: 1fr;
                gap: 0;
            }
        }
    </style>
</head>

<body>
    <div class="card">
        <h1>Create Account</h1>
        <p>Register as a customer to manage your orders and profile.</p>

        @if ($errors->any())
            <div class="alert">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('customer.register.store') }}">
            @csrf

            <label>Full Name</label>
            <input type="text" name="name" value="{{ old('name') }}" required>

            <div class="grid">
                <div>
                    <label>Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required>
                </div>
                <div>
                    <label>Phone</label>
                    <input type="text" name="phone" value="{{ old('phone') }}">
                </div>
            </div>

            <div class="grid">
                <div>
                    <label>Password</label>
                    <input type="password" name="password" required>
                </div>
                <div>
                    <label>Confirm Password</label>
                    <input type="password" name="password_confirmation" required>
                </div>
            </div>

            <button type="submit">Register</button>
        </form>

        <div class="links">
            <a href="{{ route('customer.login') }}">Already have an account?</a>
            <a href="{{ route('website.home') }}">Back to website</a>
        </div>
    </div>
</body>

</html>
