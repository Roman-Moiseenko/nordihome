<?php

namespace App\Modules\Cart\Tests\Unit\Application\Actions;

use App\Modules\Cart\Application\Actions\RemoveCartItemUseCase;
use App\Modules\Cart\Domain\Entities\CartItem;
use App\Modules\Cart\Infrastructure\Persistence\HybridStorage;
use App\Modules\Catalog\Infrastructure\Models\Product;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class RemoveCartItemUseCaseTest extends TestCase
{
    private HybridStorage $storage;
    private RemoveCartItemUseCase $useCase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->storage = Mockery::mock(HybridStorage::class);
        $this->useCase = new RemoveCartItemUseCase($this->storage);
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
    public function it_removes_item_and_returns_its_quantity(): void
    {
        $item = $this->makeItem(7, 10, 4.0);

        $this->storage->shouldReceive('load')->once()->andReturn([$item]);
        $this->storage->shouldReceive('remove')->once()->with(7);

        $this->assertSame(4, $this->useCase->execute(10));
    }

    #[Test]
    public function it_returns_zero_when_item_not_found(): void
    {
        $item = $this->makeItem(7, 10, 4.0);

        $this->storage->shouldReceive('load')->once()->andReturn([$item]);
        $this->storage->shouldNotReceive('remove');

        $this->assertSame(0, $this->useCase->execute(99));
    }
}
