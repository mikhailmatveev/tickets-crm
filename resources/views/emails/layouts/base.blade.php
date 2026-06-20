{{-- emails/layouts/base.blade.php --}}
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ваш аккаунт создан</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .wrapper {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        .header {
            background-color: #1a56db;
            padding: 32px;
            text-align: center;
        }
        .header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 22px;
        }
        .body {
            padding: 32px;
            color: #333333;
            line-height: 1.6;
        }
        .credentials {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 16px 20px;
            margin: 24px 0;
        }
        .credentials p {
            margin: 6px 0;
            font-size: 15px;
        }
        .credentials span {
            font-weight: bold;
            color: #1a56db;
        }
        .button-wrap {
            text-align: center;
            margin: 32px 0;
        }
        .button {
            display: inline-block;
            background-color: #1a56db;
            color: #ffffff;
            text-decoration: none;
            padding: 14px 32px;
            border-radius: 6px;
            font-size: 15px;
            font-weight: bold;
        }
        .notice {
            font-size: 13px;
            color: #888888;
            text-align: center;
            margin-top: 8px;
        }
        .footer {
            background-color: #f8fafc;
            border-top: 1px solid #e2e8f0;
            padding: 20px 32px;
            text-align: center;
            font-size: 13px;
            color: #888888;
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
