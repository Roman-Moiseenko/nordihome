<?php

namespace App\Modules\Auth\Infrastructure\Models;

use App\Modules\Base\Casts\GeoAddressCast;
use App\Modules\Base\Entity\GeoAddress;
use App\Modules\Catalog\Domain\ValueObjects\PriceType;
use App\Modules\Catalog\Entity\Review;
use App\Modules\Order\Infrastructure\Models\Order;
use App\Modules\User\Entity\Wish;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * @property int $id
 * @property string $last_name
 * @property string $first_name
 * @property string $middle_name
 * @property string $email почта уведомлений
 * @property string $phone
 * @property Carbon $birth_date
 * @property string $gender
 *
 * @property string $country
 * @property int $region_code
 * @property string $region
 * @property string $city
 * @property string $street
 * @property string $postal_code
 * @property bool $is_pickup
 *
 * @property Carbon $banned_at
 * @property bool $consented
 * @property Carbon $consented_at
 * @property string $policy_version
 * @property string $action_identifier
 * @property bool $consent_active
 * @property User $user
 * @property GeoAddress $address
 * @property string $price_type
 * @property float $discount
 * @property Wish[] $wishes
 * @property Review[] $reviews
 * @property Order[] $orders
 *
 */
class Client extends Model
{
    protected $table = 'clients';
    protected $attributes = [
        'address' => '{}',
    ];

    protected $fillable = [
        'last_name',
        'first_name',
        'middle_name',
        'email',
        'phone',
        'birth_date',
        'gender',
        'country',
        'region_code',
        'region',
        'city',
        'street',
        'postal_code',
        'banned_at',
        'consented',
        'consented_at',
        'policy_version',
        'action_identifier',
        'consent_active',
        'price_type',
        'discount',
        'is_pickup',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'banned_at' => 'datetime',
        'consented' => 'boolean',
        'consented_at' => 'datetime',
        'consent_active' => 'boolean',
        'address' => GeoAddressCast::class
    ];

    public function user(): MorphOne
    {
        return $this->morphOne(User::class, 'profileable');
    }

    public function getFullNameAttribute(): string
    {
        return implode(' ', array_filter([
            $this->last_name,
            $this->first_name,
            $this->middle_name,
        ]));
    }

    public function isBulk(): bool
    {
        return false;
    }
    public function isSpecial(): bool
    {
        return false;
    }

    //FixMe Сделать поле и изменение цены клиентам, плюс может быть индивидальную скидку
    public function getPriceType(): PriceType
    {
        return PriceType::retail();
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'client_id', 'id');
    }
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'client_id', 'id')->orderByDesc('created_at');
    }

    public function wishes(): HasMany
    {
        return $this->hasMany(Wish::class, 'client_id', 'id');
    }

    public function isWish(mixed $productId)
    {
        foreach ($this->wishes as $wish) {
            if ($wish->product_id == $productId) return true;
        }
        return false;
    }

    public function isStorage(): bool
    {
        return true;
    }

    public function isLocal(): bool
    {
        return false;
    }

    public function isRegion(): bool
    {
        return false;
    }

}
