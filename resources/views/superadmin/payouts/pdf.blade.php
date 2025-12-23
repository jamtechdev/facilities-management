<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payout Report - {{ $staff->name }}</title>
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
    </style>
</head>
<body>
    <div class="header">
        <h1>PAYOUT REPORT</h1>
        <p><strong>Staff:</strong> {{ $staff->name }}</p>
        <p><strong>Period:</strong> {{ $start_date->format('M d, Y') }} to {{ $end_date->format('M d, Y') }}</p>
    </div>

    <div style="margin-bottom: 20px;">
        <p><strong>Hourly Rate:</strong> ${{ number_format($hourly_rate, 2) }}</p>
        <p><strong>Total Payable Hours:</strong> {{ number_format($total_payable_hours, 2) }} hours</p>
        <h3 style="color: #28a745;">Total Payout: ${{ number_format($payout, 2) }}</h3>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Client</th>
                <th class="text-right">Hours Worked</th>
                <th class="text-right">Payable Hours</th>
            </tr>
        </thead>
        <tbody>
            @foreach($timesheets as $timesheet)
            <tr>
                <td>{{ $timesheet->work_date->format('M d, Y') }}</td>
                <td>{{ $timesheet->client->company_name }}</td>
                <td class="text-right">{{ number_format($timesheet->hours_worked, 2) }}</td>
                <td class="text-right">{{ number_format($timesheet->payable_hours, 2) }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="2"><strong>Total</strong></td>
                <td class="text-right"><strong>{{ number_format($timesheets->sum('hours_worked'), 2) }}</strong></td>
                <td class="text-right"><strong>{{ number_format($total_payable_hours, 2) }}</strong></td>
            </tr>
        </tbody>
    </table>

    <div style="margin-top: 40px; padding-top: 20px; border-top: 1px solid #ddd; font-size: 10px; color: #666;">
        <p>Generated on {{ now()->format('M d, Y h:i A') }}</p>
    </div>
</body>
</html>

