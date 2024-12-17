<?php
declare(strict_types=1);

namespace App\Modules\Mail\Mailable;

use App\Modules\Setting\Entity\AbstractSetting;
use App\Modules\Setting\Repository\SettingRepository;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Envelope;
use JetBrains\PhpStorm\Pure;

abstract class SystemMailable extends AbstractMailable
{
    //protected string $subject;

    #[Pure] public function __construct()
    {
        parent::__construct();
        /**
         * Определить subject
         */
        $this->subject = 'Не назначена тема письма';
    }

    public function envelope(): Envelope
    {
        $mail_set = (new SettingRepository())->getMail();

        return new Envelope(
            from: new Address(
                $mail_set->system_name . '@' . $mail_set->mail_domain,
                $mail_set->system_from
            ),
            subject: $this->subject,
        );
    }
}
