<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Tests\Unit\Application\Actions\Wp;

use App\Modules\Catalog\Application\Actions\Wp\GetRoomByWpIdUseCase;
use App\Modules\Catalog\Application\Interfaces\RoomRepositoryInterface;
use App\Modules\Catalog\Domain\Entities\RoomEntity;
use App\Modules\Shared\Domain\ValueObjects\Slug;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class GetRoomByWpIdUseCaseTest extends TestCase
{
    private RoomRepositoryInterface $roomRepository;
    private GetRoomByWpIdUseCase $useCase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->roomRepository = Mockery::mock(RoomRepositoryInterface::class);
        $this->useCase = new GetRoomByWpIdUseCase($this->roomRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function it_returns_room_without_children(): void
    {
        $room = new RoomEntity('Спальня', new Slug('Спальня'));
        $room->id = 5;

        $this->roomRepository->shouldReceive('findByWpId')->with(42)->once()->andReturn($room);
        $this->roomRepository->shouldReceive('hasChildren')->with(5)->once()->andReturn(false);

        $this->assertSame($room, $this->useCase->execute(42));
    }

    #[Test]
    public function it_returns_null_when_not_found(): void
    {
        $this->roomRepository->shouldReceive('findByWpId')->with(42)->once()->andReturn(null);
        $this->roomRepository->shouldNotReceive('hasChildren');

        $this->assertNull($this->useCase->execute(42));
    }

    #[Test]
    public function it_returns_null_when_room_has_children(): void
    {
        $room = new RoomEntity('Спальня', new Slug('Спальня'));
        $room->id = 5;

        $this->roomRepository->shouldReceive('findByWpId')->with(42)->once()->andReturn($room);
        $this->roomRepository->shouldReceive('hasChildren')->with(5)->once()->andReturn(true);

        $this->assertNull($this->useCase->execute(42));
    }
}
