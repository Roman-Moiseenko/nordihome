<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Tests\Unit\Application\Actions\CategoryProduct;

use App\Modules\Catalog\Application\Actions\CategoryProduct\ListProductByCategoryUseCase;
use App\Modules\Catalog\Application\DTOs\Product\ProductCategoryData;
use App\Modules\Catalog\Domain\Entities\ProductEntity;
use App\Modules\Catalog\Domain\Interfaces\CategoryProductRepositoryInterface;
use App\Modules\Catalog\Domain\Interfaces\ProductRepositoryInterface;
use App\Modules\Catalog\Domain\ValueObjects\Code;
use App\Modules\Shared\Domain\ValueObjects\Slug;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ListProductByCategoryUseCaseTest extends TestCase
{
    private CategoryProductRepositoryInterface $categoryProductRepository;
    private ProductRepositoryInterface $productRepository;
    private ListProductByCategoryUseCase $useCase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->categoryProductRepository = Mockery::mock(CategoryProductRepositoryInterface::class);
        $this->productRepository = Mockery::mock(ProductRepositoryInterface::class);
        $this->useCase = new ListProductByCategoryUseCase($this->categoryProductRepository, $this->productRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function it_returns_paginated_product_dtos(): void
    {
        $product = new ProductEntity('Стол', new Code('ART-001'), new Slug('Стол'), 10, 20);
        $product->id = 10;

        $idPaginator = new LengthAwarePaginator(new Collection([['product_id' => 10]]), 1, 15, 1);

        $this->categoryProductRepository->shouldReceive('getProductIdsByCategoryId')->with(5, 15, 1)->once()->andReturn($idPaginator);
        $this->productRepository->shouldReceive('findByIds')->with([10])->once()->andReturn([$product]);

        $result = $this->useCase->execute(5, 15, 1);

        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
        $this->assertSame(1, $result->total());
        $this->assertInstanceOf(ProductCategoryData::class, $result->getCollection()->first());
        $this->assertSame(10, $result->getCollection()->first()->id);
    }

    #[Test]
    public function it_returns_empty_paginator_when_no_products(): void
    {
        $idPaginator = new LengthAwarePaginator(new Collection(), 0, 15, 1);

        $this->categoryProductRepository->shouldReceive('getProductIdsByCategoryId')->with(5, 15, 1)->once()->andReturn($idPaginator);
        $this->productRepository->shouldNotReceive('findByIds');

        $result = $this->useCase->execute(5, 15, 1);

        $this->assertSame(0, $result->total());
        $this->assertTrue($result->getCollection()->isEmpty());
    }
}
