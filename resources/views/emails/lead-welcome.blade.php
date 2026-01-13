<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome - Thank You for Your Interest</title>
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
        .welcome-message {
            font-size: 18px;
            color: #555;
            margin: 20px 0;
        }
        .highlight-box {
            background-color: #f8f9fa;
            border-left: 4px solid #84c373;
            padding: 20px;
            margin: 25px 0;
            border-radius: 4px;
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
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>Thank You for Your Interest!</h1>
        </div>

        <div class="email-body">
            <p>Dear {{ $lead->name }},</p>

            <div class="welcome-message">
                <p>Thank you for reaching out to {{ config('app.name') }}. We have received your inquiry and are excited about the opportunity to serve you.</p>
            </div>

            @if($lead->company)
            <p>We noticed that <strong>{{ $lead->company }}</strong> is looking for facilities management services, and we would be delighted to discuss how we can help meet your needs.</p>
            @else
            <p>We would be delighted to discuss how we can help meet your facilities management needs.</p>
            @endif

            <div class="highlight-box">
                <p><strong>What happens next?</strong></p>
                <p>Our team will review your inquiry and get back to you shortly. We'll discuss your specific requirements and how our services can help your business thrive.</p>
            </div>

            @if($user && $password)
            <div class="highlight-box" style="background-color: #e7f5e7; border-left-color: #28a745;">
                <h3 style="color: #28a745; margin-top: 0;">Your Account Credentials</h3>
                <p>We've created an account for you to access our portal. Here are your login credentials:</p>
                <div style="background-color: #ffffff; padding: 15px; border-radius: 4px; margin: 15px 0;">
                    <div style="margin: 10px 0;">
                        <strong style="color: #555;">Email:</strong>
                        <span style="color: #333; font-family: monospace;">{{ $user->email }}</span>
                    </div>
                    <div style="margin: 10px 0;">
                        <strong style="color: #555;">Password:</strong>
                        <span style="color: #333; font-family: monospace; font-size: 16px; font-weight: 600;">{{ $password }}</span>
                    </div>
                </div>
                <p style="margin-bottom: 0;"><strong>Important:</strong> Please save these credentials securely. You can use them to log in to your account at any time.</p>
                <div style="text-align: center; margin-top: 20px;">
                    <a href="{{ $loginUrl }}" class="button" style="text-decoration: none; display: inline-block; padding: 12px 30px; background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: #ffffff !important; border-radius: 6px; font-weight: 600;">
                        Login to Your Account
                    </a>
                </div>
            </div>
            @endif

            <p>If you have any immediate questions or would like to speak with us directly, please feel free to reach out to us.</p>

            <p>We look forward to the opportunity to work with you.</p>

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
