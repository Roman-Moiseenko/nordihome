<?php
declare(strict_types=1);

namespace App\Modules\Discount\Entity;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $user_id
 * @property int $bonus
 * @property int $status
 * @property string $code
 * @property Carbon $started_at
 * @property Carbon $finished_at
 *
 */
class Coupon extends Model
{
    const NEW = 501;
    const STARTED = 502; //Можно использовать, ??? Cron задача ?
    const FINISHED = 503; //Дата завершения прошла, купонном не воспользовались
    const CANCEL = 504;
    const COMPLETED = 505; //Использован

    public $timestamps = false;
    protected $fillable = [
        'user_id',
        'bonus',
        'status',
        'code',
        'started_at',
        'finished_at',
    ];
    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public static function register(int $user_id, int $bonus, $started_at, $finished_at): self
    {
        /** @var Coupon $coupon */
        $coupon =  self::make([
            'user_id' => $user_id,
            'bonus' => $bonus,
            'status' => self::NEW,
            'started_at' => $started_at,
            'finished_at' => $finished_at,
        ]);

        $coupon->generate();
        $coupon->save();
        return $coupon;
    }

    private function generate(): void
    {
        //$start = Carbon::parse('01-01-2024');
        //$difference = $start->diff(now())->days;
        $a = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $this->code = '';

        for ($i = 0; $i < rand(3, 5); $i++) {
            $this->code .= $a[rand(0, 35)];
        }
    }
}

