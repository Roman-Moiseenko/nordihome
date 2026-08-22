<?php

namespace App\Modules\Cart\Tests\Unit\Domain\Entities;

use App\Modules\Cart\Domain\Entities\CartItem;
use App\Modules\Catalog\Infrastructure\Models\Product;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class CartItemTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Создаёт мок Eloquent-модели Product без обращения к БД.
     * Чтение свойств идёт через реальный Model::__get -> getAttribute(), который мы мокаем.
     */
    private function mockProduct(
        int $id,
        float $price = 100.0,
        ?float $quantitySell = null,
        mixed $parser = null,
    ): Product {
        $product = Mockery::mock(Product::class);
        $product->shouldReceive('getAttribute')
            ->andReturnUsing(function (string $key) use ($id, $parser) {
                return match ($key) {
                    'id' => $id,
                    'parser' => $parser,
                    default => null,
                };
            });
        $product->shouldReceive('getPrice')->andReturn($price);

        if ($quantitySell !== null) {
            $product->shouldReceive('getQuantitySell')->andReturn($quantitySell);
        }

        return $product;
    }

    #[Test]
    public function it_creates_item_with_required_fields_and_defaults(): void
    {
        $item = CartItem::create(productId: 10, quantity: 2, is_parser: false);

        $this->assertSame(10, $item->productId);
        $this->assertSame(2.0, $item->quantity);
        $this->assertFalse($item->is_parser);
        $this->assertTrue($item->check);
    }

    #[Test]
    public function it_loads_item_from_storage_with_product(): void
    {
        $product = $this->mockProduct(10, 100.0);

        $item = CartItem::load(1, $product, 2.0, false, true);

        $this->assertSame(1, $item->id);
        $this->assertSame(10, $item->productId);
        $this->assertSame($product, $item->product);
        $this->assertSame(2.0, $item->quantity);
        $this->assertFalse($item->is_parser);
        $this->assertTrue($item->check);
        $this->assertSame(100.0, $item->base_cost);
        $this->assertSame('', $item->discount_name);
        $this->assertSame(0.0, $item->discount_cost);
    }

    #[Test]
    public function it_loads_parser_item_with_parser_base_price(): void
    {
        $parser = new \stdClass();
        $parser->price_base = 50.0;

        $product = $this->mockProduct(10, parser: $parser);

        $item = CartItem::load(1, $product, 1.0, true, true);

        $this->assertTrue($item->is_parser);
        $this->assertSame(50.0, $item->base_cost);
    }

    #[Test]
    public function it_checks_product_by_id(): void
    {
        $item = new CartItem();
        $item->product = $this->mockProduct(10);

        $this->assertTrue($item->isProduct(10));
        $this->assertFalse($item->isProduct(99));
    }

    #[Test]
    public function it_toggles_check_flag(): void
    {
        $item = CartItem::create(10, 2, false);

        $this->assertTrue($item->check);
        $item->check();
        $this->assertFalse($item->check);
        $item->check();
        $this->assertTrue($item->check);
    }

    #[Test]
    public function it_returns_quantity_and_product(): void
    {
        $product = $this->mockProduct(10);
        $item = CartItem::load(1, $product, 3.0, false, true);

        $this->assertSame(3.0, $item->getQuantity());
        $this->assertSame($product, $item->getProduct());
        $this->assertSame(100.0, $item->getBaseCost());
        $this->assertTrue($item->getCheck());
    }

    #[Test]
    public function it_returns_clone_with_new_quantity(): void
    {
        $product = $this->mockProduct(10);
        $item = CartItem::load(1, $product, 2.0, false, true);

        $clone = $item->withQuantity(5.0);

        $this->assertSame(5.0, $clone->quantity);
        $this->assertSame(2.0, $item->quantity);
        $this->assertSame($item->id, $clone->id);
    }

    #[Test]
    public function it_returns_sell_cost(): void
    {
        // Для парсера — всегда базовая цена (скидок нет)
        $parserItem = CartItem::create(10, 1, true);
        $parserItem->base_cost = 100.0;
        $parserItem->discount_cost = 50.0;
        $this->assertSame(100.0, $parserItem->getSellCost());

        // Без скидки — базовая цена
        $plainItem = CartItem::create(10, 1, false);
        $plainItem->base_cost = 100.0;
        $plainItem->discount_cost = 0.0;
        $this->assertSame(100.0, $plainItem->getSellCost());

        // Со скидкой — цена со скидкой
        $discountedItem = CartItem::create(10, 1, false);
        $discountedItem->base_cost = 100.0;
        $discountedItem->discount_cost = 70.0;
        $this->assertSame(70.0, $discountedItem->getSellCost());
    }

    #[Test]
    public function it_manages_discount_metadata(): void
    {
        $item = CartItem::create(10, 1, false);

        $item->setSellCost(70.0);
        $item->setDiscountName('Акция');
        $item->setDiscount(42);
        $item->setDiscountType('promotion');

        $this->assertSame(42, $item->getDiscount());
        $this->assertSame('promotion', $item->getDiscountType());
        $this->assertSame(70.0, $item->discount_cost);
        $this->assertSame('Акция', $item->discount_name);
    }

    #[Test]
    public function it_returns_null_discount_when_not_set(): void
    {
        $item = CartItem::create(10, 1, false);

        $this->assertNull($item->getDiscount());
        $this->assertSame('', $item->getDiscountType());
    }

    #[Test]
    public function it_detects_preorder_and_availability(): void
    {
        $product = $this->mockProduct(10, 100.0, quantitySell: 3.0);

        $item = CartItem::load(1, $product, 5.0, false, true);

        $this->assertTrue($item->preorder());
        $this->assertSame(3.0, $item->availability());
    }

    #[Test]
    public function it_is_not_preorder_when_quantity_fits_availability(): void
    {
        $product = $this->mockProduct(10, 100.0, quantitySell: 5.0);

        $item = CartItem::load(1, $product, 3.0, false, true);

        $this->assertFalse($item->preorder());
    }

    #[Test]
    public function it_reports_parser_flag_and_preorder_flag(): void
    {
        $item = CartItem::create(10, 1, true);

        $this->assertTrue($item->isParser());
        $this->assertFalse($item->getPreorder());
    }
}
