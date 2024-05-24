<?php

namespace App\Jobs;

use App\Events\ThrowableHasAppeared;
use App\Modules\Analytics\Entity\LoggerCron;
use App\Modules\Order\Entity\Order\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Tests\CreatesApplication;

class RequestReview implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, CreatesApplication;

    private Order $order;
    protected $app;


    /**
     * Create a new job instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //LoggerCron::new('Очередь на отзывы по заказу - ' . $this->order->htmlNumDate());
        try {

            $this->app = $this->createApplication();
            $service = $this->app->make('App\Modules\Shop\Parser\ParserService'); //new ParserService(new HttpPage());

            $user = $this->order->user;
            $products = [];
            foreach ($this->order->expenses as $expense) {
                foreach ($expense->items as $item) {
                    $products[$item->orderItem->product_id][] = [
                        'name' => $item->orderItem->product->name,
                        'code'=> $item->orderItem->product->code,
                        'url' => route('shop.product.view', $item->orderItem->product->slug),
                        'review' => $service->generateUrl($item->orderItem->product, $user),
                    ];


                }
            }


        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
        }

        //TODO Формируем список ссылок на отзывы, фиксируем get-параметры для бонусов и отправляем клиенту письмо
    }
}
