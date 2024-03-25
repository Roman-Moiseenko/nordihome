<?php
declare(strict_types=1);

namespace App\Modules\Order\Service;

use App\Events\OrderHasCanceled;
use App\Events\ThrowableHasAppeared;
use App\Modules\Admin\Entity\Options;
use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Entity\Order\OrderStatus;
use App\Modules\Order\Entity\Reserve;
use App\Modules\Product\Entity\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use JetBrains\PhpStorm\Deprecated;
use function event;
use function now;

class ReserveService
{

    #[Deprecated]
    public function clearByTimer() //Удаляем все у которых время резерва вышло
    {
        /** @var Reserve[] $reserves */
        $reserves = Reserve::where('reserve_at', '<', now())->get();
        foreach ($reserves as $reserve) {
            if ($reserve->type == Reserve::TYPE_ORDER) $order = $reserve->orderItem->order;
            $this->delete($reserve);
            if (isset($order) && $order->checkOutReserve()) {
                $order->setStatus(OrderStatus::CANCEL, 'Закончилось время резерва');
                event(new OrderHasCanceled($order));
            }
        }
    }

    public function clearByUser(int $user_id)
    {
        $reserves = Reserve::where('user_id', $user_id)->get();
        foreach ($reserves as $reserve) {
            $this->delete($reserve);
        }
    }

    public function toReserve(Product $product, int $quantity, string $type, int $minutes, int $userId = null): ?Reserve
    {
        if (!Auth::guard('user')->check() && $userId == null)
            throw new \DomainException('Нельзя добавить в резерв без ID пользователя');
        $user_id = $userId ?? Auth::guard('user')->user()->id;

        if (Reserve::where('user_id', $user_id)->where('product_id', $product->id)->first())
            throw new \DomainException('Товар в резерве по неисполненному заказу. Добавить новый невозможно. Дождитесь исполнения');

        if ($product->count_for_sell == 0) return null;


        //Проверка на запас для резерва
        if ($product->count_for_sell < $quantity) $quantity = $product->count_for_sell;

        $product->count_for_sell -= $quantity;
        $product->save();

        return Reserve::register(
            $product->id,
            $quantity,
            $user_id,
            $minutes,
            $type
        );
    }

    public function delete(Reserve $reserve)
    {
        $product = $reserve->product;
        $product->count_for_sell += $reserve->quantity;
        $product->save();
        if ($reserve->type == Reserve::TYPE_CART) $reserve->cart->clearReserve();
        if ($reserve->type == Reserve::TYPE_ORDER) $reserve->orderItem->clearReserve();
        Reserve::destroy($reserve->id);
    }

    public function deleteById(int $reserve_id)
    {
        $reserve = Reserve::find($reserve_id);
        $this->delete($reserve);
    }

    public function subReserve(int $reserve_id, int $quantity)
    {
        /** @var Reserve $reserve */
        $reserve = Reserve::find($reserve_id);
        $product = $reserve->product;
        $product->count_for_sell += $quantity;
        $product->save();
        $reserve->quantity -= $quantity;
        $reserve->save();
    }

    public function addReserve(int $reserve_id, int $quantity)
    {
        /** @var Reserve $reserve */
        $reserve = Reserve::find($reserve_id);
        $product = $reserve->product;
        if ($product->count_for_sell < $quantity) $quantity = $product->count_for_sell;

        $product->count_for_sell -= $quantity;
        $product->save();

        $reserve->quantity += $quantity;
        $reserve->save();

    }
}
