<x-mail::message>
# Your Subscription is Active! 🎉

Dear **{{ $company->name }}**,

Your payment was successful and your **{{ $plan->name }}** subscription is now active.

<x-mail::panel>
**Plan:** {{ $plan->name }}
**Billing Cycle:** {{ ucfirst($subscription->billing_cycle) }}
**Valid Until:** {{ $subscription->ends_at?->format('d M Y') ?? 'Lifetime' }}
**Amount Paid:** {{ $transaction->currency }} {{ number_format($transaction->amount, 2) }}
**Invoice #:** {{ $subscription->invoice_number }}
</x-mail::panel>

Please find your invoice attached to this email.

<x-mail::button :url="route('company.subscription.index')">
View My Subscription
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
