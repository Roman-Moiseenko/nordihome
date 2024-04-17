<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Service;

use App\Events\SupplyHasCompleted;
use App\Events\SupplyHasSent;
use App\Modules\Accounting\Entity\Distributor;
use App\Modules\Accounting\Entity\Storage;
use App\Modules\Accounting\Entity\SupplyDocument;
use App\Modules\Accounting\Entity\SupplyProduct;
use App\Modules\Accounting\Entity\SupplyStack;
use App\Modules\Admin\Entity\Admin;
use App\Modules\Order\Entity\Order\OrderItem;
use App\Modules\Product\Entity\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SupplyService
{

    private ArrivalService $arrivalService;

    public function __construct(ArrivalService $arrivalService)
    {
        $this->arrivalService = $arrivalService;
    }

    public function add_stack(OrderItem $item, int $storage_id): SupplyStack
    {
        /** @var Admin $staff */
        $staff = Auth::guard('admin')->user();
        $stack = SupplyStack::register($item->product_id, $item->quantity, $staff->id, $storage_id, 'Заказ # ' . $item->order->htmlNum());
        $item->supply_stack_id = $stack->id;
        $item->save();
        return $stack;
    }

    public function del_stack(SupplyStack $stack)
    {
        if (!is_null($stack->orderItem)) throw new \DomainException('Нельзя удалить товар из стека под Заказ клиенту!');
        $staff = $stack->staff;
        //TODO Оповещение Менеджера ??
        $stack->delete();
    }

    //Создание пустого заказа
    public function create_empty(Distributor $distributor): SupplyDocument
    {
        /** @var Admin $staff */
        $staff = Auth::guard('admin')->user();
        $supply = SupplyDocument::register($staff->id, $distributor->id, '');

        return $supply;
    }

    public function create(int $distributor_id, array $data)
    {
        $distributor = Distributor::find($distributor_id);
        $supply = $this->create_empty($distributor);

        foreach ($data as $stack_id) {
            /** @var SupplyStack $stack */
            $stack = SupplyStack::find((int)$stack_id); //В стеке указываем Документ на заказ
            $stack->supply_id = $supply->id;
            $stack->save();
            $supply->addProduct($stack->product, $stack->quantity);
            $supply->refresh();
        }
        return $supply;
    }


    public function add_product(SupplyDocument $supply, int $product_id, int $quantity)
    {
        $distributor = $supply->distributor;
        /** @var Product $product */
        $product = Product::find($product_id);
        if (!$distributor->isProduct($product)) { //Если товара нет у поставщика $supply->distributor, то
            $distributor->addProduct($product, 0); // добавляем с ценой закупа = 0
        }
        $supply->addProduct($product, $quantity);//Добавляем товар в Заказ
    }

    public function del_product(SupplyProduct $supplyProduct)
    {
        $supply = $supplyProduct->supply;
        //Проверка на стек, если есть в стеке удалить нельзя
        foreach ($supply->stacks as $stack) {
            if ($stack->product_id == $supplyProduct->product_id) {
                throw new \DomainException('Нельзя удалить товар, который добавлен через стек заказов');
            }
        }
        $supplyProduct->delete();
    }

    public function set_product(SupplyProduct $supplyProduct, int $quantity)
    {
        $supply = $supplyProduct->supply;
        //Проверка на стек, если кол-во меньше чем в стеке, то изменить нельзя
        ///Доп.защита!!
        $quantity_stack = $supply->getQuantityStack($supplyProduct->product);
        if ($quantity < $quantity_stack) throw new \DomainException('Кол-во товара по стеку ' . $quantity_stack . '. Нельзя ставить меньше.');
        $supplyProduct->quantity = $quantity;
        $supplyProduct->save();
        return true;
    }


    public function sent(SupplyDocument $supply)
    {
        $supply->status = SupplyDocument::SENT;
        $supply->setNumber();
        $supply->save();

        event(new SupplyHasSent($supply));
    }

    public function completed(SupplyDocument $supply)
    {
        //Проходим по стеку и вытаскиваем товары
        $distributor = $supply->distributor;
        $storage_base = Storage::where('default', true)->first()->id;

        //Создаем Поступление на базовый склад
        $arrival = $this->arrivalService->create([
            'number' => 'Из заказа ' . $supply->htmlNum(),
            'distributor' => $distributor->id,
            'storage' => $storage_base,
        ]);
        $arrival->supply_id = $supply->id;
        $arrival->save();
        foreach ($supply->products as $supplyProduct) {
            //Добавляем товар в поступление
            $this->arrivalService->add($arrival, [
                    'product_id' => $supplyProduct->product_id,
                    'quantity' => $supplyProduct->quantity
                ]
            );
        }
/*
        //Хранилища из стека по текущему заказу
        $storages_in_stack = SupplyStack::select('storage_id')->where('supply_id', $supply->id)->groupby('storage_id')->pluck('storage_id')->toArray();
        //Убираем базовый склад
        foreach ($storages_in_stack as $i => $storage_id) {
            if ($storage_id == $storage_base) unset($storages_in_stack[$i]);
        }
        $supply_products = []; //Все товары и кол-во в заказе
        foreach ($supply->products as $product) {
            $supply_products[$product->product_id] = $product->quantity;
        }

        foreach ($storages_in_stack as $storage_id) {
            //Получаем список товаров по хранилищу из стека, просуммировано
            $stack_products = DB::table('supply_stack')
                ->select('product_id', DB::raw('SUM(quantity) as total_quantity'))->
                where('supply_id', $supply->id)->where('storage_id', $storage_id)->
                groupBy('product_id')->get();

            //Создаем Поступление
            $arrival = $this->arrivalService->create([
                'number' => 'Из заказа ' . $supply->htmlNum(),
                'distributor' => $distributor->id,
                'storage' => $storage_id,
            ]);
            $arrival->supply_id = $supply->id;
            $arrival->save();

            foreach ($stack_products as $item) {
                //Добавляем товар в поступление
                $this->arrivalService->add($arrival, [
                        'product_id' => $item->product_id,
                        'quantity' => (int)$item->total_quantity
                    ]
                );
                //Уменьшаем кол-во товара из списка
                $supply_products[$item->product_id] -= (int)$item->total_quantity;
            }
        }
        */
        /*****/
        //Создаем Поступление на базовый склад
      /*  $arrival = $this->arrivalService->create([
            'number' => 'Из заказа ' . $supply->htmlNum(),
            'distributor' => $distributor->id,
            'storage' => $storage_base,
        ]);
        $arrival->supply_id = $supply->id;
        $arrival->save();
        foreach ($supply_products as $product_id => $quantity) {
            //Добавляем товар в поступление
            $this->arrivalService->add($arrival, [
                    'product_id' => $product_id,
                    'quantity' => (int)$quantity
                ]
            );
        }*/
        $supply->status = SupplyDocument::COMPLETED;
        $supply->save();
        event(new SupplyHasCompleted($supply));
    }


}
