<?php

namespace App\Modules\Order\Tests\Unit\Application\Actions\Order;

use App\Modules\Order\Application\Actions\Order\SetStatusOrderUseCase;
use App\Modules\Order\Application\DTOs\Order\StatusOrderAssignData;
use App\Modules\Order\Domain\Entities\OrderEntity;
use App\Modules\Order\Domain\Interfaces\OrderRepositoryInterface;
use App\Modules\Order\Domain\ValueObjects\OrderSellType;
use App\Modules\Order\Domain\ValueObjects\OrderStatus;
use Mockery;
use PHPUnit\Framework\TestCase;

class SetStatusOrderUseCaseTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_adds_status_and_saves_order(): void
    {
        $repository = Mockery::mock(OrderRepositoryInterface::class);
        $order = new OrderEntity(traderId: 1, type: new OrderSellType(OrderSellType::ONLINE));
        $order->id = 10;

        $repository->shouldReceive('getById')->with(10)->once()->andReturn($order);
        $repository->shouldReceive('save')->with($order)->once()->andReturn($order);

        $useCase = new SetStatusOrderUseCase($repository);
        $dto = new StatusOrderAssignData(
            orderId: 10,
            status: OrderStatus::inWork(),
            comment: 'Взят в работу',
        );
        $useCase->execute($dto);

        $this->assertSame(OrderStatus::IN_WORK, $order->status->value->getValue());
        $this->assertSame('Взят в работу', $order->status->comment);
        $this->assertCount(1, $order->statuses);
    }
}
