<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Tests\Unit\Domain\Entities;

use App\Modules\Catalog\Domain\Entities\ProductEntity;
use App\Modules\Catalog\Domain\ValueObjects\Code;
use App\Modules\Parser\Domain\ValueObjects\Package;
use App\Modules\Shared\Domain\ValueObjects\Slug;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ProductEntityTest extends TestCase
{
    private function makeProduct(): ProductEntity
    {
        return new ProductEntity(
            name: 'Стол',
            code: new Code('ART-001'),
            slug: new Slug('Стол'),
            mainCategoryId: 10,
            brandId: 20,
        );
    }

    #[Test]
    public function it_creates_with_required_fields_and_defaults(): void
    {
        $product = $this->makeProduct();

        $this->assertSame('Стол', $product->name);
        $this->assertSame('Стол', $product->namePrint);
        $this->assertSame('ART-001', $product->code->getCode());
        $this->assertSame('stol', $product->slug->getValue());
        $this->assertSame(10, $product->mainCategoryId);
        $this->assertSame(20, $product->brandId);
        $this->assertFalse($product->isPublished());
        $this->assertSame(105, $product->frequency);
    }

    #[Test]
    public function it_publishes_and_sets_published_at(): void
    {
        $product = $this->makeProduct();

        $product->publish();

        $this->assertTrue($product->isPublished());
        $this->assertInstanceOf(\DateTimeImmutable::class, $product->publishedAt);
    }

    #[Test]
    public function it_unpublishes(): void
    {
        $product = $this->makeProduct();
        $product->publish();
        $product->unpublish();

        $this->assertFalse($product->isPublished());
    }

    #[Test]
    public function it_calculates_total_weight_from_packages(): void
    {
        $product = $this->makeProduct();
        $product->packages = [
            new Package(height: 10, width: 10, length: 10, weight: 2.5, quantity: 2),
            new Package(height: 5, width: 5, length: 5, weight: 1.0, quantity: 1),
        ];

        $this->assertSame(6.0, $product->weight());
    }

    #[Test]
    public function it_calculates_total_volume_from_packages(): void
    {
        // 1000 * 2 + 125 = 2125 см³ => 0.002125 м³ (округляется до 0.003)
        $product = $this->makeProduct();
        $product->packages = [
            new Package(height: 10, width: 10, length: 10, weight: 0, quantity: 2),
            new Package(height: 5, width: 5, length: 5, weight: 0, quantity: 1),
        ];

        $this->assertSame(0.003, $product->volume());
    }
}
