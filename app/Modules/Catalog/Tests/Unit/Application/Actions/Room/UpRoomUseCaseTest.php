<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Tests\Unit\Application\Actions\Room;

use App\Modules\Catalog\Application\Actions\Room\UpRoomUseCase;
use App\Modules\Catalog\Domain\Interfaces\RoomRepositoryInterface;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tests\Trait\MockPermission;

class UpRoomUseCaseTest extends TestCase
{
    use MockPermission;

    private RoomRepositoryInterface $roomRepository;
    private UpRoomUseCase $useCase;

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
        $this->useCase = new UpRoomUseCase($this->roomRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function it_moves_room_up(): void
    {
        $this->roomRepository->shouldReceive('moveUp')->with(5)->once();

        $this->useCase->execute(5, $this->mockUserPermission(edit: true));
        $this->addToAssertionCount(1);
    }

    #[Test]
    public function it_throws_access_denied_when_missing_permission(): void
    {
        $this->roomRepository->shouldNotReceive('moveUp');

        $this->expectException(\DomainException::class);
        $this->useCase->execute(5, $this->mockUserPermission(edit: false));
    }
}
