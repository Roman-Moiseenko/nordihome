<?php

namespace App\Modules\Order\Tests\Unit\Application\Services\StatusServices;

use App\Modules\Lead\Application\Actions\SetStatusLeadFromOrderUseCase;
use App\Modules\Lead\Domain\Entities\LeadEntity;
use App\Modules\Lead\Domain\Interfaces\LeadRepositoryInterface;
use App\Modules\Order\Application\Actions\Order\SetStatusOrderUseCase;
use App\Modules\Order\Application\Actions\OrderLogger\CreateOrderLoggerUseCase;
use App\Modules\Order\Application\Services\StatusServices\StatusReturnInWorkOrderService;
use App\Modules\Order\Domain\Entities\OrderEntity;
use App\Modules\Order\Domain\Interfaces\OrderLoggerRepositoryInterface;
use App\Modules\Order\Domain\Interfaces\OrderRepositoryInterface;
use App\Modules\Order\Domain\ValueObjects\OrderSellType;
use App\Modules\Order\Domain\ValueObjects\OrderStatus;
use App\Modules\Order\Tests\Support\SetsUpLaravelHelpers;
use App\Modules\Shared\Application\Interfaces\TransactionManagerInterface;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;
use DomainException;
use Mockery;
use PHPUnit\Framework\TestCase;
use Tests\Trait\MockPermission;

class StatusReturnInWorkOrderServiceTest extends TestCase
{
    use MockPermission;
    use SetsUpLaravelHelpers;

    private OrderRepositoryInterface $repository;
    private LeadRepositoryInterface $leadRepository;
    private OrderLoggerRepositoryInterface $loggerRepository;
    private OrderRepositoryInterface $loggerOrderRepository;
    private TransactionManagerInterface $transactionManager;
    private StatusReturnInWorkOrderService $useCase;

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
        $this->leadRepository = Mockery::mock(LeadRepositoryInterface::class);
        $this->loggerRepository = Mockery::mock(OrderLoggerRepositoryInterface::class);
        $this->loggerOrderRepository = Mockery::mock(OrderRepositoryInterface::class);
        $this->transactionManager = Mockery::mock(TransactionManagerInterface::class);
        $this->transactionManager->shouldReceive('execute')->andReturnUsing(fn ($callback) => $callback());

        $statusOrderUseCase = new SetStatusOrderUseCase($this->repository);
        $leadFromOrderUseCase = new SetStatusLeadFromOrderUseCase($this->leadRepository);
        $loggerUseCase = new CreateOrderLoggerUseCase($this->loggerRepository, $this->loggerOrderRepository);

        $this->useCase = new StatusReturnInWorkOrderService(
            $this->transactionManager,
            $statusOrderUseCase,
            $leadFromOrderUseCase,
            $this->repository,
            $loggerUseCase,
        );
    }

    protected function tearDown(): void
    {
        $this->tearDownLaravelHelpers();
        Mockery::close();
        parent::tearDown();
    }

    private function makeAwaitingOrder(): OrderEntity
    {
        $order = new OrderEntity(traderId: 1, type: new OrderSellType(OrderSellType::ONLINE));
        $order->id = 10;
        $order->addStatus(OrderStatus::awaiting());

        return $order;
    }

    public function test_throws_access_denied_when_missing_permission(): void
    {
        $this->transactionManager->shouldNotReceive('execute');

        $permission = $this->mockUserPermission(edit: false);
        $this->expectException(AccessDeniedException::class);
        $this->useCase->execute(10, $permission);
    }

    public function test_returns_order_to_work(): void
    {
        $order = $this->makeAwaitingOrder();
        $lead = new LeadEntity(leadableId: 10, leadableType: 'order.order', data: []);

        // getById: проверка статуса + смена статуса
        $this->repository->shouldReceive('getById')->with(10)->twice()->andReturn($order);
        $this->repository->shouldReceive('save')->with($order)->once()->andReturn($order);

        $this->leadRepository->shouldReceive('findByOrderId')->with(10)->once()->andReturn($lead);
        $this->leadRepository->shouldReceive('save')->with($lead)->once()->andReturn($lead);

        $loggerOrder = new OrderEntity(traderId: 1, type: new OrderSellType(OrderSellType::ONLINE));
        $loggerOrder->id = 10;
        $loggerOrder->addStatus(OrderStatus::new());
        $this->loggerOrderRepository->shouldReceive('getById')->with(10)->once()->andReturn($loggerOrder);
        $this->loggerRepository->shouldReceive('save')->once()->andReturnUsing(fn ($log) => $log);

        $permission = $this->mockUserPermission(edit: true);
        $this->useCase->execute(10, $permission);

        $this->assertSame(OrderStatus::IN_WORK, $order->status->value->getValue());
    }

    public function test_throws_when_order_is_not_awaiting(): void
    {
        $order = new OrderEntity(traderId: 1, type: new OrderSellType(OrderSellType::ONLINE));
        $order->id = 10;
        $order->addStatus(OrderStatus::new());

        $this->repository->shouldReceive('getById')->with(10)->once()->andReturn($order);
        $this->repository->shouldNotReceive('save');

        $permission = $this->mockUserPermission(edit: true);
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Заказ нельзя вернуть в работу');
        $this->useCase->execute(10, $permission);
    }
}
