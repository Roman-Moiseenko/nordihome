<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Tests\Unit\Application\Actions\TagProduct;

use App\Modules\Catalog\Application\Actions\TagProduct\ListProductByTagUseCase;
use App\Modules\Catalog\Application\DTOs\Product\ProductRoomData;
use App\Modules\Catalog\Application\Interfaces\ProductRepositoryInterface;
use App\Modules\Catalog\Application\Interfaces\TagProductRepositoryInterface;
use App\Modules\Catalog\Domain\Entities\ProductEntity;
use App\Modules\Catalog\Domain\ValueObjects\Code;
use App\Modules\Shared\Domain\ValueObjects\Slug;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ListProductByTagUseCaseTest extends TestCase
{
    private TagProductRepositoryInterface $tagProductRepository;
    private ProductRepositoryInterface $productRepository;
    private ListProductByTagUseCase $useCase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tagProductRepository = Mockery::mock(TagProductRepositoryInterface::class);
        $this->productRepository = Mockery::mock(ProductRepositoryInterface::class);
        $this->useCase = new ListProductByTagUseCase($this->tagProductRepository, $this->productRepository);
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

        $this->tagProductRepository->shouldReceive('getProductIdsByTag')->with(5, 15)->once()->andReturn($idPaginator);
        $this->productRepository->shouldReceive('findByIds')->with([10])->once()->andReturn([$product]);

        $result = $this->useCase->execute(5, 15);

        $this->assertSame(1, $result->total());
        $this->assertInstanceOf(ProductRoomData::class, $result->getCollection()->first());
        $this->assertSame(10, $result->getCollection()->first()->id);
    }
}
