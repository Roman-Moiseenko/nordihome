<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Tests\Unit\Application\Actions\Wp;

use App\Modules\Catalog\Application\Actions\Wp\CreateRoomWpUseCase;
use App\Modules\Catalog\Application\DTOs\Wp\CategoryRoomWpData;
use App\Modules\Catalog\Application\Interfaces\RoomRepositoryInterface;
use App\Modules\Catalog\Domain\Entities\RoomEntity;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tests\Trait\MockPermission;

class CreateRoomWpUseCaseTest extends TestCase
{
    use MockPermission;

    private RoomRepositoryInterface $roomRepository;
    private CreateRoomWpUseCase $useCase;

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
        $this->useCase = new CreateRoomWpUseCase($this->roomRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function it_creates_room_with_wp_id(): void
    {
        $this->roomRepository->shouldReceive('existsByWpId')->with(42)->once()->andReturn(false);
        $this->roomRepository->shouldReceive('existsSlug')->with('spalnya')->once()->andReturn(false);
        $this->roomRepository->shouldReceive('save')
            ->once()
            ->with(Mockery::on(fn(RoomEntity $room) => $room->wpId === 42 && $room->name === 'Спальня'))
            ->andReturnUsing(fn(RoomEntity $room) => $room);

        $dto = new CategoryRoomWpData(wpId: 42, name: 'Спальня', parentId: null);

        $result = $this->useCase->execute($dto, $this->mockUserPermission(create: true));

        $this->assertInstanceOf(RoomEntity::class, $result);
        $this->assertSame(42, $result->wpId);
    }

    #[Test]
    public function it_returns_null_when_wp_id_exists(): void
    {
        $this->roomRepository->shouldReceive('existsByWpId')->with(42)->once()->andReturn(true);
        $this->roomRepository->shouldNotReceive('save');

        $dto = new CategoryRoomWpData(wpId: 42, name: 'Спальня', parentId: null);

        $this->assertNull($this->useCase->execute($dto, $this->mockUserPermission(create: true)));
    }

    #[Test]
    public function it_throws_access_denied_when_missing_permission(): void
    {
        $this->roomRepository->shouldNotReceive('existsByWpId');

        $dto = new CategoryRoomWpData(wpId: 42, name: 'Спальня', parentId: null);

        $this->expectException(\DomainException::class);
        $this->useCase->execute($dto, $this->mockUserPermission(create: false));
    }
}
