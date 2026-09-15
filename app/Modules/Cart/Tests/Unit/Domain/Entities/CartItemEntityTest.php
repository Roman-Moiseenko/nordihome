<?php

namespace App\Modules\Cart\Tests\Unit\Domain\Entities;

use App\Modules\Cart\Domain\Entities\CartItemEntity;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class CartItemEntityTest extends TestCase
{
    #[Test]
    public function it_creates_entity_with_defaults(): void
    {
        $item = new CartItemEntity(productId: 10, quantity: 2.5, isParser: false);

        $this->assertNull($item->id);
        $this->assertSame(10, $item->productId);
        $this->assertSame(2.5, $item->quantity);
        $this->assertFalse($item->isParser);
        $this->assertTrue($item->check);
    }

    #[Test]
    public function it_assigns_id_and_state(): void
    {
        $item = new CartItemEntity(productId: 10, quantity: 1.0, isParser: true);

        $item->id = 7;
        $item->check = false;
        $item->quantity = 3.0;

        $this->assertSame(7, $item->id);
        $this->assertSame(10, $item->productId);
        $this->assertSame(3.0, $item->quantity);
        $this->assertTrue($item->isParser);
        $this->assertFalse($item->check);
    }
}
