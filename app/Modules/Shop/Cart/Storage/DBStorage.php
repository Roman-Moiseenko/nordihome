<?php
declare(strict_types=1);

namespace App\Modules\Shop\Cart\Storage;

use App\Modules\Shop\Cart\CartItem;
use App\Modules\Shop\Infrastructure\Models\CartStorage;

class DBStorage implements StorageInterface
{
    private int|null $client_id;

    public function __construct()
    {
        if (!auth()->check())
            throw new \DomainException('Неправильный вызов DBStorage, user == null');
        $this->client_id = auth()->user()->profileable_id;
    }

    /** @return CartItem[] */
    public function load(): array
    {
        $items = CartStorage::where('client_id', $this->client_id)->get();
        $result = [];
        /** @var CartStorage $item */
        foreach ($items as $item) {
            $result[] = CartItem::load(
                $item->id,
                $item->product,
                (float)$item->quantity,
                $item->is_parser,
                $item->check
            );
        }
        return $result;
    }

    public function add(CartItem $item): void
    {
        CartStorage::register(
            $this->client_id,
            $item->productId,
            $item->quantity,
            $item->is_parser
        );
    }

    public function sub(CartItem $item, float $quantity): void
    {
        $new_quantity = $item->quantity - $quantity;
        $this->updateQuantity($item->id, $new_quantity);
    }

    public function plus(CartItem $item, float $quantity): void
    {
        $new_quantity = $item->quantity + $quantity;
        $this->updateQuantity($item->id, $new_quantity);
    }

    public function remove(int $itemId): void
    {
        CartStorage::destroy($itemId);
    }

    public function clear(): void
    {
        $this->clearByUser($this->client_id);
    }

    public function check(CartItem $item): void
    {
        $this->updateCheck($item->id, $item->check);
    }


    private function clearByUser($id)
    {
        CartStorage::where('client_id', $id)->delete();
    }


    private function updateQuantity(int $id, float $new_quantity)
    {
        $storage = CartStorage::find($id);
        if ($storage == null) return;
        if ($new_quantity == 0) {
            $storage->delete();
        } else {
            $storage->update([
                'quantity' => $new_quantity,
            ]);
        }
    }

    private function updateCheck(int $id, bool $check)
    {
        $storage = CartStorage::find($id);
        $storage->update([
            'check' => $check,
        ]);
    }

    private function fromStorage(int $id)
    {

    }

}
