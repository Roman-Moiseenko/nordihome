<?php

namespace App\Modules\Order\Tests\Unit\Application\Actions\Order;

use App\Modules\Order\Application\Actions\Order\SetManagerOrderUseCase;
use App\Modules\Order\Domain\Entities\OrderEntity;
use App\Modules\Order\Domain\Interfaces\OrderRepositoryInterface;
use App\Modules\Order\Domain\ValueObjects\OrderSellType;
use Mockery;
use PHPUnit\Framework\TestCase;

class SetManagerOrderUseCaseTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_assigns_manager_to_order(): void
    {
        $repository = Mockery::mock(OrderRepositoryInterface::class);
        $order = new OrderEntity(traderId: 1, type: new OrderSellType(OrderSellType::ONLINE));
        $order->id = 10;

        $repository->shouldReceive('getById')->with(10)->once()->andReturn($order);
        $repository->shouldReceive('save')->with($order)->once()->andReturn($order);

        $useCase = new SetManagerOrderUseCase($repository);
        $useCase->execute(10, 33);

        $this->assertSame(33, $order->staffId);
    }

    public function test_clears_manager_when_staff_id_is_null(): void
    {
        $repository = Mockery::mock(OrderRepositoryInterface::class);
        $order = new OrderEntity(traderId: 1, type: new OrderSellType(OrderSellType::ONLINE));
        $order->id = 10;
        $order->staffId = 33;

        $repository->shouldReceive('getById')->with(10)->once()->andReturn($order);
        $repository->shouldReceive('save')->with($order)->once()->andReturn($order);

        $useCase = new SetManagerOrderUseCase($repository);
        $useCase->execute(10, null);

        $this->assertNull($order->staffId);
    }
}
