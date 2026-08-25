<?php

namespace App\Modules\Order\Tests\Unit\Application\Actions\Order;

use App\Modules\Auth\Application\Actions\Client\ViewClientUseCase;
use App\Modules\Auth\Application\Interfaces\ClientRepositoryInterface;
use App\Modules\Auth\Domain\Entities\ClientEntity;
use App\Modules\Auth\Domain\ValueObjects\Email;
use App\Modules\Auth\Domain\ValueObjects\FullName;
use App\Modules\Mail\Entity\MailTemplate;
use App\Modules\Order\Application\Actions\Order\SendMailNewOrderClientUseCase;
use App\Modules\Order\Application\Interfaces\OrderRepositoryInterface;
use App\Modules\Order\Domain\Entities\OrderEntity;
use App\Modules\Order\Domain\ValueObjects\OrderSellType;
use App\Modules\Shared\Application\Interfaces\Mail\MailServiceInterface;
use App\Modules\Shared\Domain\Entities\Mail\Recipient;
use Mockery;
use PHPUnit\Framework\TestCase;

class SendMailNewOrderClientUseCaseTest extends TestCase
{
    private MailServiceInterface $mailService;
    private ClientRepositoryInterface $clientRepository;
    private OrderRepositoryInterface $repository;
    private SendMailNewOrderClientUseCase $useCase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mailService = Mockery::mock(MailServiceInterface::class);
        $this->clientRepository = Mockery::mock(ClientRepositoryInterface::class);
        $this->repository = Mockery::mock(OrderRepositoryInterface::class);

        // ViewClientUseCase — readonly-класс, Mockery его не умеет мокать,
        // поэтому подставляем реальный экземпляр с моком репозитория.
        $viewClientUseCase = new ViewClientUseCase($this->clientRepository);

        $this->useCase = new SendMailNewOrderClientUseCase(
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

        return $order;
    }

    private function makeClient(): ClientEntity
    {
        return new ClientEntity(
            new FullName('Иванов Иван Иванович'),
            new Email('client@example.com'),
        );
    }

    public function test_sends_to_each_provided_email(): void
    {
        $order = $this->makeOrder();

        $this->repository->shouldReceive('getById')->with(10)->once()->andReturn($order);
        $this->clientRepository->shouldNotReceive('findById');

        $sent = [];
        $this->mailService->shouldReceive('send')
            ->twice()
            ->with(
                Mockery::on(fn (MailTemplate $template) => $template->code === 'order.new'),
                ['order' => $order],
                Mockery::on(fn (Recipient $recipient) => $recipient->clientId === 10),
            )
            ->andReturnUsing(function (MailTemplate $template, array $data, Recipient $recipient) use (&$sent): void {
                $sent[] = $recipient->email;
            });

        $this->useCase->execute(10, ['a@example.com', 'b@example.com']);

        $this->assertSame(['a@example.com', 'b@example.com'], $sent);
    }

    public function test_uses_client_email_when_emails_is_null(): void
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

        $this->useCase->execute(10, null);

        $this->assertSame('order.new', $sent['code']);
        $this->assertSame($order, $sent['data']['order']);
        $this->assertSame('client@example.com', $sent['email']);
        $this->assertSame(10, $sent['clientId']);
    }

    public function test_uses_client_email_when_emails_is_empty_array(): void
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

        $this->useCase->execute(10, []);

        $this->assertSame('order.new', $sent['code']);
        $this->assertSame($order, $sent['data']['order']);
        $this->assertSame('client@example.com', $sent['email']);
        $this->assertSame(10, $sent['clientId']);
    }
}
