<?php
declare(strict_types=1);

namespace App\Modules\Shop\Cart\Storage;

use App\Modules\Product\Entity\Product;
use App\Modules\Shop\Cart\CartItem;
use App\Modules\User\Entity\CartStorage;

use Illuminate\Support\Facades\Auth;

class DBStorage implements StorageInterface
{
    private int|null $user_id;

    public function __construct()
    {
        if (!Auth::guard('user')->check())
            throw new \DomainException('Неправильный вызов DBStorage, user == null');
        $this->user_id = Auth::guard('user')->user()->id;
    }

    /** @return CartItem[] */
    public function load(): array
    {
        $items = CartStorage::where('user_id', $this->user_id)->get();
        $result = [];
        /** @var CartStorage $item */
        foreach ($items as $item) {
            $result[] = CartItem::load(
                $item->id,
                $item->product,
                $item->quantity,
                json_decode($item->options_json),
                $item->check
            );
        }
        return $result;
    }

    public function add(CartItem $item): void
    {
        $this->toStorage(
            $this->user_id,
            $item->getProduct(),
            $item->getQuantity(),
            $item->options);
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

    public function remove(CartItem $item): void
    {
        $this->fromStorage($item->id);
    }

    public function clear(): void
    {
        $this->clearByUser($this->user_id);
    }

    public function check(CartItem $item): void
    {
        $this->updateCheck($item->id, $item->check);
    }


    private function clearByUser($id)
    {
        CartStorage::where('user_id', $id)->delete();
    }

    private function toStorage(int $user_id, Product $product, float $quantity, array $options = [])
    {
        CartStorage::register(
            $user_id,
            $product->id,
            $quantity,
            $options
        );
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
        CartStorage::destroy($id);
    }

}
