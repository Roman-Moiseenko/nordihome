<?php

namespace App\Modules\Cart\Tests\Unit\Application\Actions;

use App\Modules\Cart\Application\Actions\SubToCartUseCase;
use App\Modules\Cart\Domain\Entities\CartItem;
use App\Modules\Cart\Infrastructure\Persistence\HybridStorage;
use App\Modules\Catalog\Infrastructure\Models\Product;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class SubToCartUseCaseTest extends TestCase
{
    private HybridStorage $storage;
    private SubToCartUseCase $useCase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->storage = Mockery::mock(HybridStorage::class);
        $this->useCase = new SubToCartUseCase($this->storage);
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
    public function it_decrements_matching_item_quantity(): void
    {
        $item = $this->makeItem(1, 10, 5.0);

        $this->storage->shouldReceive('load')->once()->andReturn([$item]);
        $this->storage->shouldReceive('sub')->once()->with($item, 2);

        $this->useCase->execute(10, 2);
        $this->addToAssertionCount(1);
    }

    #[Test]
    public function it_does_nothing_when_item_not_found(): void
    {
        $item = $this->makeItem(1, 10, 5.0);

        $this->storage->shouldReceive('load')->once()->andReturn([$item]);
        $this->storage->shouldNotReceive('sub');

        $this->useCase->execute(99, 2);
        $this->addToAssertionCount(1);
    }
}
