<?php
declare(strict_types=1);

namespace App\Modules\Delivery\Entity;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property Carbon $date_at
 * @property boolean $blocked
 * @property CalendarPeriod[] $periods
 */
class Calendar extends Model
{

    public $timestamps = false;
    protected $fillable = [
        'date_at',
    ];

    protected $casts = [
        'date_at' => 'datetime',
    ];

    public function isBlocked(): bool
    {
        return $this->blocked == true;
    }

    public static function register(Carbon $date_at): self
    {
        return self::create([
            'date_at' => $date_at->toDateString(),
        ]);
    }

    public function getPeriod(int $time): ?CalendarPeriod
    {
        foreach ($this->periods as $period) {
            if ($period->time == $time) return $period;
        }
        return null;
    }

    public function addPeriod(int $time, float $weight, float $volume)
    {
        if ($this->getPeriod($time) != null) return null;
        $period = CalendarPeriod::new($time, $weight, $volume);
        $this->periods()->save($period);
    }

    public function periods()
    {
        return $this->hasMany(CalendarPeriod::class, 'calendar_id', 'id')->orderBy('time');
    }

    public function htmlDate(): string
    {
        return $this->date_at->translatedFormat('j F Y');
    }
}
