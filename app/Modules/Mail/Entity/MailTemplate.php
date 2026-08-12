<?php

namespace App\Modules\Mail\Entity;

final readonly class MailTemplate
{
    /**
     * @param string $code             Уникальный код (например 'user.verify')
     * @param string $subject          Тема письма
     * @param string $view             Blade-шаблон
     * @param array  $defaultParams    Параметры по умолчанию (если есть)
     */
    public function __construct(
        public string $code,
        public string $subject,
        public string $view,
        public array $defaultParams = [],
        public ?\Closure $attachmentsCallback = null,
    ) {}
    public function getAttachments(array $params): array
    {
        if ($this->attachmentsCallback === null) {
            return [];
        }
        return ($this->attachmentsCallback)($params);
    }
}
