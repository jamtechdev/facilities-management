<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to {{ config('app.name') }}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 0;
        }
        .email-header {
            background: linear-gradient(135deg, #84c373 0%, #6ba85a 100%);
            padding: 40px 30px;
            text-align: center;
            color: #ffffff;
        }
        .email-header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
        }
        .email-body {
            padding: 40px 30px;
        }
        .credentials-box {
            background-color: #f8f9fa;
            border-left: 4px solid #84c373;
            padding: 20px;
            margin: 25px 0;
            border-radius: 4px;
        }
        .credentials-box h3 {
            margin-top: 0;
            color: #84c373;
            font-size: 18px;
        }
        .credential-item {
            margin: 10px 0;
            font-size: 16px;
        }
        .credential-label {
            font-weight: 600;
            color: #555;
            display: inline-block;
            width: 120px;
        }
        .credential-value {
            color: #333;
            font-family: 'Courier New', monospace;
            background-color: #ffffff;
            padding: 5px 10px;
            border-radius: 4px;
            border: 1px solid #dee2e6;
        }
        .button {
            display: inline-block;
            padding: 14px 30px;
            background: linear-gradient(135deg, #84c373 0%, #6ba85a 100%);
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin: 20px 0;
            text-align: center;
        }
        .button:hover {
            opacity: 0.9;
        }
        .email-footer {
            background-color: #f8f9fa;
            padding: 30px;
            text-align: center;
            color: #666;
            font-size: 14px;
        }
        .security-note {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>Welcome to {{ config('app.name') }}!</h1>
        </div>

        <div class="email-body">
            <p>Hello {{ $user->name }},</p>

            <p>Thank you for registering as a <strong>{{ $role }}</strong> on our platform. Your account has been successfully created.</p>

            <div class="credentials-box">
                <h3>Your Login Credentials</h3>
                <div class="credential-item">
                    <span class="credential-label">Email:</span>
                    <span class="credential-value">{{ $user->email }}</span>
                </div>
                <div class="credential-item">
                    <span class="credential-label">Password:</span>
                    <span class="credential-value">{{ $password }}</span>
                </div>
            </div>

            <div style="text-align: center;">
                <a href="{{ $loginUrl }}" class="button">Login to Your Account</a>
            </div>

            <div class="security-note">
                <strong>Security Tip:</strong> For your security, please change your password after your first login. Keep your credentials confidential and never share them with anyone.
            </div>

            <p>If you have any questions or need assistance, please don't hesitate to contact our support team.</p>

            <p>Best regards,<br>
            <strong>{{ config('app.name') }} Team</strong></p>
        </div>

        <div class="email-footer">
            <p>This is an automated email. Please do not reply to this message.</p>
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
