<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Service;

use App\Entity\Admin;
use App\Modules\Accounting\Entity\Distributor;
use App\Modules\Accounting\Entity\SupplyDocument;
use App\Modules\Accounting\Entity\SupplyProduct;
use App\Modules\Accounting\Entity\SupplyStack;
use App\Modules\Order\Entity\Order\OrderItem;
use App\Modules\Product\Entity\Product;
use Illuminate\Support\Facades\Auth;

class SupplyService
{

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
            if (!empty($supplyItem = $supply->getProduct($stack->product))) {
                $supplyItem->quantity += $stack->quantity;
                $supplyItem->save();
            } else {
                $supplyItem = SupplyProduct::new($stack->product_id, $stack->quantity); //В документ заносим данные из стека
                $supply->products()->save($supplyItem);
            }
            $supply->refresh();
        }
        return $supply;
    }


    public function add_product(SupplyDocument $supply, Product $product, int $quantity)
    {

        //Если товара нет у поставщика $supply->distributor, то
        // добавляем с ценой закупа = 0

    }


    public function arrival(SupplyDocument $supply)
    {
        //Проходим по стеку и вытаскиваем товары
        //из хранилищ и создаем несколько документов поступления,
        //Оставшиеся товары добавляем в хранилище по умолчанию

        //Для заказов ставим резерв
    }

}
