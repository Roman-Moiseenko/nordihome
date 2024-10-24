<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Entity;

use App\Modules\Admin\Entity\Admin;
use App\Traits\HtmlInfoData;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @property int $id
 * @property int $distributor_id
 * @property string $number
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property bool $completed
 * @property int $storage_id
 * @property int $currency_id
 * @property int $supply_id
 * @property float $exchange_fix //Курс на момент создания документа
 * @property string $comment Комментарий к документу, пока отключена, на будущее
 * @property int $staff_id - автор документа
 * @property Storage $storage
 * @property Currency $currency
 * @property ArrivalProduct[] $arrivalProducts
 * @property Distributor $distributor
 * @property SupplyDocument $supply
 * @property Admin $staff
 * @property PricingDocument $pricing
 */
class ArrivalDocument extends Model implements AccountingDocument
{

    use HtmlInfoData;

    protected $table = 'arrival_documents';

    protected $fillable = [
        'number',
        'distributor_id',
        'storage_id',
        'currency_id',
        'exchange_fix',
        'completed',
        'comment',
        'staff_id',
        'supply_id',
    ];
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public static function register(string $number, int $distributor_id,
                                    int $storage_id, Currency $currency,
                                    string $comment, ?int $staff_id): self
    {

        if (empty($number)) {
            $number = self::count() + 1;
        }
        return self::create([
            'number' => $number,
            'distributor_id' => $distributor_id,
            'storage_id' => $storage_id,
            'currency_id' => $currency->id,
            'exchange_fix' => $currency->getExchange(), //Запоминаем текущий курс
            'completed' => false,
            'comment' => $comment,
            'staff_id' => $staff_id,
        ]);
    }

    //*** IS-...
    public function isCompleted(): bool
    {
        return $this->completed == true;
    }

    public function isProduct(int $product_id): bool
    {
        foreach ($this->arrivalProducts as $item) {
            if ($item->product_id == $product_id) return true;
        }
        return false;
    }

    //*** SET-...
    public function completed()
    {
        $this->completed = true;
        $this->save();
    }

    public function setExchange(float $exchange_fix)
    {
        if ($this->completed == true) throw new \DomainException('Нельзя менять проведенный документ');
        $this->exchange_fix = $exchange_fix;
        $this->save();
        foreach ($this->arrivalProducts as $item) {
            $item->cost_ru = $item->cost_currency * $this->exchange_fix;
            $item->save();
        }
    }

    //*** GET-...
    #[ArrayShape([
        'quantity' => 'int',
        'cost_currency' => 'float',
        'price_sell' => 'float',
        'cost_ru' => 'float',
        'currency_sign' => 'string',
    ])]
    public function getInfoData(): array
    {
        $quantity = 0;
        $cost_currency = 0;
        $price_sell = 0;
        foreach ($this->arrivalProducts as $item) {
            $quantity += $item->quantity;
            $cost_currency += $item->quantity * $item->cost_currency;
            $price_sell += $item->quantity * $item->price_sell;
        }
        $cost_ru = ceil($cost_currency * $this->exchange_fix * 100) / 100;
        //dd($this->currency_id);
        return [
            'quantity' => $quantity,
            'cost_currency' => $cost_currency,
            'price_sell' => $price_sell,
            'cost_ru' => $cost_ru,
            'currency_sign' => $this->currency->sign,
        ];
    }

    public function getManager(): string
    {
        if ($this->staff_id == null) return 'Не установлен';
        return $this->staff->fullname->getFullName();
    }

    //*** RELATION
    public function staff()
    {
        return $this->belongsTo(Admin::class, 'staff_id', 'id');
    }

    public function supply()
    {
        return $this->belongsTo(SupplyDocument::class, 'supply_id', 'id');
    }

    public function arrivalProducts()
    {
        return $this->hasMany(ArrivalProduct::class, 'arrival_id', 'id');
    }

    public function distributor()
    {
        return $this->belongsTo(Distributor::class, 'distributor_id', 'id');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id', 'id');
    }

    public function storage()
    {
        return $this->belongsTo(Storage::class, 'storage_id', 'id');
    }

    public function pricing()
    {
        return $this->hasOne(PricingDocument::class, 'arrival_id', 'id');
    }


    public function setComment(string $comment): void
    {
        $this->comment = $comment;
        $this->save();
    }

    public function getComment(): string
    {
        return $this->comment;
    }
}
