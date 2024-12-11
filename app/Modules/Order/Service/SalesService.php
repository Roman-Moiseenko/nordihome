<?php


namespace App\Modules\Order\Service;


use App\Events\OrderHasCanceled;
use App\Mail\OrderAwaiting;
use App\Modules\Accounting\Service\MovementService;
use App\Modules\Admin\Entity\Admin;
use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Entity\Order\OrderAddition;
use App\Modules\Order\Entity\Order\OrderStatus;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Deprecated;

#[Deprecated]
class SalesService
{

    #[Deprecated]
    public function setManager(Order $order, int $staff_id)
    {
        $staff = Admin::find($staff_id);
        if (empty($staff)) throw new \DomainException('Менеджер под ID ' . $staff_id . ' не существует!');
        $order->setStatus(OrderStatus::SET_MANAGER);
        $order->setManager($staff->id);
    }

    #[Deprecated]
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
    #[Deprecated]
    public function setAwaiting(Order $order)
    {
        if ($order->status->value != OrderStatus::SET_MANAGER) throw new \DomainException('Нельзя отправить заказ на оплату. Не верный статус');
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

    #[Deprecated]
    public function destroy(Order $order)
    {
        if ($order->status->value == OrderStatus::FORMED) {
            $order->delete();
        } else {
            throw new \DomainException('Нельзя удалить заказ, который уже в работе');
        }
    }

    /**
     * Отменить заказ
     * @param Order $order
     * @param string $comment
     * @return void
     */
    #[Deprecated]
    public function canceled(Order $order, string $comment)
    {
        $order->clearReserve();
        $order->setStatus(value: OrderStatus::CANCEL, comment: $comment);
        event(new OrderHasCanceled($order));
    }

    /**
     * Пересчет суммы для выдачи товара по распоряжению.
     * Остаток неизрасходованного лимита денег должен быть выше
     * стоимости товаров и услуг для нового распоряжения
     * @param Order $order
     * @param string $_data
     * @return array
     */
    #[ArrayShape(['remains' => "float", 'discount'=> "float", 'expense' => "int", 'disable' => "bool"])]
    #[Deprecated]
    public function expenseCalculate(Order $order, string $_data): array
    {
        $remains = $order->getPaymentAmount() - $order->getExpenseAmount()+  $order->getCoupon() + $order->getDiscountOrder();
        $data = json_decode($_data, true);
        $amount = 0;

        foreach ($data['items'] as $item) { //Суммируем по товарам
            $id_item = (int)$item['id'];
            $amount += $order->getItemById($id_item)->sell_cost * (float)$item['value'];
        }

        foreach ($data['additions'] as $addition) { //Суммируем по услугам
                $amount += (float)$addition['value'];
        }

        return [
            'remains' => price($remains),
            'expense' => price($amount),
            'discount' => price($order->getCoupon() + $order->getDiscountOrder()),
            'disable' => $amount > $remains || $amount == 0,
        ];
    }
}
