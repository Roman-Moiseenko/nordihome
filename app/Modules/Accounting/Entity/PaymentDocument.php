<?php

namespace App\Modules\Accounting\Entity;

use App\Modules\Admin\Entity\Admin;
use App\Modules\Base\Casts\BankPaymentCast;
use App\Modules\Base\Entity\BankPayment;
use App\Modules\Base\Traits\CompletedFieldModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Исходящие платежи
 * @property float $amount
 * @property bool $manual
 * @property int $recipient_id
 * @property int $payer_id
 *
 * @property BankPayment $bank_payment

 * @property Organization $recipient Получатель платежа
 * @property Organization $payer Плательщик платежа
 * @property PaymentDecryption[] $decryptions
 */
class PaymentDocument extends AccountingDocument
{
    protected string $blank = 'Платежное поручение';
    protected $attributes = [
        'comment' => '',
        'bank_payment' => '{}',
    ];

    protected $fillable = [
        'amount',
        'recipient_id',
        'payer_id',
        'manual',
    ];
    protected $casts = [
        'bank_payment' => BankPaymentCast::class,
    ];

    public static function register(int $recipient_id, int $payer_id, float $amount, int $staff_id): self
    {
        $document = parent::baseNew($staff_id);
        $document->recipient_id = $recipient_id;
        $document->payer_id = $payer_id;
        $document->amount = $amount;
        $document->manual = false;
        $document->save();

        return $document;
    }

    public function manual(): void
    {
        $this->manual = true;
        $this->bank_payment->purpose = 'В ручную';
        $this->save();
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function addDecryption(float $amount, int $supply_id = null): void
    {
        $decryption = PaymentDecryption::register($amount, $supply_id);
        $this->decryptions()->save($decryption);
    }

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'recipient_id', 'id');
    }

    public function payer(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'payer_id', 'id');
    }

    public function decryptions(): HasMany
    {
        return $this->hasMany(PaymentDecryption::class, 'payment_id', 'id');
    }


    //TODO Сделать рефакторинг ???
    public function fillDecryptions(): void
    {
        $distributor = $this->recipient->distributor;
        if (!is_null($distributor)) {
            foreach ($distributor->supplies as $supply) {
                $debit = $supply->debit();
                //Если задолженность > 0 и  по данному заказу еще нет записи
                if ($debit != 0 && is_null($this->getDecryption($supply->id))) {
                    $decryption = PaymentDecryption::register($debit, $supply->id, $this->bank_payment->purpose);
                    $this->decryptions()->save($decryption);
                }
            }
        }
    }

    public function getDecryption(int $supply_id): ?PaymentDecryption
    {
        foreach ($this->decryptions as $decryption) {
            if ($decryption->supply_id == $supply_id) return $decryption;
        }
        return null;
    }

    function documentUrl(): string
    {
        return route('admin.accounting.payment.show', ['payment' => $this->id], false);
    }

    public function products(): HasMany
    {
        throw new \DomainException('Неверный вызов');
    }

    public function onBased(): ?array
    {
        return null;
    }

    public function onFounded(): ?array
    {
        $founded = [];
        foreach ($this->decryptions as $decryption) {
            $supply = $decryption->supply;
            $founded[] = $supply;
        }
        return $this->foundedGenerate($founded);
    }

    public function restore(): void
    {
        $is_trashed = false;
        foreach ($this->decryptions as $decryption) {
            if ($decryption->supply->trashed()) $is_trashed = true;
        }
        if ($is_trashed) throw new \DomainException('Восстановите документ основание');
        parent::restore();
    }
}
