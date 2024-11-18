<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Entity;

use App\Modules\Base\Casts\FullNameCast;
use App\Modules\Base\Casts\GeoAddressCast;
use App\Modules\Base\Entity\FullName;
use App\Modules\Base\Entity\GeoAddress;
use App\Modules\User\Entity\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
 */
class Organization extends Model
{
    protected $fillable = [
        'full_name',
        'short_name',
        'inn',
        'active',
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

    public function holding(): BelongsTo
    {
        return$this->belongsTo(OrganizationHolding::class, 'holding_id', 'id');
    }

    public function trader(): HasOne
    {
        return $this->hasOne(Trader::class, 'organization_id', 'id');
    }

    public function distributor(): HasOne
    {
        return $this->hasOne(Distributor::class, 'organization_id', 'id');
    }

    public function shopper(): HasOne
    {
        return $this->hasOne(User::class, 'organization_id', 'id');
    }

    public function types(): string
    {
        $types = [];
        if ($this->isTrader()) $types[] = 'Продавец';
        if ($this->isShopper()) $types[] = 'Покупатель';
        if ($this->isDistributor()) $types[] = 'Поставщик';
        return implode(' | ', $types);
    }

    public function getContactById(int $id):? OrganizationContact
    {
        foreach ($this->contacts as $contact) {
            if ($contact->id == $id) return $contact;
        }
        return null;
    }
}
