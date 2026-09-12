<?php
declare(strict_types=1);

namespace App\Modules\Cart\Infrastructure\Persistence;

use App\Modules\Cart\Domain\Entities\CartItemEntity;
use App\Modules\Cart\Domain\Interfaces\StorageInterface;
use App\Modules\Cart\Infrastructure\Models\CartStorage;

class DBStorage implements StorageInterface
{
    private int|null $clientId;

    public function __construct()
    {
        if (!auth()->check())
            throw new \DomainException('Неправильный вызов DBStorage, user == null');
        $this->clientId = auth()->user()->profileable_id;
    }

    /** @return CartItemEntity[] */
    public function load(): array
    {
        $items = CartStorage::where('client_id', $this->clientId)->get();
        $result = [];
        /** @var CartStorage $item */
        foreach ($items as $item) {
            $cartItem = new CartItemEntity(
                $item->product_id,
                (float)$item->quantity,
                $item->is_parser);
            $cartItem->id = $item->id;
            $cartItem->check = $item->check;

            $result[] = $cartItem;
        }
        return $result;
    }

    public function add(CartItemEntity $item): void
    {
        CartStorage::register(
            $this->clientId,
            $item->productId,
            $item->quantity,
            $item->isParser
        );
    }

    public function sub(CartItemEntity $item, float $quantity): void
    {
        $new_quantity = $item->quantity - $quantity;
        $this->updateQuantity($item->id, $new_quantity);
    }

    public function plus(CartItemEntity $item, float $quantity): void
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
        $this->clearByUser($this->clientId);
    }

    public function check(CartItemEntity $item): void
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
