<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset OTP</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .email-header {
            background: linear-gradient(135deg, #84c373 0%, #6ba85a 100%);
            padding: 40px 30px;
            text-align: center;
            color: #ffffff;
        }
        .email-header h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 10px;
        }
        .email-header p {
            font-size: 16px;
            opacity: 0.95;
        }
        .email-body {
            padding: 40px 30px;
        }
        .otp-container {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border: 2px dashed #84c373;
            border-radius: 12px;
            padding: 30px;
            text-align: center;
            margin: 30px 0;
        }
        .otp-code {
            font-size: 48px;
            font-weight: 700;
            color: #84c373;
            letter-spacing: 8px;
            font-family: 'Courier New', monospace;
            margin: 20px 0;
        }
        .email-body h2 {
            color: #1a1f2e;
            font-size: 24px;
            margin-bottom: 15px;
        }
        .email-body p {
            color: #6c757d;
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 15px;
        }
        .info-box {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .info-box p {
            color: #856404;
            font-size: 14px;
            margin: 0;
        }
        .email-footer {
            background-color: #f8f9fa;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e9ecef;
        }
        .email-footer p {
            color: #6c757d;
            font-size: 14px;
            margin: 5px 0;
        }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, #84c373 0%, #6ba85a 100%);
            color: #ffffff;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin-top: 20px;
        }
        @media only screen and (max-width: 600px) {
            .email-container {
                width: 100% !important;
            }
            .email-body {
                padding: 30px 20px;
            }
            .otp-code {
                font-size: 36px;
                letter-spacing: 4px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>KEYSTONE</h1>
            <p>Password Reset Request</p>
        </div>

        <div class="email-body">
            <h2>Hello!</h2>
            <p>You have requested to reset your password for your KEYSTONE account. Use the OTP code below to verify your identity and proceed with resetting your password.</p>

            <div class="otp-container">
                <p style="color: #6c757d; font-size: 14px; margin-bottom: 10px;">Your OTP Code is:</p>
                <div class="otp-code">{{ $otp }}</div>
                <p style="color: #6c757d; font-size: 12px; margin-top: 10px;">This code will expire in 15 minutes</p>
            </div>

            <p>Enter this code on the password reset page to continue. If you didn't request this password reset, please ignore this email or contact support if you have concerns.</p>

            <div class="info-box">
                <p><strong>⚠️ Security Tip:</strong> Never share your OTP code with anyone. KEYSTONE staff will never ask for your OTP code.</p>
            </div>
        </div>

        <div class="email-footer">
            <p><strong>KEYSTONE Facilities Management</strong></p>
            <p>This is an automated email. Please do not reply to this message.</p>
            <p style="margin-top: 15px; font-size: 12px; color: #adb5bd;">
                © {{ date('Y') }} keystonefm.co.uk. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>
