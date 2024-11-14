<?php

namespace App\Modules\Accounting\Entity;

use App\Modules\Admin\Entity\Admin;
use App\Modules\Base\Traits\CompletedFieldModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $number
 * @property float $amount
 * @property string $comment
 * @property int $staff_id
 * @property bool $completed
 * @property bool $manual
 * @property int $recipient_id
 * @property int $payer_id
 * @property string $recipient_account
 * @property string $payer_account
 *
 *
 *
 * @property string $bank_purpose Назначение платежа Банковские данные
 * @property string $bank_number Банковские данные
 * @property Carbon $bank_date Банковские данные
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Organization $recipient Получатель платежа
 * @property Organization $payer Плательщик платежа
 * @property Admin $staff
 * @property PaymentDecryption[] $decryptions
 */
class PaymentDocument extends Model
{
    use CompletedFieldModel;

    protected $attributes = [
        'comment' => '',
    ];
    protected $fillable = [
        'number',
        'amount',
        'recipient_id',
        'payer_id',
        'completed',
        'staff_id',
        'manual',
        'recipient_account',
        'payer_account'
    ];

    public static function register(int $recipient_id, string $recipient_account, int $payer_id, string $payer_account, float $amount, int $staff_id): self
    {
        return self::create([
            'number' => self::count() + 1,
            'amount' => $amount,
            'recipient_id' => $recipient_id,
            'payer_id' => $payer_id,
            'completed' => false,
            'staff_id' => $staff_id,
            'manual' => false,
            'recipient_account' => $recipient_account,
            'payer_account' => $payer_account
        ]);
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

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'staff_id', 'id');
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

}
