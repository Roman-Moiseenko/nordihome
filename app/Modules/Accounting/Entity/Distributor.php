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

/**
 * @property int $id
 * @property int $currency_id
 * @property int $organization_id
 * @property string $name
 *
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

    public function arrivals()
    {
        return $this->hasMany(ArrivalDocument::class, 'distributor_id', 'id');
    }

    public function products()
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

    public function currency()
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
        $this->products()->attach($product->id, ['cost' => $cost]);
    }

    public function updateProduct(Product $product, float $cost)
    {
        $this->products()->updateExistingPivot($product->id, ['cost' => $cost]);
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class, 'organization_id', 'id');
    }
}
