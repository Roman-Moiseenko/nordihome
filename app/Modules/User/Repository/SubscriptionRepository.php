<?php
declare(strict_types=1);

namespace App\Modules\User\Repository;

use App\Modules\User\Entity\Subscription;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;

class SubscriptionRepository
{
    public function getIndex(Request $request): Arrayable
    {
        return Subscription::orderBy('name')->get()
            ->map(fn(Subscription $subscription) => $this->SubscriptionToArray($subscription));
    }

    private function SubscriptionToArray(Subscription $subscription): array
    {
        return array_merge($subscription->toArray(), [
            'count_users' => $subscription->users()->count(),
        ]);
    }

    public function SubscriptionWithToArray(Subscription $subscription): array
    {
        return array_merge($this->SubscriptionToArray($subscription), [

        ]);
    }
}
