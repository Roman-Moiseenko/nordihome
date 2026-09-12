<?php
declare(strict_types=1);

namespace App\Modules\Cart\Infrastructure\Persistence;

use App\Modules\Cart\Domain\Entities\CartItemEntity;
use App\Modules\Cart\Domain\Interfaces\StorageInterface;
use App\Modules\Cart\Infrastructure\Models\CartCookie;
use Illuminate\Support\Facades\Cookie;

class CookieDBStorage implements StorageInterface
{

    private ?string $uuid;

    public function __construct()
    {
        $this->uuid = Cookie::get('user_cookie_id');
    }

    public function load(): array
    {
        $items = CartCookie::where('user_ui', $this->uuid)->get();
        $result = [];
        /** @var CartCookie $item */
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
        CartCookie::register(
            $this->uuid,
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
        CartCookie::destroy($itemId);
    }

    public function clear(): void
    {
        $this->clearByUser($this->uuid);
    }

    public function check(CartItemEntity $item): void
    {
        $this->updateCheck($item->id, $item->check);
    }

    private function clearByUser(string $ui): void
    {
        CartCookie::where('user_ui', $ui)->delete();
    }

    private function updateQuantity(int $id, float $new_quantity): void
    {
        $cookie = CartCookie::find($id);
        if ($new_quantity == 0) {
            $cookie->delete();
        } else {
            $cookie->update([
                'quantity' => $new_quantity,
            ]);
        }
    }

    private function updateCheck(int $id, bool $check): void
    {
        $cookie = CartCookie::find($id);
        $cookie->update([
            'check' => $check,
        ]);
    }

    private function fromStorage(int $id): void
    {

    }


}
