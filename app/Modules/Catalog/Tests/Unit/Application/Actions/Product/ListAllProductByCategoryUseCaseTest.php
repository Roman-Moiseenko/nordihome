<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Tests\Unit\Application\Actions\Product;

use App\Modules\Catalog\Application\Actions\Product\ListAllProductByCategoryUseCase;
use App\Modules\Catalog\Application\DTOs\Product\ProductCategoryData;
use App\Modules\Catalog\Domain\Entities\ProductEntity;
use App\Modules\Catalog\Domain\Interfaces\ProductRepositoryInterface;
use App\Modules\Catalog\Domain\ValueObjects\Code;
use App\Modules\Shared\Domain\ValueObjects\Slug;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ListAllProductByCategoryUseCaseTest extends TestCase
{
    private ProductRepositoryInterface $productRepository;
    private ListAllProductByCategoryUseCase $useCase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->productRepository = Mockery::mock(ProductRepositoryInterface::class);
        $this->useCase = new ListAllProductByCategoryUseCase($this->productRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function it_returns_paginated_dtos(): void
    {
        $product = new ProductEntity('Стол', new Code('ART-001'), new Slug('Стол'), 10, 20);
        $product->id = 10;

        $paginator = new LengthAwarePaginator(new Collection([$product]), 1, 15, 1);

        $this->productRepository->shouldReceive('findAllByCategoryId')->with(5, 15, 1)->once()->andReturn($paginator);

        $result = $this->useCase->execute(5, 15, 1);

        $this->assertSame(1, $result->total());
        $this->assertInstanceOf(ProductCategoryData::class, $result->getCollection()->first());
        $this->assertSame(10, $result->getCollection()->first()->id);
    }
}
