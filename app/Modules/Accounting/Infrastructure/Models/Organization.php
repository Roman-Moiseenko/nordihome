<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Infrastructure\Models;

use App\Modules\Accounting\Entity\Distributor;
use App\Modules\Accounting\Entity\DistributorOrganization;
use App\Modules\Accounting\Entity\PaymentDocument;
use App\Modules\Auth\Infrastructure\Models\Client;
use App\Modules\Base\Entity\FileStorage;
use App\Modules\Guide\Entity\VAT;
use App\Modules\Order\Entity\Order\OrderPayment;
use App\Modules\Order\Infrastructure\Models\Order;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @property int $id
 *
 * @property string $legal_address
 * @property array $actual_address
 * @property array $geo
 *
 * @property string $full_name
 * @property string $short_name
 * @property string $inn
 * @property string $kpp
 * @property string $ogrn
 * @property bool $foreign
 *
 * @property string $bik
 * @property string $bank_name
 * @property string $corr_account
 * @property string $pay_account
 *
 * @property int $vat_id
 * @property VAT $VAT
 *
 * @property string $email
 * @property string $phone
 * @property OrganizationContact[] $contacts
 * @property string $post //Должность
 * @property array $chief
 * @property bool $active
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Trader $trader
 * @property Distributor $distributor
 * @property Client $shopper
 * @property FileStorage[] $files
 * @property FileStorage[] $contracts
 * @property FileStorage[] $documents
 * @property PaymentDocument[] $paymentsPayer - Заплачено Поставщикам
 * @property PaymentDocument[] $paymentsRecipient - Получено Поставщиком
 *
 * @property OrderPayment[] $paymentsShopper - Куплено Покупателем
 * @property OrderPayment[] $paymentsTrader - Продано Продавцом
 */
class Organization extends Model
{
    protected $fillable = [
        'full_name',
        'short_name',
        'inn',
        'active',
        'foreign',
    ];

    protected $attributes = [
        'legal_address' => '',
        'actual_address' => '[]',
        'geo' => '[]',
        'chief' => '',
    ];

    protected $casts = [
        'actual_address' => 'array',
        'geo' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function contacts(): HasMany
    {
        return $this->hasMany(OrganizationContact::class, 'organization_id', 'id');
    }

    public function isContact(int $id): bool
    {
        foreach ($this->contacts as $contact) {
            if ($contact->id == $id) return true;
        }

        return false;
    }

    public function isForeign(): bool
    {
        return $this->foreign == true;
    }

    public function isTrader(): bool
    {
        if (is_null($this->trader)) return false;
        return true;
    }

    public function isDistributor(): bool
    {
        if (is_null($this->distributor)) return false;
        return true;
    }

    public function isShopper(): bool
    {
        if (is_null($this->shopper)) return false;
        return true;
    }

    public function getContactById(int $id): ?OrganizationContact
    {
        foreach ($this->contacts as $contact) {
            if ($contact->id == $id) return $contact;
        }
        return null;
    }

    public function getEmails(): array
    {
        $array = [];
        if (!empty($this->email)) $array['Общий'] = $this->email;
        foreach ($this->contacts as $contact) {
            if (!empty($contact->email))
                $array[$contact->post] = $contact->email;
        }
        return $array;

    }

    public function types(): string
    {
        $types = [];
        if ($this->isTrader()) $types[] = 'Продавец';
        if ($this->isShopper()) $types[] = 'Покупатель';
        if ($this->isDistributor()) $types[] = 'Поставщик';
        return implode(' | ', $types);
    }

    //RELATIONS
    public function files(): MorphMany
    {
        return $this->morphMany(FileStorage::class, 'fileable')->orderByDesc('created_at');;
    }

    public function contracts(): MorphMany
    {
        return $this->morphMany(FileStorage::class, 'fileable')->where('type', 'contract')->orderByDesc('created_at');;
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(FileStorage::class, 'fileable')->where('type', '<>', 'contract')->orderByDesc('created_at');
    }

    public function paymentsShopper(): HasOneThrough
    {
        //TODO Протестировать
        return $this->hasOneThrough(
            OrderPayment::class,
            Order::class,
            'shopper_id', 'order_id', 'id', 'id'
        );
    }

    public function paymentsTrader(): HasOneThrough
    {
        //TODO Протестировать
        return $this->hasOneThrough(
            OrderPayment::class,
            Order::class,
            'trader_id', 'order_id', 'id', 'id'
        );
    }

    public function trader(): HasOneThrough
    {
        return $this->hasOneThrough(
            Trader::class,
            TraderOrganization::class,
            'organization_id', 'id', 'id',
            'trader_id');
    }

    public function distributor(): HasOneThrough
    {
        return $this->hasOneThrough(
            Distributor::class,
            DistributorOrganization::class,
            'organization_id', 'id', 'id',
            'distributor_id');
    }

    public function shopper(): HasOneThrough
    {
        return $this->hasOneThrough(
            Client::class,
            ShopperOrganization::class,
            'organization_id', 'id', 'id',
            'client_id');

    }
    //СПРАВОЧНИКИ GUIDE
    public function VAT(): BelongsTo
    {
        return $this->belongsTo(VAT::class, 'vat_id', 'id')->withDefault(['name' => 'Без НДС', 'value' => null]);
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

}
