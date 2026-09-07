<?php

namespace App\Modules\Order\Tests\Unit\Application\Actions\OrderItem;

use App\Modules\Order\Application\Actions\GetAdditionDataUseCase;
use App\Modules\Order\Application\Actions\OrderItem\RemoveOrderItemUseCase;
use App\Modules\Order\Application\Actions\OrderLogger\CreateOrderLoggerUseCase;
use App\Modules\Order\Application\Services\OrderCalculateService;
use App\Modules\Order\Domain\Entities\OrderEntity;
use App\Modules\Order\Domain\Entities\OrderItemEntity;
use App\Modules\Order\Domain\Interfaces\OrderLoggerRepositoryInterface;
use App\Modules\Order\Domain\Interfaces\OrderRepositoryInterface;
use App\Modules\Order\Domain\ValueObjects\OrderSellType;
use App\Modules\Order\Domain\ValueObjects\OrderStatus;
use App\Modules\Order\Tests\Support\SetsUpLaravelHelpers;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;
use Mockery;
use PHPUnit\Framework\TestCase;
use Tests\Trait\MockPermission;

class RemoveOrderItemUseCaseTest extends TestCase
{
    use MockPermission;
    use SetsUpLaravelHelpers;

    private OrderRepositoryInterface $repository;
    private OrderLoggerRepositoryInterface $loggerRepository;
    private OrderRepositoryInterface $loggerOrderRepository;
    private GetAdditionDataUseCase $getAdditionDataUseCase;
    private RemoveOrderItemUseCase $useCase;

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

        $calculateService = new OrderCalculateService($this->repository, $this->getAdditionDataUseCase);
        $loggerUseCase = new CreateOrderLoggerUseCase($this->loggerRepository, $this->loggerOrderRepository);

        $this->useCase = new RemoveOrderItemUseCase($this->repository, $calculateService, $loggerUseCase);
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

    private function makeItem(int $id): OrderItemEntity
    {
        $item = new OrderItemEntity(productId: $id, quantity: 3, baseCost: 100.0, sellCost: 100.0);
        $item->id = $id;

        return $item;
    }

    public function test_throws_access_denied_when_missing_permission(): void
    {
        $this->repository->shouldNotReceive('getById');
        $this->loggerRepository->shouldNotReceive('save');

        $permission = $this->mockUserPermission(edit: false);
        $this->expectException(AccessDeniedException::class);
        $this->useCase->execute(10, 1, $permission);
    }

    public function test_removes_item_and_logs(): void
    {
        $order = $this->makeOrder();
        $item = $this->makeItem(1);
        $order->items = [$item];

        $this->repository->shouldReceive('getById')->with(10)->twice()->andReturn($order);
        $this->repository->shouldReceive('save')->with($order)->twice()->andReturn($order);
        $this->getAdditionDataUseCase->shouldNotReceive('execute');

        $loggerOrder = $this->makeOrder();
        $this->loggerOrderRepository->shouldReceive('getById')->with(10)->once()->andReturn($loggerOrder);
        $this->loggerRepository->shouldReceive('save')
            ->once()
            ->andReturnUsing(function ($log) {
                $this->assertSame('Товар удален из заказ', $log->action);
                $this->assertSame('1', $log->object);
                $this->assertSame('3 шт.', $log->value);

                return $log;
            });

        $permission = $this->mockUserPermission(edit: true);
        $this->useCase->execute(10, 1, $permission);

        $this->assertCount(0, $order->items);
    }
}
