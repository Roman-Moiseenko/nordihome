<?php
declare(strict_types=1);

namespace App\Modules\User\Repository;

use App\Modules\User\Entity\Subscription;
use App\Modules\User\Entity\User;
use App\Modules\User\Entity\Wish;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use JetBrains\PhpStorm\ArrayShape;

class UserRepository
{
    public function getWish(User $user): array
    {
        return array_map(function (Wish $wish) {
            return [
                'img' => $wish->product->photo->getThumbUrl('thumb'),
                'name' => $wish->product->name,
                'url' => route('shop.product.view', $wish->product),
                'cost' => $wish->product->getLastPrice(),
                'remove' => route('cabinet.wish.toggle', $wish->product),
                'product_id' => $wish->product->id,
            ];
        }, $user->wishes()->getModels());
    }

    public function getIndex(Request $request)
    {
        $query = User::orderByDesc('id');
        if (!empty($value = $request->get('id'))) {
            $query->where('id', $value);
        }
        if (!empty($value = $request->get('phone'))) {
            $query->where('phone', 'like', '%' . $value . '%');
        }
        if (!empty($value = $request->get('email'))) {
            $query->where('email', 'like', '%' . $value . '%');
        }
        if (!empty($value = $request->get('status'))) {
            $query->where('status', $value);
        }
        return $query;
    }

    public function getUsersBySubscription(string $class)
    {
        $subscription = Subscription::where('listener', $class)->first();

        return User::where('status', User::STATUS_ACTIVE)->whereHas('subscriptions', function ($query) use ($subscription) {
            $query->where('subscription_id', $subscription->id);
        })->getModels();
    }

    #[ArrayShape(['last' => "string", 'count' => "int", 'amount' => "string"])]
    public function getOrderData(int $id): array
    {
        /** @var User $user */
        $user = User::find($id);

        if (is_null($user->getLastOrder())) {
            $last = '<span class="text-slate-500  text-xs p-1 mt-0.5 rounded-full text-white bg-secondary" > нет</span >';
        } else {
            if (($days = $user->getLastOrder()->created_at->diff(now())->days) < 30) {
                $last = '<span class="text-slate-500  text-xs p-1 mt-0.5 rounded-full text-white bg-success" >' . $days . ' дней </span >';
            } else {
                $last = '<span class="text-slate-500  text-xs p-1 mt-0.5 rounded-full text-white bg-warning" >' .
                    (int)($days / 30) . 'месяцев</span >';
            }
        }

        return [
            'last' => $last,
            'count' => $user->orders()->count(),
            'amount' => price($user->getAmountOrders())
        ];
    }

    public function findOrCreate(string $phone = null, string $email = null): User
    {
        if (empty($phone) && empty($email)) throw new \DomainException('Должен быть заполнен хотя бы один параметр');

        if (!empty($phone)) {
            if (is_null($user = User::where('phone', $phone)->get())) {
                $user = User::create([
                    'phone' => $phone,
                    'email' => $email,
                    'password' => Str::random(8),
                ]);
            }
            return $user;
        }

        if (!empty($email)) {

            if (is_null($user = User::where('email', $email)->get())) {
                $user = User::create([
                    'phone' => $phone,
                    'email' => $email,
                    'password' => Str::random(8),
                ]);
            }
            return $user;

        }

        throw new \DomainException('Что-то пошло не так');

    }
}
