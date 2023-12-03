<?php
declare(strict_types=1);

namespace App\Modules\Discount\Service;

use App\Modules\Discount\Entity\Discount;
use Illuminate\Http\Request;

class DiscountService
{

    public function create(Request $request)
    {
        $discount = Discount::register(
            $request['name'],
            $request['title'] ?? '',
            (int)$request['discount'],
            $request['class'],
            $request['_from'],
            $request['_to'] ?? '',
        );

        return $discount;
    }

    public function update(Request $request, Discount $discount)
    {
        $discount->update([
            'name' => $request['name'],
            'title' => $request['title'] ?? '',
            'discount' => (int)$request['discount'],
            'class' => $request['class'],
            '_from' => $request['_from'],
            '_to' => $request['_to'] ?? '',
        ]);
        $discount->active = false;
        $discount->save();
        return $discount;
    }

    public function delete(Discount $discount)
    {
        if ($discount->active()) throw new \DomainException('Нельзя удалить активную скидку');
        //TODO проверять на использование в продажах и корзине $cartItem->discount_id
        Discount::destroy($discount->id);
    }

    public function draft(Discount $discount)
    {
        if (!$discount->active()) throw new \DomainException('Скидка уже отключена');
        $discount->active = false;
        $discount->save();
    }

    public function published(Discount $discount)
    {
        if ($discount->active) throw new \DomainException('Скидка уже активна');
        $discount->active = true;
        $discount->save();
    }
}
