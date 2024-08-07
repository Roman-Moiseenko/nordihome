<?php

namespace App\Mail;

use App\Modules\Admin\Entity\Options;
use App\Modules\Setting\Repository\SettingRepository;
use App\Modules\User\Entity\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserReview extends Mailable
{
    use Queueable, SerializesModels;

    private array $products;
    private User $user;
    private int $bonus_amount;
    private bool $bonus_review;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, array $products)
    {
        $settings = new SettingRepository();
        $coupon = $settings->getCoupon();

        $this->products = $products;
        $this->user = $user;

        $this->bonus_amount = $coupon->bonus_amount;
        $this->bonus_review = $coupon->bonus_review;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Оставьте отзыв',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.user.review',
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

    public function build()
    {
        return $this
            ->markdown('mail.user.review')
            ->with([ 'user' => $this->user, 'products' => $this->products, 'bonus_amount' => $this->bonus_amount, 'bonus_review' => $this->bonus_review]);
    }
}
