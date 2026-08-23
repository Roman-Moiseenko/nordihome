<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Tests\Unit\Application\Actions\CategoryProduct;

use App\Modules\Catalog\Application\Actions\CategoryProduct\AssignCategoriesToProductUseCase;
use App\Modules\Catalog\Application\Interfaces\CategoryProductRepositoryInterface;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tests\Trait\MockPermission;

class AssignCategoriesToProductUseCaseTest extends TestCase
{
    use MockPermission;

    private CategoryProductRepositoryInterface $repository;
    private AssignCategoriesToProductUseCase $useCase;

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
        $this->repository = Mockery::mock(CategoryProductRepositoryInterface::class);
        $this->useCase = new AssignCategoriesToProductUseCase($this->repository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function it_syncs_categories_to_product(): void
    {
        $this->repository->shouldReceive('syncCategories')->with(5, [1, 2])->once();

        $this->useCase->execute(5, [1, 2], $this->mockUserPermission(edit: true));
        $this->addToAssertionCount(1);
    }

    #[Test]
    public function it_throws_access_denied_when_missing_permission(): void
    {
        $this->repository->shouldNotReceive('syncCategories');

        $this->expectException(\DomainException::class);
        $this->useCase->execute(5, [1, 2], $this->mockUserPermission(edit: false));
    }
}
