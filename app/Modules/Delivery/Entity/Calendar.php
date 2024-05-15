<?php
declare(strict_types=1);

namespace App\Modules\Delivery\Entity;

use App\Modules\Order\Entity\Order\OrderExpense;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property Carbon $date_at
 * @property int $period
 * @property float $weight
 * @property float $volume
// * @property int $truck_id - ???
// * @property int $staff_id
 * @property int $status
 * @property OrderExpense[] $expenses
 */
class Calendar extends Model
{

    const PERIOD_FIRST = 9901;
    const PERIOD_SECOND = 9902;
    const PERIOD_THIRD = 9903;
    const PERIOD_FOURTH = 9904;

    /// ?????
    const STATUS_DRAFT = 9951;
    const STATUS_FULL = 9952;
    const STATUS_COMPLETED = 9953;

    const PERIODS = [
        self::PERIOD_FIRST => '08:00 - 13:00',
        self::PERIOD_SECOND => '14:00 - 19:00',
    ];

    public $timestamps = false;
    protected $fillable = [
        'date_at',
        'period',
        'weight',
        'volume',
        'status'
    ];

    protected $casts = [
        'date_at' => 'datetime',
    ];

    public static function register(Carbon $date_at, int $period, float $weight, float $volume): self
    {
        return self::create([
            'date_at' => $date_at->toDateString(),
            'period' => $period,
            'weight' => $weight,
            'volume' => $volume,
            'status' => self::STATUS_DRAFT
        ]);
    }


    public function isDraft(): bool
    {
        return $this->status == self::STATUS_DRAFT;
    }

    public function isFull(): bool
    {
        return $this->status == self::STATUS_FULL;
    }

    public function isCompleted(): bool
    {
        return $this->status == self::STATUS_COMPLETED;
    }

    public function periodHtml(): string
    {
        return self::PERIODS[$this->period];
    }

    public function freeWeight(): float
    {
        $weight = 0;
        foreach ($this->expenses as $expense) {
            $weight += $expense->getWeight();
        }
        //TODO Подсчет свободного веса
        return $this->weight - $weight;
    }

    public function freeVolume(): float
    {
        //TODO Подсчет свободного объема

        $volume = 0;
        foreach ($this->expenses as $expense) {
            $volume += $expense->getVolume();
        }
        return $this->volume - $volume;
    }

    public function expenses()
    {
        return $this->belongsToMany(OrderExpense::class, 'calendars_expenses', 'calendar_id', 'expense_id');
    }

    public function htmlDate(): string
    {
        return $this->date_at->translatedFormat('j F Y');
    }
}
