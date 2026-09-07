<?php

namespace App\Modules\Order\Tests\Unit\Application\Actions\Order;

use App\Modules\Order\Application\Actions\GetAdditionDataUseCase;
use App\Modules\Order\Application\Actions\Order\SetDiscountOrderUseCase;
use App\Modules\Order\Application\Actions\OrderLogger\CreateOrderLoggerUseCase;
use App\Modules\Order\Application\DTOs\Order\DiscountOrderData;
use App\Modules\Order\Application\Services\OrderCalculateService;
use App\Modules\Order\Domain\Entities\OrderEntity;
use App\Modules\Order\Domain\Entities\OrderItemEntity;
use App\Modules\Order\Domain\Interfaces\OrderLoggerRepositoryInterface;
use App\Modules\Order\Domain\Interfaces\OrderRepositoryInterface;
use App\Modules\Order\Domain\ValueObjects\OrderSellType;
use App\Modules\Order\Domain\ValueObjects\OrderStatus;
use App\Modules\Order\Tests\Support\SetsUpLaravelHelpers;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;
use DomainException;
use Mockery;
use PHPUnit\Framework\TestCase;
use Tests\Trait\MockPermission;

class SetDiscountOrderUseCaseTest extends TestCase
{
    use MockPermission;
    use SetsUpLaravelHelpers;

    private OrderRepositoryInterface $repository;
    private OrderLoggerRepositoryInterface $loggerRepository;
    private OrderRepositoryInterface $loggerOrderRepository;
    private GetAdditionDataUseCase $getAdditionDataUseCase;
    private OrderCalculateService $calculateService;
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
        $this->setUpLaravelHelpers();

        $this->repository = Mockery::mock(OrderRepositoryInterface::class);
        $this->loggerRepository = Mockery::mock(OrderLoggerRepositoryInterface::class);
        $this->loggerOrderRepository = Mockery::mock(OrderRepositoryInterface::class);

        $this->getAdditionDataUseCase = Mockery::mock(GetAdditionDataUseCase::class);
        $this->calculateService = new OrderCalculateService(
            $this->repository,
            $this->getAdditionDataUseCase,
        );

        $loggerUseCase = new CreateOrderLoggerUseCase(
            $this->loggerRepository,
            $this->loggerOrderRepository,
        );

        $this->useCase = new SetDiscountOrderUseCase(
            $this->repository,
            $this->calculateService,
            $loggerUseCase,
        );
    }

    protected function tearDown(): void
    {
        $this->tearDownLaravelHelpers();
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

    private function expectLoggerOrder(): void
    {
        $loggerOrder = $this->makeOrder();
        $this->loggerOrderRepository->shouldReceive('getById')->with(10)->andReturn($loggerOrder);
    }

    public function test_applies_percent_discount_to_eligible_items_and_logs(): void
    {
        $order = $this->makeOrder();
        $eligible = $this->makeItem(productId: 1, quantity: 2, baseCost: 100.0);
        $discounted = $this->makeItem(productId: 2, quantity: 1, baseCost: 50.0, discountId: 5);
        $order->items = [$eligible, $discounted];

        $this->repository->shouldReceive('getById')->with(10)->twice()->andReturn($order);
        $this->getAdditionDataUseCase->shouldNotReceive('execute');
        $this->repository->shouldReceive('save')->with($order)->twice()->andReturn($order);

        $this->expectLoggerOrder();
        $this->loggerRepository->shouldReceive('save')
            ->once()
            ->andReturnUsing(function ($log) {
                $this->assertSame('Установлена общая скидка', $log->action);
                $this->assertSame('10 %', $log->value);
                $this->assertSame('0 ₽', $log->old);

                return $log;
            });

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

        $this->repository->shouldReceive('getById')->with(10)->twice()->andReturn($order);
        $this->getAdditionDataUseCase->shouldNotReceive('execute');
        $this->repository->shouldReceive('save')->with($order)->twice()->andReturn($order);

        $this->expectLoggerOrder();
        $this->loggerRepository->shouldReceive('save')
            ->once()
            ->andReturnUsing(function ($log) {
                $this->assertSame('Установлена общая скидка', $log->action);
                $this->assertSame('100 ₽', $log->value);
                $this->assertSame('0 ₽', $log->old);

                return $log;
            });

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
        $this->loggerRepository->shouldNotReceive('save');

        $permission = $this->mockUserPermission(edit: true);
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('В заказе нет товаров для установки ручной скидки');
        $this->useCase->execute(10, new DiscountOrderData(percent: 10.0), $permission);
    }

    public function test_throws_access_denied_when_missing_permission(): void
    {
        $this->repository->shouldNotReceive('getById');
        $this->loggerRepository->shouldNotReceive('save');

        $permission = $this->mockUserPermission(edit: false);
        $this->expectException(AccessDeniedException::class);
        $this->useCase->execute(10, new DiscountOrderData(percent: 10.0), $permission);
    }
}
