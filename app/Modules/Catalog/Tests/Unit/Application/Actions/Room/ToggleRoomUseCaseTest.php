<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Tests\Unit\Application\Actions\Room;

use App\Modules\Catalog\Application\Actions\Room\ToggleRoomUseCase;
use App\Modules\Catalog\Application\Interfaces\RoomRepositoryInterface;
use App\Modules\Catalog\Domain\Entities\RoomEntity;
use App\Modules\Shared\Domain\ValueObjects\Slug;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tests\Trait\MockPermission;

class ToggleRoomUseCaseTest extends TestCase
{
    use MockPermission;

    private RoomRepositoryInterface $roomRepository;
    private ToggleRoomUseCase $useCase;

    public function getModuleName(): string
    {
        return 'catalog';
    }

    public function getEntityName(): string
    {
        return 'category';
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->roomRepository = Mockery::mock(RoomRepositoryInterface::class);
        $this->useCase = new ToggleRoomUseCase($this->roomRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function it_toggles_published_and_updates_descendants(): void
    {
        $room = new RoomEntity('Спальня', new Slug('Спальня'));
        $room->id = 5;

        $this->roomRepository->shouldReceive('getById')->with(5)->once()->andReturn($room);
        $this->roomRepository->shouldReceive('save')->once()->with($room)->andReturn($room);
        $this->roomRepository->shouldReceive('getDescendantIds')->with(5)->once()->andReturn([6]);
        $this->roomRepository->shouldReceive('bulkTogglePublished')->with([6], true)->once();

        $this->useCase->execute(5, $this->mockUserPermission(edit: true));

        $this->assertTrue($room->isPublished());
    }

    #[Test]
    public function it_throws_access_denied_when_missing_permission(): void
    {
        $this->roomRepository->shouldNotReceive('getById');

        $this->expectException(\DomainException::class);
        $this->useCase->execute(5, $this->mockUserPermission(edit: false));
    }
}
