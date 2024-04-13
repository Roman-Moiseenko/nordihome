<?php
declare(strict_types=1);

namespace App\Modules\Shop\Cart\Storage;

use App\Modules\Admin\Entity\Options;
use App\Modules\Order\Entity\Reserve;
use App\Modules\Order\Service\ReserveService;
use App\Modules\Product\Entity\Product;
use App\Modules\Shop\Cart\CartItem;
use App\Modules\User\Entity\CartStorage;

use Illuminate\Support\Facades\Auth;

class DBStorage implements StorageInterface
{
    private int|null $user_id;
    private ReserveService $reserveService;
    private $minutes;

    public function __construct(ReserveService $reserveService)
    {
        if (!Auth::guard('user')->check())
            throw new \DomainException('Неправильный вызов DBStorage, user == null');
        $this->user_id = Auth::guard('user')->user()->id;
        $this->reserveService = $reserveService;
        $this->minutes = (new Options())->shop->reserve_cart;
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
                $item->check,
                is_null($item->reserve_id) ? null : $item->reserve,
            );
        }
        return $result;
    }

    public function add(CartItem $item): void
    {
        if ($this->minutes > 0) {
            $reserve = $this->reserveService->toReserve($item->getProduct(), $item->getQuantity(), Reserve::TYPE_CART, $this->minutes);
        } else {
            $reserve = null;
        }

        $this->toStorage(
            $this->user_id,
            $item->getProduct(),
            $item->getQuantity(),
            is_null($reserve) ? null : $reserve->id,
            $item->options);
    }

    public function sub(CartItem $item, int $quantity): void
    {

        $new_quantity = $item->quantity - $quantity;

        if ($this->minutes > 0 && !is_null($item->reserve)) {
            $_reserve = $item->reserve->quantity;
            if ($new_quantity < $_reserve)
                $this->reserveService->subReserve($item->reserve->id, $_reserve - $new_quantity);
        }

        $this->updateQuantity($item->id, $new_quantity);
    }

    public function plus(CartItem $item, int $quantity): void
    {
        if ($this->minutes > 0 && !is_null($item->reserve)) $this->reserveService->addReserve($item->reserve->id, $quantity);

        $new_quantity = $item->quantity + $quantity;
        $this->updateQuantity($item->id, $new_quantity);
    }

    public function remove(CartItem $item): void
    {
        if ($this->minutes > 0 && !is_null($item->reserve)) $this->reserveService->deleteById($item->reserve->id);
        $this->fromStorage($item->id);
    }

    public function clear(): void
    {
        if ($this->minutes > 0) $this->reserveService->clearByUser($this->user_id);
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

    private function toStorage(int $user_id, Product $product, int $quantity, ?int $reserve_id, array $options = [])
    {
        CartStorage::register(
            $user_id,
            $product->id,
            $quantity,
            $reserve_id,
            $options
        );
    }

    private function updateQuantity(int $id, int $new_quantity)
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
