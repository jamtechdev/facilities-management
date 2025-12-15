<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
        }
        .header {
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .invoice-info {
            text-align: right;
        }
        .bill-to {
            margin-bottom: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
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
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <div style="display: flex; justify-content: space-between;">
            <div>
                <h1 style="margin: 0;">INVOICE</h1>
                <p style="margin: 5px 0;">{{ config('app.name') }}</p>
            </div>
            <div class="invoice-info">
                <p style="margin: 5px 0;"><strong>Invoice #:</strong> {{ $invoice->invoice_number }}</p>
                <p style="margin: 5px 0;"><strong>Date:</strong> {{ $invoice->created_at->format('M d, Y') }}</p>
                <p style="margin: 5px 0;"><strong>Status:</strong> {{ ucfirst($invoice->status) }}</p>
            </div>
        </div>
    </div>

    <div class="bill-to">
        <h3 style="margin-bottom: 10px;">Bill To:</h3>
        <p style="margin: 5px 0;"><strong>{{ $invoice->client->company_name }}</strong></p>
        <p style="margin: 5px 0;">{{ $invoice->client->contact_person }}</p>
        <p style="margin: 5px 0;">{{ $invoice->client->email }}</p>
        <p style="margin: 5px 0;">{{ $invoice->client->address }}</p>
    </div>

    <div>
        <p><strong>Billing Period:</strong> {{ $invoice->billing_period_start->format('M d, Y') }} to {{ $invoice->billing_period_end->format('M d, Y') }}</p>
    </div>

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
    <div>
        <h4>Notes:</h4>
        <p>{{ $invoice->notes }}</p>
    </div>
    @endif

    <div class="footer">
        <p>Thank you for your business!</p>
        <p>Generated on {{ now()->format('M d, Y h:i A') }}</p>
    </div>
</body>
</html>

