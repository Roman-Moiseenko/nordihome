<?php

namespace App\Modules\Auth\Tests\Unit\Application\Actions\Staff;

use App\Modules\Auth\Application\Actions\Staff\ListStaffGroupPositions;
use App\Modules\Auth\Application\Interfaces\StaffRepositoryInterface;
use App\Modules\Auth\Domain\Entities\StaffEntity;
use App\Modules\Auth\Domain\ValueObjects\FullName;
use App\Modules\Auth\Domain\ValueObjects\StaffPosition;
use App\Modules\Auth\Domain\ValueObjects\StaffPositions;
use Mockery;
use PHPUnit\Framework\TestCase;

class ListStaffGroupPositionsTest extends TestCase
{
    private StaffRepositoryInterface $staffRepo;
    private ListStaffGroupPositions $useCase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->staffRepo = Mockery::mock(StaffRepositoryInterface::class);
        $this->useCase = new ListStaffGroupPositions($this->staffRepo);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_groups_staff_by_position(): void
    {
        $manager = new StaffEntity(
            new FullName('Иванов Иван Иванович'),
            new StaffPositions([StaffPosition::CUSTOMER_MANAGER]),
        );
        $manager->id = 1;

        $worker = new StaffEntity(
            new FullName('Петров Пётр Петрович'),
            new StaffPositions([StaffPosition::DRIVER]),
        );
        $worker->id = 2;

        $this->staffRepo->shouldReceive('findAll')->once()->andReturn([$manager, $worker]);

        $groups = $this->useCase->execute();

        $this->assertCount(1, $groups[StaffPosition::CUSTOMER_MANAGER]);
        $this->assertCount(1, $groups[StaffPosition::DRIVER]);
        $this->assertCount(1, $groups['managers']);
        $this->assertCount(1, $groups['workers']);
        $this->assertSame('Иванов И.И.', $groups['managers'][0]->fullName);
        $this->assertSame('Петров П.П.', $groups['workers'][0]->fullName);
    }
}
