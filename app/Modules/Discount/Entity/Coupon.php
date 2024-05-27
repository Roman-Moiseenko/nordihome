<?php
declare(strict_types=1);

namespace App\Modules\Discount\Entity;

use App\Modules\User\Entity\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;


//TODO Внести изменения в структуры
/**
 * @property int $id
 * @property int $user_id
 * @property int $bonus
 * @property int $status
 * @property string $code
 * @property Carbon $started_at
 * @property Carbon $finished_at
 *
 * @property bool $fixid //true - сумма скидки, false - %%
 * @property int $count
 * @property int $min_amount //Минимальная сумма заказа
 * @property int $who //кто выдал
 * @property int $rrr //Основание/условие выдачи
 * @property User $user
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

    public function isNew(): bool
    {
        return $this->status == self::NEW;
    }

    public function completed(): void
    {
        $this->status = self::COMPLETED;
        $this->save();
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

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function htmlStart(): string
    {
        return $this->started_at->translatedFormat('j F Y');
    }

    public function htmlFinish(): string
    {
        return $this->finished_at->translatedFormat('j F Y');
    }
}

