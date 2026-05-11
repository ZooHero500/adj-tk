<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verify Your Email on PornTk</title>
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
            background-color: #3b82f6;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
            margin: 24px 0;
        }
        .button:hover {
            background-color: #2563eb;
        }
        .footer {
            margin-top: 32px;
            padding-top: 24px;
            border-top: 1px solid #e5e7eb;
            font-size: 14px;
            color: #6b7280;
        }
        .warning {
            background-color: #fef3c7;
            border: 1px solid #f59e0b;
            border-radius: 6px;
            padding: 16px;
            margin: 24px 0;
        }
        .warning-icon {
            color: #f59e0b;
            margin-right: 8px;
        }
    </style>
</head>
<body>
    @php($host = parse_url(config('app.url'), PHP_URL_HOST))
    <div class="container">
        <div class="header">
            <div class="logo">PornTk</div>
            <h1 class="title">Verify Your Email on PornTk</h1>
        </div>
        
        <p>Hi <strong>{{ $user->username }}</strong>,</p>
        
        <p>You recently requested to change your email address on your <strong>{{ $host }}</strong> account. To complete this change and verify your new email address, please click the button below:</p>
        
        <div style="text-align: center;">
            <a href="{{ $verificationUrl }}" class="button">Verify New Email Address</a>
        </div>
        
        <p>If the button doesn't work, you can also copy and paste this link into your browser:</p>
        <p style="word-break: break-all; color: #3b82f6;">{{ $verificationUrl }}</p>
        
        <div class="warning">
            <span class="warning-icon">⚠️</span>
            <strong>Important:</strong> This verification link will expire in 24 hours. If you didn't request this email change, please ignore this email and consider changing your account password.
        </div>
        
        <p style="font-size:14px;">Once verified, your new email address will become your primary email for:</p>
        <ul style="font-size:14px;">
            <li>Account login</li>
            <li>Security notifications</li>
            <li>Account recovery</li>
            <li>All other account communications</li>
        </ul>
        
        <div class="footer">
            <p>This email was sent to you because a request was made to change your PornTk account email address.</p>
            <p>If you have any questions, please contact our <a href="{{ url('/platform/contact') }}">support team</a>.</p>
            <p>&copy; {{ date('Y') }} {{ $host }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
