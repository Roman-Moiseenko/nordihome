<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Tests\Unit\Application\Actions\TagProduct;

use App\Modules\Catalog\Application\Actions\TagProduct\AssignProductsToTagUseCase;
use App\Modules\Catalog\Domain\Interfaces\TagProductRepositoryInterface;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tests\Trait\MockPermission;

class AssignProductsToTagUseCaseTest extends TestCase
{
    use MockPermission;

    private TagProductRepositoryInterface $repository;
    private AssignProductsToTagUseCase $useCase;

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
        $this->repository = Mockery::mock(TagProductRepositoryInterface::class);
        $this->useCase = new AssignProductsToTagUseCase($this->repository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function it_delegates_to_repository(): void
    {
        $this->repository->shouldReceive('syncProducts')->with(5, [1, 2])->once();

        $this->useCase->execute(5, [1, 2], $this->mockUserPermission(edit: true));
        $this->addToAssertionCount(1);
    }

    #[Test]
    public function it_throws_access_denied_when_missing_permission(): void
    {
        $this->repository->shouldNotReceive('syncProducts');

        $this->expectException(\DomainException::class);
        $this->useCase->execute(5, [1, 2], $this->mockUserPermission(edit: false));
    }
}
