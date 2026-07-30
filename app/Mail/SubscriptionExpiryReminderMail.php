<?php

namespace App\Mail;

use App\Models\Company;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubscriptionExpiryReminderMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Company $company,
        public Subscription $subscription,
        public ?Plan $plan
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '⚠️ Your subscription is expiring soon',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.subscription.expiry-reminder',
        );
    }
}
