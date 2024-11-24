<?php
declare(strict_types=1);

namespace App\Modules\User\Service;

use App\Modules\User\Entity\Subscription;
use App\Modules\User\Entity\User;
use Illuminate\Http\Request;

class SubscriptionService
{
    /**
     * Проверяем, есть ли рассылка в базе, если нет, то создаем
     * @param string $class
     * @return bool
     */
    public function check_subscription(string $class): bool
    {
        if (Subscription::where('listener', $class)->count() == 0) {
            Subscription::register($class, '** Заголовок для клиента **', '** Описание для клиента **', $class);
            return false; //Вновь созданная, использовать еще нельзя
        }
        return true;
    }

    public function toggle(User $user, Subscription $subscription)
    {
        if ($user->isSubscription($subscription)) {
            $user->subscriptions()->detach($subscription->id);
        } else {
            $user->subscriptions()->attach($subscription->id);
        }
    }

    public function setInfo(Subscription $subscription, Request $request): void
    {
        $subscription->update([
            'name' => $request->string('name')->trim()->value(),
            'title' => $request->string('title')->trim()->value(),
            'description' => $request->string('description')->trim()->value(),
        ]);
    }
}
