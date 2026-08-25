<?php

namespace App\Modules\Order\Tests\Unit\Application\Actions\Order;

use App\Modules\Auth\Application\Actions\Client\ViewClientUseCase;
use App\Modules\Auth\Application\Interfaces\ClientRepositoryInterface;
use App\Modules\Auth\Domain\Entities\ClientEntity;
use App\Modules\Auth\Domain\ValueObjects\Email;
use App\Modules\Auth\Domain\ValueObjects\FullName;
use App\Modules\Mail\Entity\MailTemplate;
use App\Modules\Order\Application\Actions\Order\SendMailCancelOrderClientUseCase;
use App\Modules\Order\Application\Interfaces\OrderRepositoryInterface;
use App\Modules\Order\Domain\Entities\OrderEntity;
use App\Modules\Order\Domain\Entities\OrderHistoryStatusEntity;
use App\Modules\Order\Domain\ValueObjects\OrderSellType;
use App\Modules\Order\Domain\ValueObjects\OrderStatus;
use App\Modules\Shared\Application\Interfaces\Mail\MailServiceInterface;
use App\Modules\Shared\Domain\Entities\Mail\Recipient;
use Mockery;
use PHPUnit\Framework\TestCase;

class SendMailCancelOrderClientUseCaseTest extends TestCase
{
    private MailServiceInterface $mailService;
    private ClientRepositoryInterface $clientRepository;
    private OrderRepositoryInterface $repository;
    private SendMailCancelOrderClientUseCase $useCase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mailService = Mockery::mock(MailServiceInterface::class);
        $this->clientRepository = Mockery::mock(ClientRepositoryInterface::class);
        $this->repository = Mockery::mock(OrderRepositoryInterface::class);

        // ViewClientUseCase — readonly-класс, Mockery его не умеет мокать,
        // поэтому подставляем реальный экземпляр с моком репозитория.
        $viewClientUseCase = new ViewClientUseCase($this->clientRepository);

        $this->useCase = new SendMailCancelOrderClientUseCase(
            $this->mailService,
            $viewClientUseCase,
            $this->repository,
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
        $order->status = new OrderHistoryStatusEntity(OrderStatus::cancelled(), 'Причина отмены');

        return $order;
    }

    private function makeClient(): ClientEntity
    {
        return new ClientEntity(
            new FullName('Иванов Иван Иванович'),
            new Email('client@example.com'),
        );
    }

    public function test_sends_cancelled_mail_to_client(): void
    {
        $order = $this->makeOrder();
        $client = $this->makeClient();

        $this->repository->shouldReceive('getById')->with(10)->once()->andReturn($order);
        $this->clientRepository->shouldReceive('findById')->with(10)->once()->andReturn($client);

        $sent = [];
        $this->mailService->shouldReceive('send')
            ->once()
            ->andReturnUsing(function (MailTemplate $template, array $data, Recipient $recipient) use (&$sent): void {
                $sent = [
                    'code' => $template->code,
                    'data' => $data,
                    'email' => $recipient->email,
                    'clientId' => $recipient->clientId,
                ];
            });

        $this->useCase->execute(10);

        $this->assertSame('order.cancelled', $sent['code']);
        $this->assertSame($order, $sent['data']['order']);
        $this->assertSame('Причина отмены', $sent['data']['comment']);
        $this->assertSame('client@example.com', $sent['email']);
        $this->assertSame(10, $sent['clientId']);
    }
}
