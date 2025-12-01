<?php

namespace App\Modules\Mail\Mailable;

use App\Modules\Discount\Entity\Coupon;
use App\Modules\User\Entity\User;
use Illuminate\Mail\Mailables\Content;
use JetBrains\PhpStorm\Pure;

class UserRegister extends SystemMailable
{

    private User $user;
    private Coupon $coupon;

    public function __construct(User $user, Coupon $coupon)
    {
        parent::__construct();
        $this->subject = 'Регистрация на NORDI HOME';
        $this->user = $user;
        $this->coupon = $coupon;
    }

    #[Pure] public function content(): Content
    {
        return new Content(
            markdown: 'mail.user.register',
            with: [
                'user' => $this->user,
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
