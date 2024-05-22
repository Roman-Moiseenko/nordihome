<?php
declare(strict_types=1);

namespace App\Console\Commands\Admin;

use App\Modules\Accounting\Entity\ArrivalDocument;
use App\Modules\Accounting\Entity\ArrivalProduct;
use App\Modules\Accounting\Entity\Distributor;
use App\Modules\Accounting\Entity\MovementDocument;
use App\Modules\Accounting\Entity\Storage;
use App\Modules\Accounting\Entity\StorageItem;
use App\Modules\Accounting\Entity\SupplyDocument;
use App\Modules\Accounting\Entity\SupplyStack;
use App\Modules\Accounting\Service\MovementService;
use App\Modules\Accounting\Service\StorageService;
use App\Modules\Delivery\Entity\Calendar;
use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Entity\Order\OrderStatus;
use App\Modules\Order\Entity\OrderReserve;

use App\Modules\Product\Entity\Product;
use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use function Laravel\Prompts\confirm;

class ClearCommand extends Command
{
    use ConfirmableTrait;

    protected $signature = 'db:clear';

    protected $description = 'Очистка базы, для тестирования';


    public function handle(): bool
    {

        if (! $this->confirmToProceed()) {
            return false;
        }

        $orders = Order::get();
        foreach ($orders as $order) {
            $order->delete();
        }
        $this->info('Заказы удалены');
        $reserves = OrderReserve::get();
        foreach ($reserves as $reserve) {
            $reserve->delete();
        }
        $this->info('Резерв очищен');

        $storage_items = StorageItem::get();
        foreach ($storage_items as $item) {
            $item->update(['quantity' => 0]);
        }
        $this->info('Склады очищены');

        $products = Product::where('published', true)->get();
        foreach ($products as $item) {
            $item->update(['count_for_sell' => 0]);
        }
        $this->info('Кол-во товаров обнулено');

        $products = Product::where('published', false)->get();
        foreach ($products as $item) {
            $item->delete();
        }
        $this->info('Товары черновики удалены');

        $documents = ArrivalDocument::get();
        foreach ($documents as $item) {
            $item->delete();
        }
        $this->info('Поступления обнулены');

        $documents = MovementDocument::get();
        foreach ($documents as $item) {
            $item->delete();
        }
        $this->info('Перемещения обнулены');

        $documents = SupplyDocument::get();
        foreach ($documents as $item) {
            $item->delete();
        }
        $this->info('Заказы товаров обнулены');

        $documents = SupplyStack::get();
        foreach ($documents as $item) {
            $item->delete();
        }
        $this->info('Стек заказов очищен');

        $calendars = Calendar::get();
        foreach ($calendars as $item) {
            $item->delete();
        }
        $this->info('Календарь очищен');

        $this->info('*******');
        $distributor = Distributor::first();
        $arrival = ArrivalDocument::register(
            'Базовое поступление',
            $distributor->id,
            Storage::first()->id,
            $distributor->currency,
            '',
            null
        );

        $products = Product::where('published', true)->get();
        foreach ($products as $product) {
            //Добавляем в документ
            $item = ArrivalProduct::new(
                $product->id,
                10,
                100,
                $arrival->exchange_fix * 100,
                $product->getLastPrice()
            );

            $arrival->arrivalProducts()->save($item);
        }

        $storagesService = new StorageService();
        $storagesService->arrival($arrival->storage, $arrival->arrivalProducts()->getModels());

        $arrival->completed();

        return true;
    }
}
