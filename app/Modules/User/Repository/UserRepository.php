<?php
declare(strict_types=1);

namespace App\Modules\User\Repository;

use App\Modules\Base\Entity\FileStorage;
use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Entity\Order\OrderAddition;
use App\Modules\User\Entity\Subscription;
use App\Modules\User\Entity\User;
use App\Modules\User\Entity\Wish;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use JetBrains\PhpStorm\ArrayShape;

class UserRepository
{

    public function getIndex(Request $request, &$filters): Arrayable
    {
        $query = User::orderByDesc('id');
        $filters = [];
        if (($name = $request->string('name')) != '') {
            $filters['name'] = $name;
            $query->where('phone', 'LIKE', "%$name%")
                ->orWhere('email', 'like', "%$name%")
                ->orWhereRaw("LOWER(fullname) like LOWER('%$name%')")
                ->orWhereHas('organizations', function ($query) use($name) {
                    $query->where('inn', $name)->orWhereRaw("LOWER(short_name) like LOWER('%$name%')");
                });
        }
        if (($address = $request->string('address')) != '') {
            $filters['address'] = $address;
            $query->whereRaw("LOWER(address) like LOWER('%$address%')");
        }
        if (($client = $request->integer('client')) > 0) {
            $filters['client'] = $client;
            $query->where('client', $client);
        }
        if ($request->has('wait')) {
            $wait = $request->string('wait');
            $filters['wait'] = $wait;
            $query->where('active', false);
        }

        if (count($filters) > 0) $filters['count'] = count($filters);

        return $query->paginate($request->input('size', 20))
            ->withQueryString()
            ->through(fn(User $user) => $this->UserToArray($user));

    }

    public function UserToArray(User $user): array
    {

        return array_merge($user->toArray(), [
            'name' => $user->getPublicName(),
            'data' => $this->getOrderData($user->id),
            'last_order' => is_null($user->getLastOrder()) ? null : $this->dataLastOrder($user->getLastOrder()->created_at),
            'pricing' => $user->pricingText(),
            'quantity' => $user->orders()->count(),
            'amount' => $user->getAmountOrders(),
            'delivery_name' => $user->deliveryText(),
            'organization' => $user->organization,
        ]);
    }

    public function UserWithToArray(User $user, Request $request): array
    {
        return array_merge($this->UserToArray($user), [
            'organizations' => $user->organizations,
            'orders' => $user->orders()->paginate($request->input('size', 20))
                ->withQueryString()
                ->through(fn(Order $order) => [
                    'id' => $order->id,
                    'created_at' => $order->created_at,
                    'quantity' => $order->getQuantity(),
                    'amount' => $order->getTotalAmount(),
                    'payment' => $order->getPaymentAmount(),
                    'status' => $order->statusHtml(),
                    'items' => $order->items()->with('product')->get()->toArray(),
                    'additions' => $order->additions()->get()->map(function (OrderAddition $addition) {
                        return array_merge($addition->toArray(), [
                            'purposeText' => $addition->addition->name,
                        ]);
                    }),
                ]),
            'files' => ($user->files()->count() == 0)
                ? []
                : $user->files()->get()->map(function (FileStorage $file) {
                    return [
                        'id' => $file->id,
                        'url' => $file->getUploadFile(),
                        'title' => $file->title,
                    ];
                }),

        ]);
    }

    public function getWish(User $user): array
    {
        return array_map(function (Wish $wish) {
            return [
                'img' => $wish->product->getImage('thumb'),
                'name' => $wish->product->name,
                'url' => route('shop.product.view', $wish->product),
                'cost' => $wish->product->getPrice(),
                'remove' => route('cabinet.wish.toggle', $wish->product),
                'product_id' => $wish->product->id,
            ];
        }, $user->wishes()->getModels());
    }

    public function getUsersBySubscription(string $class): array
    {
        $subscription = Subscription::where('listener', $class)->first();

        return User::where('active', true)->whereHas('subscriptions', function ($query) use ($subscription) {
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


    private function dataLastOrder(Carbon $date): array
    {
        $diff = $date->diff(now());
        $days = $date->diff(now())->dayz;

      //  if ($diff->da == false) $days = 0;//dd([$date->diff(now())->minutes, $days]);
        if ($diff->years > 0)
            return [
                'type' => 'danger',
                'text' => $diff->years . ' л.',
            ];
        if ($diff->months > 0) return [
            'type' => 'warning',
            'text' => $diff->months . ' м.',
        ];
       return [
            'type' => 'primary',
            'text' => $days . ' д.',
        ];

    }

    public function search(string $value): Arrayable
    {
        $query = User::where('phone', 'like', "%$value%")
            ->orWhere('email', 'like', "%$value%")
            ->orWhereRaw("LOWER(fullname) like LOWER('%$value%')")
            ->orWhereHas('organizations', function ($query) use ($value) {
                $query->where('inn', 'LIKE', "%$value%")
                    ->orWhereRaw("LOWER(short_name) like LOWER('%$value%')")
                    ->orWhereRaw("LOWER(full_name) like LOWER('%$value%')")
                    ->orWhereHas('contacts', function ($query) use ($value) {
                        $query->where('email', 'like', "%$value%")
                            ->where('phone', 'like', "%$value%")
                            ->orWhereRaw("LOWER(fullname) like LOWER('%$value%')");
                    });
            });

        return $query->get()->map(function (User $user) {
            return array_merge($user->toArray(), [
                'public_name' => $user->getPublicName(),
            ]);
        });
    }
}
