<?php

namespace App\Modules\Order\Tests\Unit\Application\Services\StatusServices;

use App\Modules\Auth\Domain\Entities\ClientEntity;
use App\Modules\Auth\Domain\Interfaces\ClientRepositoryInterface;
use App\Modules\Auth\Domain\ValueObjects\Email;
use App\Modules\Auth\Domain\ValueObjects\FullName;
use App\Modules\Lead\Application\Actions\SetStatusLeadFromOrderUseCase;
use App\Modules\Lead\Domain\Entities\LeadEntity;
use App\Modules\Lead\Domain\Interfaces\LeadRepositoryInterface;
use App\Modules\Order\Application\Actions\Order\SendMailCancelOrderClientUseCase;
use App\Modules\Order\Application\Actions\Order\SetStatusOrderUseCase;
use App\Modules\Order\Application\Actions\OrderLogger\CreateOrderLoggerUseCase;
use App\Modules\Order\Application\Services\StatusServices\StatusCancelOrderService;
use App\Modules\Order\Domain\Entities\OrderEntity;
use App\Modules\Order\Domain\Entities\OrderHistoryStatusEntity;
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

class StatusCancelOrderServiceTest extends TestCase
{
    use MockPermission;
    use SetsUpLaravelHelpers;

    private OrderRepositoryInterface $repository;
    private OrderRepositoryInterface $mailOrderRepository;
    private LeadRepositoryInterface $leadRepository;
    private ClientRepositoryInterface $clientRepository;
    private MailServiceInterface $mailService;
    private OrderLoggerRepositoryInterface $loggerRepository;
    private OrderRepositoryInterface $loggerOrderRepository;
    private TransactionManagerInterface $transactionManager;
    private StatusCancelOrderService $useCase;

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
        $this->mailOrderRepository = Mockery::mock(OrderRepositoryInterface::class);
        $this->leadRepository = Mockery::mock(LeadRepositoryInterface::class);
        $this->clientRepository = Mockery::mock(ClientRepositoryInterface::class);
        $this->mailService = Mockery::mock(MailServiceInterface::class);
        $this->loggerRepository = Mockery::mock(OrderLoggerRepositoryInterface::class);
        $this->loggerOrderRepository = Mockery::mock(OrderRepositoryInterface::class);
        $this->transactionManager = Mockery::mock(TransactionManagerInterface::class);
        $this->transactionManager->shouldReceive('execute')->andReturnUsing(fn ($callback) => $callback());

        $statusOrderUseCase = new SetStatusOrderUseCase($this->repository);
        $mailCancelUseCase = new SendMailCancelOrderClientUseCase(
            $this->mailService,
            $this->mailOrderRepository,
            $this->clientRepository,
        );
        $leadFromOrderUseCase = new SetStatusLeadFromOrderUseCase($this->leadRepository);
        $loggerUseCase = new CreateOrderLoggerUseCase($this->loggerRepository, $this->loggerOrderRepository);

        $this->useCase = new StatusCancelOrderService(
            $statusOrderUseCase,
            $mailCancelUseCase,
            $leadFromOrderUseCase,
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
        $this->useCase->execute(10, 'Причина', $permission);
    }

    public function test_cancels_order_and_sends_mail(): void
    {
        $order = new OrderEntity(traderId: 1, type: new OrderSellType(OrderSellType::ONLINE));
        $order->id = 10;
        $order->addStatus(OrderStatus::inWork());

        $lead = new LeadEntity(leadableId: 10, leadableType: 'order.order', data: []);

        $mailOrder = new OrderEntity(traderId: 1, type: new OrderSellType(OrderSellType::ONLINE));
        $mailOrder->id = 10;
        $mailOrder->clientId = 10;
        $mailOrder->status = new OrderHistoryStatusEntity(OrderStatus::cancelled(), 'Причина отмены');

        $client = new ClientEntity(new FullName('Иванов Иван'), new Email('client@example.com'));

        $this->repository->shouldReceive('getById')->with(10)->once()->andReturn($order);
        $this->repository->shouldReceive('save')->with($order)->once()->andReturn($order);

        $this->leadRepository->shouldReceive('findByOrderId')->with(10)->once()->andReturn($lead);
        $this->leadRepository->shouldReceive('save')->with($lead)->once()->andReturn($lead);

        $loggerOrder = new OrderEntity(traderId: 1, type: new OrderSellType(OrderSellType::ONLINE));
        $loggerOrder->id = 10;
        $loggerOrder->addStatus(OrderStatus::new());
        $this->loggerOrderRepository->shouldReceive('getById')->with(10)->once()->andReturn($loggerOrder);
        $this->loggerRepository->shouldReceive('save')->once()->andReturnUsing(fn ($log) => $log);

        $this->mailOrderRepository->shouldReceive('getById')->with(10)->once()->andReturn($mailOrder);
        $this->clientRepository->shouldReceive('findById')->with(10)->once()->andReturn($client);
        $this->mailService->shouldReceive('send')->once()->andReturnNull();

        $permission = $this->mockUserPermission(edit: true);
        $this->useCase->execute(10, 'Причина отмены', $permission);

        $this->assertSame(OrderStatus::CANCELLED, $order->status->value->getValue());
        $this->assertSame('Причина отмены', $order->status->comment);
    }
}
