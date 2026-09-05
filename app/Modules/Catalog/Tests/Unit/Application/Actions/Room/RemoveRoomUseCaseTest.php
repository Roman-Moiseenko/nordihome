<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Tests\Unit\Application\Actions\Room;

use App\Modules\Catalog\Application\Actions\Room\RemoveRoomUseCase;
use App\Modules\Catalog\Domain\Interfaces\RoomRepositoryInterface;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tests\Trait\MockPermission;

class RemoveRoomUseCaseTest extends TestCase
{
    use MockPermission;

    private RoomRepositoryInterface $roomRepository;
    private RemoveRoomUseCase $useCase;

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
        $this->useCase = new RemoveRoomUseCase($this->roomRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function it_deletes_room(): void
    {
        $this->roomRepository->shouldReceive('delete')->with(5)->once();

        $this->useCase->execute(5, $this->mockUserPermission(delete: true));
        $this->addToAssertionCount(1);
    }

    #[Test]
    public function it_throws_access_denied_when_missing_permission(): void
    {
        $this->roomRepository->shouldNotReceive('delete');

        $this->expectException(\DomainException::class);
        $this->useCase->execute(5, $this->mockUserPermission(delete: false));
    }
}
