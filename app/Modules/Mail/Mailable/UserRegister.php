<?php

namespace App\Modules\Mail\Mailable;

use App\Modules\Auth\Infrastructure\Models\Client;
use App\Modules\Discount\Entity\Coupon;
use Illuminate\Mail\Mailables\Content;
use JetBrains\PhpStorm\Pure;

class UserRegister extends SystemMailable
{

    private Client $client;
    private Coupon $coupon;

    public function __construct(Client $client, Coupon $coupon)
    {
        parent::__construct();
        $this->subject = 'Регистрация на NORDI HOME';
        $this->client = $client;
        $this->coupon = $coupon;
    }

    #[Pure] public function content(): Content
    {
        return new Content(
            markdown: 'mail.user.register',
            with: [
                'client' => $this->client,
                'coupon' => $this->coupon,
            ],
        );
    }

    public function attachments(): array
    {
       return [];
    }

    public function getFiles(): array
    {
        return [];
    }
}
