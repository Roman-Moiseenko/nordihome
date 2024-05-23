<?php

namespace App\Listeners;

use App\Events\PricingHasCompleted;
use App\Mail\ProductArrival;
use App\Mail\ProductPricing;
use App\Modules\Accounting\Entity\PricingProduct;
use App\Modules\Admin\Entity\Responsibility;
use App\Modules\Admin\Repository\StaffRepository;
use App\Modules\User\Service\SubscriptionService;
use App\Modules\User\UserRepository;
use App\Notifications\StaffMessage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class NotificationPricingCompleted
{
    private array $users;
    private StaffRepository $staffRepository;

    public function __construct(SubscriptionService $subscriptionService, UserRepository $userRepository, StaffRepository $staffRepository)
    {
        if ($subscriptionService->check_subscription(self::class)) {
            $this->users = $userRepository->getUsersBySubscription(self::class);
        } else {
            $this->users = [];
        }
        $this->staffRepository = $staffRepository;
    }

    /**
     * Handle the event.
     */
    public function handle(PricingHasCompleted $event): void
    {
        $staffs = $this->staffRepository->getStaffsByCode(Responsibility::MANAGER_ORDER);

        foreach ($staffs as $staff) {
            $staff->notify(new StaffMessage(
                'Изменились цены на товар',
                'Необходимо сверить с наличием и распечатать ценники',
                route('admin.accounting.pricing.show', $event->pricing),
                'badge-russian-ruble'
            ));
        }

        if (!empty($this->users)) {

            $products = []; //Список товаров в поступлении, которых не было

            foreach ($event->pricing->pricingProducts as $pricingProduct) {
                if ($pricingProduct->product->getPriceRetail() < $pricingProduct->product->getPriceRetail(true)) { //Снизилась цен
                    $products[] = $pricingProduct->product;
                }
            }
            foreach ($this->users as $user) { //Проверяем всех пользователей, кто подписан на уведомление
                $user_products = []; //Список товаров из Избранное, которые есть в Поступлении
                foreach ($products as $product) {
                    if ($user->isWish($product->id)) $user_products[] = $product; //Если есть в избранном, добавляем на уведомление
                }
                if (!empty($user_products)) //Одним письмом клиенту, о поступлении товаров из избранного
                    Mail::to($user->email)->queue(new ProductPricing($user_products, $user));
            }
        }
    }
}
