<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Tests\Unit\Application\Actions\Room;

use App\Modules\Catalog\Application\Actions\Room\TreeRoomUseCase;
use App\Modules\Catalog\Application\Interfaces\RoomRepositoryInterface;
use App\Modules\Catalog\Domain\Entities\RoomEntity;
use App\Modules\Shared\Domain\ValueObjects\Slug;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class TreeRoomUseCaseTest extends TestCase
{
    private RoomRepositoryInterface $roomRepository;
    private TreeRoomUseCase $useCase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->roomRepository = Mockery::mock(RoomRepositoryInterface::class);
        $this->useCase = new TreeRoomUseCase($this->roomRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function it_returns_tree(): void
    {
        $tree = [new RoomEntity('Спальня', new Slug('Спальня'))];

        $this->roomRepository->shouldReceive('getTree')->once()->andReturn($tree);

        $this->assertSame($tree, $this->useCase->execute());
    }
}
