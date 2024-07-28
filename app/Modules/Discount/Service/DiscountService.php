<?php
declare(strict_types=1);

namespace App\Modules\Discount\Service;

use App\Modules\Discount\Entity\Discount;
use Illuminate\Http\Request;

class DiscountService
{

    public function create(Request $request)
    {
        return Discount::register(
            $request->string('name')->trim()->value(),
            $request->string('title')->trim()->value(),
            $request->integer('discount'),

            $request->string('class')->trim()->value(),
            $request->string('_from')->trim()->value(),
            $request->string('_to')->trim()->value(),
        );
    }

    public function update(Request $request, Discount $discount)
    {
        $discount->update([
            'name' => $request->string('name')->trim()->value(),
            'title' => $request->string('title')->trim()->value(),
            'discount' => $request->integer('discount'),
            'class' => $request->string('class')->trim()->value(),
            $request->string('_from')->trim()->value(),
            $request->string('_to')->trim()->value(),
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
