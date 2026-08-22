<?php

namespace App\Modules\Auth\Tests\Unit\Application\Actions\Client;

use App\Modules\Auth\Application\Actions\Client\FindClientByContactUseCase;
use App\Modules\Auth\Application\DTOs\Client\FindClientByContactData;
use App\Modules\Auth\Application\Interfaces\ClientRepositoryInterface;
use App\Modules\Auth\Domain\Entities\ClientEntity;
use App\Modules\Auth\Domain\ValueObjects\Email;
use App\Modules\Auth\Domain\ValueObjects\FullName;
use App\Modules\Auth\Domain\ValueObjects\PhoneNumber;
use Mockery;
use PHPUnit\Framework\TestCase;

class FindClientByContactUseCaseTest extends TestCase
{
    private ClientRepositoryInterface $clientRepo;
    private FindClientByContactUseCase $useCase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->clientRepo = Mockery::mock(ClientRepositoryInterface::class);
        $this->useCase = new FindClientByContactUseCase($this->clientRepo);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_finds_by_phone_first(): void
    {
        $client = new ClientEntity(new FullName('Иванов Иван'), new Email('i@example.com'));
        $this->clientRepo->shouldReceive('findByPhone')
            ->with(Mockery::on(fn(PhoneNumber $p) => $p->getValue() === '+79991234567'))
            ->once()
            ->andReturn($client);

        $result = $this->useCase->execute(new FindClientByContactData(phone: '+79991234567', email: 'i@example.com'));

        $this->assertSame($client, $result);
    }

    public function test_falls_back_to_email(): void
    {
        $client = new ClientEntity(new FullName('Иванов Иван'), new Email('i@example.com'));
        $this->clientRepo->shouldReceive('findByPhone')
            ->with(Mockery::type(PhoneNumber::class))
            ->once()
            ->andReturn(null);
        $this->clientRepo->shouldReceive('findByEmail')
            ->with(Mockery::on(fn(Email $e) => $e->value === 'i@example.com'))
            ->once()
            ->andReturn($client);

        $result = $this->useCase->execute(new FindClientByContactData(phone: '+79991234567', email: 'i@example.com'));

        $this->assertSame($client, $result);
    }

    public function test_returns_null_when_not_found(): void
    {
        $this->clientRepo->shouldReceive('findByPhone')->once()->andReturn(null);
        $this->clientRepo->shouldReceive('findByEmail')->once()->andReturn(null);

        $result = $this->useCase->execute(new FindClientByContactData(phone: '+79991234567', email: 'i@example.com'));

        $this->assertNull($result);
    }
}
