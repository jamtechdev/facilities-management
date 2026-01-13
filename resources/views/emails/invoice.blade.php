<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            border-bottom: 2px solid #84c373;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .invoice-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .invoice-details {
            margin-bottom: 20px;
        }
        .invoice-details table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .invoice-details th,
        .invoice-details td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .invoice-details th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .total-row {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 style="margin: 0; color: #84c373;">INVOICE</h1>
            <p style="margin: 5px 0;">{{ config('app.name') }}</p>
        </div>

        @if($message)
        <div style="margin-bottom: 20px; padding: 15px; background: #f8f9fa; border-radius: 8px;">
            {!! nl2br(e($message)) !!}
        </div>
        @endif

        <div class="invoice-info">
            <div style="display: flex; justify-content: space-between; margin-bottom: 15px;">
                <div>
                    <h3 style="margin: 0 0 10px 0;">Bill To:</h3>
                    <p style="margin: 5px 0;"><strong>{{ $invoice->client->company_name }}</strong></p>
                    <p style="margin: 5px 0;">{{ $invoice->client->contact_person }}</p>
                    <p style="margin: 5px 0;">{{ $invoice->client->email }}</p>
                    @if($invoice->client->address)
                        <p style="margin: 5px 0;">{{ $invoice->client->address }}</p>
                    @endif
                </div>
                <div style="text-align: right;">
                    <p style="margin: 5px 0;"><strong>Invoice #:</strong> {{ $invoice->invoice_number }}</p>
                    <p style="margin: 5px 0;"><strong>Date:</strong> {{ $invoice->created_at->format('M d, Y') }}</p>
                    @if($invoice->due_date)
                        <p style="margin: 5px 0;"><strong>Due Date:</strong> {{ $invoice->due_date->format('M d, Y') }}</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="invoice-details">
            <p><strong>Billing Period:</strong> {{ $invoice->billing_period_start->format('M d, Y') }} to {{ $invoice->billing_period_end->format('M d, Y') }}</p>

            <table>
                <thead>
                    <tr>
                        <th>Description</th>
                        <th class="text-right">Hours</th>
                        <th class="text-right">Rate</th>
                        <th class="text-right">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Cleaning Services</td>
                        <td class="text-right">{{ number_format($invoice->total_hours, 2) }}</td>
                        <td class="text-right">${{ number_format($invoice->hourly_rate, 2) }}</td>
                        <td class="text-right">${{ number_format($invoice->subtotal, 2) }}</td>
                    </tr>
                    @if($invoice->tax > 0)
                    <tr>
                        <td>Tax</td>
                        <td class="text-right" colspan="2"></td>
                        <td class="text-right">${{ number_format($invoice->tax, 2) }}</td>
                    </tr>
                    @endif
                    <tr class="total-row">
                        <td><strong>Total</strong></td>
                        <td class="text-right" colspan="2"></td>
                        <td class="text-right"><strong>${{ number_format($invoice->total_amount, 2) }}</strong></td>
                    </tr>
                </tbody>
            </table>

            @if($invoice->notes)
            <div style="margin-top: 20px;">
                <h4>Notes:</h4>
                <p>{{ $invoice->notes }}</p>
            </div>
            @endif
        </div>

        <div class="footer">
            <p>Thank you for your business!</p>
            <p>Please find the invoice PDF attached to this email.</p>
        </div>
    </div>
</body>
</html>
