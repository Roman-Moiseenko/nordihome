<?php

namespace App\Modules\Cart\Tests\Unit\Application\Actions;

use App\Modules\Cart\Application\Actions\CheckToCartUseCase;
use App\Modules\Cart\Domain\Entities\CartItemEntity;
use App\Modules\Cart\Infrastructure\Persistence\CartRepository;
use App\Modules\Catalog\Infrastructure\Models\Product;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class CheckToCartUseCaseTest extends TestCase
{
    private CartRepository $storage;
    private CheckToCartUseCase $useCase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->storage = Mockery::mock(CartRepository::class);
        $this->useCase = new CheckToCartUseCase($this->storage);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    private function makeItem(int $id, int $productId, bool $check = true): CartItemEntity
    {
        $product = Mockery::mock(Product::class);
        $product->shouldReceive('getAttribute')->with('id')->andReturn($productId);

        $item = CartItemEntity::create($productId, 1, false);
        $item->id = $id;
        $item->product = $product;
        $item->check = $check;

        return $item;
    }

    #[Test]
    public function it_toggles_check_of_matching_item(): void
    {
        $item = $this->makeItem(1, 10, true);

        $this->storage->shouldReceive('load')->once()->andReturn([$item]);
        $this->storage->shouldReceive('check')->once()->with($item);

        $this->useCase->execute(10);

        $this->assertFalse($item->check);
    }

    #[Test]
    public function it_does_not_touch_non_matching_items(): void
    {
        $item = $this->makeItem(1, 10, true);

        $this->storage->shouldReceive('load')->once()->andReturn([$item]);
        $this->storage->shouldNotReceive('check');

        $this->useCase->execute(99);

        $this->assertTrue($item->check);
    }

    #[Test]
    public function it_does_nothing_when_cart_is_empty(): void
    {
        $this->storage->shouldReceive('load')->once()->andReturn([]);
        $this->storage->shouldNotReceive('check');

        $this->useCase->execute(10);
        $this->addToAssertionCount(1);
    }
}
