<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Tests\Unit\Application\Actions\Room;

use App\Modules\Catalog\Application\Actions\Room\UpdateRoomUseCase;
use App\Modules\Catalog\Application\DTOs\Room\RoomUpdateData;
use App\Modules\Catalog\Application\Interfaces\RoomRepositoryInterface;
use App\Modules\Catalog\Domain\Entities\RoomEntity;
use App\Modules\Shared\Domain\ValueObjects\Slug;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tests\Trait\MockPermission;

class UpdateRoomUseCaseTest extends TestCase
{
    use MockPermission;

    private RoomRepositoryInterface $roomRepository;
    private UpdateRoomUseCase $useCase;

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
        $this->useCase = new UpdateRoomUseCase($this->roomRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function it_updates_fields_and_saves(): void
    {
        $room = new RoomEntity('Спальня', new Slug('Спальня'));
        $room->id = 5;

        $this->roomRepository->shouldReceive('getById')->with(5)->once()->andReturn($room);
        $this->roomRepository->shouldReceive('existsSlug')->with('new-room', 5)->once()->andReturn(false);
        $this->roomRepository->shouldReceive('save')->once()->with($room)->andReturn($room);

        $dto = new RoomUpdateData(
            name: 'Гостиная',
            slug: 'new-room',
            parentId: null,
            svgIcon: null,
            published: null,
            metaTitle: null,
            metaDescription: null,
        );

        $result = $this->useCase->execute(5, $dto, $this->mockUserPermission(edit: true));

        $this->assertSame('Гостиная', $result->name);
        $this->assertSame('new-room', $result->slug->getValue());
    }

    #[Test]
    public function it_throws_access_denied_when_missing_permission(): void
    {
        $this->roomRepository->shouldNotReceive('getById');

        $dto = new RoomUpdateData(
            name: null,
            slug: null,
            parentId: null,
            svgIcon: null,
            published: null,
            metaTitle: null,
            metaDescription: null,
        );

        $this->expectException(\DomainException::class);
        $this->useCase->execute(5, $dto, $this->mockUserPermission(edit: false));
    }
}
