<?php

namespace App\Modules\Order\Tests\Unit\Application\Services\StatusServices;

use App\Modules\Auth\Application\Actions\Client\ViewClientUseCase;
use App\Modules\Auth\Domain\Interfaces\ClientRepositoryInterface;
use App\Modules\Lead\Application\Actions\SetStatusLeadFromOrderUseCase;
use App\Modules\Lead\Domain\Entities\LeadEntity;
use App\Modules\Lead\Domain\Interfaces\LeadRepositoryInterface;
use App\Modules\Order\Application\Actions\Order\SendMailNewOrderClientUseCase;
use App\Modules\Order\Application\Actions\Order\SetStatusOrderUseCase;
use App\Modules\Order\Application\Actions\OrderLogger\CreateOrderLoggerUseCase;
use App\Modules\Order\Application\Services\StatusServices\StatusAwaitingOrderService;
use App\Modules\Order\Domain\Entities\OrderEntity;
use App\Modules\Order\Domain\Entities\OrderItemEntity;
use App\Modules\Order\Domain\Interfaces\OrderLoggerRepositoryInterface;
use App\Modules\Order\Domain\Interfaces\OrderRepositoryInterface;
use App\Modules\Order\Domain\ValueObjects\OrderSellType;
use App\Modules\Order\Domain\ValueObjects\OrderStatus;
use App\Modules\Order\Tests\Support\SetsUpLaravelHelpers;
use App\Modules\Shared\Application\Interfaces\Mail\MailServiceInterface;
use App\Modules\Shared\Application\Interfaces\TransactionManagerInterface;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;
use Mockery;
use PHPUnit\Framework\TestCase;
use Tests\Trait\MockPermission;

class StatusAwaitingOrderServiceTest extends TestCase
{
    use MockPermission;
    use SetsUpLaravelHelpers;

    private OrderRepositoryInterface $repository;
    private LeadRepositoryInterface $leadRepository;
    private ClientRepositoryInterface $clientRepository;
    private MailServiceInterface $mailService;
    private OrderLoggerRepositoryInterface $loggerRepository;
    private OrderRepositoryInterface $loggerOrderRepository;
    private TransactionManagerInterface $transactionManager;
    private StatusAwaitingOrderService $useCase;

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
        $this->clientRepository = Mockery::mock(ClientRepositoryInterface::class);
        $this->mailService = Mockery::mock(MailServiceInterface::class);
        $this->loggerRepository = Mockery::mock(OrderLoggerRepositoryInterface::class);
        $this->loggerOrderRepository = Mockery::mock(OrderRepositoryInterface::class);
        $this->transactionManager = Mockery::mock(TransactionManagerInterface::class);
        $this->transactionManager->shouldReceive('execute')->andReturnUsing(fn ($callback) => $callback());

        $sendMailUseCase = new SendMailNewOrderClientUseCase(
            $this->mailService,
            new ViewClientUseCase($this->clientRepository),
            $this->repository,
        );
        $statusOrderUseCase = new SetStatusOrderUseCase($this->repository);
        $leadFromOrderUseCase = new SetStatusLeadFromOrderUseCase($this->leadRepository);
        $loggerUseCase = new CreateOrderLoggerUseCase($this->loggerRepository, $this->loggerOrderRepository);

        $this->useCase = new StatusAwaitingOrderService(
            $this->transactionManager,
            $sendMailUseCase,
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

    private function makeInWorkOrder(): OrderEntity
    {
        $order = new OrderEntity(traderId: 1, type: new OrderSellType(OrderSellType::ONLINE));
        $order->id = 10;
        $order->addStatus(OrderStatus::inWork());
        $item = new OrderItemEntity(productId: 1, quantity: 1, baseCost: 100.0, sellCost: 100.0);
        $item->id = 1;
        $order->items = [$item];

        return $order;
    }

    public function test_throws_access_denied_when_missing_permission(): void
    {
        $this->transactionManager->shouldNotReceive('execute');

        $permission = $this->mockUserPermission(edit: false);
        $this->expectException(AccessDeniedException::class);
        $this->useCase->execute(10, ['a@example.com'], $permission);
    }

    public function test_sends_order_to_payment(): void
    {
        $order = $this->makeInWorkOrder();
        $lead = new LeadEntity(leadableId: 10, leadableType: 'order.order', data: []);

        // getById: проверка суммы/статуса + смена статуса + письмо
        $this->repository->shouldReceive('getById')->with(10)->times(3)->andReturn($order);
        $this->repository->shouldReceive('save')->with($order)->once()->andReturn($order);

        $this->leadRepository->shouldReceive('findByOrderId')->with(10)->once()->andReturn($lead);
        $this->leadRepository->shouldReceive('save')->with($lead)->once()->andReturn($lead);

        $loggerOrder = new OrderEntity(traderId: 1, type: new OrderSellType(OrderSellType::ONLINE));
        $loggerOrder->id = 10;
        $loggerOrder->addStatus(OrderStatus::new());
        $this->loggerOrderRepository->shouldReceive('getById')->with(10)->once()->andReturn($loggerOrder);
        $this->loggerRepository->shouldReceive('save')->once()->andReturnUsing(fn ($log) => $log);

        $this->mailService->shouldReceive('send')->once()->andReturnNull();

        $permission = $this->mockUserPermission(edit: true);
        $this->useCase->execute(10, ['a@example.com'], $permission);

        $this->assertSame(OrderStatus::AWAITING, $order->status->value->getValue());
    }
}
