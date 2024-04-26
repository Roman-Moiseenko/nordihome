<?php
declare(strict_types=1);

namespace App\Modules\Shop\Cart;

use App\Modules\Product\Entity\Product;
use App\Modules\Shop\Calculate\CalculatorOrder;
use App\Modules\Shop\Cart\Storage\HybridStorage;
use JetBrains\PhpStorm\ArrayShape;


class Cart
{
    /** @var CartItem[] $items */
    private array $items;
    /** @var CartItem[] $itemsOrder */
    private array $itemsOrder;
    /** @var CartItem[] $itemsPreOrder */
    private array $itemsPreOrder;

    private HybridStorage $storage;
    private CalculatorOrder $calculator;
    public CartInfo $info;


    public function __construct(HybridStorage $storage, CalculatorOrder $calculator)
    {
        $this->storage = $storage;
        $this->calculator = $calculator;
        $this->info = new CartInfo();
        $this->itemsOrder = [];
        $this->itemsPreOrder = [];
    }

    public function getItems(): array
    {
        $this->loadItems();
        return $this->items;
    }

    public function getOrderItems(): array
    {
        $this->loadItems();
        return $this->itemsOrder;
    }

    public function getPreOrderItems(): array
    {
        $this->loadItems();
        return $this->itemsPreOrder;
    }

    public function add(Product $product, $quantity, array $options)
    {
        $this->loadItems();
        foreach ($this->items as $i => $current) {
            if ($current->isProduct($product->id)) {
                $this->storage->plus($current, $quantity);
                return;
            }
        }
        $this->storage->add(CartItem::create($product, $quantity, $options));
    }

    public function plus(Product $product, $quantity)
    {
        $this->loadItems();
        foreach ($this->items as $i => $current) {
            if ($current->isProduct($product->id)) {
                $this->storage->plus($current, $quantity);
                return;
            }
        }
    }

    public function sub(Product $product, $quantity)
    {
        $this->loadItems();
        foreach ($this->items as $i => $current) {
            if ($current->isProduct($product->id)) {
                $this->storage->sub($current, $quantity);
                return;
            }
        }
        throw new \DomainException('Элемент не найден');
    }

    public function set(Product $product, $quantity)
    {
        if ($quantity == 0) {
            $this->remove($product);
            return;
        }
        $old_quantity = $this->getQuantity($product->id);
        if ($quantity > $old_quantity) $this->plus($product, $quantity - $old_quantity);
        if ($quantity < $old_quantity) $this->sub($product, $old_quantity - $quantity);
    }

    public function remove(Product $product)
    {
        $this->loadItems();
        foreach ($this->items as $i => $current) {
            if ($current->isProduct($product->id)) {
                $this->storage->remove($current);
                return;
            }
        }
    }

    public function clear()
    {
        $this->storage->clear();
    }

    public function clearOrder(): void
    {
        foreach ($this->itemsOrder as $item) {
            $this->storage->sub($item, $item->quantity); //Очищаем напрямую позицию
        }
    }

    public function clearPreOrder(): void
    {
        foreach ($this->itemsPreOrder as $item) {
            $this->storage->sub($item, $item->quantity);
        }
    }

    public function loadItems(): void
    {
        if (empty($this->items)) {
            $this->items = $this->storage->load();
            $this->itemsOrder = [];
            $this->itemsPreOrder = [];
            foreach ($this->items as $item) {
                if ($item->check) {
                    if ($item->preorder()) {
                        $this->itemsPreOrder[] = $item->withQuantity($item->quantity - $item->availability())->withNotReserve();
                        $this->itemsOrder[] = $item->withQuantity($item->availability());
                    } else {
                        $this->itemsOrder[] = $item->withQuantity($item->quantity);
                    }
                }
            }

            $this->items = $this->calculator->calculate($this->items);
            $this->itemsOrder = $this->calculator->calculate($this->itemsOrder);
            $this->info->clear();

            $this->info->order = $this->calcInfoBlock($this->itemsOrder);

            $this->info->pre_order = $this->calcInfoBlock($this->itemsPreOrder);
            $this->info->all = $this->calcInfoBlock($this->items);

            $this->info->preorder = !empty($this->itemsPreOrder);
            if (($this->info->order->count + $this->info->pre_order->count) != $this->info->all->count) $this->info->check_all = false;
        }
    }

    public function getQuantity(int $product_id): int
    {
        $this->loadItems();
        foreach ($this->items as $current) {
            if ($current->isProduct($product_id)) return $current->getQuantity();
        }
        throw new \DomainException('Товар с id ' . $product_id . ' не найден');
    }

    public function getItem(int $product_id): CartItem
    {
        $this->loadItems();
        foreach ($this->items as $current) {
            if ($current->isProduct($product_id)) return $current;
        }
        throw new \DomainException('Товар с id ' . $product_id . ' не найден');
    }

    #[ArrayShape(['common' => "array", 'items' => "array", 'items_order' => "array", 'items_preorder' => "array"])]
    public function getCartToFront($tz): array
    {
        $this->items = [];
        $this->loadItems();
        return [
            'common' => $this->CommonData(),
            'items' => $this->ItemsData($tz, $this->items),
            'items_order' => $this->ItemsData($tz, $this->itemsOrder),
            'items_preorder' => $this->ItemsData($tz, $this->itemsPreOrder),
        ];

    }

    #[ArrayShape([
        'id' => 'int',
        'img' => 'string',
        'name' => 'string',
        'url' => 'string',
        'product_id' => 'int',
        'cost' => 'float',
        'price' => 'float',
        'quantity' => 'int',
        'discount_id' => 'int|null',
        'discount_cost' => 'float|null',
        'discount_name' => 'string',
        'remove' => 'string',
        'check' => 'bool',
        'available' => 'int|null'
    ])]
    private function ItemsData($tz, array $items): array
    {
        $timeZone = timezone_name_from_abbr("", (int)$tz * 60, 0);
        $result = [];
        /** @var CartItem $item */
        foreach ($items as $item) {
            $result[] = [
                'id' => $item->id,
                'img' => is_null($item->getProduct()->photo) ? $item->getProduct()->getImage() : $item->getProduct()->photo->getThumbUrl('thumb'),
                'name' => $item->getProduct()->name,
                'url' => route('shop.product.view', $item->getProduct()->slug),
                'product_id' => $item->getProduct()->id,
                'cost' => $item->base_cost * $item->getQuantity(),
                'price' => empty($item->discount_cost) ? $item->base_cost : $item->discount_cost,
                'quantity' => $item->getQuantity(),
                'discount_id' => $item->discount_id ?? null,
                'discount_cost' => empty($item->discount_cost) ? null : $item->discount_cost * $item->getQuantity(),
                'discount_name' => $item->discount_name,
                'remove' => route('shop.cart.remove', $item->getProduct()->id),
                'check' => $item->check,
                'available' => ($item->preorder()) ? $item->availability() : null,
            ];
        }
        return $result;
    }

    #[ArrayShape([
        'count' => "int",
        'full_cost' => "float|int",
        'discount' => "float|int",
        'amount' => "float|int",
        'check_all' => "bool",
        'preorder' => "bool",
        'count_preorder' => "int",
        'full_cost_preorder' => "float|int"
    ])]
    private function CommonData(): array
    {
        return [
            'count' => $this->info->order->count, //Кол-во товаров
            'full_cost' => $this->info->order->amount, //Полная стоимость
            'discount' => $this->info->order->discount, //Скидка
            'amount' => $this->info->order->amount - $this->info->order->discount, //Итого со скидкой
            'check_all' => $this->info->check_all,
            'preorder' => $this->info->preorder,

            'count_preorder' => $this->info->pre_order->count,
            'full_cost_preorder' => $this->info->pre_order->amount, //Полная стоимость
        ];
    }

    public function removeByIds(array $ids)
    {
        foreach ($ids as $id) {
            $product = Product::find((int)$id);
            $this->remove($product);
        }
    }

    public function check(Product $product)
    {
        $this->loadItems();
        foreach ($this->items as $current) {
            if ($current->isProduct($product->id)) {
                $current->check();
                $this->storage->check($current);
                return;
            }
        }
    }

    public function check_all(bool $all)
    {
        $this->loadItems();
        foreach ($this->items as $current) {
            $current->check = $all;
            $this->storage->check($current);
        }
    }

    public function setAvailability()
    {
        $this->loadItems();
        foreach ($this->items as $i => $item) {
            if ($item->preorder()) {
                $this->set($item->product, $item->availability());

                if ($item->quantity == 0) {
                    unset($this->items[$i]);
                }
            }
        }
    }

    private function calcInfoBlock(array $items): CartInfoBlock
    {
        $result = new CartInfoBlock();
        /** var CartItem[] $items */
        foreach ($items as $item) {
            $result->count += $item->quantity;
            $result->amount += $item->quantity * $item->product->getLastPrice();
            $result->discount += empty($item->discount_cost) ? 0 : $item->quantity * ($item->base_cost - $item->discount_cost);
        }

        return $result;
    }
}
