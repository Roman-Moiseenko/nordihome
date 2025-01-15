<?php
declare(strict_types=1);

namespace App\Modules\Setting\Entity;

class Notification extends AbstractSetting
{
    public string $telegram_api = '';
}

