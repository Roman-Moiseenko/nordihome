<?php
declare(strict_types=1);

namespace App\Modules\Base\Entity;

class BankDetail
{
    public string $pay_account;
    public string $corr_account;
    public string $bank_name;
    public string $bik;

    public function __construct()
    {
        $this->pay_account = '';
        $this->corr_account = '';
        $this->bank_name = '';
        $this->bik = '';
    }

    public static function create(
        string $pay_account = '',
        string $corr_account = '',
        string $bank_name = '',
        string $bik = '',
        array $params = [])
    : self
    {
        $bank = new static();
        if (!empty($params)) {
            foreach ($params as $key => $value) {
                if (isset($bank->$key))
                    $bank->$key = $value ?? '';
            }
        } else {
            $bank->pay_account = $pay_account;
            $bank->corr_account = $corr_account;
            $bank->bank_name = $bank_name;
            $bank->bik = $bik;
        }
        return $bank;
    }

    public static function fromArray(?array $params)
    {
        $bank = new static();
        if (!empty($params)) {
            $bank->pay_account = $params['pay_account'] ?? '';
            $bank->corr_account = $params['corr_account'] ?? '';
            $bank->bank_name = $params['bank_name'] ?? '';
            $bank->bik = $params['bik'] ?? '';
        }
        return $bank;
    }

    public function toArray(): array
    {
        return json_decode(json_encode($this), true);
    }
}
