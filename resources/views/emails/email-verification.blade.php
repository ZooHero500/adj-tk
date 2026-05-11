<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verify Your Email Address</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8fafc;
        }
        .container {
            background: white;
            border-radius: 8px;
            padding: 32px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 32px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #3b82f6;
            margin-bottom: 8px;
        }
        .title {
            font-size: 20px;
            font-weight: 600;
            color: #111827;
            margin-bottom: 16px;
        }
        .button {
            display: inline-block;
            background-color: #10b981;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
            margin: 24px 0;
        }
        .button:hover {
            background-color: #059669;
        }
        .footer {
            margin-top: 32px;
            padding-top: 24px;
            border-top: 1px solid #e5e7eb;
            font-size: 14px;
            color: #6b7280;
        }
    </style>
</head>
<body>
    @php($host = parse_url(config('app.url'), PHP_URL_HOST))
    <div class="container">
        <div class="header">
            <div class="logo">PornTk</div>
            <h1 class="title">Welcome to PornTk!</h1>
        </div>

        <p>Hi @<strong>{{ $user->username }}</strong>,</p>

        <p>Thanks for joining <a href="https://{{$host}}">PornTk</a>! To get started and secure your account, please verify your email address by clicking the button below:</p>

        <div style="text-align: center;">
            <a href="{{ $verificationUrl }}" class="button">Verify Email Address</a>
        </div>

        <p>If the button doesn't work, you can also copy and paste this link into your browser:</p>
        <p style="word-break: break-all; color: #3b82f6;">{{ $verificationUrl }}</p>

        <p style="font-size:14px;">Once your email is verified, you'll be able to:</p>
        <ul style="font-size:14px;">
            <li>Receive important security notifications</li>
            <li>Reset your password if needed</li>
        </ul>

        <div class="footer">
            <p>If you didn't create a PornTk account on {{ $host }}, you can safely ignore this email.</p>
            <p>&copy; {{ date('Y') }} {{ $host }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
