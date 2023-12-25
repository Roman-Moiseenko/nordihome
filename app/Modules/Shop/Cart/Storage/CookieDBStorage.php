<?php
declare(strict_types=1);

namespace App\Modules\Shop\Cart\Storage;

use App\Modules\Product\Entity\Product;
use App\Modules\Shop\Cart\CartItem;
use App\Modules\User\Entity\CartCookie;
use App\Modules\User\Service\CartCookieService;
use Illuminate\Support\Facades\Cookie;

class CookieDBStorage implements StorageInterface
{

    private ?string $user_ui;

    public function __construct()
    {
        $this->user_ui = Cookie::get('user_cookie_id');
        if (empty($this->user_ui)) throw new \DomainException('Что-то пошло не так, user_cookie_id пустой');
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
                $item->quantity,
                json_decode($item->options_json),
                $item->check
            );
        }
        return $result;
    }

    public function add(CartItem $item): void
    {
        $this->toStorage($this->user_ui, $item->getProduct(), $item->getQuantity(), $item->options);
    }

    public function sub(CartItem $item, int $quantity): void
    {
        $new_quantity = $item->quantity - $quantity;
        $this->updateQuantity($item->id, $new_quantity);
    }

    public function plus(CartItem $item, int $quantity): void
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

    private function clearByUser(string $ui)
    {
        CartCookie::where('user_ui', $ui)->delete();
    }

    private function toStorage(string $user_ui, Product $product, int $quantity, array $options = [])
    {
        CartCookie::register(
            $user_ui,
            $product->id,
            $quantity,
            $options
        );
    }

    private function updateQuantity(int $id, int $new_quantity)
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
    private function updateCheck(int $id, bool $check)
    {
        $cookie = CartCookie::find($id);
        $cookie->update([
            'check' => $check,
        ]);
    }
    private function fromStorage(int $id)
    {
        CartCookie::destroy($id);
    }


}
