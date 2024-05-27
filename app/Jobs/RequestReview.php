<?php

namespace App\Jobs;

use App\Events\ThrowableHasAppeared;
use App\Mail\UserReview;
use App\Modules\Analytics\Entity\LoggerCron;
use App\Modules\Order\Entity\Order\Order;
use App\Modules\Product\Service\ReviewService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Tests\CreatesApplication;

class RequestReview implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, CreatesApplication;

    private Order $order;
    protected Application $app;

    /**
     * Create a new job instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Создаются черновые варианты отзывов на доставленные товары, и пакетно отправляются клиенту
     */
    public function handle(): void
    {

        try {
            $this->app = $this->createApplication();
            /** @var ReviewService $service */
            $service = $this->app->make('App\Modules\Product\Service\ReviewService');

            $user = $this->order->user;
            $products = [];
            foreach ($this->order->expenses as $expense) {
                foreach ($expense->items as $item) {
                    //Проверка есть ли уже отзыв на товар
                    if (is_null($user->getReview($item->orderItem->product_id))) {

                        $review = $service->createEmpty($item->orderItem->product, $user, $this->order);
                        $products[$item->orderItem->product_id][] = [
                            'name' => $item->orderItem->product->name,
                            'code' => $item->orderItem->product->code,
                            'url' => route('shop.product.view', $item->orderItem->product->slug),
                            'review' => $review,
                            'link_review' => route('shop.product.review.show', $review),
                            'bonus_review' => is_null($review->discount) ? null : $review->discount->amount,
                        ];
                    }
                }
            }
            Mail::to($user->email)->queue(new UserReview($user, $products));

        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
        }
    }
}
