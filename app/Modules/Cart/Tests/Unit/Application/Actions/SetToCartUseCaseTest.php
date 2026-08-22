<?php

namespace App\Modules\Cart\Tests\Unit\Application\Actions;

use App\Modules\Cart\Application\Actions\SetToCartUseCase;
use App\Modules\Cart\Domain\Entities\CartItem;
use App\Modules\Cart\Infrastructure\Persistence\HybridStorage;
use App\Modules\Catalog\Infrastructure\Models\Product;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class SetToCartUseCaseTest extends TestCase
{
    private HybridStorage $storage;
    private SetToCartUseCase $useCase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->storage = Mockery::mock(HybridStorage::class);
        $this->useCase = new SetToCartUseCase($this->storage);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    private function makeItem(int $id, int $productId, float $quantity = 1.0): CartItem
    {
        $product = Mockery::mock(Product::class);
        $product->shouldReceive('getAttribute')->with('id')->andReturn($productId);

        $item = CartItem::create($productId, $quantity, false);
        $item->id = $id;
        $item->product = $product;

        return $item;
    }

    #[Test]
    public function it_removes_item_when_quantity_is_zero(): void
    {
        $item = $this->makeItem(7, 10, 2.0);

        $this->storage->shouldReceive('load')->once()->andReturn([$item]);
        $this->storage->shouldReceive('remove')->once()->with(7);

        $this->assertSame(0, $this->useCase->execute(10, 0));
    }

    #[Test]
    public function it_returns_positive_delta_when_quantity_increases(): void
    {
        $item = $this->makeItem(7, 10, 2.0);

        $this->storage->shouldReceive('load')->once()->andReturn([$item]);
        $this->storage->shouldReceive('plus')->once()->with($item, 3.0);
        $this->storage->shouldNotReceive('sub');

        $this->assertSame(3, $this->useCase->execute(10, 5));
    }

    #[Test]
    public function it_returns_negative_delta_when_quantity_decreases(): void
    {
        $item = $this->makeItem(7, 10, 5.0);

        $this->storage->shouldReceive('load')->once()->andReturn([$item]);
        $this->storage->shouldReceive('sub')->once()->with($item, 3.0);
        $this->storage->shouldNotReceive('plus');

        $this->assertSame(-3, $this->useCase->execute(10, 2));
    }

    #[Test]
    public function it_returns_zero_when_item_not_found(): void
    {
        $item = $this->makeItem(7, 10, 2.0);

        $this->storage->shouldReceive('load')->once()->andReturn([$item]);
        $this->storage->shouldNotReceive('plus');
        $this->storage->shouldNotReceive('sub');
        $this->storage->shouldNotReceive('remove');

        $this->assertSame(0, $this->useCase->execute(99, 5));
    }
}
