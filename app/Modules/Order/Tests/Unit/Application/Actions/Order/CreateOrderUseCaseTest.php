<?php

namespace App\Modules\Order\Tests\Unit\Application\Actions\Order;

use App\Modules\Accounting\Application\Actions\Trader\GetDefaultTraderIdUseCase;
use App\Modules\Auth\Application\Actions\Client\ViewClientUseCase;
use App\Modules\Auth\Application\Interfaces\ClientRepositoryInterface;
use App\Modules\Auth\Domain\Entities\ClientEntity;
use App\Modules\Auth\Domain\ValueObjects\Address;
use App\Modules\Auth\Domain\ValueObjects\Email;
use App\Modules\Auth\Domain\ValueObjects\FullName;
use App\Modules\Catalog\Domain\ValueObjects\PriceType;
use App\Modules\Order\Application\Actions\Order\CreateOrderUseCase;
use App\Modules\Order\Application\Interfaces\OrderLoggerServiceInterface;
use App\Modules\Order\Application\Interfaces\OrderRepositoryInterface;
use App\Modules\Order\Domain\Entities\OrderEntity;
use App\Modules\Order\Domain\ValueObjects\OrderSellType;
use App\Modules\Order\Domain\ValueObjects\OrderStatus;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;
use Mockery;
use PHPUnit\Framework\TestCase;
use Tests\Trait\MockPermission;

class CreateOrderUseCaseTest extends TestCase
{
    use MockPermission;

    private OrderRepositoryInterface $repository;
    private ClientRepositoryInterface $clientRepository;
    private OrderLoggerServiceInterface $logger;
    private GetDefaultTraderIdUseCase $traderIdUseCase;
    private CreateOrderUseCase $useCase;

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
        $this->clientRepository = Mockery::mock(ClientRepositoryInterface::class);
        $this->logger = Mockery::mock(OrderLoggerServiceInterface::class);

        // GetDefaultTraderIdUseCase — обычный класс, мокается напрямую.
        $this->traderIdUseCase = Mockery::mock(GetDefaultTraderIdUseCase::class);

        // ViewClientUseCase — readonly-класс, Mockery его не умеет мокать,
        // поэтому подставляем реальный экземпляр с моком репозитория.
        $viewClientUseCase = new ViewClientUseCase($this->clientRepository);

        $this->useCase = new CreateOrderUseCase(
            $this->repository,
            $viewClientUseCase,
            $this->traderIdUseCase,
            $this->logger,
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Права, при которых проходит и проверка UseCase (order.order.create),
     * и вложенная проверка ViewClientUseCase (auth.buyer.view).
     */
    private function permissionWithFullAccess(): UserPermission
    {
        $permission = Mockery::mock(UserPermission::class);
        $permission->shouldReceive('can')->andReturn(true);

        return $permission;
    }

    private function makeClient(): ClientEntity
    {
        $client = new ClientEntity(
            new FullName('Иванов Иван Иванович'),
            new Email('ivan@example.com'),
        );
        $client->id = 5;
        $client->priceType = new PriceType(PriceType::BULK);
        $client->address = new Address(country: 'Россия', city: 'Москва', street: 'Тверская');
        $client->isPickup = true;

        return $client;
    }

    public function test_creates_order_with_client(): void
    {
        $client = $this->makeClient();

        $this->traderIdUseCase->shouldReceive('execute')->once()->andReturn(3);
        $this->clientRepository->shouldReceive('findById')->with(5)->once()->andReturn($client);
        $this->repository->shouldReceive('save')
            ->once()
            ->with(Mockery::type(OrderEntity::class))
            ->andReturnUsing(function (OrderEntity $order) {
                $order->id = 100;

                return $order;
            });
        $this->logger->shouldReceive('log')
            ->once()
            ->with(100, 'Заказ создан менеджером');

        $result = $this->useCase->execute(clientId: 5, staffId: 7, permission: $this->permissionWithFullAccess());

        $this->assertSame(100, $result->id);
        $this->assertSame(3, $result->traderId);
        $this->assertSame(OrderSellType::MANUAL, $result->type->getValue());
        $this->assertSame(5, $result->clientId);
        $this->assertSame(7, $result->staffId);
        $this->assertSame($client->priceType, $result->priceType);
        $this->assertSame($client->address, $result->address);
        $this->assertTrue($result->isPickup);
        $this->assertSame(OrderStatus::DRAFT, $result->status);
        $this->assertCount(2, $result->statuses);
    }

    public function test_creates_order_without_client(): void
    {
        $this->traderIdUseCase->shouldReceive('execute')->once()->andReturn(3);
        $this->clientRepository->shouldNotReceive('findById');
        $this->repository->shouldReceive('save')
            ->once()
            ->with(Mockery::type(OrderEntity::class))
            ->andReturnUsing(function (OrderEntity $order) {
                $order->id = 100;

                return $order;
            });
        $this->logger->shouldReceive('log')
            ->once()
            ->with(100, 'Заказ создан менеджером');

        $permission = $this->mockUserPermission(create: true);
        $result = $this->useCase->execute(clientId: null, staffId: 7, permission: $permission);

        $this->assertSame(100, $result->id);
        $this->assertSame(3, $result->traderId);
        $this->assertSame(OrderSellType::MANUAL, $result->type->getValue());
        $this->assertNull($result->clientId);
        $this->assertSame(7, $result->staffId);
        $this->assertNull($result->priceType);
        $this->assertNull($result->address);
        $this->assertFalse($result->isPickup);
        $this->assertSame(OrderStatus::DRAFT, $result->status);
        $this->assertCount(2, $result->statuses);
    }

    public function test_throws_access_denied_when_missing_permission(): void
    {
        $this->traderIdUseCase->shouldNotReceive('execute');
        $this->clientRepository->shouldNotReceive('findById');
        $this->repository->shouldNotReceive('save');
        $this->logger->shouldNotReceive('log');

        $permission = $this->mockUserPermission(create: false);
        $this->expectException(AccessDeniedException::class);
        $this->useCase->execute(clientId: 5, staffId: 7, permission: $permission);
    }
}
