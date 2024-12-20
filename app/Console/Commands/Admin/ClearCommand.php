<?php
declare(strict_types=1);

namespace App\Console\Commands\Admin;

use App\Modules\Accounting\Entity\ArrivalDocument;
use App\Modules\Accounting\Entity\ArrivalExpenseDocument;
use App\Modules\Accounting\Entity\ArrivalProduct;
use App\Modules\Accounting\Entity\DepartureDocument;
use App\Modules\Accounting\Entity\Distributor;
use App\Modules\Accounting\Entity\DistributorProduct;
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
use App\Modules\Accounting\Service\MovementService;
use App\Modules\Accounting\Service\StorageService;
use App\Modules\Admin\Entity\Admin;
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
use Illuminate\Support\Facades\DB;
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

        /** @var Admin[] $staffs */
        $staffs = Admin::get();
        foreach ($staffs as $staff) {
            $staff->notifications()->delete();

        }
        $this->info('Уведомления очищены');

        /** @var Report[] $reports */
        $reports = Report::get();
        foreach ($reports as $report) {
            unlink($report->file);
            $report->delete();
        }
        $this->info('Отчеты удалены');

        $this->info('*******');


        $distributors = Distributor::get();

        $staff = Admin::where('role', Admin::ROLE_ADMIN)->first();

        foreach ($distributors as $distributor) {

            $supply = SupplyDocument::register(
                $distributor->id,
                $staff->id,
                $distributor->currency->exchange,
                $distributor->currency_id,
            );
            $supply->organization_id = $distributor->organization->id;
            $supply->save();
            $this->warn('Загрузка товаров ' . $distributor->products()->count() . ' В Заказ');
            foreach ($distributor->products as $product) {
                $distributor->products()->updateExistingPivot($product->id, ['cost' => 0, 'pre_cost' => null]);
                $item = SupplyProduct::new(
                    $product->id,
                    10,
                    1
                );
                $supply->products()->save($item);
            }
            $supply->number .= ' БП';
            $supply->comment = 'Базовое поступление';
            $supply->save();
            $this->info('Загрузка завершена');
            //$supply->completed();
        }



        return true;
    }

    private function clearDocument($documents, $caption): void
    {
        foreach ($documents as $item) $item->delete();
        $this->info($caption);
    }
}
