<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Tests\Unit\Application\Actions\Room;

use App\Modules\Catalog\Application\Actions\Room\ViewRoomUseCase;
use App\Modules\Catalog\Application\Interfaces\RoomRepositoryInterface;
use App\Modules\Catalog\Domain\Entities\RoomEntity;
use App\Modules\Shared\Domain\ValueObjects\Slug;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tests\Trait\MockPermission;

class ViewRoomUseCaseTest extends TestCase
{
    use MockPermission;

    private RoomRepositoryInterface $roomRepository;
    private ViewRoomUseCase $useCase;

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
        $this->useCase = new ViewRoomUseCase($this->roomRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function it_returns_entity(): void
    {
        $entity = new RoomEntity('Спальня', new Slug('Спальня'));
        $entity->id = 5;

        $this->roomRepository->shouldReceive('getById')->with(5)->once()->andReturn($entity);

        $this->assertSame($entity, $this->useCase->execute(5, $this->mockUserPermission(view: true)));
    }

    #[Test]
    public function it_throws_access_denied_when_missing_permission(): void
    {
        $this->roomRepository->shouldNotReceive('getById');

        $this->expectException(\DomainException::class);
        $this->useCase->execute(5, $this->mockUserPermission(view: false));
    }
}
