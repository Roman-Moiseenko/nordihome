<?php


namespace App\Modules\Order\Service;


use App\Events\OrderHasCanceled;
use App\Events\OrderHasRefund;
use App\Mail\OrderAwaiting;
use App\Modules\Accounting\Service\MovementService;
use App\Modules\Admin\Entity\Admin;
use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Entity\Order\OrderAddition;
use App\Modules\Order\Entity\Order\OrderPaymentRefund;
use App\Modules\Order\Entity\Order\OrderStatus;
use App\Modules\Order\Entity\UserPayment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Deprecated;

class SalesService
{

    private MovementService $movements;
    private ExpenseService $expenseService;

    public function __construct(
        MovementService $movements,
        ExpenseService $expenseService
    )
    {
        $this->movements = $movements;
        $this->expenseService = $expenseService;
    }

    public function setManager(Order $order, int $staff_id)
    {
        $staff = Admin::find($staff_id);
        if (empty($staff)) throw new \DomainException('Менеджер под ID ' . $staff_id . ' не существует!');
        $order->setStatus(OrderStatus::SET_MANAGER);
        $order->setManager($staff->id);
//        $order->responsible()->save(OrderResponsible::registerManager($staff->id));
    }

    public function setReserveService(Order $order, string $date, string $time)
    {
        $new_reserve = $date . ' ' . $time . ':00';
        $order->setReserve(Carbon::parse($new_reserve));
    }


    /**
     * Отправить заказ на оплату - резерв, присвоение номера заказу, счет, услуги по сборке
     * @param Order $order
     * @return void
     */
    public function setAwaiting(Order $order)
    {
        if ($order->status->value != OrderStatus::SET_MANAGER) throw new \DomainException('Нельзя отправить заказ на оплату. Не верный статус');
        //if ($order->getSellAmount() == 0)  throw new \DomainException('Нет товара или цена равна 0!');
        if ($order->getTotalAmount() == 0)  throw new \DomainException('Сумма заказа не может быть равно нулю');

        foreach ($order->items as $item) {
            if ($item->assemblage == true) {
                $addition = OrderAddition::new($item->getAssemblage(), OrderAddition::PAY_ASSEMBLY, $item->product->name);
                $order->additions()->save($addition);
                $item->assemblage = false;
                $item->save();
            }
        }
        $order->setReserve(now()->addDays(3));
        $order->setNumber();
        $order->setStatus(OrderStatus::AWAITING);
        $order->refresh();

        Mail::to($order->user->email)->queue(new OrderAwaiting($order));
        flash('Заказ успешно создан! Ему присвоен номер ' . $order->number, 'success');
    }


    public function destroy(Order $order)
    {
        if ($order->status->value == OrderStatus::FORMED) {
            $order->delete();
        } else {
            throw new \DomainException('Нельзя удалить заказ, который уже в работе');
        }
    }

    public function canceled(Order $order, string $comment)
    {
        $order->clearReserve();
        $order->setStatus(value: OrderStatus::CANCEL, comment: $comment);
        event(new OrderHasCanceled($order));
    }

    public function setStatus(Order $order, int $status)
    {
        $order->setStatus($status);
    }

    public function refund(Order $order, string $comment)
    {
        //TODO Возврат денег!!!!Алгоритм?? Тестить!
        $order->setStatus(OrderStatus::REFUND, $comment);
        //Возврат товаров в продажу

        $order->clearReserve();
        //Все платежи на возврат
        foreach ($order->payments as $payment) {
            //Платежный документ для возврата
            if (!$payment->isRefund()) {
                //Возможно для разных типов платежей разный способ расчета возврата денег
                $amount = $payment->amount;
                OrderPaymentRefund::register($payment->id, $amount, $comment);
                //$payment->setRefund();
            }
        }
        event(new OrderHasRefund($order)); //Оповещение менеджера по возврату денег
    }

    public function completed(Order $order)
    {
        $order->setStatus(OrderStatus::COMPLETED);
        $order->finished = true;
        $order->save();
        //TODO !!!!!

    }

    public function createOrder(Request $request)
    {
        return null;
    }

    /**
     * Пересчет суммы для выдачи товара по распоряжению.
     * Остаток неизрасходованного лимита денег должен быть выше
     * стоимости товаров и услуг для нового распоряжения
     * @param Order $order
     * @param string $_data
     * @return array
     */
    #[ArrayShape(['remains' => "float", 'expense' => "int", 'disable' => "bool"])]
    public function expenseCalculate(Order $order, string $_data): array
    {
        $remains = $order->getPaymentAmount() - $order->getExpenseAmount();
        $data = json_decode($_data, true);
        $amount = 0;

        foreach ($data['items'] as $item) { //Суммируем по товарам
            $id_item = (int)$item['id'];
            $amount += $order->getItemById($id_item)->sell_cost * (int)$item['value'];
        }

        foreach ($data['additions'] as $addition) { //Суммируем по услугам
                $amount += (float)$addition['value'];
        }

        return [
            'remains' => price($remains),
            'expense' => price($amount),
            'disable' => $amount > $remains || $amount == 0,
        ];
    }
}
