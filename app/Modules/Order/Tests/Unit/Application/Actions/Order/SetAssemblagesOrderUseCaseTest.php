<?php

namespace App\Modules\Order\Tests\Unit\Application\Actions\Order;

use App\Modules\Guide\Domain\Entities\AdditionEntity;
use App\Modules\Guide\Domain\Interfaces\AdditionRepositoryInterface;
use App\Modules\Guide\Domain\ValueObjects\AdditionType;
use App\Modules\Order\Application\Actions\AdditionGuide\GetAssemblageAdditionUseCase;
use App\Modules\Order\Application\Actions\GetAdditionDataUseCase;
use App\Modules\Order\Application\Actions\Order\SetAssemblagesOrderUseCase;
use App\Modules\Order\Application\Actions\OrderLogger\CreateOrderLoggerUseCase;
use App\Modules\Order\Application\DTOs\AdditionData;
use App\Modules\Order\Application\Services\OrderCalculateService;
use App\Modules\Order\Domain\Entities\OrderEntity;
use App\Modules\Order\Domain\Entities\OrderItemEntity;
use App\Modules\Order\Domain\Interfaces\OrderLoggerRepositoryInterface;
use App\Modules\Order\Domain\Interfaces\OrderRepositoryInterface;
use App\Modules\Order\Domain\ValueObjects\OrderSellType;
use App\Modules\Order\Domain\ValueObjects\OrderStatus;
use App\Modules\Order\Tests\Support\SetsUpLaravelHelpers;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;
use App\Modules\Shared\Domain\ValueObjects\Slug;
use Mockery;
use PHPUnit\Framework\TestCase;
use Tests\Trait\MockPermission;

class SetAssemblagesOrderUseCaseTest extends TestCase
{
    use MockPermission;
    use SetsUpLaravelHelpers;

    private OrderRepositoryInterface $repository;
    private AdditionRepositoryInterface $additionRepository;
    private OrderLoggerRepositoryInterface $loggerRepository;
    private OrderRepositoryInterface $loggerOrderRepository;
    private GetAdditionDataUseCase $getAdditionDataUseCase;
    private SetAssemblagesOrderUseCase $useCase;

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
        $this->additionRepository = Mockery::mock(AdditionRepositoryInterface::class);
        $this->loggerRepository = Mockery::mock(OrderLoggerRepositoryInterface::class);
        $this->loggerOrderRepository = Mockery::mock(OrderRepositoryInterface::class);
        $this->getAdditionDataUseCase = Mockery::mock(GetAdditionDataUseCase::class);

        $calculateService = new OrderCalculateService($this->repository, $this->getAdditionDataUseCase);
        $assemblageUseCase = new GetAssemblageAdditionUseCase($this->additionRepository);
        $loggerUseCase = new CreateOrderLoggerUseCase($this->loggerRepository, $this->loggerOrderRepository);

        $this->useCase = new SetAssemblagesOrderUseCase(
            $this->repository,
            $calculateService,
            $assemblageUseCase,
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

    private function makeGuide(): AdditionEntity
    {
        $guide = new AdditionEntity('Сборка', new Slug('assembly-15'), new AdditionType(null));
        $guide->id = 50;

        return $guide;
    }

    public function test_throws_access_denied_when_missing_permission(): void
    {
        $this->repository->shouldNotReceive('getById');
        $this->loggerRepository->shouldNotReceive('save');

        $permission = $this->mockUserPermission(edit: false);
        $this->expectException(AccessDeniedException::class);
        $this->useCase->execute(10, true, [1], $permission);
    }

    public function test_sets_assemblage_when_guide_not_found(): void
    {
        $order = $this->makeOrder();
        $item = $this->makeItem(1);
        $order->items = [$item];

        $this->repository->shouldReceive('getById')->with(10)->twice()->andReturn($order);
        $this->repository->shouldReceive('save')->with($order)->twice()->andReturn($order);
        $this->additionRepository->shouldReceive('findBySlug')->with('assembly-15')->once()->andReturnNull();
        $this->getAdditionDataUseCase->shouldNotReceive('execute');
        $this->loggerRepository->shouldNotReceive('save');

        $permission = $this->mockUserPermission(edit: true);
        $this->useCase->execute(10, true, [1], $permission);

        $this->assertTrue($item->assemblage);
    }

    public function test_adds_addition_and_logs_when_multiple_items(): void
    {
        $order = $this->makeOrder();
        $item1 = $this->makeItem(1);
        $item2 = $this->makeItem(2);
        $order->items = [$item1, $item2];

        $guide = $this->makeGuide();

        $this->repository->shouldReceive('getById')->with(10)->twice()->andReturn($order);
        $this->repository->shouldReceive('save')->with($order)->twice()->andReturn($order);
        $this->additionRepository->shouldReceive('findBySlug')->with('assembly-15')->once()->andReturn($guide);
        $this->getAdditionDataUseCase->shouldReceive('execute')
            ->with(50)
            ->once()
            ->andReturn(new AdditionData(baseRatio: 0, name: 'Сборка', isQuantity: false, isManual: true, calculate: null, type: 'assembly'));

        $loggerOrder = $this->makeOrder();
        $this->loggerOrderRepository->shouldReceive('getById')->with(10)->once()->andReturn($loggerOrder);
        $this->loggerRepository->shouldReceive('save')->once()->andReturnUsing(fn ($log) => $log);

        $permission = $this->mockUserPermission(edit: true);
        $this->useCase->execute(10, true, [1, 2], $permission);

        $this->assertTrue($item1->assemblage);
        $this->assertTrue($item2->assemblage);
        $this->assertCount(1, $order->additions);
        $this->assertSame(50, $order->additions[0]->additionId);
    }

    public function test_removes_addition_when_no_items_need_assemblage(): void
    {
        $order = $this->makeOrder();
        $item = $this->makeItem(1);
        $order->items = [$item];
        $order->addAddition(50);

        $guide = $this->makeGuide();

        $this->repository->shouldReceive('getById')->with(10)->twice()->andReturn($order);
        $this->repository->shouldReceive('save')->with($order)->twice()->andReturn($order);
        $this->additionRepository->shouldReceive('findBySlug')->with('assembly-15')->once()->andReturn($guide);
        $this->getAdditionDataUseCase->shouldNotReceive('execute');
        $this->loggerRepository->shouldNotReceive('save');

        $permission = $this->mockUserPermission(edit: true);
        $this->useCase->execute(10, false, [1], $permission);

        $this->assertFalse($item->assemblage);
        $this->assertCount(0, $order->additions);
    }
}
