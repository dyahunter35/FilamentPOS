<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LowStockSummaryMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The collection of products that are low on stock.
     *
     * @var \Illuminate\Database\Eloquent\Collection
     */
    public Collection $lowStockProducts;

    /**
     * Create a new message instance.
     */
    public function __construct(Collection $lowStockProducts)
    {
        $this->lowStockProducts = $lowStockProducts;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'تنبيه: منتجات وصلت إلى حد المخزون الآمن',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.low_stock_summary',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
