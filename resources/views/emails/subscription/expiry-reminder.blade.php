<x-mail::message>
# Your subscription is expiring soon ⚠️

Dear **{{ $company->name }}**,

Your **{{ $plan?->name ?? 'current plan' }}** subscription will expire soon. Please renew it to keep your account active.

<x-mail::panel>
**Valid Until:** {{ $subscription->ends_at?->format('d M Y') ?? 'N/A' }}
</x-mail::panel>

<x-mail::button :url="route('company.subscription.index')">
Manage Subscription
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
