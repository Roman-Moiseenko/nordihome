<?php
declare(strict_types=1);

namespace App\Modules\Mail\Mailable;

use App\Modules\Setting\Entity\AbstractSetting;
use App\Modules\Setting\Entity\Settings;
use App\Modules\Setting\Repository\SettingRepository;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Envelope;
use JetBrains\PhpStorm\Pure;

abstract class SystemMailable extends AbstractMailable
{

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
        return new Envelope(
            from: new Address(
                $this->mail_settings->system_name . '@' . $this->mail_settings->mail_domain,
                $this->mail_settings->system_from
            ),
            subject: $this->subject,
        );
    }

    abstract public function getName(): string;
}
