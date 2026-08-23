<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Tests\Unit\Application\Actions\CategoryProduct;

use App\Modules\Catalog\Application\Actions\CategoryProduct\DetachProductsFromCategoryUseCase;
use App\Modules\Catalog\Application\Interfaces\CategoryProductRepositoryInterface;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tests\Trait\MockPermission;

class DetachProductsFromCategoryUseCaseTest extends TestCase
{
    use MockPermission;

    private CategoryProductRepositoryInterface $repository;
    private DetachProductsFromCategoryUseCase $useCase;

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
        $this->repository = Mockery::mock(CategoryProductRepositoryInterface::class);
        $this->useCase = new DetachProductsFromCategoryUseCase($this->repository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function it_delegates_to_repository(): void
    {
        $this->repository->shouldReceive('detachProducts')->with(5, [1, 2])->once();

        $this->useCase->execute(5, [1, 2], $this->mockUserPermission(edit: true));
        $this->addToAssertionCount(1);
    }

    #[Test]
    public function it_throws_access_denied_when_missing_permission(): void
    {
        $this->repository->shouldNotReceive('detachProducts');

        $this->expectException(\DomainException::class);
        $this->useCase->execute(5, [1, 2], $this->mockUserPermission(edit: false));
    }
}
