<?php

namespace App\Modules\Auth\Infrastructure\Events;

use App\Modules\Shared\Infrastructure\Events\CustomEvent;

readonly class UserIsLogin extends CustomEvent
{

    public function __construct(){}
}
