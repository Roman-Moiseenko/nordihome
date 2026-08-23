<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Tests\Unit\Domain\Entities;

use App\Modules\Catalog\Domain\Entities\BrandEntity;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class BrandEntityTest extends TestCase
{
    #[Test]
    public function it_creates_with_required_fields_and_defaults(): void
    {
        $brand = new BrandEntity('Икеа');

        $this->assertSame('Икеа', $brand->name);
        $this->assertSame('', $brand->description);
        $this->assertSame('', $brand->url);
        $this->assertSame([], $brand->sameAs);
        $this->assertNull($brand->image_url);
        $this->assertNull($brand->currencyId);
        $this->assertNull($brand->parserClass);
        $this->assertNull($brand->id);
    }

    #[Test]
    public function it_accepts_description_and_url(): void
    {
        $brand = new BrandEntity('New Balance', 'Описание', 'https://example.com');

        $this->assertSame('New Balance', $brand->name);
        $this->assertSame('Описание', $brand->description);
        $this->assertSame('https://example.com', $brand->url);
    }

    #[Test]
    public function it_allows_setting_id_and_optional_fields(): void
    {
        $brand = new BrandEntity('Икеа');
        $brand->id = 5;
        $brand->parserClass = 'SomeParser';
        $brand->sameAs = ['https://ikea.ru'];

        $this->assertSame(5, $brand->id);
        $this->assertSame('SomeParser', $brand->parserClass);
        $this->assertSame(['https://ikea.ru'], $brand->sameAs);
    }
}
