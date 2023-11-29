<?php
declare(strict_types=1);

namespace App\Modules\User\Service;

use App\Modules\Admin\Entity\Options;
use App\Modules\Product\Entity\Product;
use App\Modules\User\Entity\Reserve;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReserveService
{
    private int $hours_reserve;
    private int $user_id;

    public function __construct()
    {
        $this->hours_reserve = (new Options())->shop->reserve_cart;
        $this->user_id = Auth::guard('user')->id();
    }

    public function clearForTimer() //Удаляем все у которых время резерва вышло
    {
        $reserves = Reserve::where('reserve_at', '<', now())->get();
        /** @var Reserve $reserve */
        foreach ($reserves as $reserve) {
            $this->delete($reserve, true);
        }
    }

    public function toReserve(Product $product, int $quantity): Reserve
    {
        DB::beginTransaction();
        try {
            $product->count_for_sell -= $quantity;
            $product->save();
            /** @var Reserve $reserve */
            $reserve = Reserve::where('user_id', $this->user_id)->where('product_id', $product->id)->first();
            if ($reserve) { //Если товар уже есть у клиента в резерве, увеличиваем время и кол-во
                $reserve->updateReserve($quantity, $this->hours_reserve);
            } else {
                $reserve = Reserve::register(
                    $product->id,
                    $quantity,
                    $this->user_id,
                    $this->hours_reserve
                );
            }
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
            Reserve::destroy($reserve->id);
            DB::commit();

            //TODO Оповещение о резерве при удалении по таймеру
            if ($timer) event($user, $product);

        } catch (\Throwable $e) {
            DB::rollBack();
            throw new \DomainException($e->getMessage());
        }
    }
}
