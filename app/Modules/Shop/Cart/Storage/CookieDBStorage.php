<?php
declare(strict_types=1);

namespace App\Modules\Shop\Cart\Storage;

use App\Modules\Catalog\Infrastructure\Models\Product;
use App\Modules\Shop\Cart\CartItem;
use App\Modules\User\Entity\CartCookie;
use Illuminate\Support\Facades\Cookie;

class CookieDBStorage implements StorageInterface
{

    private ?string $user_ui;

    public function __construct()
    {
        $this->user_ui = Cookie::get('user_cookie_id');
        //if (empty($this->user_ui)) throw new \DomainException('Что-то пошло не так, user_cookie_id пустой');
    }

    public function load(): array
    {
        $items = CartCookie::where('user_ui', $this->user_ui)->get();
        $result = [];
        /** @var CartCookie $item */
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
        $this->toStorage($this->user_ui, $item->id, $item->quantity, $item->is_parser);
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
        $this->clearByUser($this->user_ui);
    }

    public function check(CartItem $item): void
    {
        $this->updateCheck($item->id, $item->check);
    }

    private function clearByUser(string $ui): void
    {
        CartCookie::where('user_ui', $ui)->delete();
    }

    private function toStorage(string $user_ui, int $productId, float $quantity, bool $is_parser): void
    {
        CartCookie::register(
            $user_ui,
            $productId,
            $quantity,
            $is_parser
        );
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
        CartCookie::destroy($id);
    }


}
