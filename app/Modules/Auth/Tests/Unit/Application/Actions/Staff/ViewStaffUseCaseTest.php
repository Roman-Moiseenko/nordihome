<?php

namespace App\Modules\Auth\Tests\Unit\Application\Actions\Staff;

use App\Modules\Auth\Application\Actions\Staff\ViewStaffUseCase;
use App\Modules\Auth\Application\Interfaces\StaffRepositoryInterface;
use App\Modules\Auth\Domain\Entities\StaffEntity;
use App\Modules\Auth\Domain\Exceptions\StaffNotFoundException;
use App\Modules\Auth\Domain\ValueObjects\FullName;
use App\Modules\Auth\Domain\ValueObjects\StaffPosition;
use App\Modules\Auth\Domain\ValueObjects\StaffPositions;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;
use Mockery;
use PHPUnit\Framework\TestCase;
use Tests\Trait\MockPermission;

class ViewStaffUseCaseTest extends TestCase
{
    use MockPermission;

    private StaffRepositoryInterface $staffRepo;
    private ViewStaffUseCase $useCase;

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
        $this->useCase = new ViewStaffUseCase($this->staffRepo);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_returns_staff(): void
    {
        $staff = new StaffEntity(new FullName('Иванов Иван'), new StaffPositions([StaffPosition::DRIVER]));
        $staff->id = 5;

        $this->staffRepo->shouldReceive('findById')->with(5)->once()->andReturn($staff);

        $permission = $this->mockUserPermission(view: true);
        $this->assertSame($staff, $this->useCase->execute(5, $permission));
    }

    public function test_throws_when_not_found(): void
    {
        $this->staffRepo->shouldReceive('findById')->with(99)->once()->andReturn(null);

        $permission = $this->mockUserPermission(view: true);
        $this->expectException(StaffNotFoundException::class);
        $this->useCase->execute(99, $permission);
    }

    public function test_throws_access_denied_when_missing_permission(): void
    {
        $permission = $this->mockUserPermission(view: false);
        $this->staffRepo->shouldNotReceive('findById');

        $this->expectException(AccessDeniedException::class);
        $this->useCase->execute(5, $permission);
    }
}
