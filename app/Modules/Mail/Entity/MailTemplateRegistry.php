<?php

namespace App\Modules\Mail\Entity;

class MailTemplateRegistry
{
    /** @return array<string, MailTemplate> */
    public static function all(): array
    {
        return [
            'user.verify' => new MailTemplate(
                code: 'user.verify',
                subject: 'Подтверждение почты',
                view: 'mail.user.verify',
            ),
            'order.new' => new MailTemplate(
                code: 'order.new',
                subject: 'Ваш заказ принят',
                view: 'mail.order.new',
            ),

            'lead.form' => new MailTemplate(
                code: 'lead.form',
                subject: 'Новая заявка с сайта',
                view: 'mail.lead.form',
            ),
        ];
    }

    public static function get(string $code): ?MailTemplate
    {
        return self::all()[$code] ?? null;
    }
}
