<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Tests\Unit\Domain\Entities;

use App\Modules\Catalog\Domain\Entities\CategoryEntity;
use App\Modules\Shared\Domain\ValueObjects\Slug;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class CategoryEntityTest extends TestCase
{
    #[Test]
    public function it_creates_with_required_fields_and_defaults(): void
    {
        $category = new CategoryEntity('Мебель', new Slug('Мебель'));

        $this->assertSame('Мебель', $category->name);
        $this->assertSame('mebel', $category->slug->getValue());
        $this->assertNull($category->parentId);
        $this->assertFalse($category->isPublished());
        $this->assertSame([], $category->children);
    }

    #[Test]
    public function it_creates_with_parent(): void
    {
        $category = new CategoryEntity('Столы', new Slug('Столы'), 3);

        $this->assertSame(3, $category->parentId);
    }

    #[Test]
    public function it_publishes_and_unpublishes(): void
    {
        $category = new CategoryEntity('Мебель', new Slug('Мебель'));

        $category->publish();
        $this->assertTrue($category->isPublished());

        $category->unpublish();
        $this->assertFalse($category->isPublished());
    }

    #[Test]
    public function it_allows_setting_id_and_meta(): void
    {
        $category = new CategoryEntity('Мебель', new Slug('Мебель'));
        $category->id = 7;

        $this->assertSame(7, $category->id);
    }
}
