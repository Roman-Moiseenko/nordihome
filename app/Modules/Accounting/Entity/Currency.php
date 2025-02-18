<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Entity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property string $sign
 * @property float $exchange
 * @property string $cbr_code
 * @property float $fixed - фиксированный курс
 * @property int $code
 * @property boolean $default - по-умолчанию Рубль
// * @property int $extra // + %
 * @property ArrivalDocument[] $arrivals
 * @property SupplyDocument[] $supplies
 */
class Currency extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'sign',
        'name',
        'exchange',
        'cbr_code',
        'default',
        'code',
        'fixed',
    ];

    protected $casts = [
        'fixed' => 'float',
        'exchange' => 'float',
        'default' => 'boolean',
    ];

    public static function register(
        string $name,
        string $sign,
        float  $exchange,
        string $cbr_code = '',
    ): self
    {
        return self::create([
            'name' => $name,
            'sign' => $sign,
            'exchange' => $exchange,
            'cbr_code' => $cbr_code,
            'fixed' => $exchange,
            'default' => false
        ]);
    }

    public function setExchange(float $exchange): bool
    {
        if ($this->exchange !== $exchange) {
            $this->exchange = $exchange;
            $this->save();
            return true;
        }
        return false;
    }

    public function getExchange(): float
    {
        return $this->exchange;

        // return (int)ceil(($this->exchange + $this->exchange * $this->extra / 100) * 100) / 100;
    }

    public function getExchangeExtra(): float
    {
        return (int)ceil(($this->exchange + $this->exchange * $this->extra / 100) * 100) / 100;
    }

    public function valueRub(int $rub): int
    {
        return (int)ceil($rub * $this->getExchange());
    }

    public function valueRubExtra(int $rub): int
    {
        return (int)ceil($rub * $this->getExchangeExtra());
    }

    public function arrivals(): HasMany
    {
        return $this->hasMany(ArrivalDocument::class, 'currency_id', 'id');
    }

    public function supplies(): HasMany
    {
        return $this->hasMany(SupplyDocument::class, 'currency_id', 'id');
    }
}
