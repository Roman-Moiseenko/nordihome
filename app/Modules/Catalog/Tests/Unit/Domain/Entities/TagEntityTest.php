<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Tests\Unit\Domain\Entities;

use App\Modules\Catalog\Domain\Entities\TagEntity;
use App\Modules\Shared\Domain\ValueObjects\Slug;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class TagEntityTest extends TestCase
{
    #[Test]
    public function it_creates_with_required_fields_and_defaults(): void
    {
        $tag = new TagEntity('Скидка', new Slug('Скидка'));

        $this->assertSame('Скидка', $tag->name);
        $this->assertSame('skidka', $tag->slug->getValue());
        $this->assertFalse($tag->isMain);
        $this->assertNull($tag->image_url);
        $this->assertNull($tag->id);
    }

    #[Test]
    public function it_allows_setting_id_and_is_main(): void
    {
        $tag = new TagEntity('Хит', new Slug('Хит'));
        $tag->id = 3;
        $tag->isMain = true;

        $this->assertSame(3, $tag->id);
        $this->assertTrue($tag->isMain);
    }
}
