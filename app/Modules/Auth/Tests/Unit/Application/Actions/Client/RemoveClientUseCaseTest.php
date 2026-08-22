<?php

namespace App\Modules\Auth\Tests\Unit\Application\Actions\Client;

use App\Modules\Auth\Application\Actions\Client\RemoveClientUseCase;
use App\Modules\Auth\Application\Interfaces\ClientRepositoryInterface;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;
use Mockery;
use PHPUnit\Framework\TestCase;
use Tests\Trait\MockPermission;

class RemoveClientUseCaseTest extends TestCase
{
    use MockPermission;

    private ClientRepositoryInterface $clientRepo;
    private RemoveClientUseCase $useCase;

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
        $this->useCase = new RemoveClientUseCase($this->clientRepo);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_deletes_client(): void
    {
        $this->clientRepo->shouldReceive('delete')->with(7)->once()->andReturn(true);

        $permission = $this->mockUserPermission(delete: true);
        $result = $this->useCase->execute(7, $permission);

        $this->assertTrue($result);
    }

    public function test_throws_access_denied_when_missing_permission(): void
    {
        $permission = $this->mockUserPermission(delete: false);
        $this->clientRepo->shouldNotReceive('delete');

        $this->expectException(AccessDeniedException::class);
        $this->useCase->execute(7, $permission);
    }
}
