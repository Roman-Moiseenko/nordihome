<?php

namespace App\Modules\Lead\Service;

use App\Modules\Accounting\Entity\Trader;
use App\Modules\Feedback\Entity\FormBack;
use App\Modules\Lead\Entity\Lead;
use App\Modules\Lead\Entity\LeadItem;
use App\Modules\Lead\Entity\LeadStatus;
use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Entity\Order\OrderExpense;
use App\Modules\Order\Entity\Order\OrderStatus;
use App\Modules\Order\Service\OrderService;
use App\Modules\User\Entity\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use JetBrains\PhpStorm\Deprecated;

class LeadService
{

    private OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function createLeadFromForm(FormBack $form): void
    {
        // $lead = Lead::register();
        // Log::info('Либ был создан!');
        $form->createLead($form->data());
        //TODO Вытащить из $form->data()  email и name

        /*
         if ($request->has('email')) {
             $email = $request->string('email')->trim()->value();
             $user = User::where('email', $email)->first();
             $form->lead->user_id = $user->id;
             $form->lead->save();
         } else if ($request->has('name')) {
             $form->lead->name = $request->string('name')->trim()->value();
             $form->lead->save();
         }
         */

    }

    public function createLeadFromOrder(Order $order): void
    {
        //TODO
        $data = [];
        $order->lead->create_lead($data);
        $order->lead->order_id = $order->id;
        $order->lead->user_id = $order->user_id;
        $order->lead->save();

        //Если есть менеджер ф-ция create_sales()
        if (!is_null($order->staff_id)) {
            $order->lead->staff_id = $order->staff_id;
            $order->lead->setStatus(LeadStatus::STATUS_IN_WORK);
            $order->lead->save();
        }

    }

    public function setStatus(Lead $lead, Request $request): bool
    {
        $newStatus = $request->integer('status');
        if (!isset(LeadStatus::STATUSES[$newStatus])) return false;

        if (!$this->checkStatus($lead, $newStatus)) return false;

        if ($lead->isNew()) {
            //$lead->setStaff(\Auth::guard('admin')->user()->id);
            $lead->staff_id = \Auth::guard('admin')->user()->id;
            $lead->setStatus($newStatus);
            $lead->save();
            $lead->refresh();
            //Если заявка уже является заказом
            if (!is_null($lead->order)) { //Установить менеджера
                $this->orderService->setManager($lead->order, $lead->staff_id);
            }
            return true;

        } else {
            if ($newStatus == LeadStatus::STATUS_NEW) {
                $lead->staff_id = null;
                foreach ($lead->statuses as $status) {
                    $status->delete();
                }
            }
            $lead->setStatus($newStatus);
            $lead->save();
            return true;
        }

    }

    private function checkStatus(Lead $lead, int $status): bool
    {
        if (is_null($lead->order_id) && $status > LeadStatus::STATUS_NOT_DECIDED) return false;
        //TODO Другие варианты
        return true;
    }

    //// Для событий по заказу ///

    /**
     * Отмена заказа - как вручную, так из события OrderHasCanceled
     */
    public function canceled(Lead $lead, int $reason): void
    {
        $lead->canceled = $reason;
        $lead->setStatus(LeadStatus::STATUS_CANCELED);
        $lead->finished_at = null;
        $lead->save();
    }

    /**
     * Заказ завершен из события OrderHasCompleted
     */
    public function completed(Lead $lead): void
    {
        $lead->completed = true;
        $lead->setStatus(LeadStatus::STATUS_COMPLETED);
        $lead->finished_at = null;
        $lead->save();
    }

    /**
     * Заказ ожидает оплаты из события OrderHasAwaiting
     */
    public function awaiting(Lead $lead): void
    {
        $lead->setStatus(LeadStatus::STATUS_INVOICE);
        $lead->save();
    }

    /**
     * Заказ вернули в работу из события OrderHasWork
     */
    public function work(Lead $lead): void
    {
        $lead->setStatus(LeadStatus::STATUS_IN_WORK);
        $staff = \Auth::guard('admin')->user();
        if (!is_null($staff)) $lead->staff_id = $staff->id;
        $lead->save();
    }

    /**
     * Заказ оплачен из события OrderHasPaid
     */
    public function paid(Lead $lead): void
    {
        $lead->setStatus(LeadStatus::STATUS_PAID);
        $lead->save();
    }
    /**
     * ExpenseHasCanceled
     */
    public function returnPaid(Lead $lead): void
    {
        $result = true;
        //Если хотя бы одно из распоряжений не отменено
        foreach ($lead->order->expenses as $expense) {
            if (!$expense->isCanceled()) $result = false;
        }
        if ($result) {
            $lead->assembly = false;
            $lead->save();
        }
        $this->paid($lead);
    }

    /**
     * Заказ на сборке из события ExpenseHasAssembling
     */
    public function assembly(Lead $lead): void
    {
        $lead->assembly = true;
        if ($lead->order->getQuantityRemains() == 0) {
            $lead->setStatus(LeadStatus::STATUS_ASSEMBLY);
            $lead->assembly = false;
        }
        $lead->save();
    }

    /**
     * Заказ на доставке из события ExpenseHasDelivery
     */
    public function delivery(Lead $lead): void
    {
        $result = true;
        $lead->delivery = true;
        foreach ($lead->order->expenses as $expense) {
            if ($expense->status < OrderExpense::STATUS_DELIVERY) $result = false;
        }
        if ($lead->order->getQuantityRemains() == 0 && $result) {
            $lead->setStatus(LeadStatus::STATUS_DELIVERY);
            $lead->assembly = false;
        }
        $lead->save();
    }

    public function setName(Lead $lead, Request $request): void
    {
        $lead->name = $request->string('name')->trim()->value();
        $lead->save();
    }

    #[Deprecated]
    public function setComment(Lead $lead, Request $request): void
    {
        $lead->comment = $request->string('comment')->trim()->value();
        $lead->save();
    }

    #[Deprecated]
    public function setFinished(Lead $lead, Request $request): void
    {
        $lead->finished_at = is_null($request->input('finished_at')) ? null : Carbon::parse($request->input('finished_at'));
        $lead->save();
    }

    public function addItem(Lead $lead, Request $request): void
    {
        $finished = $request->input('finished_at');
        $comment = $request->string('comment')->trim()->value();
        $item = LeadItem::new($comment, $finished);
        $item->staff_id = $lead->staff_id;
        $lead->items()->save($item);
        $lead->finished_at = $finished;
        $lead->comment = $comment;
        $lead->save();
    }

    public function createUser(Lead $lead, Request $request): void
    {
        $user = User::new(
            $request->string('email')->trim()->value(),
            $request->string('phone')->trim()->value()
        );
        $user->setNameField(
            $request->string('surname')->trim()->value(),
            $request->string('firstname')->trim()->value(),
            $request->string('secondname')->trim()->value(),

        );
        $lead->user_id = $user->id;
        $lead->save();
    }

    public function createOrder(Lead $lead, Request $request): Order
    {
        $order = $this->orderService->createOrder($lead->user_id);
        $order->setStatus(OrderStatus::SET_MANAGER);
        $order->setManager($lead->staff_id);
        $lead->order_id = $order->id;
        $lead->save();
        return $order;
    }

}
