<?php

namespace App\Modules\Cart\Tests\Unit\Application\Actions;

use App\Modules\Cart\Application\Actions\AddToCartUseCase;
use App\Modules\Cart\Application\DTOs\AddProductToCartData;
use App\Modules\Cart\Domain\Entities\CartItem;
use App\Modules\Cart\Infrastructure\Persistence\HybridStorage;
use App\Modules\Catalog\Infrastructure\Models\Product;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class AddToCartUseCaseTest extends TestCase
{
    private HybridStorage $storage;
    private AddToCartUseCase $useCase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->storage = Mockery::mock(HybridStorage::class);
        $this->useCase = new AddToCartUseCase($this->storage);
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
    public function it_adds_new_item_to_cart(): void
    {
        $this->storage->shouldReceive('load')->once()->andReturn([]);
        $this->storage->shouldReceive('add')
            ->once()
            ->with(Mockery::on(fn(CartItem $item) => $item->productId === 10
                && $item->quantity === 2.0
                && $item->is_parser === false));

        $dto = new AddProductToCartData(id: 10, quantity: 2, isParser: false);

        $this->useCase->execute($dto);
        $this->addToAssertionCount(1);
    }

    #[Test]
    public function it_increments_quantity_when_item_already_exists(): void
    {
        $existing = $this->makeItem(5, 10, 1.0);

        $this->storage->shouldReceive('load')->once()->andReturn([$existing]);
        $this->storage->shouldReceive('plus')
            ->once()
            ->with($existing, 3);
        $this->storage->shouldNotReceive('add');

        $dto = new AddProductToCartData(id: 10, quantity: 3, isParser: false);

        $this->useCase->execute($dto);
        $this->addToAssertionCount(1);
    }
}
