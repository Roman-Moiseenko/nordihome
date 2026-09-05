<?php

namespace App\Modules\Auth\Tests\Unit\Application\Actions\Staff;

use App\Modules\Auth\Application\Actions\Staff\RemoveStaffUseCase;
use App\Modules\Auth\Domain\Entities\StaffEntity;
use App\Modules\Auth\Domain\Interfaces\StaffRepositoryInterface;
use App\Modules\Auth\Domain\ValueObjects\FullName;
use App\Modules\Auth\Domain\ValueObjects\StaffPosition;
use App\Modules\Auth\Domain\ValueObjects\StaffPositions;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;
use Mockery;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\Trait\MockPermission;

class RemoveStaffUseCaseTest extends TestCase
{
    use MockPermission;

    private StaffRepositoryInterface $staffRepo;
    private RemoveStaffUseCase $useCase;

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
        $this->useCase = new RemoveStaffUseCase($this->staffRepo);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_deletes_staff(): void
    {
        $staff = new StaffEntity(new FullName('Иванов Иван'), new StaffPositions([StaffPosition::DRIVER]));
        $staff->id = 1;

        $this->staffRepo->shouldReceive('findById')->with(1)->once()->andReturn($staff);
        $this->staffRepo->shouldReceive('delete')->with(1)->once()->andReturn(true);

        $permission = $this->mockUserPermission(delete: true);
        $this->assertTrue($this->useCase->execute(1, $permission));
    }

    public function test_throws_when_not_found(): void
    {
        $this->staffRepo->shouldReceive('findById')->with(99)->once()->andReturn(null);
        $this->staffRepo->shouldNotReceive('delete');

        $permission = $this->mockUserPermission(delete: true);
        $this->expectException(NotFoundHttpException::class);
        $this->useCase->execute(99, $permission);
    }

    public function test_throws_access_denied_when_missing_permission(): void
    {
        $permission = $this->mockUserPermission(delete: false);
        $this->staffRepo->shouldNotReceive('findById');

        $this->expectException(AccessDeniedException::class);
        $this->useCase->execute(1, $permission);
    }
}
