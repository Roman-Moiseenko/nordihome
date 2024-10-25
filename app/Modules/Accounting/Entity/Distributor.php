<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Entity;

use App\Modules\Base\Casts\BankDetailCast;
use App\Modules\Base\Casts\CompanyContactCast;
use App\Modules\Base\Casts\CompanyDetailCast;
use App\Modules\Base\Casts\GeoAddressCast;
use App\Modules\Base\Entity\BankDetail;
use App\Modules\Base\Entity\CompanyContact;
use App\Modules\Base\Entity\CompanyDetail;
use App\Modules\Base\Entity\CompanyModel;
use App\Modules\Base\Entity\GeoAddress;
use App\Modules\Product\Entity\Product;
use Faker\Provider\ar_EG\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $currency_id
 * @property int $organization_id
 * @property string $name
 * @property bool $default
 * @property ArrivalDocument[] $arrivals
 * @property Product[] $products
 * @property Currency $currency
 * @property Organization $organization
 */
class Distributor extends Model
{

    public $timestamps = false;
    public $fillable =[
        'name',
        'currency_id',
    ];

    public static function register(string $name, int $currency_id): self
    {
        return self::create([
            'name' => $name,
            'currency_id' => $currency_id,
            ]);
    }

    public function arrivals(): HasMany
    {
        return $this->hasMany(ArrivalDocument::class, 'distributor_id', 'id');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'distributors_products',
            'distributor_id', 'product_id')->withPivot('cost');
    }

    public function getCostItem(int $product_id): float
    {
        foreach ($this->products as $product) {
            if ($product->id == $product_id) return $product->pivot->cost;
        }
        return 0.0;
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_id', 'id');
    }

    public function isProduct(Product $product): bool
    {
        foreach ($this->products as $_product) {
            if ($_product->id == $product->id) return true;
        }
        return false;
    }

    public function addProduct(Product $product, float $cost): void
    {
        if ($this->isProduct($product)) {
            $this->updateProduct($product, $cost);
        } else {
            $this->products()->attach($product->id, ['cost' => $cost]);
        }
    }

    public function updateProduct(Product $product, float $cost): void
    {
        $this->products()->updateExistingPivot($product->id, ['cost' => $cost]);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'organization_id', 'id');
    }
}
