<?php

namespace App\Mail;

use App\Models\Company;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubscriptionConfirmationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * invoicePath এর পরিবর্তে invoiceStoragePath (storage relative path) store করা হচ্ছে।
     * Queue serialize হলে absolute path invalid হয়ে যায়, তাই storage-relative path ব্যবহার করা হচ্ছে।
     */
    public function __construct(
        public Company $company,
        public Subscription $subscription,
        public Plan $plan,
        public Transaction $transaction,
        public string $invoiceStoragePath  // e.g. "invoices/INV-XXXXXXXX.pdf"
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🎉 Subscription Confirmed — ' . $this->plan->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.subscription.confirmation',
        );
    }

    public function attachments(): array
    {
        // Queue থেকে run হলেও storage_path() সঠিক absolute path দেবে
        $absolutePath = storage_path('app/' . $this->invoiceStoragePath);

        if (!file_exists($absolutePath)) {
            return [];
        }

        return [
            Attachment::fromPath($absolutePath)
                ->as('Invoice-' . $this->subscription->invoice_number . '.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
