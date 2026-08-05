<?php

namespace App\Mail;

use App\Models\Company;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class LowStockAlertMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Company $company,
        public Collection $lowStockItems
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '⚠️ Low Stock Alert — ' . $this->lowStockItems->count() . ' item(s) need attention',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.inventory.low-stock-alert',
        );
    }
}
