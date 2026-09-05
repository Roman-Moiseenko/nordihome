<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Tests\Unit\Application\Actions\Room;

use App\Modules\Catalog\Application\Actions\Room\CreateRoomUseCase;
use App\Modules\Catalog\Application\DTOs\Room\RoomCreateData;
use App\Modules\Catalog\Domain\Entities\RoomEntity;
use App\Modules\Catalog\Domain\Interfaces\RoomRepositoryInterface;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tests\Trait\MockPermission;

class CreateRoomUseCaseTest extends TestCase
{
    use MockPermission;

    private RoomRepositoryInterface $roomRepository;
    private CreateRoomUseCase $useCase;

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
        $this->useCase = new CreateRoomUseCase($this->roomRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function it_creates_and_saves_room(): void
    {
        $this->roomRepository->shouldReceive('existsSlug')->with('spalnya')->once()->andReturn(false);
        $this->roomRepository->shouldReceive('save')
            ->once()
            ->with(Mockery::on(fn(RoomEntity $room) => $room->name === 'Спальня'))
            ->andReturnUsing(fn(RoomEntity $room) => $room);

        $dto = new RoomCreateData(name: 'Спальня', slug: null, parentId: null);

        $result = $this->useCase->execute($dto, $this->mockUserPermission(create: true));

        $this->assertInstanceOf(RoomEntity::class, $result);
        $this->assertSame('Спальня', $result->name);
    }

    #[Test]
    public function it_throws_access_denied_when_missing_permission(): void
    {
        $this->roomRepository->shouldNotReceive('existsSlug');

        $dto = new RoomCreateData(name: 'Спальня', slug: null, parentId: null);

        $this->expectException(\DomainException::class);
        $this->useCase->execute($dto, $this->mockUserPermission(create: false));
    }
}
