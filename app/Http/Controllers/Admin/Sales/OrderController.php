<?php


namespace App\Http\Controllers\Admin\Sales;


use App\Entity\Admin;
use App\Http\Controllers\Controller;
use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Entity\Order\OrderItem;
use App\Modules\Order\Entity\Order\OrderResponsible;
use App\Modules\Order\Entity\Order\OrderStatus;
use Illuminate\Http\Request;

/**
 * Общие операции с моделью Order. Все запросы POST или DELETE
 * Class OrderController
 * @package App\Http\Controllers\Admin\Sales
 *
 */
class OrderController extends Controller
{
    //TODO
    public function destroy(Order $order) {


    }

    public function del_item(OrderItem $item)
    {

    }

    public function set_manager(Request $request, Order $order) {
        //TODO Перенести в сервис
        $staff = Admin::find((int)$request['staff_id']);
        $order->setStatus(OrderStatus::SET_MANAGER);
        $order->responsible()->save(OrderResponsible::registerManager($staff->id));
        return redirect()->back();
    }
}
