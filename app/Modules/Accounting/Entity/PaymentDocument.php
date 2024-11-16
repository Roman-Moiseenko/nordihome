<?php

namespace App\Modules\Accounting\Entity;

use App\Modules\Admin\Entity\Admin;
use App\Modules\Base\Traits\CompletedFieldModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property float $amount
 * @property bool $manual
 * @property int $recipient_id
 * @property int $payer_id
 * @property string $recipient_account
 * @property string $payer_account
 *
 * @property string $bank_purpose Назначение платежа Банковские данные
 * @property string $bank_number Банковские данные
 * @property Carbon $bank_date Банковские данные

 * @property Organization $recipient Получатель платежа
 * @property Organization $payer Плательщик платежа
 * @property PaymentDecryption[] $decryptions
 */
class PaymentDocument extends AccountingDocument
{
    protected string $blank = 'Платежное поручение';
    protected $attributes = [
        'comment' => '',
    ];
    protected $fillable = [
        'amount',
        'recipient_id',
        'payer_id',
        'manual',
        'recipient_account',
        'payer_account'
    ];

    public static function register(int $recipient_id, string $recipient_account, int $payer_id, string $payer_account, float $amount, int $staff_id): self
    {
        $document = parent::baseNew($staff_id);
        $document->recipient_id = $recipient_id;
        $document->recipient_account = $recipient_account;
        $document->payer_id = $payer_id;
        $document->payer_account = $payer_account;
        $document->amount = $amount;
        $document->manual = false;
        $document->save();

        return $document;
    }

    public function manual(): void
    {
        $this->manual = true;
        $this->bank_purpose = 'В ручную';
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
                if ($debit != 0) {
                    $decryption = PaymentDecryption::register($debit, $supply->id, $this->bank_purpose);
                    $this->decryptions()->save($decryption);
                }
            }
        }
    }

    function documentUrl(): string
    {
        return route('admin.accounting.payment.show', ['payment' => $this->id]);
    }

    public function products(): HasMany
    {
        throw new \DomainException('Неверный вызов');
    }

    public function onBased(): ?array
    {
        return [];
    }
}
