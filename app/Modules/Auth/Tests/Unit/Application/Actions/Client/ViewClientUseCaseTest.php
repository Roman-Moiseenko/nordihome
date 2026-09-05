<?php

namespace App\Modules\Auth\Tests\Unit\Application\Actions\Client;

use App\Modules\Auth\Application\Actions\Client\ViewClientUseCase;
use App\Modules\Auth\Domain\Entities\ClientEntity;
use App\Modules\Auth\Domain\Exceptions\ClientNotFoundException;
use App\Modules\Auth\Domain\Interfaces\ClientRepositoryInterface;
use App\Modules\Auth\Domain\ValueObjects\Email;
use App\Modules\Auth\Domain\ValueObjects\FullName;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;
use Mockery;
use PHPUnit\Framework\TestCase;
use Tests\Trait\MockPermission;

class ViewClientUseCaseTest extends TestCase
{
    use MockPermission;

    private ClientRepositoryInterface $clientRepo;
    private ViewClientUseCase $useCase;

    public function getModuleName(): string
    {
        return 'auth';
    }

    public function getEntityName(): string
    {
        return 'buyer';
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->clientRepo = Mockery::mock(ClientRepositoryInterface::class);
        $this->useCase = new ViewClientUseCase($this->clientRepo);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_returns_client(): void
    {
        $client = new ClientEntity(new FullName('Иванов Иван'), new Email('i@example.com'));
        $client->id = 5;

        $this->clientRepo->shouldReceive('findById')->with(5)->once()->andReturn($client);

        $permission = $this->mockUserPermission(view: true);
        $result = $this->useCase->execute(5, $permission);

        $this->assertSame($client, $result);
    }

    public function test_throws_when_not_found(): void
    {
        $this->clientRepo->shouldReceive('findById')->with(99)->once()->andReturn(null);

        $permission = $this->mockUserPermission(view: true);
        $this->expectException(ClientNotFoundException::class);
        $this->useCase->execute(99, $permission);
    }

    public function test_throws_access_denied_when_missing_permission(): void
    {
        $permission = $this->mockUserPermission(view: false);
        $this->clientRepo->shouldNotReceive('findById');

        $this->expectException(AccessDeniedException::class);
        $this->useCase->execute(5, $permission);
    }
}
