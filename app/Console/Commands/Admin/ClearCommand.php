<?php
declare(strict_types=1);

namespace App\Console\Commands\Admin;

use App\Modules\Accounting\Entity\ArrivalDocument;
use App\Modules\Accounting\Entity\ArrivalExpenseDocument;
use App\Modules\Accounting\Entity\DepartureDocument;
use App\Modules\Accounting\Entity\InventoryDocument;
use App\Modules\Accounting\Entity\MovementDocument;
use App\Modules\Accounting\Entity\PaymentDocument;
use App\Modules\Accounting\Entity\PricingDocument;
use App\Modules\Accounting\Entity\RefundDocument;
use App\Modules\Accounting\Entity\Storage;
use App\Modules\Accounting\Entity\StorageItem;
use App\Modules\Accounting\Entity\SupplyDocument;
use App\Modules\Accounting\Entity\SupplyProduct;
use App\Modules\Accounting\Entity\SupplyStack;
use App\Modules\Delivery\Entity\Calendar;
use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Entity\Order\OrderStatus;
use App\Modules\Order\Entity\OrderReserve;

use App\Modules\Product\Entity\Product;
use App\Modules\Service\Entity\Report;
use App\Modules\Shop\Cart\Storage\DBStorage;
use App\Modules\User\Entity\CartCookie;
use App\Modules\User\Entity\CartStorage;
use App\Modules\User\Entity\ParserStorage;
use App\Modules\User\Entity\Wish;
use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;


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

        $storage_items = StorageItem::get();
        foreach ($storage_items as $item) {
            $item->update(['quantity' => 0]);
        }
        $this->info('Склады очищены');




        $this->clearDocument(Order::get(), 'Заказы удалены');
        $this->clearDocument(OrderReserve::get(), 'Резерв очищен');

        $this->clearDocument(RefundDocument::get(), 'Возврату удалены');
        $this->clearDocument(ArrivalExpenseDocument::get(), 'Доп.расходы удалены');
        $this->clearDocument(MovementDocument::get(),'Перемещения обнулены');
        $this->clearDocument(InventoryDocument::get(),'Инвентаризация очищена');
        $this->clearDocument(DepartureDocument::get(),'Списания обнулены');

        $this->clearDocument(PricingDocument::get(),'Цены обнулены');
        $this->clearDocument(ArrivalDocument::get(),'Поступления обнулены');

        $this->clearDocument(PaymentDocument::get(),'Платежки обнулены');

        $this->clearDocument(SupplyStack::get(),'Стек заказов очищен');
        $this->clearDocument(SupplyDocument::get(),'Заказы товаров обнулены');

        $this->clearDocument(Calendar::get(),'Календарь очищены');
        $this->clearDocument(CartStorage::get(),'Корзина очищена');
        $this->clearDocument(CartCookie::get(),'Куки очищены');
        $this->clearDocument(ParserStorage::get(),'Корзина парсера очищена');
        $this->clearDocument(Wish::get(),'Избранное очищено');

/*
        $items = DistributorProduct::get();
        foreach ($items as $item) {
            $item->update([
                'cost' => null,
                'pre_cost' => null,
            ]);
        }*/


        //FIXME - очистка уведомлений
        $this->info('Уведомления очищены');

        /** @var Report[] $reports */
        $reports = Report::get();
        foreach ($reports as $report) {
            unlink($report->file);
            $report->delete();
        }
        $this->info('Отчеты удалены');

        $this->info('*******');





        return true;
    }

    private function clearDocument($documents, $caption): void
    {
        foreach ($documents as $item) $item->superDelete();
        $this->info($caption);
    }
}
