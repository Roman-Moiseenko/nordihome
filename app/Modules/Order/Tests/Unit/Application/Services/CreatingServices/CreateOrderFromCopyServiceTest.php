<?php

namespace App\Modules\Order\Tests\Unit\Application\Services\CreatingServices;

use App\Modules\Lead\Application\Actions\SetManagerLeadFromOrderUseCase;
use App\Modules\Lead\Application\Actions\SetStatusLeadFromOrderUseCase;
use App\Modules\Lead\Domain\Entities\LeadEntity;
use App\Modules\Lead\Domain\Interfaces\LeadRepositoryInterface;
use App\Modules\Order\Application\Actions\Order\SetStatusOrderUseCase;
use App\Modules\Order\Application\Actions\OrderLogger\CreateOrderLoggerUseCase;
use App\Modules\Order\Application\Services\CreatingServices\CreateOrderFromCopyService;
use App\Modules\Order\Domain\Entities\OrderEntity;
use App\Modules\Order\Domain\Interfaces\OrderLoggerRepositoryInterface;
use App\Modules\Order\Domain\Interfaces\OrderRepositoryInterface;
use App\Modules\Order\Domain\ValueObjects\OrderSellType;
use App\Modules\Order\Domain\ValueObjects\OrderStatus;
use App\Modules\Order\Tests\Support\SetsUpLaravelHelpers;
use App\Modules\Shared\Application\Interfaces\TransactionManagerInterface;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;
use Illuminate\Events\Dispatcher;
use Mockery;
use PHPUnit\Framework\TestCase;
use Tests\Trait\MockPermission;

class CreateOrderFromCopyServiceTest extends TestCase
{
    use MockPermission;
    use SetsUpLaravelHelpers;

    private OrderRepositoryInterface $repository;
    private LeadRepositoryInterface $leadRepository;
    private OrderLoggerRepositoryInterface $loggerRepository;
    private OrderRepositoryInterface $loggerOrderRepository;
    private Dispatcher $dispatcher;
    private TransactionManagerInterface $transactionManager;
    private CreateOrderFromCopyService $useCase;

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
        $this->dispatcher = Mockery::mock(Dispatcher::class);
        $this->transactionManager = Mockery::mock(TransactionManagerInterface::class);
        $this->transactionManager->shouldReceive('execute')->andReturnUsing(fn ($callback) => $callback());

        $setStatusOrderUseCase = new SetStatusOrderUseCase($this->repository);
        $leadFromOrderUseCase = new SetStatusLeadFromOrderUseCase($this->leadRepository);
        $setManagerLeadUseCase = new SetManagerLeadFromOrderUseCase($this->leadRepository);
        $loggerUseCase = new CreateOrderLoggerUseCase($this->loggerRepository, $this->loggerOrderRepository);

        $this->useCase = new CreateOrderFromCopyService(
            $this->repository,
            $setStatusOrderUseCase,
            $this->dispatcher,
            $this->transactionManager,
            $leadFromOrderUseCase,
            $setManagerLeadUseCase,
            $loggerUseCase,
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
        $this->useCase->execute(10, 33, $permission);
    }

    public function test_copies_order(): void
    {
        $order = new OrderEntity(traderId: 1, type: new OrderSellType(OrderSellType::ONLINE));
        $order->id = 10;
        $order->addStatus(OrderStatus::new());

        $lead = new LeadEntity(leadableId: 10, leadableType: 'order.order', data: []);

        $this->repository->shouldReceive('getById')->with(10)->twice()->andReturn($order);
        $this->repository->shouldReceive('save')
            ->twice()
            ->andReturnUsing(function ($copied) {
                $copied->id = 200;

                return $copied;
            });

        $this->leadRepository->shouldReceive('findByOrderId')->with(10)->twice()->andReturn($lead);
        $this->leadRepository->shouldReceive('save')->with($lead)->twice()->andReturn($lead);

        $this->dispatcher->shouldReceive('dispatch')->once()->andReturnNull();

        $loggerOrder = new OrderEntity(traderId: 1, type: new OrderSellType(OrderSellType::ONLINE));
        $loggerOrder->id = 200;
        $loggerOrder->addStatus(OrderStatus::new());
        $this->loggerOrderRepository->shouldReceive('getById')->with(200)->once()->andReturn($loggerOrder);
        $this->loggerRepository->shouldReceive('save')->once()->andReturnUsing(fn ($log) => $log);

        $permission = $this->mockUserPermission(create: true);
        $result = $this->useCase->execute(10, 33, $permission);

        $this->assertNull($result);
        $this->assertSame(200, $order->id);
        $this->assertSame(33, $order->staffId);
        $this->assertSame(OrderStatus::IN_WORK, $order->status->value->getValue());
    }
}
