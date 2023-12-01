<?php
declare(strict_types=1);

namespace App\Modules\Shop\Cart\Storage;

use App\Modules\Shop\Cart\CartItem;
use App\Modules\User\Entity\CartStorage;
use App\Modules\User\Service\CartStorageService;
use App\Modules\User\Service\ReserveService;
use Illuminate\Support\Facades\Auth;

class DBStorage implements StorageInterface
{
    private int|null $user_id;
    private ReserveService $reserveService;
    private CartStorageService $cartStorageService;

    public function __construct(ReserveService $reserveService, CartStorageService $cartStorageService)
    {
        if (!Auth::guard('user')->check())
            throw new \DomainException('Неправильный вызов DBStorage, user == null');
        $this->user_id = Auth::guard('user')->user()->id;
        $this->reserveService = $reserveService;
        $this->cartStorageService = $cartStorageService;
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
                is_null($item->reserve_id) ? null : $item->reserve,
            );
        }
        return $result;
    }

    public function add(CartItem $item): void
    {
        $reserve = $this->reserveService->toReserve($item->getProduct(), $item->getQuantity());
        $this->cartStorageService->toStorage($this->user_id, $item->getProduct(), $item->getQuantity(), $reserve->id, $item->options);
    }

    public function sub(CartItem $item, int $quantity): void
    {
        if (!is_null($item->reserve)) $this->reserveService->subReserve($item->reserve->id, $quantity);

        $new_quantity = $item->quantity - $quantity;
        $this->cartStorageService->updateStorage($item->id, $new_quantity);
    }

    public function plus(CartItem $item, int $quantity): void
    {
        if (!is_null($item->reserve)) $this->reserveService->addReserve($item->reserve->id, $quantity);

        $new_quantity = $item->quantity + $quantity;
        $this->cartStorageService->updateStorage($item->id, $new_quantity);
    }

    public function remove(CartItem $item): void
    {
        if (!is_null($item->reserve)) $this->reserveService->deleteById($item->reserve->id);
        $this->cartStorageService->fromStorage($item->id);
    }

    public function clear(): void
    {
        $this->reserveService->clearByUser($this->user_id);
        $this->cartStorageService->clearByUser($this->user_id);
    }
}
