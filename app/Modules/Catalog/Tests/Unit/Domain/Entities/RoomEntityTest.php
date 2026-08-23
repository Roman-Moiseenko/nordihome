<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Tests\Unit\Domain\Entities;

use App\Modules\Catalog\Domain\Entities\RoomEntity;
use App\Modules\Shared\Domain\ValueObjects\Slug;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class RoomEntityTest extends TestCase
{
    #[Test]
    public function it_creates_with_required_fields_and_defaults(): void
    {
        $room = new RoomEntity('Спальня', new Slug('Спальня'));

        $this->assertSame('Спальня', $room->name);
        $this->assertSame('spalnya', $room->slug->getValue());
        $this->assertNull($room->parentId);
        $this->assertFalse($room->isPublished());
        $this->assertSame([], $room->children);
    }

    #[Test]
    public function it_creates_with_parent(): void
    {
        $room = new RoomEntity('Кровать', new Slug('Кровать'), 2);

        $this->assertSame(2, $room->parentId);
    }

    #[Test]
    public function it_publishes_and_unpublishes(): void
    {
        $room = new RoomEntity('Спальня', new Slug('Спальня'));

        $room->publish();
        $this->assertTrue($room->isPublished());

        $room->unpublish();
        $this->assertFalse($room->isPublished());
    }

    #[Test]
    public function it_allows_setting_id_and_wp_id(): void
    {
        $room = new RoomEntity('Спальня', new Slug('Спальня'));
        $room->id = 11;
        $room->wpId = 42;

        $this->assertSame(11, $room->id);
        $this->assertSame(42, $room->wpId);
    }
}
