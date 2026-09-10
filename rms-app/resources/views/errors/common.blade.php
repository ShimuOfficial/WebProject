<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error {{ $status ?? 500 }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            background: #f3f4f6;
            color: #111827;
        }

        .box {
            max-width: 520px;
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 28px;
            box-shadow: 0 12px 30px rgba(0, 0, 0, .06);
            text-align: center;
        }

        .code {
            font-size: 48px;
            font-weight: 700;
            margin: 0 0 10px;
        }

        .msg {
            font-size: 16px;
            color: #4b5563;
            margin: 0 0 18px;
        }

        a {
            display: inline-block;
            padding: 10px 16px;
            border-radius: 8px;
            background: #4f46e5;
            color: white;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <div class="box">
        <p class="code">{{ $status ?? 500 }}</p>
        <p class="msg">{{ $message ?? 'Something went wrong.' }}</p>
        <a href="{{ url()->previous() !== url()->current() ? url()->previous() : url('/') }}">Go Back</a>
    </div>
</body>

</html>
