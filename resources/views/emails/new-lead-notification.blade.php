<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Lead Created</title>
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
        .info-box {
            background-color: #f8f9fa;
            border-left: 4px solid #84c373;
            padding: 20px;
            margin: 25px 0;
            border-radius: 4px;
        }
        .info-box h3 {
            margin-top: 0;
            color: #84c373;
            font-size: 18px;
        }
        .info-item {
            margin: 10px 0;
            font-size: 16px;
        }
        .info-label {
            font-weight: 600;
            color: #555;
            display: inline-block;
            width: 150px;
        }
        .info-value {
            color: #333;
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
        .badge {
            display: inline-block;
            padding: 5px 12px;
            background-color: #84c373;
            color: #ffffff;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>New Lead Created</h1>
        </div>

        <div class="email-body">
            <p>Hello Admin,</p>

            <p>A new lead has been created on the platform.</p>

            <div class="info-box">
                <h3>Lead Details</h3>
                <div class="info-item">
                    <span class="info-label">Name:</span>
                    <span class="info-value">{{ $lead->name }}</span>
                </div>
                @if($lead->company)
                <div class="info-item">
                    <span class="info-label">Company:</span>
                    <span class="info-value">{{ $lead->company }}</span>
                </div>
                @endif
                <div class="info-item">
                    <span class="info-label">Email:</span>
                    <span class="info-value">{{ $lead->email }}</span>
                </div>
                @if($lead->phone)
                <div class="info-item">
                    <span class="info-label">Phone:</span>
                    <span class="info-value">{{ $lead->phone }}</span>
                </div>
                @endif
                @if($lead->city)
                <div class="info-item">
                    <span class="info-label">City:</span>
                    <span class="info-value">{{ $lead->city }}</span>
                </div>
                @endif
                <div class="info-item">
                    <span class="info-label">Stage:</span>
                    <span class="badge">{{ ucfirst($lead->stage) }}</span>
                </div>
                @if($lead->assignedStaff)
                <div class="info-item">
                    <span class="info-label">Assigned Staff:</span>
                    <span class="info-value">{{ $lead->assignedStaff->name }}</span>
                </div>
                @endif
                <div class="info-item">
                    <span class="info-label">Created At:</span>
                    <span class="info-value">{{ $lead->created_at->format('F d, Y h:i A') }}</span>
                </div>
            </div>

            <p>Please review the new lead and take any necessary actions.</p>

            <p>Best regards,<br>
            <strong>{{ config('app.name') }} System</strong></p>
        </div>

        <div class="email-footer">
            <p>This is an automated notification email.</p>
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
