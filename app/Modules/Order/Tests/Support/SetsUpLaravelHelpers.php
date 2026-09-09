<?php

namespace App\Modules\Order\Tests\Support;

use App\Modules\Order\Application\Actions\OrderLogger\CreateOrderLoggerUseCase;
use App\Modules\Order\Domain\Interfaces\OrderLoggerRepositoryInterface;
use App\Modules\Order\Domain\Interfaces\OrderRepositoryInterface;
use Illuminate\Container\Container;
use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Illuminate\Routing\UrlGenerator;
use Mockery;

/**
 * Подставляет минимальный контейнер Laravel, чтобы в чистых юнит-тестах
 * (PHPUnit\Framework\TestCase) работали глобальные помощники route() и auth(),
 * которые используют логирующие useCase-ы.
 */
trait SetsUpLaravelHelpers
{
    protected Container $container;

    protected function setUpLaravelHelpers(): void
    {
        $this->container = new Container();

        $url = Mockery::mock(UrlGenerator::class);
        $url->shouldReceive('route')->andReturn('http://localhost/test-route');
        $this->container->instance('url', $url);

        $auth = Mockery::mock(AuthFactory::class);
        $auth->shouldReceive('check')->andReturn(false);
        $auth->shouldReceive('user')->andReturn(null);
        $this->container->instance(AuthFactory::class, $auth);

        Container::setInstance($this->container);
    }

    protected function tearDownLaravelHelpers(): void
    {
        Container::setInstance(null);
    }

    protected function makeLoggerUseCase(
        OrderLoggerRepositoryInterface $loggerRepository,
        ?OrderRepositoryInterface $orderRepository = null,
    ): CreateOrderLoggerUseCase {
        return new CreateOrderLoggerUseCase(
            $loggerRepository,
            $orderRepository ?? Mockery::mock(OrderRepositoryInterface::class),
        );
    }
}
