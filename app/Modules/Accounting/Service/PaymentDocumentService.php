<?php

namespace App\Modules\Accounting\Service;

use App\Modules\Accounting\Entity\PaymentDocument;
use App\Modules\Accounting\Entity\Trader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentDocumentService
{
    public function create(int $distributor_id, float $amount, int $supply_id = null): PaymentDocument
    {
        $staff = Auth::guard('admin')->user();
        $paymentOrder = PaymentDocument::register($distributor_id, $amount, $staff->id);
        if (!is_null($supply_id)) {
            $paymentOrder->supply_id = $supply_id;
            $paymentOrder->save();
        }
        $this->setTrader($paymentOrder, Trader::default());

        return $paymentOrder;
    }

    private function setTrader(PaymentDocument $paymentOrder, Trader $trader): void
    {
        $paymentOrder->trader_id = $trader->id;
        $paymentOrder->account = $trader->organization->pay_account;
        $paymentOrder->save();
    }

    public function completed(PaymentDocument $payment): void
    {
        $payment->completed();
        //TODO Возможные расчеты или создание документов
    }

    public function set_info(PaymentDocument $payment, Request $request): void
    {
        $payment->number = $request->string('number')->value();
        $payment->created_at = $request->date('created_at');
        $payment->amount = $request->input('amount');
        $payment->comment = $request->string('comment')->value();
        $payment->save();
        //dd($request->all());
        if ($payment->trader_id != $request->integer('trader_id')) $this->setTrader(Trader::find($request->integer('trader_id')));
    }

    public function work(PaymentDocument $payment): void
    {
        $payment->work();
        //TODO Возможные перерасчеты или отмена документов
    }
}
