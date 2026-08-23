<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Tests\Unit\Domain\ValueObjects;

use App\Modules\Catalog\Domain\ValueObjects\PriceType;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class PriceTypeTest extends TestCase
{
    #[Test]
    public function it_defaults_to_retail_when_null_is_passed(): void
    {
        $type = new PriceType(null);

        $this->assertSame(PriceType::RETAIL, $type->value);
    }

    #[Test]
    public function it_creates_with_explicit_value(): void
    {
        $type = new PriceType(PriceType::BULK);

        $this->assertSame(PriceType::BULK, $type->value);
    }

    #[Test]
    public function it_has_retail_named_factory(): void
    {
        $this->assertSame(PriceType::RETAIL, PriceType::retail()->value);
    }

    #[Test]
    public function it_creates_from_string(): void
    {
        $this->assertSame(PriceType::COST, PriceType::fromString('cost')->value);
    }

    #[Test]
    public function it_compares_equality_by_value(): void
    {
        $this->assertTrue((new PriceType(PriceType::RETAIL))->equals(new PriceType(PriceType::RETAIL)));
        $this->assertFalse((new PriceType(PriceType::RETAIL))->equals(new PriceType(PriceType::SPECIAL)));
    }

    #[Test]
    public function it_returns_default_type(): void
    {
        $this->assertSame(PriceType::RETAIL, PriceType::default()->value);
    }

    #[Test]
    public function it_casts_to_string(): void
    {
        $this->assertSame('retail', (string) new PriceType(PriceType::RETAIL));
    }
}
