<?php

namespace App\Modules\Order\Tests\Unit\Application\Actions\Order;

use App\Modules\Order\Application\Actions\Order\UpdateOrderUseCase;
use App\Modules\Order\Application\DTOs\Order\OrderUpdateData;
use App\Modules\Order\Domain\Entities\OrderEntity;
use App\Modules\Order\Domain\Interfaces\OrderRepositoryInterface;
use App\Modules\Order\Domain\ValueObjects\OrderSellType;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;
use Mockery;
use PHPUnit\Framework\TestCase;
use Tests\Trait\MockPermission;

class UpdateOrderUseCaseTest extends TestCase
{
    use MockPermission;

    private OrderRepositoryInterface $repository;
    private UpdateOrderUseCase $useCase;

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
        $this->useCase = new UpdateOrderUseCase($this->repository);
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

    public function test_updates_provided_fields(): void
    {
        $order = $this->makeOrder();
        $this->repository->shouldReceive('getById')->with(10)->once()->andReturn($order);
        $this->repository->shouldReceive('save')->with($order)->once()->andReturn($order);

        $permission = $this->mockUserPermission(edit: true);
        $result = $this->useCase->execute(
            10,
            new OrderUpdateData(comment: 'Комментарий', traderId: 7, shopperId: 9),
            $permission,
        );

        $this->assertSame($order, $result);
        $this->assertSame('Комментарий', $order->comment);
        $this->assertSame(7, $order->traderId);
        $this->assertSame(9, $order->shopperId);
    }

    public function test_ignores_null_fields(): void
    {
        $order = $this->makeOrder();
        $this->repository->shouldReceive('getById')->with(10)->once()->andReturn($order);
        $this->repository->shouldReceive('save')->with($order)->once()->andReturn($order);

        $permission = $this->mockUserPermission(edit: true);
        $this->useCase->execute(10, new OrderUpdateData(comment: 'Только комментарий'), $permission);

        $this->assertSame('Только комментарий', $order->comment);
        $this->assertSame(1, $order->traderId);
        $this->assertNull($order->shopperId);
    }

    public function test_throws_access_denied_when_missing_permission(): void
    {
        $this->repository->shouldNotReceive('getById');
        $this->repository->shouldNotReceive('save');

        $permission = $this->mockUserPermission(edit: false);
        $this->expectException(AccessDeniedException::class);
        $this->useCase->execute(10, new OrderUpdateData(comment: 'x'), $permission);
    }
}
