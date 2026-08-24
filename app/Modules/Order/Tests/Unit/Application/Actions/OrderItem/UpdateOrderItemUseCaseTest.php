<?php

namespace App\Modules\Order\Tests\Unit\Application\Actions\OrderItem;

use App\Modules\Guide\Entity\Addition;
use App\Modules\Order\Application\Actions\AdditionGuide\GetAssemblageAdditionUseCase;
use App\Modules\Order\Application\Actions\AdditionGuide\GetPackingAdditionUseCase;
use App\Modules\Order\Application\Actions\OrderItem\UpdateOrderItemUseCase;
use App\Modules\Order\Application\DTOs\OrderItem\OrderItemUpdateData;
use App\Modules\Order\Application\Interfaces\OrderRepositoryInterface;
use App\Modules\Order\Application\Services\OrderCalculateService;
use App\Modules\Order\Domain\Entities\OrderAdditionEntity;
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
        $this->orderCalculateService = Mockery::mock(OrderCalculateService::class);
        $this->assemblageAdditionUseCase = Mockery::mock(GetAssemblageAdditionUseCase::class);
        $this->packingAdditionUseCase = Mockery::mock(GetPackingAdditionUseCase::class);

        $this->useCase = new UpdateOrderItemUseCase(
            $this->repository,
            $this->orderCalculateService,
            $this->assemblageAdditionUseCase,
            $this->packingAdditionUseCase,
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

    private function addition(int $id): Addition
    {
        // Мок Eloquent-модели без обращения к БД: чтение ->id идёт через
        // реальный Model::__get() -> getAttribute(), который мы мокаем.
        $addition = Mockery::mock(Addition::class);
        $addition->shouldReceive('getAttribute')->with('id')->andReturn($id);

        return $addition;
    }

    public function test_throws_access_denied_when_missing_permission(): void
    {
        $this->repository->shouldNotReceive('getById');
        $this->repository->shouldNotReceive('save');
        $this->orderCalculateService->shouldNotReceive('execute');
        $this->assemblageAdditionUseCase->shouldNotReceive('execute');
        $this->packingAdditionUseCase->shouldNotReceive('execute');

        $permission = $this->mockUserPermission(edit: false);
        $this->expectException(AccessDeniedException::class);
        $this->useCase->execute(10, new OrderItemUpdateData(id: 1), $permission);
    }

    public function test_adds_assemblage_addition_when_assemblage_is_true(): void
    {
        $order = $this->makeOrder();
        $item = $this->makeItem(1);
        $order->items = [$item];

        $this->repository->shouldReceive('getById')->with(10)->once()->andReturn($order);
        $this->assemblageAdditionUseCase->shouldReceive('execute')->once()->andReturn($this->addition(104));
        $this->packingAdditionUseCase->shouldNotReceive('execute');
        $this->repository->shouldReceive('save')->with($order)->once()->andReturn($order);
        $this->orderCalculateService->shouldReceive('execute')->with(10)->once();

        $permission = $this->mockUserPermission(edit: true);
        $this->useCase->execute(10, new OrderItemUpdateData(id: 1, assemblage: true), $permission);

        $this->assertCount(1, $order->additions);
        $this->assertSame(104, $order->additions[0]->additionId);
        $this->assertTrue($item->assemblage);
    }

    public function test_removes_assemblage_addition_when_assemblage_is_false_and_no_other_item(): void
    {
        $order = $this->makeOrder();
        $item = $this->makeItem(1);
        $order->items = [$item];
        $order->additions[] = new OrderAdditionEntity(104);

        $this->repository->shouldReceive('getById')->with(10)->once()->andReturn($order);
        $this->assemblageAdditionUseCase->shouldReceive('execute')->once()->andReturn($this->addition(104));
        $this->packingAdditionUseCase->shouldNotReceive('execute');
        $this->repository->shouldReceive('save')->with($order)->once()->andReturn($order);
        $this->orderCalculateService->shouldReceive('execute')->with(10)->once();

        $permission = $this->mockUserPermission(edit: true);
        $this->useCase->execute(10, new OrderItemUpdateData(id: 1, assemblage: false), $permission);

        $this->assertCount(0, $order->additions);
        $this->assertFalse($item->assemblage);
    }

    public function test_keeps_assemblage_addition_when_another_item_has_assemblage(): void
    {
        $order = $this->makeOrder();
        $item = $this->makeItem(1);
        $other = $this->makeItem(2);
        $other->assemblage = true;
        $order->items = [$item, $other];
        $order->additions[] = new OrderAdditionEntity(104);

        $this->repository->shouldReceive('getById')->with(10)->once()->andReturn($order);
        $this->assemblageAdditionUseCase->shouldReceive('execute')->once()->andReturn($this->addition(104));
        $this->packingAdditionUseCase->shouldNotReceive('execute');
        $this->repository->shouldReceive('save')->with($order)->once()->andReturn($order);
        $this->orderCalculateService->shouldReceive('execute')->with(10)->once();

        $permission = $this->mockUserPermission(edit: true);
        $this->useCase->execute(10, new OrderItemUpdateData(id: 1, assemblage: false), $permission);

        $this->assertCount(1, $order->additions);
    }

    public function test_adds_packing_addition_when_packing_is_true(): void
    {
        $order = $this->makeOrder();
        $item = $this->makeItem(1);
        $order->items = [$item];

        $this->repository->shouldReceive('getById')->with(10)->once()->andReturn($order);
        $this->assemblageAdditionUseCase->shouldNotReceive('execute');
        $this->packingAdditionUseCase->shouldReceive('execute')->once()->andReturn($this->addition(103));
        $this->repository->shouldReceive('save')->with($order)->once()->andReturn($order);
        $this->orderCalculateService->shouldReceive('execute')->with(10)->once();

        $permission = $this->mockUserPermission(edit: true);
        $this->useCase->execute(10, new OrderItemUpdateData(id: 1, packing: true), $permission);

        $this->assertCount(1, $order->additions);
        $this->assertSame(103, $order->additions[0]->additionId);
        $this->assertTrue($item->packing);
    }

    public function test_removes_packing_addition_when_packing_is_false_and_no_other_item(): void
    {
        $order = $this->makeOrder();
        $item = $this->makeItem(1);
        $order->items = [$item];
        $order->additions[] = new OrderAdditionEntity(103);

        $this->repository->shouldReceive('getById')->with(10)->once()->andReturn($order);
        $this->assemblageAdditionUseCase->shouldNotReceive('execute');
        $this->packingAdditionUseCase->shouldReceive('execute')->once()->andReturn($this->addition(103));
        $this->repository->shouldReceive('save')->with($order)->once()->andReturn($order);
        $this->orderCalculateService->shouldReceive('execute')->with(10)->once();

        $permission = $this->mockUserPermission(edit: true);
        $this->useCase->execute(10, new OrderItemUpdateData(id: 1, packing: false), $permission);

        $this->assertCount(0, $order->additions);
        $this->assertFalse($item->packing);
    }
}
