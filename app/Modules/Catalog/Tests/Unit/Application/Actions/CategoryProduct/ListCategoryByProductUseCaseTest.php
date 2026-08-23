<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Tests\Unit\Application\Actions\CategoryProduct;

use App\Modules\Catalog\Application\Actions\CategoryProduct\ListCategoryByProductUseCase;
use App\Modules\Catalog\Application\DTOs\Category\CategoryProductData;
use App\Modules\Catalog\Application\Interfaces\CategoryProductRepositoryInterface;
use App\Modules\Catalog\Application\Interfaces\CategoryRepositoryInterface;
use App\Modules\Catalog\Domain\Entities\CategoryEntity;
use App\Modules\Shared\Domain\ValueObjects\Slug;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ListCategoryByProductUseCaseTest extends TestCase
{
    private CategoryProductRepositoryInterface $categoryProductRepository;
    private CategoryRepositoryInterface $categoryRepository;
    private ListCategoryByProductUseCase $useCase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->categoryProductRepository = Mockery::mock(CategoryProductRepositoryInterface::class);
        $this->categoryRepository = Mockery::mock(CategoryRepositoryInterface::class);
        $this->useCase = new ListCategoryByProductUseCase($this->categoryProductRepository, $this->categoryRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function it_returns_empty_array_when_no_categories(): void
    {
        $this->categoryProductRepository->shouldReceive('getCategoriesByProductId')->with(5)->once()->andReturn([]);
        $this->categoryRepository->shouldNotReceive('findByIds');

        $this->assertSame([], $this->useCase->execute(5));
    }

    #[Test]
    public function it_returns_category_dtos(): void
    {
        $category = new CategoryEntity('Мебель', new Slug('Мебель'));
        $category->id = 3;

        $this->categoryProductRepository->shouldReceive('getCategoriesByProductId')->with(5)->once()->andReturn([3]);
        $this->categoryRepository->shouldReceive('findByIds')->with([3])->once()->andReturn([$category]);

        $result = $this->useCase->execute(5);

        $this->assertCount(1, $result);
        $this->assertInstanceOf(CategoryProductData::class, $result[0]);
        $this->assertSame(3, $result[0]->id);
    }
}
