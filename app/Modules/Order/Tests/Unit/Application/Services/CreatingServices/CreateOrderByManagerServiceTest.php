<?php

namespace App\Modules\Order\Tests\Unit\Application\Services\CreatingServices;

use App\Modules\Accounting\Application\Actions\Trader\GetDefaultTraderIdUseCase;
use App\Modules\Auth\Application\Actions\Client\ViewClientUseCase;
use App\Modules\Auth\Domain\Interfaces\ClientRepositoryInterface;
use App\Modules\Lead\Application\Actions\SetManagerLeadFromOrderUseCase;
use App\Modules\Lead\Application\Actions\SetStatusLeadFromOrderUseCase;
use App\Modules\Lead\Domain\Entities\LeadEntity;
use App\Modules\Lead\Domain\Interfaces\LeadRepositoryInterface;
use App\Modules\Order\Application\Actions\Order\CreateOrderUseCase;
use App\Modules\Order\Application\Actions\Order\SetStatusOrderUseCase;
use App\Modules\Order\Application\Actions\OrderLogger\CreateOrderLoggerUseCase;
use App\Modules\Order\Application\Services\CreatingServices\CreateOrderByManagerService;
use App\Modules\Order\Domain\Interfaces\OrderLoggerRepositoryInterface;
use App\Modules\Order\Domain\Interfaces\OrderRepositoryInterface;
use App\Modules\Order\Domain\ValueObjects\OrderStatus;
use App\Modules\Order\Tests\Support\SetsUpLaravelHelpers;
use App\Modules\Shared\Application\Interfaces\TransactionManagerInterface;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;
use Illuminate\Events\Dispatcher;
use Mockery;
use PHPUnit\Framework\TestCase;
use Tests\Trait\MockPermission;

class CreateOrderByManagerServiceTest extends TestCase
{
    use MockPermission;
    use SetsUpLaravelHelpers;

    private OrderRepositoryInterface $repository;
    private ClientRepositoryInterface $clientRepository;
    private LeadRepositoryInterface $leadRepository;
    private OrderLoggerRepositoryInterface $loggerRepository;
    private OrderRepositoryInterface $loggerOrderRepository;
    private GetDefaultTraderIdUseCase $traderIdUseCase;
    private Dispatcher $dispatcher;
    private TransactionManagerInterface $transactionManager;
    private CreateOrderByManagerService $useCase;

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
        $this->clientRepository = Mockery::mock(ClientRepositoryInterface::class);
        $this->leadRepository = Mockery::mock(LeadRepositoryInterface::class);
        $this->loggerRepository = Mockery::mock(OrderLoggerRepositoryInterface::class);
        $this->loggerOrderRepository = Mockery::mock(OrderRepositoryInterface::class);
        $this->traderIdUseCase = Mockery::mock(GetDefaultTraderIdUseCase::class);
        $this->dispatcher = Mockery::mock(Dispatcher::class);
        $this->transactionManager = Mockery::mock(TransactionManagerInterface::class);
        $this->transactionManager->shouldReceive('execute')->andReturnUsing(fn ($callback) => $callback());

        $setStatusOrderUseCase = new SetStatusOrderUseCase($this->repository);
        $createOrderUseCase = new CreateOrderUseCase(
            $this->repository,
            new ViewClientUseCase($this->clientRepository),
            $this->traderIdUseCase,
        );
        $leadFromOrderUseCase = new SetStatusLeadFromOrderUseCase($this->leadRepository);
        $setManagerLeadUseCase = new SetManagerLeadFromOrderUseCase($this->leadRepository);
        $loggerUseCase = new CreateOrderLoggerUseCase($this->loggerRepository, $this->loggerOrderRepository);

        $this->useCase = new CreateOrderByManagerService(
            $setStatusOrderUseCase,
            $this->dispatcher,
            $createOrderUseCase,
            $leadFromOrderUseCase,
            $setManagerLeadUseCase,
            $loggerUseCase,
            $this->transactionManager,
        );
    }

    protected function tearDown(): void
    {
        $this->tearDownLaravelHelpers();
        Mockery::close();
        parent::tearDown();
    }

    public function test_throws_access_denied_when_missing_permission(): void
    {
        $this->transactionManager->shouldNotReceive('execute');

        $permission = $this->mockUserPermission(create: false);
        $this->expectException(AccessDeniedException::class);
        $this->useCase->execute(null, 33, $permission);
    }

    public function test_creates_order_by_manager(): void
    {
        $lead = new LeadEntity(leadableId: 100, leadableType: 'order.order', data: []);

        $this->traderIdUseCase->shouldReceive('execute')->once()->andReturn(3);

        $createdOrder = null;
        // save: создание заказа (id=100) + смена статуса
        $this->repository->shouldReceive('save')
            ->twice()
            ->andReturnUsing(function ($order) use (&$createdOrder) {
                $order->id = 100;
                $createdOrder = $order;

                return $order;
            });
        $this->repository->shouldReceive('getById')->with(100)->once()->andReturnUsing(function () use (&$createdOrder) {
            return $createdOrder;
        });

        $this->leadRepository->shouldReceive('findByOrderId')->with(100)->twice()->andReturn($lead);
        $this->leadRepository->shouldReceive('save')->with($lead)->twice()->andReturn($lead);

        $this->dispatcher->shouldReceive('dispatch')->once()->andReturnNull();

        $loggerOrder = new \App\Modules\Order\Domain\Entities\OrderEntity(
            traderId: 1,
            type: new \App\Modules\Order\Domain\ValueObjects\OrderSellType(\App\Modules\Order\Domain\ValueObjects\OrderSellType::ONLINE),
        );
        $loggerOrder->id = 100;
        $loggerOrder->addStatus(OrderStatus::new());
        $this->loggerOrderRepository->shouldReceive('getById')->with(100)->once()->andReturn($loggerOrder);
        $this->loggerRepository->shouldReceive('save')->once()->andReturnUsing(fn ($log) => $log);

        $permission = $this->mockUserPermission(create: true);
        $result = $this->useCase->execute(null, 33, $permission);

        // Сервис всегда возвращает null (результат транзакции не пробрасывается наружу)
        $this->assertNull($result);

        $this->assertNotNull($createdOrder);
        $this->assertSame(100, $createdOrder->id);
        $this->assertSame(33, $createdOrder->staffId);
        $this->assertSame(OrderStatus::IN_WORK, $createdOrder->status->value->getValue());
    }
}
