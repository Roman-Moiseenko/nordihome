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
            'order.cancelled' => new MailTemplate(
                code: 'order.cancelled',
                subject: 'Ваш заказ отменен',
                view: 'mail.order.cancelled',
            ),


            'lead.form' => new MailTemplate(
                code: 'lead.form',
                subject: 'Новая заявка с сайта',
                view: 'mail.lead.form',
            ),
            'lead.order' => new MailTemplate(
                code: 'lead.order',
                subject: 'Новый заказ с сайта',
                view: 'mail.lead.order',
            ),
            'user.password-reset' => new MailTemplate(
                code: 'user.password-reset',
                subject: 'Восстановление пароя',
                view: 'mail.user.password-reset',
            ),
        ];
    }

    public static function get(string $code): ?MailTemplate
    {
        return self::all()[$code] ?? null;
    }
}
