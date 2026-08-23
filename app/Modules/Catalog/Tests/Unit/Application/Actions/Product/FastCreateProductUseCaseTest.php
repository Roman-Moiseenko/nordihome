<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Tests\Unit\Application\Actions\Product;

use App\Modules\Catalog\Application\Actions\Product\FastCreateProductUseCase;
use App\Modules\Catalog\Application\DTOs\Product\ProductFastCreateData;
use App\Modules\Catalog\Application\Interfaces\ProductRepositoryInterface;
use App\Modules\Catalog\Domain\Entities\ProductEntity;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tests\Trait\MockPermission;

class FastCreateProductUseCaseTest extends TestCase
{
    use MockPermission;

    private ProductRepositoryInterface $productRepository;
    private FastCreateProductUseCase $useCase;

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
        $this->useCase = new FastCreateProductUseCase($this->productRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function it_creates_product_when_code_is_free(): void
    {
        $this->productRepository->shouldReceive('findByCode')->with('ART-001')->once()->andReturn(null);
        $this->productRepository->shouldReceive('save')
            ->once()
            ->with(Mockery::on(fn(ProductEntity $product) => $product->name === 'Стол' && $product->mainCategoryId === 10 && $product->brandId === 20))
            ->andReturnUsing(fn(ProductEntity $product) => $product);

        $dto = new ProductFastCreateData(name: 'Стол', code: 'ART-001', brandId: 20, categoryId: 10, slug: null);

        $result = $this->useCase->execute($dto, $this->mockUserPermission(create: true));

        $this->assertInstanceOf(ProductEntity::class, $result);
        $this->assertSame('ART-001', $result->code->getCode());
    }

    #[Test]
    public function it_throws_when_code_already_exists(): void
    {
        $existing = new ProductEntity('Старый', new \App\Modules\Catalog\Domain\ValueObjects\Code('ART-001'), new \App\Modules\Shared\Domain\ValueObjects\Slug('staryi'), 10, 20);

        $this->productRepository->shouldReceive('findByCode')->with('ART-001')->once()->andReturn($existing);
        $this->productRepository->shouldNotReceive('save');

        $dto = new ProductFastCreateData(name: 'Стол', code: 'ART-001', brandId: 20, categoryId: 10, slug: null);

        $this->expectException(\DomainException::class);
        $this->useCase->execute($dto, $this->mockUserPermission(create: true));
    }

    #[Test]
    public function it_throws_access_denied_when_missing_permission(): void
    {
        $this->productRepository->shouldNotReceive('findByCode');

        $dto = new ProductFastCreateData(name: 'Стол', code: 'ART-001', brandId: 20, categoryId: 10, slug: null);

        $this->expectException(\DomainException::class);
        $this->useCase->execute($dto, $this->mockUserPermission(create: false));
    }
}
