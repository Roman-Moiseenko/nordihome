<?php

namespace App\Modules\Order\Tests\Unit\Application\Services;

use App\Modules\Lead\Application\Actions\SetClientLeadByOrderIdUseCase;
use App\Modules\Lead\Domain\Entities\LeadEntity;
use App\Modules\Lead\Domain\Interfaces\LeadRepositoryInterface;
use App\Modules\Order\Application\Actions\Order\SetClientOrderUseCase;
use App\Modules\Order\Application\Actions\OrderLogger\CreateOrderLoggerUseCase;
use App\Modules\Order\Application\DTOs\Order\AssignClientToOrderData;
use App\Modules\Order\Application\Services\AssignClientToOrderService;
use App\Modules\Order\Domain\Entities\OrderEntity;
use App\Modules\Order\Domain\Interfaces\OrderLoggerRepositoryInterface;
use App\Modules\Order\Domain\Interfaces\OrderRepositoryInterface;
use App\Modules\Order\Domain\ValueObjects\OrderSellType;
use App\Modules\Order\Domain\ValueObjects\OrderStatus;
use App\Modules\Order\Tests\Support\SetsUpLaravelHelpers;
use App\Modules\Shared\Application\Interfaces\TransactionManagerInterface;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;
use Mockery;
use PHPUnit\Framework\TestCase;
use Tests\Trait\MockPermission;

class AssignClientToOrderServiceTest extends TestCase
{
    use MockPermission;
    use SetsUpLaravelHelpers;

    private OrderRepositoryInterface $repository;
    private LeadRepositoryInterface $leadRepository;
    private OrderLoggerRepositoryInterface $loggerRepository;
    private OrderRepositoryInterface $loggerOrderRepository;
    private TransactionManagerInterface $transactionManager;
    private AssignClientToOrderService $useCase;

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

        $setClientOrderUseCase = new SetClientOrderUseCase($this->repository);
        $setClientLeadUseCase = new SetClientLeadByOrderIdUseCase($this->leadRepository);
        $loggerUseCase = new CreateOrderLoggerUseCase($this->loggerRepository, $this->loggerOrderRepository);

        $this->useCase = new AssignClientToOrderService(
            $setClientOrderUseCase,
            $setClientLeadUseCase,
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

        $permission = $this->mockUserPermission(edit: false);
        $this->expectException(AccessDeniedException::class);
        $this->useCase->execute(new AssignClientToOrderData(orderId: 10, clientId: 5), $permission);
    }

    public function test_assigns_client_to_order_and_lead(): void
    {
        $order = new OrderEntity(traderId: 1, type: new OrderSellType(OrderSellType::ONLINE));
        $order->id = 10;
        $order->addStatus(OrderStatus::new());

        $lead = new LeadEntity(leadableId: 10, leadableType: 'order.order', data: []);

        $this->repository->shouldReceive('getById')->with(10)->once()->andReturn($order);
        $this->repository->shouldReceive('save')->with($order)->once()->andReturn($order);

        $this->leadRepository->shouldReceive('findByOrderId')->with(10)->once()->andReturn($lead);
        $this->leadRepository->shouldReceive('save')->with($lead)->once()->andReturn($lead);

        $loggerOrder = new OrderEntity(traderId: 1, type: new OrderSellType(OrderSellType::ONLINE));
        $loggerOrder->id = 10;
        $loggerOrder->addStatus(OrderStatus::new());
        $this->loggerOrderRepository->shouldReceive('getById')->with(10)->once()->andReturn($loggerOrder);
        $this->loggerRepository->shouldReceive('save')
            ->once()
            ->andReturnUsing(function ($log) {
                $this->assertSame('Заказу назначен клиент', $log->action);

                return $log;
            });

        $permission = $this->mockUserPermission(edit: true);
        $this->useCase->execute(new AssignClientToOrderData(orderId: 10, clientId: 5), $permission);

        $this->assertSame(5, $order->clientId);
        $this->assertSame(5, $lead->clientId);
    }
}
