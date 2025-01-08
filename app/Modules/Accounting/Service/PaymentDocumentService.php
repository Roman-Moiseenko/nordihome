<?php

namespace App\Modules\Accounting\Service;

use App\Modules\Accounting\Entity\Organization;
use App\Modules\Accounting\Entity\PaymentDecryption;
use App\Modules\Accounting\Entity\PaymentDocument;
use App\Modules\Accounting\Entity\Trader;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use JetBrains\PhpStorm\Deprecated;

class PaymentDocumentService
{
    public function create(int $recipient_id, int $payer_id, float $amount): PaymentDocument
    {
        $staff = Auth::guard('admin')->user();

        return PaymentDocument::register(
            $recipient_id, //Получатель
            $payer_id, //Плательщик
            $amount, $staff->id
        );
    }

    public function completed(PaymentDocument $payment): void
    {
        //Проверка совпадения суммы
        DB::transaction(function () use($payment) {
            $amount = 0.0;
            foreach ($payment->decryptions as $decryption) {
                $amount += $decryption->amount;
            }
            if ($payment->amount != $amount) throw new \DomainException('Нельзя провести документ. Не совпадают суммы');

            foreach ($payment->decryptions as $decryption) {
                if ($decryption->amount == 0) $decryption->delete();
            }
            $payment->completed();
        });

        //TODO Возможные расчеты или создание документов
    }

    public function setInfo(PaymentDocument $payment, Request $request): void
    {
        $payment->baseSave($request->input('document'));

        $payment->amount = $request->input('amount');
        $payer = Organization::find($request->integer('payer_id')); //Смена плательщика
        if ($payment->payer_id != $payer->id) {
            $payment->payer_id = $payer->id;

            $payment->bank_payment->account_payer = $payer->pay_account;
            $payment->bank_payment->bik_payer = $payer->bik;
        }
        $payment->save();
    }

    public function work(PaymentDocument $payment): void
    {
        DB::transaction(function () use($payment) {
            $payment->work();
        });
    }

    public function delete(PaymentDocument $payment): void
    {
        if (!$payment->isCompleted()) $payment->delete();
    }

    public function setAmount(PaymentDecryption $decryption, Request $request): void
    {
        $decryption->amount = $request->float('amount');
        $decryption->save();
    }

    public function notPaid(PaymentDocument $payment): void
    {
        $payment->fillDecryptions();
    }


}
