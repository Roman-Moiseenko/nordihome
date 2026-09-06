<?php

namespace App\Modules\Auth\Tests\Unit\Application\Actions\Staff;

use App\Modules\Auth\Application\Actions\Staff\ListStaffByPositionUseCase;
use App\Modules\Auth\Domain\Entities\StaffEntity;
use App\Modules\Auth\Domain\Interfaces\StaffRepositoryInterface;
use App\Modules\Auth\Domain\ValueObjects\FullName;
use App\Modules\Auth\Domain\ValueObjects\StaffPosition;
use App\Modules\Auth\Domain\ValueObjects\StaffPositions;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;
use Mockery;
use PHPUnit\Framework\TestCase;
use Tests\Trait\MockPermission;

class ListStaffByPositionUseCaseTest extends TestCase
{
    use MockPermission;

    private StaffRepositoryInterface $staffRepo;
    private ListStaffByPositionUseCase $useCase;

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
        $this->useCase = new ListStaffByPositionUseCase($this->staffRepo);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_lists_by_single_position(): void
    {
        $position = new StaffPosition(StaffPosition::DRIVER);
        $staff = [new StaffEntity(new FullName('Иванов Иван'), new StaffPositions([StaffPosition::DRIVER]))];

        $this->staffRepo->shouldReceive('findByPosition')->with($position)->once()->andReturn($staff);

        $result = $this->useCase->execute($position);

        $this->assertSame($staff, $result);
    }

    public function test_lists_by_array_of_positions(): void
    {
        $positions = [new StaffPosition(StaffPosition::DRIVER), new StaffPosition(StaffPosition::LOADER)];
        $staff = [new StaffEntity(new FullName('Иванов Иван'), new StaffPositions([StaffPosition::DRIVER]))];

        $this->staffRepo->shouldReceive('findByPosition')
            ->with(Mockery::type(StaffPositions::class))
            ->once()
            ->andReturn($staff);

        $result = $this->useCase->execute($positions);

        $this->assertSame($staff, $result);
    }

}
