<?php

namespace App\Modules\Auth\Tests\Unit\Application\Actions\Staff;

use App\Modules\Auth\Application\Actions\Staff\IndexStaffUseCase;
use App\Modules\Auth\Domain\Interfaces\StaffRepositoryInterface;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;
use Illuminate\Pagination\LengthAwarePaginator;
use Mockery;
use PHPUnit\Framework\TestCase;
use Tests\Trait\MockPermission;

class IndexStaffUseCaseTest extends TestCase
{
    use MockPermission;

    private StaffRepositoryInterface $staffRepo;
    private IndexStaffUseCase $useCase;

    public function getModuleName(): string
    {
        return 'auth';
    }

    public function getEntityName(): string
    {
        return 'employee';
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->staffRepo = Mockery::mock(StaffRepositoryInterface::class);
        $this->useCase = new IndexStaffUseCase($this->staffRepo);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_returns_paginator(): void
    {
        $paginator = Mockery::mock(LengthAwarePaginator::class);
        $this->staffRepo->shouldReceive('paginate')->with(15)->once()->andReturn($paginator);

        $permission = $this->mockUserPermission(view: true);
        $result = $this->useCase->execute($permission);

        $this->assertSame($paginator, $result);
    }

    public function test_throws_access_denied_when_missing_permission(): void
    {
        $permission = $this->mockUserPermission(view: false);
        $this->staffRepo->shouldNotReceive('paginate');

        $this->expectException(AccessDeniedException::class);
        $this->useCase->execute($permission);
    }
}
