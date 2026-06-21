{{-- emails/layouts/base.blade.php --}}
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ваш аккаунт создан</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #fafafa;
            font-family: Arial, sans-serif;
            font-size: 16px;
        }
        .header,
        .button-wrap,
        .notice,
        .footer {
            text-align: center;
        }
        .notice,
        .footer {
            color: #888;
        }
        .wrapper {
            max-width: 600px;
            margin: 40px auto;
            padding: 30px;
            overflow: hidden;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        .header {
            padding: 32px;
            background-color: #1a56db;
        }
        .header h1 {
            margin: 0;
            color: #fff;
            font-size: 24px;
        }
        .credentials {
            margin: 24px 0;
            padding: 16px 20px;
            background-color: #f8fafc;
            border-top: 1px solid #e2e8f0;
            border-bottom: 1px solid #e2e8f0;
        }
        .credentials p {
            margin: 6px 0;
        }
        .credentials span {
            color: #1a56db;
            font-weight: 700;
        }
        .button-wrap {
            margin: 32px 0;
        }
        .button {
            display: inline-block;
            padding: 14px 32px;
            background-color: #1a56db;
            border-radius: 6px;
            color: #fff;
            text-decoration: none;
        }
        .footer {
            border-top: 1px solid #e2e8f0;
            border-bottom: 1px solid #e2e8f0;
            font-size: 14px;
        }
        .footer p {
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        @yield('content')
        <div class="footer">
            &copy; {{ date('Y') }} {{ config('app.name') }}. Все права защищены.
        </div>
    </div>
</body>
</html>
