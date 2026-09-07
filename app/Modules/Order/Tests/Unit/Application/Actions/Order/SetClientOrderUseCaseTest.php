<?php

namespace App\Modules\Order\Tests\Unit\Application\Actions\Order;

use App\Modules\Order\Application\Actions\Order\SetClientOrderUseCase;
use App\Modules\Order\Domain\Entities\OrderEntity;
use App\Modules\Order\Domain\Interfaces\OrderRepositoryInterface;
use App\Modules\Order\Domain\ValueObjects\OrderSellType;
use Mockery;
use PHPUnit\Framework\TestCase;

class SetClientOrderUseCaseTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_assigns_client_to_order(): void
    {
        $repository = Mockery::mock(OrderRepositoryInterface::class);
        $order = new OrderEntity(traderId: 1, type: new OrderSellType(OrderSellType::ONLINE));
        $order->id = 10;

        $repository->shouldReceive('getById')->with(10)->once()->andReturn($order);
        $repository->shouldReceive('save')->with($order)->once()->andReturn($order);

        $useCase = new SetClientOrderUseCase($repository);
        $useCase->execute(10, 55);

        $this->assertSame(55, $order->clientId);
    }
}
