<?php

namespace App\Modules\Lead\Service;
use App\Modules\Feedback\Infrastructure\Models\FormBack;
use App\Modules\Lead\Infrastructure\Models\Lead;
use App\Modules\Lead\Infrastructure\Models\LeadStatus;
use App\Modules\Order\Entity\Order\OrderExpense;


class LeadService
{

    public function __construct(
        )
    {

    }

    public function createLeadFromForm(FormBack $form): void
    {
        // $lead = Lead::register();
        // Log::info('Либ был создан!');
        $form->createLead($form->data());
        //TODO Вытащить из $form->data()  email и name



    }

    //// Для событий по заказу ///

    /**
     * Отмена заказа - как вручную, так из события OrderHasCanceled
     */
    public function canceled(Lead $lead, int $reason): void
    {
        $lead->canceled = $reason;
        $lead->setStatus(LeadStatus::CANCELLED);
        $lead->finished_at = null;
        $lead->save();
    }

    /**
     * Заказ завершен из события OrderHasCompleted
     */
    public function completed(Lead $lead): void
    {
        $lead->completed = true;
        $lead->setStatus(LeadStatus::COMPLETED);
        $lead->finished_at = null;
        $lead->save();
    }

    /**
     * Заказ ожидает оплаты из события OrderHasAwaiting
     */
    public function awaiting(Lead $lead): void
    {
        $lead->setStatus(LeadStatus::INVOICE);
        $lead->save();
    }

    /**
     * Заказ вернули в работу из события OrderHasWork
     */
    public function work(Lead $lead): void
    {
        $lead->setStatus(LeadStatus::IN_WORK);
        $staff = auth()->user()->profileable;
        if (!is_null($staff)) $lead->staff_id = $staff->id;
        $lead->save();
    }

    /**
     * Заказ оплачен из события OrderHasPaid
     */
    public function paid(Lead $lead): void
    {
        $lead->setStatus(LeadStatus::PAID);
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
            $lead->setStatus(LeadStatus::ASSEMBLY);
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
            $lead->setStatus(LeadStatus::DELIVERY);
            $lead->assembly = false;
        }
        $lead->save();
    }


}
