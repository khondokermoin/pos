<x-mail::message>
# New subscription payment received

A new subscription payment was received for **{{ $company->name }}**.

<x-mail::panel>
**Plan:** {{ $plan->name }}
**Amount:** {{ $transaction->currency }} {{ number_format($transaction->amount, 2) }}
**Invoice #:** {{ $subscription->invoice_number }}
</x-mail::panel>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
