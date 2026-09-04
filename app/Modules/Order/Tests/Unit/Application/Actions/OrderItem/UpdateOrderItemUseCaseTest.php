<?php

namespace App\Modules\Order\Tests\Unit\Application\Actions\OrderItem;

use App\Modules\Order\Application\Actions\AdditionGuide\GetAssemblageAdditionUseCase;
use App\Modules\Order\Application\Actions\AdditionGuide\GetPackingAdditionUseCase;
use App\Modules\Order\Application\Actions\GetAdditionDataUseCase;
use App\Modules\Order\Application\Actions\Order\SetAssemblagesOrderUseCase;
use App\Modules\Order\Application\Actions\Order\SetPackingsOrderUseCase;
use App\Modules\Order\Application\Actions\OrderItem\UpdateOrderItemUseCase;
use App\Modules\Order\Application\DTOs\OrderItem\OrderItemUpdateData;
use App\Modules\Order\Application\Interfaces\OrderRepositoryInterface;
use App\Modules\Order\Application\Services\OrderCalculateService;
use App\Modules\Order\Domain\Entities\OrderEntity;
use App\Modules\Order\Domain\Entities\OrderItemEntity;
use App\Modules\Order\Domain\ValueObjects\OrderSellType;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;
use Mockery;
use PHPUnit\Framework\TestCase;
use Tests\Trait\MockPermission;

class UpdateOrderItemUseCaseTest extends TestCase
{
    use MockPermission;

    private OrderRepositoryInterface $repository;
    private GetAdditionDataUseCase $getAdditionDataUseCase;
    private OrderCalculateService $orderCalculateService;
    private GetAssemblageAdditionUseCase $assemblageAdditionUseCase;
    private GetPackingAdditionUseCase $packingAdditionUseCase;
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
        $this->repository = Mockery::mock(OrderRepositoryInterface::class);
        $this->getAdditionDataUseCase = Mockery::mock(GetAdditionDataUseCase::class);
        $this->orderCalculateService = new OrderCalculateService(
            $this->repository,
            $this->getAdditionDataUseCase,
        );
        $this->assemblageAdditionUseCase = Mockery::mock(GetAssemblageAdditionUseCase::class);
        $this->packingAdditionUseCase = Mockery::mock(GetPackingAdditionUseCase::class);

        // SetAssemblagesOrderUseCase, SetPackingsOrderUseCase и OrderCalculateService —
        // readonly-классы, Mockery их не умеет мокать, поэтому подставляем реальные
        // экземпляры с замоканными зависимостями.
        $setAssemblagesOrderUseCase = new SetAssemblagesOrderUseCase(
            $this->repository,
            $this->orderCalculateService,
            $this->assemblageAdditionUseCase,
        );
        $setPackingsOrderUseCase = new SetPackingsOrderUseCase(
            $this->repository,
            $this->orderCalculateService,
            $this->packingAdditionUseCase,
        );

        $this->useCase = new UpdateOrderItemUseCase(
            $this->repository,
            $this->orderCalculateService,
            $setAssemblagesOrderUseCase,
            $setPackingsOrderUseCase,
        );
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

    private function makeItem(int $id): OrderItemEntity
    {
        $item = new OrderItemEntity(productId: $id, quantity: 1, baseCost: 100.0, sellCost: 100.0);
        $item->id = $id;

        return $item;
    }

    public function test_throws_access_denied_when_missing_permission(): void
    {
        $this->repository->shouldNotReceive('getById');
        $this->repository->shouldNotReceive('save');
        $this->assemblageAdditionUseCase->shouldNotReceive('execute');
        $this->packingAdditionUseCase->shouldNotReceive('execute');

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
        $this->assemblageAdditionUseCase->shouldNotReceive('execute');
        $this->packingAdditionUseCase->shouldNotReceive('execute');
        $this->repository->shouldReceive('save')->with($order)->twice()->andReturn($order);

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
        $this->assemblageAdditionUseCase->shouldReceive('execute')->once()->andReturnNull();
        $this->packingAdditionUseCase->shouldNotReceive('execute');
        $this->repository->shouldReceive('save')->with($order)->times(4)->andReturn($order);

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
        $this->assemblageAdditionUseCase->shouldReceive('execute')->once()->andReturnNull();
        $this->packingAdditionUseCase->shouldNotReceive('execute');
        $this->repository->shouldReceive('save')->with($order)->times(4)->andReturn($order);

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
        $this->assemblageAdditionUseCase->shouldNotReceive('execute');
        $this->packingAdditionUseCase->shouldReceive('execute')->once()->andReturnNull();
        $this->repository->shouldReceive('save')->with($order)->times(4)->andReturn($order);

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
        $this->assemblageAdditionUseCase->shouldNotReceive('execute');
        $this->packingAdditionUseCase->shouldReceive('execute')->once()->andReturnNull();
        $this->repository->shouldReceive('save')->with($order)->times(4)->andReturn($order);

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
        $this->assemblageAdditionUseCase->shouldReceive('execute')->once()->andReturnNull();
        $this->packingAdditionUseCase->shouldReceive('execute')->once()->andReturnNull();
        $this->repository->shouldReceive('save')->with($order)->times(6)->andReturn($order);

        $permission = $this->mockUserPermission(edit: true);
        $this->useCase->execute(10, new OrderItemUpdateData(id: 1, assemblage: true, packing: true), $permission);

        $this->assertTrue($item->assemblage);
        $this->assertTrue($item->packing);
    }
}
