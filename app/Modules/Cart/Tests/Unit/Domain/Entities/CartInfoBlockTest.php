<?php

namespace App\Modules\Cart\Tests\Unit\Domain\Entities;

use App\Modules\Cart\Domain\Entities\CartInfoBlock;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class CartInfoBlockTest extends TestCase
{
    #[Test]
    public function it_has_zero_defaults(): void
    {
        $block = new CartInfoBlock();

        $this->assertSame(0.0, $block->count);
        $this->assertSame(0.0, $block->amount);
        $this->assertSame(0.0, $block->discount);
    }

    #[Test]
    public function it_can_accumulate_values(): void
    {
        $block = new CartInfoBlock();

        $block->count += 3;
        $block->amount += 150.5;
        $block->discount += 20.0;

        $this->assertSame(3.0, $block->count);
        $this->assertSame(150.5, $block->amount);
        $this->assertSame(20.0, $block->discount);
    }

    #[Test]
    public function it_resets_values_on_clear(): void
    {
        $block = new CartInfoBlock();
        $block->count = 5;
        $block->amount = 500;
        $block->discount = 50;

        $block->clear();

        $this->assertSame(0.0, $block->count);
        $this->assertSame(0.0, $block->amount);
        $this->assertSame(0.0, $block->discount);
    }
}
