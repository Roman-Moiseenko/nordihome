<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Tests\Unit\Application\Actions\Product;

use App\Modules\Catalog\Application\Actions\Product\UpdateProductUseCase;
use App\Modules\Catalog\Application\DTOs\Product\ProductUpdateData;
use App\Modules\Catalog\Application\Interfaces\ProductRepositoryInterface;
use App\Modules\Catalog\Domain\Entities\ProductEntity;
use App\Modules\Catalog\Domain\ValueObjects\Code;
use App\Modules\Shared\Domain\ValueObjects\Slug;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tests\Trait\MockPermission;

class UpdateProductUseCaseTest extends TestCase
{
    use MockPermission;

    private ProductRepositoryInterface $productRepository;
    private UpdateProductUseCase $useCase;

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
        $this->productRepository = Mockery::mock(ProductRepositoryInterface::class);
        $this->useCase = new UpdateProductUseCase($this->productRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function it_updates_fields_and_saves(): void
    {
        $product = new ProductEntity('Стол', new Code('ART-001'), new Slug('Стол'), 10, 20);
        $product->id = 5;

        $this->productRepository->shouldReceive('getById')->with(5)->once()->andReturn($product);
        $this->productRepository->shouldReceive('save')->once()->with($product)->andReturn($product);

        $dto = new ProductUpdateData(id: 5, name: 'Новый стол', code: 'ART-002', mainCategoryId: 11, brandId: 21);

        $result = $this->useCase->execute($dto, $this->mockUserPermission(edit: true));

        $this->assertSame('Новый стол', $result->name);
        $this->assertSame('ART-002', $result->code->getCode());
        $this->assertSame(11, $result->mainCategoryId);
        $this->assertSame(21, $result->brandId);
    }

    #[Test]
    public function it_publishes_when_requested(): void
    {
        $product = new ProductEntity('Стол', new Code('ART-001'), new Slug('Стол'), 10, 20);
        $product->id = 5;

        $this->productRepository->shouldReceive('getById')->with(5)->once()->andReturn($product);
        $this->productRepository->shouldReceive('save')->once()->with($product)->andReturn($product);

        $dto = new ProductUpdateData(id: 5, published: true);

        $result = $this->useCase->execute($dto, $this->mockUserPermission(edit: true));

        $this->assertTrue($result->isPublished());
    }

    #[Test]
    public function it_throws_access_denied_when_missing_permission(): void
    {
        $this->productRepository->shouldNotReceive('getById');

        $dto = new ProductUpdateData(id: 5);

        $this->expectException(\DomainException::class);
        $this->useCase->execute($dto, $this->mockUserPermission(edit: false));
    }
}
