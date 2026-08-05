<?php

use Illuminate\Support\Facades\Broadcast;

// Staff-only channel for the Company Admin / Branch live-chat inbox. Authorized
// purely by company_id match — Company Admin, Manager, and Salesman all share
// the same company-wide inbox (see app/Models/Scopes/CompanyScope.php for the
// equivalent Eloquent-level scoping used everywhere else in the app).
//
// NOTE: the per-conversation channel ("conversation.{uuid}") is intentionally
// PUBLIC, not listed here — storefront visitors are always anonymous (no
// customer auth guard exists), so private-channel auth (which requires
// auth()->user()) isn't available to them. The uuid is unguessable and never
// combined with company_id in the channel name, so this is a deliberate,
// low-risk tradeoff rather than an oversight.
Broadcast::channel('chat.company.{companyId}', function ($user, $companyId) {
    return (int) $user->company_id === (int) $companyId;
});
