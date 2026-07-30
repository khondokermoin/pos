<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 13px; color: #333; }
        .header { background: #1a56db; color: white; padding: 20px; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 24px; }
        .header p { margin: 5px 0 0; opacity: 0.8; }
        .invoice-meta { display: flex; justify-content: space-between; margin-bottom: 20px; }
        .section-title { font-size: 11px; text-transform: uppercase; color: #888; margin-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background: #f3f4f6; padding: 10px; text-align: left; font-size: 12px; }
        td { padding: 10px; border-bottom: 1px solid #e5e7eb; }
        .total-row td { font-weight: bold; font-size: 15px; background: #f9fafb; }
        .badge { padding: 3px 10px; border-radius: 20px; font-size: 11px; }
        .badge-success { background: #d1fae5; color: #065f46; }
        .footer { margin-top: 40px; text-align: center; color: #9ca3af; font-size: 11px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ config('app.name') }}</h1>
        <p>Subscription Invoice</p>
    </div>

    <div class="invoice-meta">
        <div>
            <div class="section-title">Invoice To</div>
            <strong>{{ $company->name }}</strong><br>
            {{ $company->email }}<br>
            {{ $company->address ?? '' }}
        </div>
        <div style="text-align: right;">
            <div class="section-title">Invoice Details</div>
            <strong>Invoice #:</strong> {{ $subscription->invoice_number }}<br>
            <strong>Date:</strong> {{ now()->format('d M Y') }}<br>
            <strong>Status:</strong> <span class="badge badge-success">PAID</span>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th>Billing Cycle</th>
                <th>Period</th>
                <th style="text-align:right;">Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <strong>{{ $plan->name }} Plan</strong><br>
                    <small>{{ $plan->user_limit }} Users · {{ $plan->branch_limit }} Branches</small>
                </td>
                <td>{{ ucfirst($subscription->billing_cycle) }}</td>
                <td>
                    {{ $subscription->started_at?->format('d M Y') }}
                    → {{ $subscription->ends_at?->format('d M Y') ?? 'Lifetime' }}
                </td>
                <td style="text-align:right;">{{ $transaction->currency }} {{ number_format($transaction->amount, 2) }}</td>
            </tr>
            <tr class="total-row">
                <td colspan="3" style="text-align:right;">Total Paid</td>
                <td style="text-align:right;">{{ $transaction->currency }} {{ number_format($transaction->amount, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p>Transaction ID: {{ $transaction->transaction_id }} · Payment via {{ ucfirst($transaction->payment_method) }}</p>
        <p>Thank you for your business! For support, contact us at {{ config('mail.from.address') }}</p>
    </div>
</body>
</html>
