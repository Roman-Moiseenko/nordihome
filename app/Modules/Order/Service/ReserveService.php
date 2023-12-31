<?php
declare(strict_types=1);

namespace App\Modules\Order\Service;

use App\Modules\Admin\Entity\Options;
use App\Modules\Order\Entity\Reserve;
use App\Modules\Product\Entity\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use function event;
use function now;

class ReserveService
{

    public function clearByTimer() //Удаляем все у которых время резерва вышло
    {
        $reserves = Reserve::where('reserve_at', '<', now())->get();
        foreach ($reserves as $reserve) {
            $this->delete($reserve, true);
        }
    }

    public function clearByUser(int $user_id)
    {
        $reserves = Reserve::where('user_id', $user_id)->get();
        foreach ($reserves as $reserve) {
            $this->delete($reserve);
        }
    }

    public function toReserve(Product $product, int $quantity, string $type, int $minutes): ?Reserve
    {
        if (!Auth::guard('user')->check())
            throw new \DomainException('Нельзя добавить в резерв для незарегистрированного пользователя');

        $user_id = Auth::guard('user')->user()->id;

        if (Reserve::where('user_id', $user_id)->where('product_id', $product->id)->first())
            throw new \DomainException('Неверная функция добавления в резерв');

        if ($product->count_for_sell == 0) return null;

        DB::beginTransaction();
        try {
            //Проверка на запас для резерва
            if ($product->count_for_sell < $quantity) $quantity = $product->count_for_sell;

            $product->count_for_sell -= $quantity;
            $product->save();

            $reserve = Reserve::register(
                $product->id,
                $quantity,
                $user_id,
                $minutes,
                $type
            );
            DB::commit();
            return $reserve;
        } catch (\Throwable $e) {
            DB::rollBack();
            throw new \DomainException($e->getMessage());
        }
    }

    public function delete(Reserve $reserve, bool $timer = false)
    {
        DB::beginTransaction();
        try {
            $user = $reserve->user;
            $product = $reserve->product;
            $product->count_for_sell += $reserve->quantity;
            $product->save();
            if ($reserve->type == Reserve::TYPE_CART) $reserve->cart->clearReserve();
            if ($reserve->type == Reserve::TYPE_ORDER) {
                //TODO Заказ поставить отмененным
                $reserve->orderItem->clearReserve();
                $reserve->orderItem->order->checkOutReserve();
            }
            Reserve::destroy($reserve->id);

            DB::commit();
            //TODO Оповещение о резерве при удалении по таймеру
            if ($timer) event($user, $product);

        } catch (\Throwable $e) {
            DB::rollBack();
            throw new \DomainException($e->getMessage());
        }
    }

    public function deleteById(int $reserve_id)
    {
        $reserve = Reserve::find($reserve_id);
        $this->delete($reserve);
    }

    public function subReserve(int $reserve_id, int $quantity)
    {
        /** @var \App\Modules\Order\Entity\Reserve $reserve */
        DB::beginTransaction();
        try {
            $reserve = Reserve::find($reserve_id);
            $product = $reserve->product;
            $product->count_for_sell += $quantity;
            $product->save();
            $reserve->quantity -= $quantity;
            $reserve->save();
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw new \DomainException($e->getMessage());
        }
    }

    public function addReserve(int $reserve_id, int $quantity)
    {
        /** @var \App\Modules\Order\Entity\Reserve $reserve */
        DB::beginTransaction();
        try {
            $reserve = Reserve::find($reserve_id);
            $product = $reserve->product;
            if ($product->count_for_sell < $quantity) $quantity = $product->count_for_sell;

            $product->count_for_sell -= $quantity;
            $product->save();

            $reserve->quantity += $quantity;
            $reserve->save();

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw new \DomainException($e->getMessage());
        }
    }
}
