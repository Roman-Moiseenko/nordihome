<?php
declare(strict_types=1);

namespace App\Modules\Base\Entity;

use Carbon\Carbon;

class BankPayment
{
    public string $purpose = '';
    public string $number = '';
    public ?Carbon $date = null;
    public string $bik_payer = '';
    public string $bik_recipient = '';
    public string $account_payer = ''; //Счет плательщика
    public string $account_recipient = ''; //Счет получателя


    public static function fromArray(?array $params): self
    {
        $payment = new static();
        if (!empty($params)) {
            $payment->purpose = $params['purpose'] ?? '';
            $payment->number = $params['number'] ?? '';
            $payment->date = is_null($params['date']) ? null : Carbon::parse($params['date']);
            $payment->bik_payer = $params['bik_payer'] ?? '';
            $payment->bik_recipient = $params['bik_recipient'] ?? '';
            $payment->account_payer = $params['account_payer'] ?? '';
            $payment->account_recipient = $params['account_recipient'] ?? '';
        }
        return $payment;
    }

    public static function createFromBankPayment(array $payment): self
    {
        $bank_payment = new static();

        $bank_payment->bik_payer = $payment['ПлательщикБИК'];
        $bank_payment->account_payer = $payment['ПлательщикРасчСчет'];
        $bank_payment->bik_recipient = $payment['ПолучательБИК'];
        $bank_payment->account_recipient = $payment['ПолучательСчет'];

        $bank_payment->purpose = $payment['НазначениеПлатежа'];
        $bank_payment->number = $payment['Номер'];
        $bank_payment->date = Carbon::parse($payment['Дата']);
        return $bank_payment;
    }


    public function toArray(): array
    {
        return json_decode(json_encode($this), true);
    }
}
