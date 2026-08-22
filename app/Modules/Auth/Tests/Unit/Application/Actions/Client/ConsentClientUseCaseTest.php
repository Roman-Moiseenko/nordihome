<?php

namespace App\Modules\Auth\Tests\Unit\Application\Actions\Client;

use App\Modules\Auth\Application\Actions\Client\ConsentClientUseCase;
use App\Modules\Auth\Application\Interfaces\ClientRepositoryInterface;
use App\Modules\Auth\Domain\Entities\ClientEntity;
use App\Modules\Auth\Domain\ValueObjects\Email;
use App\Modules\Auth\Domain\ValueObjects\FullName;
use InvalidArgumentException;
use Mockery;
use PHPUnit\Framework\TestCase;

class ConsentClientUseCaseTest extends TestCase
{
    private ClientRepositoryInterface $clientRepo;
    private ConsentClientUseCase $useCase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->clientRepo = Mockery::mock(ClientRepositoryInterface::class);
        $this->useCase = new ConsentClientUseCase($this->clientRepo);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_sets_consent_and_saves_client(): void
    {
        $client = new ClientEntity(
            new FullName('Иванов Иван'),
            new Email('ivan@example.com'),
        );
        $client->id = 7;

        $this->clientRepo->shouldReceive('findById')->with(7)->once()->andReturn($client);
        $this->clientRepo->shouldReceive('save')->once()->with($client)->andReturn($client);

        $this->useCase->execute(7);

        $this->assertNotNull($client->dataConsent);
        $this->assertTrue($client->dataConsent->active);
        $this->assertSame('v1 от 01.01.2026', $client->dataConsent->policyVersion);
    }

    public function test_throws_when_client_id_is_null(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Нет id Client');
        $this->useCase->execute(null);
    }
}
