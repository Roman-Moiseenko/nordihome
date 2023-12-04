<?php
declare(strict_types=1);

namespace App\Modules\Shop\Cart\Storage;

use App\Modules\Shop\Cart\CartItem;
use App\Modules\User\Entity\CartCookie;
use App\Modules\User\Service\CartCookieService;
use Illuminate\Support\Facades\Cookie;

class CookieDBStorage implements StorageInterface
{

    private ?string $user_ui;
    private CartCookieService $cartCookieService;

    public function __construct(CartCookieService $cartCookieService)
    {
        $this->user_ui = Cookie::get('user_cookie_id');
        if (empty($this->user_ui)) throw new \DomainException('Что-то пошло не так, user_cookie_id пустой');
        $this->cartCookieService = $cartCookieService;
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
                json_decode($item->options_json)
            );
        }
        return $result;
    }

    public function add(CartItem $item): void
    {
        $this->cartCookieService->toStorage($this->user_ui, $item->getProduct(), $item->getQuantity(), $item->options);
    }

    public function sub(CartItem $item, int $quantity): void
    {
        $new_quantity = $item->quantity - $quantity;
        $this->cartCookieService->updateStorage($item->id, $new_quantity);
    }

    public function plus(CartItem $item, int $quantity): void
    {
        $new_quantity = $item->quantity + $quantity;
        $this->cartCookieService->updateStorage($item->id, $new_quantity);
    }

    public function remove(CartItem $item): void
    {
        $this->cartCookieService->fromStorage($item->id);
    }

    public function clear(): void
    {
        $this->cartCookieService->clearByUser($this->user_ui);
    }
}
