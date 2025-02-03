<?php
declare(strict_types=1);

namespace App\Modules\Delivery\Entity;

use App\Modules\Order\Entity\Order\OrderExpense;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $calendar_id
 * @property int $time
 * @property int $caption
 * @property float $weight
 * @property float $volume
 * @property int $status
 * @property OrderExpense[] $expenses
 * @property Calendar $calendar
 */
class CalendarPeriod extends Model
{
    const TIME_AM = 9901;
    const TIME_PM = 9902;


    /// ?????
    const STATUS_DRAFT = 9951;
    const STATUS_FULL = 9952;
    const STATUS_COMPLETED = 9953;

    const TIMES = [
        self::TIME_AM => '08:00-13:00',
        self::TIME_PM => '14:00-19:00',
    ];


    public $timestamps = false;
    protected $fillable = [
        'time',
        'weight',
        'volume',
        'status'
    ];

    public static function new(int $time, float $weight, float $volume): self
    {
        return  self::make([
            'time' => $time,
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

    public function timeHtml(): string
    {
        return self::TIMES[$this->time];
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
        return ceil(($this->volume - $volume) * 1000)/1000;
    }

    public function expenses()
    {
        return $this->belongsToMany(OrderExpense::class, 'calendars_expenses', 'period_id', 'expense_id');
    }

    public function calendar()
    {
        return $this->belongsTo(Calendar::class, 'calendar_id', 'id');
    }
}
