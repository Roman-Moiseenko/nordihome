<?php

namespace App\Modules\Cart\Tests\Unit\Application\Actions;

use App\Modules\Cart\Application\Actions\CheckAllToCartUseCase;
use App\Modules\Cart\Domain\Entities\CartItem;
use App\Modules\Cart\Infrastructure\Persistence\HybridStorage;
use App\Modules\Catalog\Infrastructure\Models\Product;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class CheckAllToCartUseCaseTest extends TestCase
{
    private HybridStorage $storage;
    private CheckAllToCartUseCase $useCase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->storage = Mockery::mock(HybridStorage::class);
        $this->useCase = new CheckAllToCartUseCase($this->storage);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    private function makeItem(int $id, int $productId, bool $check = true): CartItem
    {
        $product = Mockery::mock(Product::class);
        $product->shouldReceive('getAttribute')->with('id')->andReturn($productId);

        $item = CartItem::create($productId, 1, false);
        $item->id = $id;
        $item->product = $product;
        $item->check = $check;

        return $item;
    }

    #[Test]
    public function it_marks_all_items_as_checked(): void
    {
        $item1 = $this->makeItem(1, 10, false);
        $item2 = $this->makeItem(2, 20, false);

        $this->storage->shouldReceive('load')->once()->andReturn([$item1, $item2]);
        $this->storage->shouldReceive('check')->twice();

        $this->useCase->execute(true);

        $this->assertTrue($item1->check);
        $this->assertTrue($item2->check);
    }

    #[Test]
    public function it_unmarks_all_items(): void
    {
        $item1 = $this->makeItem(1, 10, true);
        $item2 = $this->makeItem(2, 20, true);

        $this->storage->shouldReceive('load')->once()->andReturn([$item1, $item2]);
        $this->storage->shouldReceive('check')->twice();

        $this->useCase->execute(false);

        $this->assertFalse($item1->check);
        $this->assertFalse($item2->check);
    }

    #[Test]
    public function it_does_nothing_when_cart_is_empty(): void
    {
        $this->storage->shouldReceive('load')->once()->andReturn([]);
        $this->storage->shouldNotReceive('check');

        $this->useCase->execute(true);
        $this->addToAssertionCount(1);
    }
}
