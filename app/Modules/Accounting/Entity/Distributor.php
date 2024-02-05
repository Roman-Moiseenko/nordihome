<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Entity;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property ArrivalDocument[] $arrivals
 */
class Distributor extends Model
{
    public $timestamps = false;
    public $fillable =[
        'name',
    ];

    public static function register(string $name): self
    {
        return self::create(['name' => $name]);
    }

    public function arrivals()
    {
        return $this->hasMany(ArrivalDocument::class, 'distributor_id', 'id');
    }
}
