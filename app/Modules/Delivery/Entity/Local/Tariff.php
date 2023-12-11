<?php
declare(strict_types=1);

namespace App\Modules\Delivery\Entity\Local;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $distance
 * @property int $tariff
 * @property int $oversize
 */
class Tariff extends Model
{
    protected $table = 'tariff_local';

    public $timestamps = false;
    protected $fillable = [
        'distance',
        'tariff',
        'oversize'
    ];

    public static function register(int $distance, int $tariff, int $oversize = 0): self
    {
        return self::create([
            'distance' => $distance,
            'tariff' => $tariff,
            'oversize' =>$oversize,
        ]);
    }


}
