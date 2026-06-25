<?php

namespace App\Listeners;

use App\Events\PricingHasCompleted;
use App\Modules\Auth\Application\Actions\Staff\ListStaffByPositionUseCase;
use App\Modules\Auth\Domain\ValueObjects\StaffPosition;
use App\Modules\User\Repository\UserRepository;
use App\Modules\User\Service\SubscriptionService;

class NotificationPricingCompleted
{
    private array $users;
    private ListStaffByPositionUseCase $positionUseCase;

    public function __construct(SubscriptionService $subscriptionService, UserRepository $userRepository,
                                ListStaffByPositionUseCase $positionUseCase)
    {
        if ($subscriptionService->check_subscription(self::class)) {
            $this->users = $userRepository->getUsersBySubscription(self::class);
        } else {
            $this->users = [];
        }
        $this->positionUseCase = $positionUseCase;
    }

    /**
     * Handle the event.
     */
    public function handle(PricingHasCompleted $event): void
    {
        $staffs = $this->positionUseCase->execute(StaffPosition::customerManager());

        //FIXME Модуль Notification - через RecipientResolverInterface
/*

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
*/
    }
}
