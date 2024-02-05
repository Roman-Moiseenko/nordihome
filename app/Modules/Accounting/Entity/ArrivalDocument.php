<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Entity;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $distributor_id
 * @property string $number
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property bool $completed
 * @property int $storage_id
 * @property int $currency_id
 * @property float $exchange_fix //Курс на момент создания документа
 *
 * @property Storage $storage
 * @property Currency $currency
 * @property  ArrivalProduct[] $arrivalProducts
 */
class ArrivalDocument extends Model implements MovementInterface
{
    protected $table = 'arrival_documents';
    protected $fillable = [
        'number',
        'distributor_id',
        'storage_id',
        'currency_id',
        'exchange_fix',
        'completed',
    ];
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public static function register(string $number, int $distributor_id, int $storage_id, Currency $currency): self
    {
        return self::create([
            'number' => $number,
            'distributor_id' => $distributor_id,
            'storage_id' => $storage_id,
            'currency_id' => $currency->id,
            'exchange_fix' => $currency->exchange, //Запоминаем текущий курс
            'completed' => false,
        ]);
    }

    public function arrivalProducts()
    {
        return $this->hasMany(ArrivalProduct::class, 'arrival_id', 'id');
    }

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
}
