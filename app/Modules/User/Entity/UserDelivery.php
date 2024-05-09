<?php
declare(strict_types=1);

namespace App\Modules\User\Entity;

use App\Casts\GeoAddressCast;
use App\Entity\GeoAddress;
use App\Modules\Accounting\Entity\Storage;
use App\Modules\Delivery\Helpers\DeliveryHelper;
use App\Modules\Order\Entity\Order\OrderExpense;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $user_id
 * @property int $type
 * @property int $storage
 * @property GeoAddress $local
 * @property GeoAddress $region
 * @property string $company
 */
class UserDelivery extends Model
{
/*
    const STORAGE = 401;
    const LOCAL = 402;
    const REGION = 403;
*/
    public $timestamps = false;


    protected $fillable = [
        'user_id',
        'type',
        'storage',
        'region',
        'local',
        'company',
        'post',
    ];

    protected $casts = [
        'region' => GeoAddressCast::class,
        'local' => GeoAddressCast::class,
    ];

    public function isStorage(): bool
    {
        return $this->type == OrderExpense::DELIVERY_STORAGE;
    }

    public function isLocal(): bool
    {
        return $this->type == OrderExpense::DELIVERY_LOCAL;
    }

    public function isRegion(): bool
    {
        return $this->type == OrderExpense::DELIVERY_REGION;
    }

    public static function register(int $user_id): self
    {
        return self::create([
            'user_id' => $user_id,
            'local' => new GeoAddress(),
            'region' => new GeoAddress(),
        ]);
    }

    public function setDeliveryType(?int $type)
    {
        $this->update([
            'type' => $type,
        ]);
    }


    public function setDeliveryLocal($storage, GeoAddress $local)
    {
        if ($storage != null) $this->storage = $storage;
        if ($local->address != '') $this->local = $local;
        $this->save();
    }

    public function setDeliveryTransport(string $company, GeoAddress $region)
    {
        if ($region->address != '') $this->region = $region;
        if ($company != null) $this->company = $company;
        $this->save();
    }

    public function getAddressDelivery(): string
    {
        if ($this->isLocal()) return $this->local->address;
        if ($this->isRegion()) return $this->region->address . ' (' . DeliveryHelper::name($this->company) . ')';
        if ($this->isStorage()) return Storage::find($this->storage)->address;
        return 'Неопределенно';
        throw new \DomainException('Неверный тип доставки, невозможно вернуть адрес');
    }
}
