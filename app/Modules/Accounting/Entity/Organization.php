<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Entity;

use App\Modules\Base\Casts\FullNameCast;
use App\Modules\Base\Casts\GeoAddressCast;
use App\Modules\Base\Entity\FileStorage;
use App\Modules\Base\Entity\FullName;
use App\Modules\Base\Entity\GeoAddress;
use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Entity\Order\OrderPayment;
use App\Modules\User\Entity\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @property int $id
 *
 * @property GeoAddress $legal_address
 * @property GeoAddress $actual_address
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
 * @property string $email
 * @property string $phone
 * @property OrganizationContact[] $contacts
 * @property string $post //Должность
 * @property FullName $chief
 * @property bool $active
 * @property int $holding_id
 * @property bool $default ///Удалить
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property OrganizationHolding $holding
 * @property Trader $trader
 * @property Distributor $distributor
 * @property User $shopper
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
        'legal_address' => '{}',
        'actual_address' => '{}',
        'chief' => '{}',
    ];

    protected $casts = [
        'legal_address' => GeoAddressCast::class,
        'actual_address' => GeoAddressCast::class,
        'chief' => FullNameCast::class,
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public static function register(string $full_name, string $short_name, string $inn): self
    {
        return self::create([
            'full_name' => $full_name,
            'short_name' => $short_name,
            'inn' => $inn,
            'active' => true,
            'foreign' => false,
        ]);
    }

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

    public function isHolding(): bool
    {
        if (is_null($this->holding)) return false;
        return true;
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

    public function getContactById(int $id):? OrganizationContact
    {
        foreach ($this->contacts as $contact) {
            if ($contact->id == $id) return $contact;
        }
        return null;
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

    public function holding(): BelongsTo
    {
        return$this->belongsTo(OrganizationHolding::class, 'holding_id', 'id');
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

        //return $this->hasOne(Trader::class, 'organization_id', 'id');
    }

    public function distributor(): HasOneThrough
    {
        return $this->hasOneThrough(
            Distributor::class,
            DistributorOrganization::class,
            'organization_id', 'id', 'id',
            'distributor_id');
        //return $this->hasOne(Distributor::class, 'organization_id', 'id');
    }

    public function shopper(): HasOneThrough
    {
        return $this->hasOneThrough(
            User::class,
            ShopperOrganization::class,
            'organization_id', 'id', 'id',
            'user_id');

        //return $this->hasOne(User::class, 'organization_id', 'id');
    }





    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

}
