<?php

namespace App\Console\Commands;

use App\Mail\SubscriptionExpiryReminderMail;
use App\Models\Subscription;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class CheckExpiredSubscriptions extends Command
{
    protected $signature = 'subscriptions:check-expired';

    protected $description = 'Mark active subscriptions as expired and suspend their companies when the end date has passed.';

    public function handle(): int
    {
        $expired = Subscription::where('status', 'active')
            ->where('ends_at', '<', now())
            ->get();

        foreach ($expired as $subscription) {
            $subscription->update(['status' => 'expired']);
            $subscription->company?->update(['status' => 'suspended']);
            $this->info("Expired: Company #{$subscription->company_id}");
        }

        $expiringSoon = Subscription::with(['company', 'plan'])
            ->where('status', 'active')
            ->whereBetween('ends_at', [now()->addDays(3)->startOfDay(), now()->addDays(3)->endOfDay()])
            ->get();

        foreach ($expiringSoon as $subscription) {
            if ($subscription->company?->email) {
                Mail::to($subscription->company->email)->queue(
                    new SubscriptionExpiryReminderMail($subscription->company, $subscription, $subscription->plan)
                );
                $this->info("Reminder sent: Company #{$subscription->company_id}");
            }
        }

        $this->info("Processed: {$expired->count()} expired, {$expiringSoon->count()} reminders sent.");

        return self::SUCCESS;
    }
}
