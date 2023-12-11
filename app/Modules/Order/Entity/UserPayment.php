<?php
declare(strict_types=1);

namespace App\Modules\Order\Entity;

use App\Modules\Order\Entity\Payment\Payment;
use App\Modules\Order\Helpers\PaymentHelper;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $user_id
 * @property bool $persona //true - физ.лицо, false - юр.лицо
 * @property mixed $data //для физ.лица ФИО, для юр.лица - реквизиты
 * @property string $class_payment
 */
class UserPayment extends Model
{

    public $timestamps = false;
    protected $fillable = [
        'user_id',
        'class_payment',
        'data'
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public static function register(int $user_id): self
    {
        $user = self::make([
            'user_id' => $user_id,
            //'class_payment' => $class_payment,
        ]);
        $user->data = [];
        $user->save();
        return $user;
    }

    public function setPayment(string $class_payment)
    {
        $this->update(['class_payment' => $class_payment]);
    }

   public function isInvoice(): bool
    {
        if (empty($this->class_payment)) return false;
        return PaymentHelper::isInvoice($this->class_payment);
    }

    public function setInvoice(string $inn)
    {
        if (empty($this->class_payment)) throw new \DomainException('Не выбран тип оплаты');
        $this->data = PaymentHelper::invoice($this->class_payment, $inn);
            //(Payment::namespace() . '\\' . $this->class_payment)::getInvoiceData($inn);
        $this->save();
    }

    public function online():bool
    {
        if (empty($this->class_payment)) return false;
        return PaymentHelper::online($this->class_payment);
    }

    public function invoice()
    {
        if (empty($this->data)) return '';
        return $this->data['name'];
    }
}
