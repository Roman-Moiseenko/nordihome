<?php

namespace App\Modules\Order\Tests\Unit\Application\Actions\Order;

use App\Modules\Order\Application\Actions\Order\SetDiscountOrderUseCase;
use App\Modules\Order\Application\DTOs\Order\DiscountOrderData;
use App\Modules\Order\Application\Interfaces\OrderLoggerServiceInterface;
use App\Modules\Order\Application\Interfaces\OrderRepositoryInterface;
use App\Modules\Order\Domain\Entities\OrderEntity;
use App\Modules\Order\Domain\Entities\OrderItemEntity;
use App\Modules\Order\Domain\ValueObjects\OrderSellType;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;
use DomainException;
use Mockery;
use PHPUnit\Framework\TestCase;
use Tests\Trait\MockPermission;

class SetDiscountOrderUseCaseTest extends TestCase
{
    use MockPermission;

    private OrderRepositoryInterface $repository;
    private OrderLoggerServiceInterface $logger;
    private SetDiscountOrderUseCase $useCase;

    public function getModuleName(): string
    {
        return 'order';
    }

    public function getEntityName(): string
    {
        return 'order';
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = Mockery::mock(OrderRepositoryInterface::class);
        $this->logger = Mockery::mock(OrderLoggerServiceInterface::class);
        $this->useCase = new SetDiscountOrderUseCase($this->repository, $this->logger);
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

        return $order;
    }

    private function makeItem(int $productId, float $quantity, float $baseCost, ?int $discountId = null): OrderItemEntity
    {
        $item = new OrderItemEntity(
            productId: $productId,
            quantity: $quantity,
            baseCost: $baseCost,
            sellCost: $baseCost,
        );
        $item->discountId = $discountId;

        return $item;
    }

    public function test_applies_percent_discount_to_eligible_items_and_logs(): void
    {
        $order = $this->makeOrder();
        $eligible = $this->makeItem(productId: 1, quantity: 2, baseCost: 100.0);
        $discounted = $this->makeItem(productId: 2, quantity: 1, baseCost: 50.0, discountId: 5);
        $order->items = [$eligible, $discounted];

        $this->repository->shouldReceive('getById')->with(10)->once()->andReturn($order);
        $this->repository->shouldReceive('save')->with($order)->once()->andReturn($order);
        $this->logger->shouldReceive('log')
            ->once()
            ->with(10, 'Установлена общая скидка', Mockery::any(), '10 %', '0 ₽');

        $permission = $this->mockUserPermission(edit: true);
        $this->useCase->execute(10, new DiscountOrderData(percent: 10.0), $permission);

        $this->assertEqualsWithDelta(90.0, $eligible->sellCost, 0.000001);
        $this->assertEqualsWithDelta(50.0, $discounted->sellCost, 0.000001);
        $this->assertEqualsWithDelta(20.0, $order->manual, 0.000001);
    }

    public function test_applies_manual_discount_and_logs_formatted_price(): void
    {
        $order = $this->makeOrder();
        $item = $this->makeItem(productId: 1, quantity: 2, baseCost: 100.0);
        $order->items = [$item];

        $this->repository->shouldReceive('getById')->with(10)->once()->andReturn($order);
        $this->repository->shouldReceive('save')->with($order)->once()->andReturn($order);
        $this->logger->shouldReceive('log')
            ->once()
            ->with(10, 'Установлена общая скидка', Mockery::any(), '100 ₽', '0 ₽');

        $permission = $this->mockUserPermission(edit: true);
        $this->useCase->execute(10, new DiscountOrderData(manual: 100.0), $permission);

        $this->assertEqualsWithDelta(50.0, $item->sellCost, 0.000001);
        $this->assertEqualsWithDelta(100.0, $order->manual, 0.000001);
    }

    public function test_throws_when_no_items_eligible_for_discount(): void
    {
        $order = $this->makeOrder();
        $order->items = [$this->makeItem(productId: 1, quantity: 1, baseCost: 100.0, discountId: 7)];

        $this->repository->shouldReceive('getById')->with(10)->once()->andReturn($order);
        $this->repository->shouldNotReceive('save');
        $this->logger->shouldNotReceive('log');

        $permission = $this->mockUserPermission(edit: true);
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('В заказе нет товаров для установки ручной скидки');
        $this->useCase->execute(10, new DiscountOrderData(percent: 10.0), $permission);
    }

    public function test_throws_access_denied_when_missing_permission(): void
    {
        $this->repository->shouldNotReceive('getById');
        $this->logger->shouldNotReceive('log');

        $permission = $this->mockUserPermission(edit: false);
        $this->expectException(AccessDeniedException::class);
        $this->useCase->execute(10, new DiscountOrderData(percent: 10.0), $permission);
    }
}
