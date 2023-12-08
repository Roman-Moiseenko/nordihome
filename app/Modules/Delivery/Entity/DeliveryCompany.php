<?php
declare(strict_types=1);

namespace App\Modules\Delivery\Entity;

use App\Entity\Photo;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property int $type_delivery
 * @property string $url
 * @property Photo $image
 */
class DeliveryCompany extends Model
{
    const DELIVERY_LOCAL = 1;
    const DELIVERY_REGION = 2;
    const DELIVERY_ALL = 3;

    public $timestamps = false;

    public $fillable = [
        'name',
        'type_delivery',
    ];


    public static function register(string $name, int $type_delivery): self
    {
        return self::create([
            'name' => $name,
            'type_delivery' => $type_delivery,
        ]);
    }

    public function image()
    {
        return $this->morphOne(Photo::class, 'imageable')->withDefault();
    }


}
