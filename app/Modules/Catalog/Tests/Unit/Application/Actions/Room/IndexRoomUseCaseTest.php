<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Tests\Unit\Application\Actions\Room;

use App\Modules\Catalog\Application\Actions\Room\IndexRoomUseCase;
use App\Modules\Catalog\Domain\Entities\RoomEntity;
use App\Modules\Catalog\Domain\Interfaces\RoomRepositoryInterface;
use App\Modules\Shared\Domain\ValueObjects\Slug;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tests\Trait\MockPermission;

class IndexRoomUseCaseTest extends TestCase
{
    use MockPermission;

    private RoomRepositoryInterface $roomRepository;
    private IndexRoomUseCase $useCase;

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
        $this->useCase = new IndexRoomUseCase($this->roomRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function it_returns_all_rooms(): void
    {
        $rooms = [new RoomEntity('Спальня', new Slug('Спальня'))];

        $this->roomRepository->shouldReceive('getAll')->once()->andReturn($rooms);

        $this->assertSame($rooms, $this->useCase->execute($this->mockUserPermission(view: true)));
    }

    #[Test]
    public function it_throws_access_denied_when_missing_permission(): void
    {
        $this->roomRepository->shouldNotReceive('getAll');

        $this->expectException(\DomainException::class);
        $this->useCase->execute($this->mockUserPermission(view: false));
    }
}
