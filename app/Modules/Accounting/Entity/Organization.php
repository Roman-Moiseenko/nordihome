<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Entity;

use App\Modules\Base\Casts\FullNameCast;
use App\Modules\Base\Casts\GeoAddressCast;
use App\Modules\Base\Entity\FullName;
use App\Modules\Base\Entity\GeoAddress;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $short_name
 * @property GeoAddress $address
 * @property string $INN
 * @property string $KPP
 * @property string $OGRN
 * @property string BIK
 * @property string $bank
 * @property string $corr_account
 * @property string $account
 * @property string $email
 * @property string $phone
 * @property string $post_chief //Должность
 * @property FullName $chief
 * @property bool $default
 * @property Carbon $created_at
 * @property Carbon $updated_at
// * @property Storage[] $storages
 */
class Organization extends Model
{
    protected $fillable = [
        'name',
        'short_name',
        'INN',
    ];

    protected $attributes = [
        'address' => '{}',
        'chief' => '{}',
    ];

    public $timestamps = false;
    protected $casts = [
        'address' => GeoAddressCast::class,
        'chief' => FullNameCast::class,
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public static function register(string $name, string $short_name, string $INN): self
    {
        return self::create([
            'name' => $name,
            'short_name' => $short_name,
            'INN' => $INN,
        ]);
    }

    public function isDefault(): bool
    {
        return $this->default == true;
    }
}
