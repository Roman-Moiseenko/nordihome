<?php

namespace App\Modules\Order\Application\DTOs\OrderLogger;

class OrderLoggerCreateData
{
    public function __construct(
        public string $action,
        public ?string $object = '',
        public ?string $old = '',
        public ?string $value = '',
        public ?string $link = '',
    )
    {

    }
}
