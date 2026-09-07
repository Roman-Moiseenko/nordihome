<?php

namespace App\Modules\Order\Tests\Unit\Application\Actions\OrderLogger;

use App\Modules\Order\Application\Actions\OrderLogger\CreateOrderLoggerUseCase;
use App\Modules\Order\Application\DTOs\OrderLogger\OrderLoggerCreateData;
use App\Modules\Order\Domain\Entities\OrderEntity;
use App\Modules\Order\Domain\Interfaces\OrderLoggerRepositoryInterface;
use App\Modules\Order\Domain\Interfaces\OrderRepositoryInterface;
use App\Modules\Order\Domain\ValueObjects\OrderSellType;
use App\Modules\Order\Domain\ValueObjects\OrderStatus;
use App\Modules\Order\Tests\Support\SetsUpLaravelHelpers;
use DomainException;
use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Mockery;
use PHPUnit\Framework\TestCase;
use stdClass;

class CreateOrderLoggerUseCaseTest extends TestCase
{
    use SetsUpLaravelHelpers;

    private OrderLoggerRepositoryInterface $loggerRepository;
    private OrderRepositoryInterface $orderRepository;
    private CreateOrderLoggerUseCase $useCase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpLaravelHelpers();

        $this->loggerRepository = Mockery::mock(OrderLoggerRepositoryInterface::class);
        $this->orderRepository = Mockery::mock(OrderRepositoryInterface::class);
        $this->useCase = new CreateOrderLoggerUseCase($this->loggerRepository, $this->orderRepository);
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

    public function test_saves_log_for_guest(): void
    {
        $order = $this->makeOrder();
        $this->orderRepository->shouldReceive('getById')->with(10)->once()->andReturn($order);

        $this->loggerRepository->shouldReceive('save')
            ->once()
            ->andReturnUsing(function ($log) {
                $this->assertSame(10, $log->orderId);
                $this->assertNull($log->staffId);
                $this->assertSame('Действие', $log->action);
                $this->assertSame('объект', $log->object);
                $this->assertSame('старое', $log->old);
                $this->assertSame('новое', $log->value);
                $this->assertSame('http://link', $log->link);
                $this->assertInstanceOf(\DateTimeImmutable::class, $log->createdAt);

                return $log;
            });

        $dto = new OrderLoggerCreateData(action: 'Действие', object: 'объект', old: 'старое', value: 'новое', link: 'http://link');
        $this->useCase->execute(10, $dto);
    }

    public function test_sets_staff_id_when_authenticated(): void
    {
        $user = new stdClass();
        $user->profileable_id = 5;

        $auth = Mockery::mock(AuthFactory::class);
        $auth->shouldReceive('check')->andReturn(true);
        $auth->shouldReceive('user')->andReturn($user);
        $this->container->instance(AuthFactory::class, $auth);

        $order = $this->makeOrder();
        $this->orderRepository->shouldReceive('getById')->with(10)->once()->andReturn($order);

        $this->loggerRepository->shouldReceive('save')
            ->once()
            ->andReturnUsing(function ($log) {
                $this->assertSame(5, $log->staffId);

                return $log;
            });

        $this->useCase->execute(10, new OrderLoggerCreateData(action: 'Действие'));
    }

    public function test_throws_when_order_finished(): void
    {
        $order = $this->makeOrder();
        $order->addStatus(OrderStatus::completed());

        $this->orderRepository->shouldReceive('getById')->with(10)->once()->andReturn($order);
        $this->loggerRepository->shouldNotReceive('save');

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Заказ завершен. Доступ закрыт');
        $this->useCase->execute(10, new OrderLoggerCreateData(action: 'Действие'));
    }
}
