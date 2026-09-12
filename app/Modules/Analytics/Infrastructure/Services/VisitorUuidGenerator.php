<?php

declare(strict_types=1);

namespace App\Modules\Analytics\Infrastructure\Services;

use App\Modules\Analytics\Domain\ValueObjects\VisitorUuid;

/**
 * VisitorUuidGenerator — генерация UUID посетителя и имя cookie, в котором он хранится.
 *
 * Cookie сохранён под историческим ключом `user_cookie_id`, который уже используется
 * в ClientContext (Shop), Cart и EncryptCookies::$except.
 */
final class VisitorUuidGenerator
{
    public const string COOKIE_NAME = 'user_cookie_id';

    public const int COOKIE_MINUTES = 60 * 24 * 365;

    public function generate(): VisitorUuid
    {
        return VisitorUuid::generate();
    }
}
