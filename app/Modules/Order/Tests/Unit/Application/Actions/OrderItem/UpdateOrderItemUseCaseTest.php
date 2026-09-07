<?php

namespace App\Modules\Order\Tests\Unit\Application\Actions\OrderItem;

use App\Modules\Guide\Domain\Interfaces\AdditionRepositoryInterface;
use App\Modules\Order\Application\Actions\AdditionGuide\GetAssemblageAdditionUseCase;
use App\Modules\Order\Application\Actions\AdditionGuide\GetPackingAdditionUseCase;
use App\Modules\Order\Application\Actions\GetAdditionDataUseCase;
use App\Modules\Order\Application\Actions\Order\SetAssemblagesOrderUseCase;
use App\Modules\Order\Application\Actions\Order\SetPackingsOrderUseCase;
use App\Modules\Order\Application\Actions\OrderItem\UpdateOrderItemUseCase;
use App\Modules\Order\Application\Actions\OrderLogger\CreateOrderLoggerUseCase;
use App\Modules\Order\Application\DTOs\OrderItem\OrderItemUpdateData;
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

class UpdateOrderItemUseCaseTest extends TestCase
{
    use MockPermission;
    use SetsUpLaravelHelpers;

    private OrderRepositoryInterface $repository;
    private OrderLoggerRepositoryInterface $loggerRepository;
    private OrderRepositoryInterface $loggerOrderRepository;
    private AdditionRepositoryInterface $additionRepository;
    private GetAdditionDataUseCase $getAdditionDataUseCase;
    private UpdateOrderItemUseCase $useCase;

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
        $this->additionRepository = Mockery::mock(AdditionRepositoryInterface::class);
        $this->getAdditionDataUseCase = Mockery::mock(GetAdditionDataUseCase::class);

        $orderCalculateService = new OrderCalculateService(
            $this->repository,
            $this->getAdditionDataUseCase,
        );

        $loggerUseCase = new CreateOrderLoggerUseCase(
            $this->loggerRepository,
            $this->loggerOrderRepository,
        );

        // readonly-классы: GetAssemblageAdditionUseCase, GetPackingAdditionUseCase,
        // SetAssemblagesOrderUseCase, SetPackingsOrderUseCase — подставляем реальные
        // экземпляры с замоканными зависимостями.
        $assemblageAdditionUseCase = new GetAssemblageAdditionUseCase($this->additionRepository);
        $packingAdditionUseCase = new GetPackingAdditionUseCase($this->additionRepository);

        $setAssemblagesOrderUseCase = new SetAssemblagesOrderUseCase(
            $this->repository,
            $orderCalculateService,
            $assemblageAdditionUseCase,
            $loggerUseCase,
        );
        $setPackingsOrderUseCase = new SetPackingsOrderUseCase(
            $this->repository,
            $orderCalculateService,
            $packingAdditionUseCase,
            $loggerUseCase,
        );

        $this->useCase = new UpdateOrderItemUseCase(
            $this->repository,
            $orderCalculateService,
            $setAssemblagesOrderUseCase,
            $setPackingsOrderUseCase,
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

    private function makeItem(int $id): OrderItemEntity
    {
        $item = new OrderItemEntity(productId: $id, quantity: 1, baseCost: 100.0, sellCost: 100.0);
        $item->id = $id;

        return $item;
    }

    private function expectLoggerOrder(): void
    {
        $loggerOrder = $this->makeOrder();
        $this->loggerOrderRepository->shouldReceive('getById')->with(10)->andReturn($loggerOrder);
    }

    public function test_throws_access_denied_when_missing_permission(): void
    {
        $this->repository->shouldNotReceive('getById');
        $this->repository->shouldNotReceive('save');
        $this->loggerRepository->shouldNotReceive('save');

        $permission = $this->mockUserPermission(edit: false);
        $this->expectException(AccessDeniedException::class);
        $this->useCase->execute(10, new OrderItemUpdateData(id: 1), $permission);
    }

    public function test_updates_item_and_recalculates_without_assemblage_and_packing(): void
    {
        $order = $this->makeOrder();
        $item = $this->makeItem(1);
        $order->items = [$item];

        $this->repository->shouldReceive('getById')->with(10)->twice()->andReturn($order);
        $this->getAdditionDataUseCase->shouldNotReceive('execute');
        $this->additionRepository->shouldNotReceive('findBySlug');
        $this->repository->shouldReceive('save')->with($order)->twice()->andReturn($order);

        $this->expectLoggerOrder();
        $this->loggerRepository->shouldReceive('save')
            ->once()
            ->andReturnUsing(function ($log) {
                $this->assertSame('Изменен комментарий позиции', $log->action);
                $this->assertSame('test', $log->value);

                return $log;
            });

        $permission = $this->mockUserPermission(edit: true);
        $this->useCase->execute(10, new OrderItemUpdateData(id: 1, quantity: 5, comment: 'test'), $permission);

        $this->assertSame(5.0, $item->quantity);
        $this->assertSame('test', $item->comment);
    }

    public function test_delegates_to_set_assemblages_when_assemblage_is_true(): void
    {
        $order = $this->makeOrder();
        $item = $this->makeItem(1);
        $order->items = [$item];

        $this->repository->shouldReceive('getById')->with(10)->times(4)->andReturn($order);
        $this->getAdditionDataUseCase->shouldNotReceive('execute');
        $this->additionRepository->shouldReceive('findBySlug')->with('assembly-15')->once()->andReturnNull();
        $this->repository->shouldReceive('save')->with($order)->times(4)->andReturn($order);

        $this->expectLoggerOrder();
        $this->loggerRepository->shouldReceive('save')->once()->andReturnUsing(fn ($log) => $log);

        $permission = $this->mockUserPermission(edit: true);
        $this->useCase->execute(10, new OrderItemUpdateData(id: 1, assemblage: true), $permission);

        $this->assertTrue($item->assemblage);
    }

    public function test_delegates_to_set_assemblages_when_assemblage_is_false(): void
    {
        $order = $this->makeOrder();
        $item = $this->makeItem(1);
        $order->items = [$item];

        $this->repository->shouldReceive('getById')->with(10)->times(4)->andReturn($order);
        $this->getAdditionDataUseCase->shouldNotReceive('execute');
        $this->additionRepository->shouldReceive('findBySlug')->with('assembly-15')->once()->andReturnNull();
        $this->repository->shouldReceive('save')->with($order)->times(4)->andReturn($order);

        $this->expectLoggerOrder();
        $this->loggerRepository->shouldReceive('save')->once()->andReturnUsing(fn ($log) => $log);

        $permission = $this->mockUserPermission(edit: true);
        $this->useCase->execute(10, new OrderItemUpdateData(id: 1, assemblage: false), $permission);

        $this->assertFalse($item->assemblage);
    }

    public function test_delegates_to_set_packings_when_packing_is_true(): void
    {
        $order = $this->makeOrder();
        $item = $this->makeItem(1);
        $order->items = [$item];

        $this->repository->shouldReceive('getById')->with(10)->times(4)->andReturn($order);
        $this->getAdditionDataUseCase->shouldNotReceive('execute');
        $this->additionRepository->shouldReceive('findBySlug')->with('packing')->once()->andReturnNull();
        $this->repository->shouldReceive('save')->with($order)->times(4)->andReturn($order);

        $this->expectLoggerOrder();
        $this->loggerRepository->shouldReceive('save')->once()->andReturnUsing(fn ($log) => $log);

        $permission = $this->mockUserPermission(edit: true);
        $this->useCase->execute(10, new OrderItemUpdateData(id: 1, packing: true), $permission);

        $this->assertTrue($item->packing);
    }

    public function test_delegates_to_set_packings_when_packing_is_false(): void
    {
        $order = $this->makeOrder();
        $item = $this->makeItem(1);
        $order->items = [$item];

        $this->repository->shouldReceive('getById')->with(10)->times(4)->andReturn($order);
        $this->getAdditionDataUseCase->shouldNotReceive('execute');
        $this->additionRepository->shouldReceive('findBySlug')->with('packing')->once()->andReturnNull();
        $this->repository->shouldReceive('save')->with($order)->times(4)->andReturn($order);

        $this->expectLoggerOrder();
        $this->loggerRepository->shouldReceive('save')->once()->andReturnUsing(fn ($log) => $log);

        $permission = $this->mockUserPermission(edit: true);
        $this->useCase->execute(10, new OrderItemUpdateData(id: 1, packing: false), $permission);

        $this->assertFalse($item->packing);
    }

    public function test_delegates_to_both_use_cases_when_assemblage_and_packing_provided(): void
    {
        $order = $this->makeOrder();
        $item = $this->makeItem(1);
        $order->items = [$item];

        $this->repository->shouldReceive('getById')->with(10)->times(6)->andReturn($order);
        $this->getAdditionDataUseCase->shouldNotReceive('execute');
        $this->additionRepository->shouldReceive('findBySlug')->with('assembly-15')->once()->andReturnNull();
        $this->additionRepository->shouldReceive('findBySlug')->with('packing')->once()->andReturnNull();
        $this->repository->shouldReceive('save')->with($order)->times(6)->andReturn($order);

        $this->expectLoggerOrder();
        $this->loggerRepository->shouldReceive('save')->once()->andReturnUsing(fn ($log) => $log);

        $permission = $this->mockUserPermission(edit: true);
        $this->useCase->execute(10, new OrderItemUpdateData(id: 1, assemblage: true, packing: true), $permission);

        $this->assertTrue($item->assemblage);
        $this->assertTrue($item->packing);
    }
}
