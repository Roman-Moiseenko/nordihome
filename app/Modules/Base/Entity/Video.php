<?php
declare(strict_types=1);

namespace App\Modules\Base\Entity;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $object_id
 * @property string $url
 * @property string $caption
 * @property string $description
 * @property int $sort
 */
class Video extends Model
{
    protected $fillable = [
        'url',
        'caption',
        'sort',
        'description',
    ];

    protected $hidden = [
        'product_id',
    ];

    final public static function register(string $url, string $caption = '', string $description = '', int $sort = 0): self
    {
        return self::make([
            'url' => $url,
            'caption' => $caption,
            'description' => $description,
            'sort' => $sort,
        ]);
    }

    public function videoable()
    {
        return $this->morphTo();
    }
}
