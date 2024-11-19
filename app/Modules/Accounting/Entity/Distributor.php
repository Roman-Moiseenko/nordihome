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
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

/**
 * @property int $id
 * @property int $currency_id
 * @property int $organization_id
 * @property string $name
 * @property bool $foreign
 * @property ArrivalDocument[] $arrivals
 * @property Product[] $products
 * @property Currency $currency
 * @property Organization $organization
 * @property Organization[] $organizations
 * @property SupplyDocument[] $supplies
 * @property PaymentDocument[] $orders
 * @property RefundDocument[] $refunds
 */
class Distributor extends Model
{

    public $timestamps = false;
    public $fillable =[
        'name',
        'currency_id',
        'foreign',
    ];

    public static function register(string $name, int $currency_id): self
    {
        return self::create([
            'name' => $name,
            'currency_id' => $currency_id,
            'foreign' => false,
            ]);
    }

    /**
     * Долг перед поставщиком по оплате товаров
     */
    public function debit(): float
    {
        $amount = 0;
        /** @var SupplyDocument[] $supplies */ //Увеличиваем долг проведенными заказами
        $supplies = $this->supplies()->where('completed', true)->get(); //Проведенные заказы
        foreach ($supplies as $supply) {
            $amount += $supply->getAmount();
        }
        /** @var RefundDocument $refunds */ //Уменьшаем долг проведенными возвратами
     /*   $refunds = $this->refunds()->where('completed', true)->get();
        foreach ($refunds as $refund) {
            $amount -= $refund->getAmount();
        }*/
        return $amount;
    }

    /**
     * Оплачено всего за товары
     */
    public function credit(): float
    {
        $amount = PaymentDocument::selectRaw('SUM(amount * 1) AS total')
            ->where('recipient_id', $this->organization_id)->where('completed', true)
            ->first();
        return (float)($amount->total ?? 0);

    }

    public function arrivals(): HasMany
    {
        return $this->hasMany(ArrivalDocument::class, 'distributor_id', 'id');
    }

    public function supplies(): HasMany
    {
        return $this->hasMany(SupplyDocument::class, 'distributor_id', 'id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(PaymentDocument::class, 'distributor_id', 'id');
    }

    public function refunds(): HasMany
    {
        return $this->hasMany(RefundDocument::class, 'distributor_id', 'id');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'distributors_products',
            'distributor_id', 'product_id')->withPivot(['cost', 'pre_cost']);
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
        $d_product = $this->getProduct($product->id);
        if ($d_product->pivot->cost !== $cost) //Если закуп.изменилась, то текущая => предыдуща, новая => текущая
            $this->products()->updateExistingPivot($product->id, ['cost' => $cost, 'pre_cost' => $d_product->pivot->cost]);
    }

    public function organization(): HasOneThrough
    {
        return $this->hasOneThrough(Organization::class, DistributorOrganization::class, 'distributor_id', 'id', 'id', 'organization_id')
            ->where('distributor_organizations.default', true);


        //return $this->belongsTo(Organization::class, 'organization_id', 'id');
       /* return $this->
        belongsToMany(Organization::class, 'distributor_organizations', 'distributor_id', 'organization_id', 'id', 'id')
            ->wherePivot('default', true)->latest();*/
    }

    public function organizations(): BelongsToMany
    {
        return $this->
        belongsToMany(Organization::class, 'distributor_organizations', 'distributor_id', 'organization_id', 'id', 'id')
            ->withPivot(['default']);
    }

    public function getProduct(int $product_id)
    {
        foreach ($this->products as $_product) {
            if ($_product->id == $product_id) return $_product;
        }
        return null;
    }
}
