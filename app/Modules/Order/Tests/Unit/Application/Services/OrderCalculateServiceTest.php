<?php

namespace App\Modules\Order\Tests\Unit\Application\Services;

use App\Modules\Order\Application\Actions\GetAdditionDataUseCase;
use App\Modules\Order\Application\DTOs\AdditionData;
use App\Modules\Order\Application\Services\OrderCalculateService;
use App\Modules\Order\Domain\Entities\OrderAdditionEntity;
use App\Modules\Order\Domain\Entities\OrderEntity;
use App\Modules\Order\Domain\Entities\OrderItemEntity;
use App\Modules\Order\Domain\Interfaces\OrderRepositoryInterface;
use App\Modules\Order\Domain\ValueObjects\OrderSellType;
use App\Modules\Order\Domain\ValueObjects\OrderStatus;
use Mockery;
use PHPUnit\Framework\TestCase;

class OrderCalculateServiceTest extends TestCase
{
    private OrderRepositoryInterface $repository;
    private GetAdditionDataUseCase $getAdditionDataUseCase;
    private OrderCalculateService $useCase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = Mockery::mock(OrderRepositoryInterface::class);
        $this->getAdditionDataUseCase = Mockery::mock(GetAdditionDataUseCase::class);
        $this->useCase = new OrderCalculateService($this->repository, $this->getAdditionDataUseCase);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    private function makeOrder(): OrderEntity
    {
        $order = new OrderEntity(traderId: 1, type: new OrderSellType(OrderSellType::ONLINE));
        $order->id = 10;
        $order->addStatus(OrderStatus::new());

        return $order;
    }

    public function test_calculates_manual_amount(): void
    {
        $order = $this->makeOrder();
        $item = new OrderItemEntity(productId: 1, quantity: 2, baseCost: 100.0, sellCost: 90.0);
        $order->items = [$item];

        $this->repository->shouldReceive('getById')->with(10)->once()->andReturn($order);
        $this->repository->shouldReceive('save')->with($order)->once()->andReturn($order);
        $this->getAdditionDataUseCase->shouldNotReceive('execute');

        $result = $this->useCase->execute(10);

        $this->assertSame($order, $result);
        $this->assertEqualsWithDelta(20.0, $order->manual, 0.000001);
    }

    public function test_recalculates_addition_amount(): void
    {
        $order = $this->makeOrder();
        $addition = new OrderAdditionEntity(77);
        $addition->amount = 0;
        $addition->quantity = 1;
        $order->additions = [$addition];

        $this->repository->shouldReceive('getById')->with(10)->once()->andReturn($order);
        $this->repository->shouldReceive('save')->with($order)->once()->andReturn($order);
        $this->getAdditionDataUseCase->shouldReceive('execute')
            ->with(77)
            ->once()
            ->andReturn(new AdditionData(baseRatio: 500, name: 'Сборка', isQuantity: false, isManual: false, calculate: null, type: 'assembly'));

        $this->useCase->execute(10);

        $this->assertEqualsWithDelta(500.0, $order->additions[0]->amount, 0.000001);
    }
}
