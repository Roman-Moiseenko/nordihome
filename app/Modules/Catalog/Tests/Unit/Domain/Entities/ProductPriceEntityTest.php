<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Tests\Unit\Domain\Entities;

use App\Modules\Catalog\Domain\Entities\ProductPriceEntity;
use App\Modules\Catalog\Domain\ValueObjects\PriceType;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ProductPriceEntityTest extends TestCase
{
    #[Test]
    public function it_creates_with_required_fields(): void
    {
        $price = new ProductPriceEntity(
            productId: 5,
            price: 1999.90,
            priceType: PriceType::retail(),
        );

        $this->assertSame(5, $price->productId);
        $this->assertSame(1999.90, $price->price);
        $this->assertSame(PriceType::RETAIL, $price->priceType->value);
        $this->assertInstanceOf(\DateTimeImmutable::class, $price->setAt);
        $this->assertNull($price->founded);
        $this->assertNull($price->comment);
    }

    #[Test]
    public function it_accepts_custom_set_at(): void
    {
        $setAt = new \DateTimeImmutable('2026-01-01 10:00:00');

        $price = new ProductPriceEntity(
            productId: 5,
            price: 100.0,
            priceType: new PriceType(PriceType::BULK),
            setAt: $setAt,
        );

        $this->assertSame($setAt, $price->setAt);
        $this->assertSame(PriceType::BULK, $price->priceType->value);
    }

    #[Test]
    public function it_allows_setting_optional_fields(): void
    {
        $price = new ProductPriceEntity(5, 100.0, new PriceType(PriceType::COST));
        $price->id = 9;
        $price->founded = 'https://source.example';
        $price->comment = 'Комментарий';

        $this->assertSame(9, $price->id);
        $this->assertSame('https://source.example', $price->founded);
        $this->assertSame('Комментарий', $price->comment);
    }
}
