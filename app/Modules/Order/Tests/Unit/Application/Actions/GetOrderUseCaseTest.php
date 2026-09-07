<?php

namespace App\Modules\Order\Tests\Unit\Application\Actions;

use App\Modules\Order\Application\Actions\GetOrderUseCase;
use App\Modules\Order\Domain\Entities\OrderEntity;
use App\Modules\Order\Domain\Interfaces\OrderRepositoryInterface;
use App\Modules\Order\Domain\ValueObjects\OrderSellType;
use Mockery;
use PHPUnit\Framework\TestCase;

class GetOrderUseCaseTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_returns_order_by_id(): void
    {
        $repository = Mockery::mock(OrderRepositoryInterface::class);
        $order = new OrderEntity(traderId: 1, type: new OrderSellType(OrderSellType::ONLINE));
        $order->id = 7;
        $repository->shouldReceive('getById')->with(7)->once()->andReturn($order);

        $useCase = new GetOrderUseCase($repository);

        $this->assertSame($order, $useCase->execute(7));
    }
}
