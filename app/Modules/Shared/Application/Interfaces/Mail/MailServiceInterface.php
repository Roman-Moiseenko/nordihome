<?php

namespace App\Modules\Shared\Application\Interfaces\Mail;

use App\Modules\Mail\Entity\MailTemplate;
use App\Modules\Shared\Domain\Entities\Mail\Recipient;

interface MailServiceInterface
{
    public function send(MailTemplate $template, array $data, Recipient $recipient): void;
}
