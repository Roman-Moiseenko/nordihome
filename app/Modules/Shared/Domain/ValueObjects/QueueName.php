<?php

declare(strict_types=1);

namespace App\Modules\Shared\Domain\ValueObjects;

/**
 * Имена очередей, используемые в проекте.
 *
 * Список значений доступен в константе ALL, а сами имена — в отдельных
 * константах для типизированного использования (например, при вызове
 * Job::dispatch(...)->onQueue(QueueName::PHOTO)).
 */
final readonly class QueueName
{
    public const string ANALYTICS = 'analytics';
    public const string DEFAULT = 'default';
    public const string PHOTO = 'photo';

    /** @var list<string> */
    public const array ALL = [
        self::ANALYTICS,
        self::DEFAULT,
        self::PHOTO,
    ];
}
