<?php

namespace App\Modules\Lead\Tests\Unit\Application\Actions;

use App\Modules\Auth\Application\Actions\Client\FindClientByContactUseCase;
use App\Modules\Auth\Domain\Entities\ClientEntity;
use App\Modules\Auth\Domain\Interfaces\ClientRepositoryInterface;
use App\Modules\Auth\Domain\ValueObjects\Email;
use App\Modules\Auth\Domain\ValueObjects\FullName;
use App\Modules\Auth\Domain\ValueObjects\PhoneNumber;
use App\Modules\Lead\Application\Actions\CreateLeadFromFormBackUseCase;
use App\Modules\Lead\Domain\Entities\LeadEntity;
use App\Modules\Lead\Domain\Interfaces\LeadRepositoryInterface;
use App\Modules\Lead\Domain\ValueObjects\LeadStatusValue;
use App\Modules\Order\Domain\Entities\OrderEntity;
use App\Modules\Order\Domain\Interfaces\OrderRepositoryInterface;
use App\Modules\Order\Domain\ValueObjects\OrderSellType;
use App\Modules\Shared\Application\DTOs\Lead\LeadSourceData;
use Mockery;
use PHPUnit\Framework\TestCase;

class CreateLeadFromFormBackUseCaseTest extends TestCase
{
    private LeadRepositoryInterface $leadRepository;
    private ClientRepositoryInterface $clientRepository;
    private OrderRepositoryInterface $orderRepository;
    private CreateLeadFromFormBackUseCase $useCase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->leadRepository = Mockery::mock(LeadRepositoryInterface::class);
        $this->clientRepository = Mockery::mock(ClientRepositoryInterface::class);
        $this->orderRepository = Mockery::mock(OrderRepositoryInterface::class);

        // FindClientByContactUseCase — readonly-класс, Mockery его не умеет мокать,
        // поэтому подставляем реальный экземпляр с моком репозитория.
        $findClientByContactUseCase = new FindClientByContactUseCase($this->clientRepository);

        $this->useCase = new CreateLeadFromFormBackUseCase(
            $this->leadRepository,
            $findClientByContactUseCase,
            $this->orderRepository,
        );
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
        $order->staffId = 7;

        return $order;
    }

    private function makeClient(): ClientEntity
    {
        $client = new ClientEntity(
            new FullName('Иванов Иван Иванович'),
            new Email('ivan@example.com'),
        );
        $client->id = 5;

        return $client;
    }

    public function test_creates_lead_without_order_and_contact(): void
    {
        $dto = new LeadSourceData(id: 1, able: 'order', data: ['name' => 'Иван Иванов'], orderId: null);

        $this->orderRepository->shouldNotReceive('getById');
        $this->clientRepository->shouldNotReceive('findByPhone');
        $this->clientRepository->shouldNotReceive('findByEmail');
        $this->leadRepository->shouldReceive('save')
            ->once()
            ->with(Mockery::type(LeadEntity::class))
            ->andReturnUsing(fn (LeadEntity $lead) => $lead);

        $result = $this->useCase->execute($dto);

        $this->assertSame(1, $result->leadableId);
        $this->assertSame('order', $result->leadableType);
        $this->assertSame('Иван Иванов', $result->name);
        $this->assertNull($result->orderId);
        $this->assertNull($result->clientId);
        $this->assertCount(1, $result->data);
        $this->assertCount(1, $result->statuses);
        $this->assertSame(LeadStatusValue::NEW_LEAD, $result->status->value->getValue());
    }

    public function test_sets_staff_from_order_when_order_id_provided(): void
    {
        $order = $this->makeOrder();
        $dto = new LeadSourceData(id: 1, able: 'order', data: [], orderId: 10);

        $this->orderRepository->shouldReceive('getById')->with(10)->once()->andReturn($order);
        $this->clientRepository->shouldNotReceive('findByPhone');
        $this->clientRepository->shouldNotReceive('findByEmail');
        $this->leadRepository->shouldReceive('save')
            ->once()
            ->with(Mockery::type(LeadEntity::class))
            ->andReturnUsing(fn (LeadEntity $lead) => $lead);

        $result = $this->useCase->execute($dto);

        $this->assertSame(10, $result->orderId);
        $this->assertSame(7, $result->staffId);
        $this->assertSame('Заказ с сайта', $result->name);
    }

    public function test_links_client_by_phone(): void
    {
        $client = $this->makeClient();
        $dto = new LeadSourceData(id: 1, able: 'order', data: ['phone' => '+79123456789'], orderId: null);

        $this->orderRepository->shouldNotReceive('getById');
        $this->clientRepository->shouldReceive('findByPhone')
            ->once()
            ->with(Mockery::type(PhoneNumber::class))
            ->andReturn($client);
        $this->clientRepository->shouldNotReceive('findByEmail');
        $this->leadRepository->shouldReceive('save')
            ->once()
            ->with(Mockery::type(LeadEntity::class))
            ->andReturnUsing(fn (LeadEntity $lead) => $lead);

        $result = $this->useCase->execute($dto);

        $this->assertSame(5, $result->clientId);
    }

    public function test_links_client_by_email(): void
    {
        $client = $this->makeClient();
        $dto = new LeadSourceData(id: 1, able: 'order', data: ['email' => 'ivan@example.com'], orderId: null);

        $this->orderRepository->shouldNotReceive('getById');
        $this->clientRepository->shouldNotReceive('findByPhone');
        $this->clientRepository->shouldReceive('findByEmail')
            ->once()
            ->with(Mockery::type(Email::class))
            ->andReturn($client);
        $this->leadRepository->shouldReceive('save')
            ->once()
            ->with(Mockery::type(LeadEntity::class))
            ->andReturnUsing(fn (LeadEntity $lead) => $lead);

        $result = $this->useCase->execute($dto);

        $this->assertSame(5, $result->clientId);
    }
}
