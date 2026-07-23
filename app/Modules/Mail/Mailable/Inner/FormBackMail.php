<?php

namespace App\Modules\Mail\Mailable\Inner;

use App\Modules\Mail\Mailable\SystemMailable;
use App\Modules\Order\Entity\Order\Order;
use App\Modules\Shared\Application\DTOs\Lead\LeadSourceData;
use App\Modules\Shared\Infrastructure\Events\LeadCollected;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FormBackMail extends SystemMailable
{
    use Queueable, SerializesModels;

    private LeadSourceData $leadData;

    /**
     * Create a new message instance.
     */
    public function __construct(LeadSourceData $leadData)
    {
        parent::__construct();
        $this->leadData = $leadData;
        $this->subject = 'Форма обратной связи';
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.feedback.form',
            with: [
                'data' => $this->leadData->data
            ],
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

    public function getFiles(): array
    {
        return [];
    }

}
