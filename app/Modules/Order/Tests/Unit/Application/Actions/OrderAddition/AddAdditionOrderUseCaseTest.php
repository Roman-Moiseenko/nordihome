<?php

namespace App\Modules\Order\Tests\Unit\Application\Actions\OrderAddition;

use App\Modules\Order\Application\Actions\GetAdditionDataUseCase;
use App\Modules\Order\Application\Actions\OrderAddition\AddAdditionOrderUseCase;
use App\Modules\Order\Application\Actions\OrderLogger\CreateOrderLoggerUseCase;
use App\Modules\Order\Application\DTOs\AdditionData;
use App\Modules\Order\Application\Services\OrderCalculateService;
use App\Modules\Order\Domain\Entities\OrderEntity;
use App\Modules\Order\Domain\Interfaces\OrderLoggerRepositoryInterface;
use App\Modules\Order\Domain\Interfaces\OrderRepositoryInterface;
use App\Modules\Order\Domain\ValueObjects\OrderSellType;
use App\Modules\Order\Domain\ValueObjects\OrderStatus;
use App\Modules\Order\Tests\Support\SetsUpLaravelHelpers;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;
use Mockery;
use PHPUnit\Framework\TestCase;
use Tests\Trait\MockPermission;

class AddAdditionOrderUseCaseTest extends TestCase
{
    use MockPermission;
    use SetsUpLaravelHelpers;

    private OrderRepositoryInterface $repository;
    private OrderLoggerRepositoryInterface $loggerRepository;
    private OrderRepositoryInterface $loggerOrderRepository;
    private GetAdditionDataUseCase $getAdditionDataUseCase;
    private AddAdditionOrderUseCase $useCase;

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

        $this->useCase = new AddAdditionOrderUseCase($this->repository, $calculateService, $loggerUseCase);
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

    public function test_throws_access_denied_when_missing_permission(): void
    {
        $this->repository->shouldNotReceive('getById');
        $this->loggerRepository->shouldNotReceive('save');

        $permission = $this->mockUserPermission(edit: false);
        $this->expectException(AccessDeniedException::class);
        $this->useCase->execute(10, 50, $permission);
    }

    public function test_adds_addition_and_logs(): void
    {
        $order = $this->makeOrder();

        $this->repository->shouldReceive('getById')->with(10)->twice()->andReturn($order);
        $this->repository->shouldReceive('save')->with($order)->twice()->andReturn($order);
        $this->getAdditionDataUseCase->shouldReceive('execute')
            ->with(50)
            ->once()
            ->andReturn(new AdditionData(baseRatio: 0, name: 'Сборка', isQuantity: false, isManual: true, calculate: null, type: 'assembly'));

        $loggerOrder = $this->makeOrder();
        $this->loggerOrderRepository->shouldReceive('getById')->with(10)->once()->andReturn($loggerOrder);
        $this->loggerRepository->shouldReceive('save')
            ->once()
            ->andReturnUsing(function ($log) {
                $this->assertSame('Услуга добавлена в заказ', $log->action);
                $this->assertSame('50', $log->object);

                return $log;
            });

        $permission = $this->mockUserPermission(edit: true);
        $this->useCase->execute(10, 50, $permission);

        $this->assertCount(1, $order->additions);
        $this->assertSame(50, $order->additions[0]->additionId);
    }
}
