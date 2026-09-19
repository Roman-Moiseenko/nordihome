<?php

namespace App\Modules\Cart\Infrastructure\Listeners;

use App\Modules\Auth\Infrastructure\Events\UserIsLogin;
use App\Modules\Cart\Application\Services\UnionStoragesService;
use App\Modules\Storefront\Application\Services\ClientContextFactory;

readonly class UnionCartAfterLoginListener
{
    public function __construct(private UnionStoragesService $service)
    {
    }

    public function handle(UserIsLogin $event): void
    {
        $context = app(ClientContextFactory::class)->make();

        $this->service->execute($context);
    }
}
