<?php

namespace App\Mail;

use App\Models\Customer;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NotifMail extends Mailable
{
    use Queueable, SerializesModels;

    public Order $order;
    public Customer $customer;
    public float $totalCost;

    /**
     * Create a new message instance.
     */
    public function __construct(Order $order, Customer $customer, float $totalCost)
    {
        $this->order = $order;
        $this->customer = $customer;
        $this->totalCost = $totalCost;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Pesanan Baru Diterima: #{$this->order->id}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.order_notification',
            with: [
                'order' => $this->order,
                'customer' => $this->customer,
                'totalCost' => $this->totalCost,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
