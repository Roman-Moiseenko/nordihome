<?php
declare(strict_types=1);

namespace App\Entity;

use Illuminate\Database\Eloquent\Model;
/**
 * @property int $id
 * @property int $object_id
 * @property string $file
 * @property string $caption
 * @property string $description
 * @property int $sort
 */
class Video extends Model
{
    protected $fillable = [
        'video',
        'caption',
        'sort',
        'description',
    ];

    protected $hidden = [
        'product_id',
    ];

    final public static function register(string $file, string $caption = '', string $description = '', int $sort = 0): self
    {
        return self::new([
            'file' => $file,
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
