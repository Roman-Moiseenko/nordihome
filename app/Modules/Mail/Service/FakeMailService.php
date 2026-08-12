<?php

namespace App\Modules\Mail\Service;

use App\Modules\Shared\Application\Interfaces\Mail\MailServiceInterface;
use App\Modules\Shared\Domain\Entities\Mail\Recipient;
use Illuminate\Support\Facades\Log;
use App\Modules\Mail\Entity\MailTemplate;

class FakeMailService implements MailServiceInterface
{
    private static array $lastSent = [];

    public function send(MailTemplate $template, array $data, Recipient $recipient): void
    {
        // Сохраняем информацию о последней отправке
        self::$lastSent = [
            'template' => $template->code,
            'subject'  => $template->subject,
            'recipient' => $recipient->email,
            'data' => $data,
        ];

        // Дополнительно можно записать в лог Laravel
        Log::info('Fake mail sent', self::$lastSent);
    }

    public static function getLastSent(): ?array
    {
        return self::$lastSent;
    }
}
