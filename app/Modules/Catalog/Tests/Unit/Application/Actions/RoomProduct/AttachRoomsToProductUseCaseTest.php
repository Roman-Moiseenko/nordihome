<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Tests\Unit\Application\Actions\RoomProduct;

use App\Modules\Catalog\Application\Actions\RoomProduct\AttachRoomsToProductUseCase;
use App\Modules\Catalog\Domain\Interfaces\RoomProductRepositoryInterface;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tests\Trait\MockPermission;

class AttachRoomsToProductUseCaseTest extends TestCase
{
    use MockPermission;

    private RoomProductRepositoryInterface $repository;
    private AttachRoomsToProductUseCase $useCase;

    public function getModuleName(): string
    {
        return 'catalog';
    }

    public function getEntityName(): string
    {
        return 'product';
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = Mockery::mock(RoomProductRepositoryInterface::class);
        $this->useCase = new AttachRoomsToProductUseCase($this->repository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function it_delegates_to_repository(): void
    {
        $this->repository->shouldReceive('attachRooms')->with(5, [1, 2])->once();

        $this->useCase->execute(5, [1, 2], $this->mockUserPermission(edit: true));
        $this->addToAssertionCount(1);
    }

    #[Test]
    public function it_throws_access_denied_when_missing_permission(): void
    {
        $this->repository->shouldNotReceive('attachRooms');

        $this->expectException(\DomainException::class);
        $this->useCase->execute(5, [1, 2], $this->mockUserPermission(edit: false));
    }
}
