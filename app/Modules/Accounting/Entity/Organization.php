<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Entity;

use App\Modules\Base\Casts\FullNameCast;
use App\Modules\Base\Casts\GeoAddressCast;
use App\Modules\Base\Entity\FullName;
use App\Modules\Base\Entity\GeoAddress;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
 * @property bool $default ///Удалить
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Organization[] $holding

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

    public function isDefault(): bool
    {
        return $this->default == true;
    }

    public function isContact(int $id): bool
    {
        foreach ($this->contacts as $contact) {
            if ($contact->id == $id) return true;
        }

        return false;
    }
}
