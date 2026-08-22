<?php

namespace App\Modules\Auth\Tests\Unit\Application\Actions\Client;

use App\Modules\Auth\Application\Actions\Client\IndexClientUseCase;
use App\Modules\Auth\Application\Interfaces\ClientRepositoryInterface;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;
use Illuminate\Pagination\LengthAwarePaginator;
use Mockery;
use PHPUnit\Framework\TestCase;
use Tests\Trait\MockPermission;

class IndexClientUseCaseTest extends TestCase
{
    use MockPermission;

    private ClientRepositoryInterface $clientRepo;
    private IndexClientUseCase $useCase;

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
        $this->useCase = new IndexClientUseCase($this->clientRepo);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_returns_paginator(): void
    {
        $paginator = Mockery::mock(LengthAwarePaginator::class);
        $this->clientRepo->shouldReceive('paginate')->with(15)->once()->andReturn($paginator);

        $permission = $this->mockUserPermission(view: true);
        $result = $this->useCase->execute($permission);

        $this->assertSame($paginator, $result);
    }

    public function test_throws_access_denied_when_missing_permission(): void
    {
        $permission = $this->mockUserPermission(view: false);
        $this->clientRepo->shouldNotReceive('paginate');

        $this->expectException(AccessDeniedException::class);
        $this->useCase->execute($permission);
    }
}
