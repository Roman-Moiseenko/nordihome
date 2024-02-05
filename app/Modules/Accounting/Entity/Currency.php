<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Entity;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $sign
 * @property float $exchange
 * @property ArrivalDocument[] $arrivals
 */
class Currency extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'sign',
        'name',
        'exchange'
    ];

    public static function register(string $name, string $sign, float $exchange): self
    {
        return self::create([
            'name' => $name,
            'sign' => $sign,
            'exchange' => $exchange,
        ]);
    }

    public function setExchange(float $exchange): void
    {
        $this->exchange = $exchange;
        $this->save();
    }

    public function arrivals()
    {
        return $this->hasMany(ArrivalDocument::class, 'currency_id', 'id');
    }
}
