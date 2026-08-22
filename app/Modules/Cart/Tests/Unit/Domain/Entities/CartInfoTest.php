<?php

namespace App\Modules\Cart\Tests\Unit\Domain\Entities;

use App\Modules\Cart\Domain\Entities\CartInfo;
use App\Modules\Cart\Domain\Entities\CartInfoBlock;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class CartInfoTest extends TestCase
{
    #[Test]
    public function it_initializes_blocks_with_defaults(): void
    {
        $info = new CartInfo();

        $this->assertInstanceOf(CartInfoBlock::class, $info->all);
        $this->assertInstanceOf(CartInfoBlock::class, $info->order);
        $this->assertInstanceOf(CartInfoBlock::class, $info->pre_order);
        $this->assertTrue($info->check_all);
        $this->assertFalse($info->preorder);
    }

    #[Test]
    public function it_resets_state_on_clear(): void
    {
        $info = new CartInfo();

        $info->all->count = 10;
        $info->order->amount = 100;
        $info->pre_order->discount = 5;
        $info->check_all = false;
        $info->preorder = true;

        $info->clear();

        $this->assertSame(0.0, $info->all->count);
        $this->assertSame(0.0, $info->order->amount);
        $this->assertSame(0.0, $info->pre_order->discount);
        $this->assertTrue($info->check_all);
        $this->assertFalse($info->preorder);
    }
}
