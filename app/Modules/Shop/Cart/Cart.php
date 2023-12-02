<?php
declare(strict_types=1);

namespace App\Modules\Shop\Cart;

use App\Modules\Product\Entity\Product;
use App\Modules\Shop\Calculate\CalculateInterface;
use App\Modules\Shop\Cart\Storage\HybridStorage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;

class Cart
{
    /** @var CartItem[] $items */
    private array $items;
    private HybridStorage $storage;

    public function __construct(HybridStorage $storage)
    {
        $this->storage = $storage;
    }

    public function getItems(): array
    {
        $this->loadItems();
        return $this->items;
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

    private function loadItems(): void
    {
        if (empty($this->items)) {
            $this->items = $this->storage->load();
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

    public function getCartToFront($tz)
    {
        $timeZone = timezone_name_from_abbr("", (int)$tz * 60, 0);
        $this->items = $this->storage->load();
        $cartItems = $this->getItems();
        //TODO Пересчитать данные для вывода $cartItems

        $result = [];
        foreach ($cartItems as $item) {
            $result[] = [
                'id' => $item->id,
                'img' => is_null($item->getProduct()->photo) ? $item->getProduct()->getImage() : $item->getProduct()->photo->getThumbUrl('thumb'),
                'name' => $item->getProduct()->name,
                'url' => route('shop.product.view', $item->getProduct()->slug),
                'product_id' => $item->getProduct()->id,
                'cost' => number_format($item->getProduct()->lastPrice->value * $item->getQuantity(), 0, ',', ' '),
                'quantity' => $item->getQuantity(),
                'discount_id' => $item->discount_id ?? null,
                'discount_cost' => $item->discount_cost ?? null,
                'discount_name' => $item->discount_name,
                'reserve_date' => !is_null($item->reserve) ? $item->reserve->reserve_at->setTimezone($timeZone)->format('H:i') : '',
                'remove' => route('shop.cart.remove', $item->getProduct()->id),
                'd' => now()->translatedFormat('d F Y'),
            ];
        }
        return $result;
    }

}
